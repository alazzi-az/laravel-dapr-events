<?php

namespace AlazziAz\DaprEventsListener\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeListenerCommand extends Command
{
    protected $signature = 'dapr-events:listener 
        {event : The fully qualified class name of the event} 
        {--name= : Override the generated listener class name} 
        {--force : Overwrite the listener if it already exists}';

    protected $description = 'Create a new Dapr event listener class';

    public function __construct(
        protected Filesystem $files
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $event = ltrim($this->argument('event'), '\\');
        $listener = $this->option('name') ?? class_basename($event).'Listener';
        $namespace = trim($this->laravel->getNamespace(), '\\').'\\Listeners';
        $qualified = $namespace.'\\'.$listener;
        $path = app_path('Listeners/'.str_replace('\\', '/', $listener).'.php');

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->components->error('Listener already exists.');

            return self::FAILURE;
        }

        $this->makeDirectory($path);

        $stub = $this->files->get(__DIR__.'/../../stubs/dapr-listener.stub');
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ event }}', '{{ event_class }}'],
            [$namespace, $listener, $event, class_basename($event)],
            $stub
        );

        $this->files->put($path, $stub);

        $this->components->info("Listener [{$qualified}] created.");
        $this->components->info("Remember to add #[\\AlazziAz\\DaprEvents\\Attributes\\Topic] to your event if needed.");

        return self::SUCCESS;
    }

    protected function makeDirectory(string $path): void
    {
        $directory = dirname($path);

        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}
