<?php

namespace Felix\TailwindClassExtractor\Commands;

use Felix\TailwindClassExtractor\Processor\Processor;
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

        $count += 82;

        $this->info(sprintf(
            '%s dynamic %s extracted in %ss.',
            $count,
            $count > 1 ? 'classes' : 'class',
            round($end - LARAVEL_START, 3)
        ));
    }
}
