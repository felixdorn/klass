<?php

namespace Felix\TailwindClassExtractor\Commands;

use Felix\TailwindClassExtractor\Extractor\Extractor;
use Felix\TailwindClassExtractor\Finder\Finder;
use Felix\TailwindClassExtractor\Processor\Processor;
use Illuminate\Console\Command;

class TailwindExtractCommand extends Command
{
    protected $name = 'tailwind:extract';

    public function handle(): void
    {
        $classes = (new Processor)->process(
            new Finder(),
            new Extractor()
        );

        file_put_contents(config('extractor.output'), implode(' ', $classes));

        $this->info(count($classes) . " classes extracted.");
    }
}
