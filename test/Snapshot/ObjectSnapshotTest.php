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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ObjectSnapshotTest extends TestCase
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
        $object = new stdClass();

        $snapshot = $this->getMockBuilder('Totem\\AbstractSnapshot')
                         ->disableOriginalConstructor()
                         ->getMock();

        return [[$object, new ObjectSnapshot($object), true],
                [$object, new ObjectSnapshot(clone $object), false],
                [$object, $snapshot, false]];
    }

    public function testConstructWithoutObject()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectSnapshot([]);
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

    public function testDeepConstructor()
    {
        $object = new Foo(
            (object) ['foo' => 'bar'], // object
            ['baz' => 'fubar'], // array
            'fubaz' // scalar
        );

        $snapshot = new ObjectSnapshot($object);
        $data = $snapshot->getComparableData();

        $this->assertInstanceOf('Totem\\Snapshot\\ObjectSnapshot', $data['foo']);
        $this->assertInstanceOf('Totem\\Snapshot\\ArraySnapshot', $data['bar']);
        $this->assertNotInstanceOf('Totem\\AbstractSnapshot', $data['baz']);
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

