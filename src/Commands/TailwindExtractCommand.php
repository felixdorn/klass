<?php

namespace Felix\TailwindClassExtractor\Commands;

use Felix\TailwindClassExtractor\Processor;
use Illuminate\Console\Command;

class TailwindExtractCommand extends Command
{
    protected $name = 'tailwind:extract';

    public function handle(): void
    {
        $classes = (new Processor())->process();
        file_put_contents(config('extractor.output'), implode(' ', $classes));
        $end = microtime(true);

        $count = count($classes);

        $this->info(sprintf(
            '%s dynamic %s extracted in %ss.',
            $count,
            $count > 1 ? 'classes' : 'class',
            round($end - LARAVEL_START, 3)
        ));
    }
}
