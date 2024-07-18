# Toru 取る (backport for lagacy PHP)

[![Test Suite](https://github.com/dakujem/toru-backport/actions/workflows/php-test.yml/badge.svg)](https://github.com/dakujem/toru-backport/actions/workflows/php-test.yml)

>
> 💿 `composer require dakujem/toru-backport`
>

This is a backport of the original package [dakujem/toru](https://github.com/dakujem/toru)
for **PHP 7.4** and **PHP 8.0** only.


## Documentation

Please refer to the documentation of the original package:  
👉 [dakujem/toru](https://github.com/dakujem/toru) 👈


## Migration to supported PHP version

After updating your project to a supported PHP version, change your project requirement to `dakujem/toru`.  
Chances are, you need not do anything else.

If you extend the `Dash` class, you may have to check and update type hints.

This backport is based on [a stable release](https://github.com/dakujem/toru/releases) of `dakujem/toru`
current at the time of update.  
PHP 8 features (most notably type hints) have been removed or replaced.
