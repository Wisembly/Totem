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

namespace Totem\Snapshot;

use stdClass;
use ReflectionMethod;

use PHPUnit_Framework_TestCase;

class ObjectSnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerCompare
     */
    public function testCompare($object, $compare, $expect)
    {
        $snapshot = new ObjectSnapshot($object);
        $this->assertSame($expect, $snapshot->isComparable($compare));
    }

    public function providerCompare()
    {
        $object = new stdClass;

        $snapshot = $this->getMockBuilder('Totem\\AbstractSnapshot')
                         ->disableOriginalConstructor()
                         ->getMock();

        return [[$object, new ObjectSnapshot($object), true],
                [$object, new ObjectSnapshot(clone $object), false],
                [$object, $snapshot, false]];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithoutObject()
    {
        new ObjectSnapshot([]);
    }

    /**
     * @dataProvider deepProvider
     */
    public function testDeepConstructor($value)
    {
        new ObjectSnapshot((object) ['foo' => $value]);
    }

    public function deepProvider()
    {
        return [
            'with a sub-object' => [(object) ['bar' => 'baz']],
            'with a sub-array' => [['bar' => 'baz']],
            'with a scalar' => ['fubar']
        ];
    }

    public function testExportAllProperties()
    {
        $snapshot = new ObjectSnapshot(new Foo('foo', 'bar', 'baz'));
        $data = $snapshot->getComparableData();

        $this->assertCount(3, $data);

        $this->assertArrayHasKey('foo', $data);
        $this->assertArrayHasKey('bar', $data);
        $this->assertArrayHasKey('baz', $data);

        $this->assertSame('foo', $data['foo']);
        $this->assertSame('bar', $data['bar']);
        $this->assertSame('baz', $data['baz']);
    }
}

// todo in php7 : use anon class !
class Foo
{
    public $foo;
    protected $bar;
    private $baz;

    public function __construct($foo, $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }
}


