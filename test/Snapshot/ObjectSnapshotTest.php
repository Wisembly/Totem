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

    public function testExportAllProperties()
    {
        $object = new class {
            public $foo = 'foo';
            protected $bar = 'bar';
            private $baz = 'baz';
        };

        $snapshot = new ObjectSnapshot($object);
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
        $object = new class {
            public $array;
            public $object;
            public $scalar = 'foo';

            public function __construct()
            {
                $this->object = new class {
                    public $foo = 'bar';
                };

                $this->array = ['foo' => 'bar'];
            }
        };

        $snapshot = new ObjectSnapshot($object);
        $data = $snapshot->getComparableData();

        $this->assertInstanceOf('Totem\\Snapshot\\ObjectSnapshot', $data['object']);
        $this->assertInstanceOf('Totem\\Snapshot\\ArraySnapshot', $data['array']);
        $this->assertNotInstanceOf('Totem\\AbstractSnapshot', $data['scalar']);
    }
}

