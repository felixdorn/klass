# Klass for Laravel

Klass extracts your dynamic classes in your Blade components to a file that PurgeCSS can process.

The plugin for Laravel Mix can be found [here](). (no yet released, still WIP)

[![Tests](https://github.com/felixdorn/klass/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/klass/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/klass/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/klass/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/klass/version)](//packagist.org/packages/felixdorn/klass)
[![Total Downloads](https://poser.pugx.org/felixdorn/klass/downloads)](//packagist.org/packages/felixdorn/klass)
[![License](https://poser.pugx.org/felixdorn/klass/license)](//packagist.org/packages/felixdorn/klass)

## Installation

> Requires [PHP7.4.0+](https://php.net/releases)

You can install the package via composer:

```bash
composer require felixdorn/klass
```

## Usage

```php
// app/View/Components/Button.php
class Button extends Component {
    public string $color;
    
    public function __construct(string $color = 'blue') {
        $this->color = $color;
    }
    
    public function render() {
        return view('components.button');
    } 
}
```

```blade
<!-- resources/views/components/button.blade.php -->
<button class="bg-{{ $color }}-500" {{ $attributes }}>
    {{ $slot }}
</button>
```

```html
<!-- resources/views/welcome.blade.php -->
<x-button color="red"/>
<x-button/>
```

```bash
php artisan klass:extract
```

```text
// storage/framework/extracted-classes.txt
bg-red-500 bg-blue-500
```

You can now add that file to PurgeCSS or a similar tool to include those classes in your final CSS build.

If you use the [Laravel Mix plugin]() (still WIP, not released yet), the last two steps are done automatically.

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Felix\\Klass\\KlassServiceProvider" --tag="klass-config"
```

This is the contents of the published config file:

```php
return [
    'components_paths' => [
        resource_path('views/components'),
        base_path('vendor'),
    ],

    'views_paths' => [
        resource_path('views'),
    ],

    'output' => base_path('storage/framework/extracted-classes.txt'),
];
```

## Limitations

```php
class Button extends Component {
    public function render() {
        return function ($componentData) {
            return view('components.button', $componentData);
        };
    }
}
```

Returning a closure in `render()` makes it impossible for Klass to extract dynamic classes in the component.

## Tailwind & JIT

Klass very well with the old Tailwind workflow aka use a build with a ton of classes in development and then remove all
the unused classes in production. But recently, Tailwind got a JIT compiler to have production-like css in development.

Klass could work quite easily with the JIT compiler. At compile time, `php artisan klass:extract` should be called but I
have yet to figure out how to do that in a smart way.

## Livewire

Blade components used in a Livewire component are analyzed. However, we do not support Livewire components in itself. I
have no plans to support it, but feel free to submit a PR.

## Testing

```bash
composer test
```

**Klass** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
