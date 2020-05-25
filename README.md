# Dua Date Calculator

Implementation of a simple due date calculator for php

## Install

Via Composer

``` bash
$ composer require mestert/php-due-date-calculator
```

## Usage

``` php
$calculator = new \mestert\DueDateCalculator();

// $param1  Issue crated date
// $param2  Turnaround time
$calculator->calculateDueDate(new \DatetimeImmutable(), 12);
```

## Testing

``` bash
$ phpunit
```

## License

The MIT License (MIT). Please see [License File](https://github.com/dnoegel/php-xdg-base-dir/blob/master/LICENSE) for more information.
