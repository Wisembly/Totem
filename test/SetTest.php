<?php
/**
 * This file is part of the Totem package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Baptiste Clavié <clavie.b@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace Totem;

use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function testChangedPropertyReportsAsChanged()
    {
        $set = new Set(['foo' => new Change('bar', 'baz')]);
        $this->assertTrue($set->hasChanged('foo'));
    }

    public function testUnchangedPropertyDoesntReportAsChanged()
    {
        $set = new Set([]);
        $this->assertFalse($set->hasChanged('foo'));
    }

    /** @expectedException Totem\UnchangedPropertyException */
    public function testUnchangedPropertyTriggersException()
    {
        $set = new Set([]);
        $set->getChange('foo');
    }

    public function testGetChangeOnChangedPropertyReturnsChange()
    {
        $set = new Set(['foo' => new Change('bar', 'baz')]);
        $this->assertInstanceOf(Change::class, $set->getChange('foo'));
    }
}

