<?php

namespace AlazziAz\DaprEvents\Console;

use AlazziAz\DaprEvents\ServiceProvider;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'dapr-events:install {--force : Overwrite any existing configuration file}';

    protected $description = 'Publish the Dapr events configuration, routes, and stubs.';

    public function handle(): int
    {
        $tags = [
            'dapr-events-config',
            'dapr-events-stubs',
        ];

        foreach ($tags as $tag) {
            $this->callSilent('vendor:publish', [
                '--provider' => ServiceProvider::class,
                '--tag' => $tag,
                '--force' => $this->option('force'),
            ]);
        }

        $this->components->info('Dapr events resources published.');
        $this->components->info('Add Route::daprSubscriptions() to your routes/api.php to expose subscriptions.');

        return self::SUCCESS;
    }
}
