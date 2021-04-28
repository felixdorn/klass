<?php

namespace Felix\Klass\Commands;

use Felix\Klass\Call;
use Felix\Klass\Facades\Klass;
use Felix\Klass\Visitors\CallableVisitor;
use Felix\Klass\Visitors\Visitor;
use Illuminate\Console\Command;
use Throwable;

class KlassExtractCommand extends Command implements Visitor
{
    protected $name          = 'klass:extract {--plain}';
    protected $description   = 'Extracts dynamic classes from your blade components';
    protected array $classes = [];

    public function handle(): void
    {
        $calls = Klass::calls()->addVisitor(new CallableVisitor([$this, 'visit']));
        $calls->visit();

        file_put_contents(config('klass.output'), implode(' ', $this->classes));

        $count = count($this->classes);

        $this->line($calls->length() . ' components calls.');
        $this->info(sprintf(
            '%s dynamic %s extracted in %ss.',
            $count,
            $count > 1 ? 'classes' : 'class',
            round(microtime(true) - LARAVEL_START, 3)
        ));
    }

    public function visit(Call $call): void
    {
        $attributes = $call->getAttributes();
        $content    = $call->getComponent()->getContents();

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

        // We filter out all irrelevant classes, we might want to straight up remove that for performances reasons.
        $this->classes = array_merge($this->classes, array_filter($matches[0], function (string $class) use ($attributes) {
            foreach ($attributes as $attribute) {
                if (str_contains($class, $attribute)) {
                    return true;
                }
            }

            return false;
        }));
    }

    public function getClasses(): array
    {
        return $this->classes;
    }
}
