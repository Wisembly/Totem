LinkSet
=======
Changeset calculator between two data. The name is temporary, looking for a better name !

Compatible PHP >= 5.4

Snapshots currently supported :
- Object

Installation
============
You have multiple ways to install LinkSet. If you are unsure what to do, go with
the archive release.

### Archive Release
1. Download the most recent release from the [release page](https://github.com/Taluu/LinkSet/releases)
2. Unpack the archive
3. Move the files somewhere in your project

### Development version
1. Install Git
2. `git clone git://github.com/Taluu/Link-TPL.git`

### Via Composer
1. Install composer in your project: `curl -s http://getcomposer.org/installer | php`
2. Create a `composer.json` file in your project root:

    ```javascript

      {
        "require": {
          "taluu/link-set": "dev-master"
        }
      }
    ```

3. Install via composer : `php composer.phar install`

Basic Usage
===========
```php
<?php

use \stdclass; // random object
use LinkSet\Snapshot\Object;

$object = new stdclass;

$snapsht = new Object($object); // LinkSet\Snapshot\Object
$set = $snapshot->diff($snapshot); // LinkSet\Set
```

