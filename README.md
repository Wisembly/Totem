Totem
=====
Changeset calculator between two state of a data.

Compatible PHP >= 5.4

Snapshots currently supported :
- Object
- Array

Documentation
=============
For any pieces of document, please look for the docs/ directory.

Installation
============
You have multiple ways to install Totem. If you are unsure what to do, go with
[the archive release](#archive-release).

### Archive Release
1. Download the most recent release from the [release page](https://github.com/Taluu/Totem/releases)
2. Unpack the archive
3. Move the files somewhere in your project

### Development version
1. Install Git
2. `git clone git://github.com/Taluu/Totem.git`

### Via Composer
1. Install composer in your project: `curl -s http://getcomposer.org/installer | php`
2. Create a `composer.json` file in your project root:

    ```javascript

      {
        "require": {
          "taluu/totem": "~1.0.0"
        }
      }
    ```

3. Install via composer : `php composer.phar install`

Basic Usage
===========
```php
<?php

use \stdclass; // random object
use Totem\Snapshot\ObjectSnapshot;

$object = new stdclass;

$snapshot = new ObjectSnapshot($object); // Totem\Snapshot\ObjectSnapshot
$set = $snapshot->diff($snapshot); // Totem\Set
```

Running Tests
=============
```console
$ php composer.phar install
$ bin/phpunit
```

