# Klass for Laravel

Klass extract your dynamic classes in your Blade components to a file that PurgeCSS can process.

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
```

```bash
php artisan klass:extract
```

```text
// storage/framework/extracted-classes.txt
bg-red-500
```

If you use the [Laravel Mix plugin]() (still WIP, not released yet), the last two steps are done automatically.

## Testing

```bash
composer test
```

**Klass** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
