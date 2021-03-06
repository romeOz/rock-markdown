Markdown parser for PHP
=================

Abstraction over the [cebe/markdown](https://github.com/cebe/markdown).

[![Latest Stable Version](https://poser.pugx.org/romeOz/rock-markdown/v/stable.svg)](https://packagist.org/packages/romeOz/rock-markdown)
[![Build Status](https://travis-ci.org/romeOz/rock-markdown.svg?branch=master)](https://travis-ci.org/romeOz/rock-markdown)
[![HHVM Status](http://hhvm.h4cc.de/badge/romeoz/rock-markdown.svg)](http://hhvm.h4cc.de/package/romeoz/rock-markdown)
[![Coverage Status](https://coveralls.io/repos/romeOz/rock-markdown/badge.svg?branch=master)](https://coveralls.io/r/romeOz/rock-markdown?branch=master)
[![License](https://poser.pugx.org/romeOz/rock-markdown/license.svg)](https://packagist.org/packages/romeOz/rock-markdown)

Features
-------------------

 * Deny tags
 * Video tag + dummy:
    * youtube
    * vimeo
    * rutube
    * VK
    * ivi
    * dailymotion
    * sapo
 * Cropping image
 * Standalone module/component for [Rock Framework](https://github.com/romeOz/rock)

 
Installation
-------------------

From the Command Line:

```
composer require romeoz/rock-markdown
```

In your composer.json:

```json
{
    "require": {
        "romeoz/rock-markdown": "*"
    }
}
```
 
Requirements
-------------------
 * **PHP 5.4+**
 * For cropping image required [Rock Image](https://github.com/romeOz/rock-validate): `composer require romeoz/rock-image`

>All unbolded dependencies is optional.

License
-------------------

Markdown parser is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).