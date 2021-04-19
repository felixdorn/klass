<?php

namespace Felix\TailwindClassExtractor\Extractor;

use Illuminate\Support\Str;

class ComponentCompiler
{
    public function compile(string $code): array
    {
        $matches = array_merge_recursive(
            $this->compileSelfClosingTags($code),
            $this->compileOpeningTags($code)
        );

        if (count(array_filter($matches)) === 0) {
            return [];
        }

        $components = [];

        foreach ($matches[1] as $k => $componentName) {
            $compiledString = app('blade.compiler')->compileString($code);

            preg_match('/\$__env->getContainer\(\)->make\(([a-zA-Z1-9\\\\]+' . ucfirst($componentName) . ')::class,/m', $compiledString, $classMatch);

            $components[] = [$componentName, $classMatch[1], $this->compileAttributeString($matches[2][$k])];
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

        return $matches;
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

        return $matches;
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

        if (blank($value)) {
            $value = 'true';
        }

        if (Str::startsWith($value, ["'", '"'])) {
            $value = substr($value, 1, -1);
        }

        return $value;
    }
}
