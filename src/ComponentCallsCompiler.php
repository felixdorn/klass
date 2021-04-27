<?php

namespace Felix\Klass;

use Illuminate\Support\Str;

class ComponentCallsCompiler
{
    public function compile(string $code): array
    {
        $matches = array_merge_recursive(
            $this->compileSelfClosingTags($code),
            $this->compileOpeningTags($code)
        );

        $classComponentAliases = app('blade.compiler')->getClassComponentAliases();

        if (count(array_filter($matches)) === 0) {
            return [];
        }

        $components = [];

        foreach ($matches['name'] as $k => $componentName) {
            $compiledString = app('blade.compiler')->compileString($code);

            if (array_key_exists($componentName, $classComponentAliases)) {
                $componentClass = $classComponentAliases[$componentName];
            } else {
                $componentClass = ucfirst(Str::afterLast($componentName, '.'));
                $componentClass = preg_replace_callback('/-([a-zA-Z1-9]+)/', function ($matches) {
                    return ucfirst($matches[1]);
                }, $componentClass) ?? '';

                preg_match('/\$__env->getContainer\(\)->make\(([a-zA-Z1-9\\\\]+' . ucfirst($componentClass) . ')::class,/m', $compiledString, $classMatch);

                $componentClass = $classMatch[1];
            }

            $components[] = [$componentName, $componentClass, $this->compileAttributeString($matches['attributes'][$k])];
        }

        return $components;
    }

    protected function compileSelfClosingTags(string $code): array
    {
        preg_match_all("/
            <
                \s*
                x[-\:](?'name'[\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
            \/>
        /x", $code, $matches);

        return [
            'name'       => $matches['name'],
            'attributes' => $matches['attributes'],
        ];
    }

    protected function compileOpeningTags(string $code): array
    {
        preg_match_all("/
            <
                \s*
                x[-\:](?'name'[\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x", $code, $matches);

        return [
            'name'       => $matches['name'],
            'attributes' => $matches['attributes'],
        ];
    }

    protected function compileAttributeString(string $attributes): array
    {
        preg_match_all('/
            (?<attribute>[\w\-:.@]+)
            (
                =
                (?<value>
                    (
                        \"[^\"]+\"
                        |
                        \\\'[^\\\']+\\\'
                        |
                        [^\s>]+
                    )
                )
            )?
        /x', $attributes, $matches);

        return array_combine($matches['attribute'], array_map([$this, 'normalizeAttributeValue'], $matches['value'])) ?: [];
    }

    protected function normalizeAttributeValue(string $value): string
    {
        $value = trim($value);

        if (empty($value)) {
            $value = 'true';
        }

        if (Str::startsWith($value, ["'", '"'])) {
            $value = substr($value, 1, -1);
        }

        return $value;
    }
}
