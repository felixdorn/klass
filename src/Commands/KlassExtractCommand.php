<?php

namespace Felix\Klass\Commands;

use Felix\Klass\ComponentCall;
use Felix\Klass\Facades\Klass;
use Illuminate\Console\Command;
use Throwable;

class KlassExtractCommand extends Command
{
    protected $name        = 'klass:extract {--plain}';
    protected $description = 'Extracts dynamic classes from your blade components';

    public function handle(): void
    {
        $classes = [];

        Klass::tree()->visit(function (ComponentCall $call) use (&$classes) {
            $attributes = $call->getAttributes();
            $content = $call->getComponent()->getContents();

            foreach ($attributes as $attribute => $value) {
                try {
                    $evaluated = eval("return $value");
                } catch (Throwable $exception) {
                }

                $evaluated ??= $value;

                if (!is_scalar($evaluated) && !is_null($evaluated)) {
                    $evaluated = '[invalid]';
                }

                $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', (string) $evaluated, $content);
                /* @phpstan-ignore-next-line */
                $content = str_replace('$' . $attribute, $evaluated, $content);
            }

            preg_match_all('/[^<>"\'`\s]*[^<>"\'`\s:]/m', $content, $matches);

            $classes = array_merge($classes, $matches[0]);
        });

        file_put_contents(config('klass.output'), implode(' ', $classes));

        $count = count($classes);

        $this->info(sprintf(
            '%s dynamic %s extracted in %ss.',
            $count,
            $count > 1 ? 'classes' : 'class',
            round(microtime(true) - LARAVEL_START, 3)
        ));
    }
}
