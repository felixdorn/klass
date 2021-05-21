<?php

namespace Felix\Klass\Commands;

use Felix\Klass\Call;
use Felix\Klass\Facades\Klass;
use Felix\Klass\Visitors\CallableVisitor;
use Felix\Klass\Visitors\Visitor;
use function Felix\PropertyAccessor\access;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Throwable;

class KlassExtractCommand extends Command implements Visitor
{
    protected $name          = 'klass:extract {--plain}';
    protected $description   = 'Extracts dynamic classes from your blade components';
    protected array $classes = [];
    protected BladeCompiler $bladeCompiler;

    public function __construct()
    {
        $this->bladeCompiler = app('blade.compiler');

        parent::__construct();
    }

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
            } catch (Throwable) {
            }

            $evaluated ??= $value;

            if (!is_scalar($evaluated) && !is_null($evaluated)) {
                $evaluated = '[invalid]';
            }

            $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', (string) $evaluated, $content);
            /* @phpstan-ignore-next-line */
            $content = str_replace('$' . $attribute, $evaluated, $content);
        }

        $content = preg_replace_callback('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
            if (Str::contains($match[1], '@')) {
                $match[0] = isset($match[4]) ? $match[1] . $match[4] : $match[1];
            } elseif (array_key_exists($match[1], access($this->bladeCompiler, 'customDirectives'))) {
                $match[0] = \call($this->bladeCompiler, 'callCustomDirective', $match[1], Arr::get($match, 4));
            } elseif (method_exists($this->bladeCompiler, 'compile' . ucfirst($match[1]))) {
                $match[0] = call($this->bladeCompiler, 'compile' . $match[1], Arr::get($match, 4));
            }

            $code = isset($match[4]) ? $match[0] : $match[0] . $match[2];
            ob_start();
            $evaluted = eval('?> ' . $code);
            $printed = ob_get_clean();

            if ($evaluted === null) {
                $evaluted = $printed;
            }

            try {
                return $evaluted;
            } catch (Throwable) {
                return '[invalid evaluated code]';
            }
        }, $content);

        preg_match_all('/[^<>"\'`\s]*[^<>"\'`\s:]/m', $content ?: '', $matches);

        // We filter out all irrelevant classes, we might want to straight up remove that for performances reasons.
        $this->classes = array_merge($this->classes, $matches[0]);
    }

    public function getClasses(): array
    {
        return $this->classes;
    }
}
