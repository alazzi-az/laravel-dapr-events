<?php

namespace AlazziAz\DaprEventsListener\Support;

use Illuminate\Filesystem\Filesystem;

class ClassFinder
{
    public function __construct(
        protected Filesystem $files
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function within(string $path): array
    {
        if (! $this->files->exists($path)) {
            return [];
        }

        $classes = [];

        foreach ($this->files->allFiles($path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = $this->extractClassName($file->getPathname());

            if ($class) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    protected function extractClassName(string $path): ?string
    {
        $contents = $this->files->get($path);

        if (! preg_match('/^namespace\s+([^;]+);/m', $contents, $namespaceMatch)) {
            return null;
        }

        if (! preg_match('/^(?:abstract\s+)?class\s+([^\s]+)/m', $contents, $classMatch)) {
            return null;
        }

        $namespace = trim($namespaceMatch[1]);
        $class = trim($classMatch[1]);

        return $namespace.'\\'.$class;
    }
}
