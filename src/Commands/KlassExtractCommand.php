<?php

namespace Felix\Klass\Commands;

use Felix\Klass\Processor;
use Illuminate\Console\Command;

class KlassExtractCommand extends Command
{
    protected $name = 'klass:extract';

    public function handle(): void
    {
        $classes = (new Processor())->process();
//
//        foreach ($classes as $class) {
//            if (str_contains($class, '$')) {
//                $this->line($class);
//            }
//        }

        file_put_contents(config('klass.output'), implode(' ', $classes));
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
