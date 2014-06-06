Totem
=====
[![License](https://poser.pugx.org/wisembly/totem/license.png)](https://packagist.org/packages/wisembly/totem)
[![Build Status](https://travis-ci.org/Wisembly/Totem.png?branch=master)](https://travis-ci.org/Wisembly/Totem)
[![Latest Stable Version](https://poser.pugx.org/wisembly/totem/v/stable.png)](https://packagist.org/packages/wisembly/totem)
[![Total Downloads](https://poser.pugx.org/wisembly/totem/downloads.png)](https://packagist.org/packages/wisembly/totem)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Wisembly/Totem/badges/quality-score.png?s=b71f67e353a379e19b651697285ffed18d6f1554)](https://scrutinizer-ci.com/g/Wisembly/Totem/)
[![Coverage Status](https://img.shields.io/coveralls/Wisembly/Totem.svg)](https://coveralls.io/r/Wisembly/Totem?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5d97b609-b57c-467e-9a6e-3a39e08bd825/mini.png)](https://insight.sensiolabs.com/projects/5d97b609-b57c-467e-9a6e-3a39e08bd825)

```
       \\\\////
        |.)(.|
        | || |
        \(__)/   Changeset calculator between two state of a data
        |-..-|   Requires PHP 5.4 ; Compatible PHP 5.5, PHP 5.6, and HHVM
        |o\/o|
   .----\    /----.
  / / / |~~~~| \ \ \
 / / / /|::::|\ \ \ \
'-'-'-'-|::::|-'-'-'-'
       (((^^)))
        >>><<< 
        ||||||   Snapshots currently natively supported :
        (o)(o)   - Object
        | /\ |   - Array
        (====)
       _(_,__)
      (___\___)
```

Documentation
=============
For any pieces of document, please look for the docs/ directory. You may also 
check up [the compiled version](http://totem.readthedocs.org/en/latest/index.html)

Installation
============
You have multiple ways to install Totem. If you are unsure what to do, go with
[the archive release](#archive-release).

### Archive Release
1. Download the most recent release from the [release page](https://github.com/Wisembly/Totem/releases)
2. Unpack the archive
3. Move the files somewhere in your project

### Development version
1. Install Git
2. `git clone git://github.com/Wisembly/Totem.git`

### Via Composer
1. Install composer in your project: `curl -s http://getcomposer.org/installer | php`
2. Create a `composer.json` file in your project root:

    ```javascript

      {
        "require": {
          "wisembly/totem": "~1.3"
        }
      }
    ```

3. Install via composer : `php composer.phar install`

Basic Usage
===========
```php
<?php

use Totem\Snapshot\ObjectSnapshot;

$object = (object) ['foo' => 'bar', 'baz' => 'qux'];
$snapshot = new ObjectSnapshot($object); // Totem\Snapshot\ObjectSnapshot

$object->foo = 'fubar';
$set = $snapshot->diff(new ObjectSnapshot($object)); // Totem\Set

var_dump($set->hasChanged('foo'),
         $set->getChange('foo')->getOld(),
         $set->getChange('foo')->getNew(),
         $set->hasChanged('bar'));

/* 
 * expected result :
 *
 * bool(true)
 * string(3) "bar"
 * string(5) "fubar"
 * bool(false)
 */
```

Running Tests
=============
```console
$ php composer.phar install --dev
$ bin/phpunit
```

