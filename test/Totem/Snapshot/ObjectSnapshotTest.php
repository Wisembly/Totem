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

namespace test\Totem\Snapshot;

use \stdClass,
    \ReflectionMethod;

use Prophecy\Prophet,
    Prophecy\PhpUnit\ProphecyTestCase;

use Totem\AbstractSnapshot,
    Totem\Snapshot\ObjectSnapshot;

class ObjectSnapshotTest extends ProphecyTestCase
{
    /** @dataProvider providerCompare */
    public function testCompare($object, AbstractSnapshot $compare, $expect)
    {
        $snapshot = new ObjectSnapshot($object);
        $this->assertSame($expect, $snapshot->isComparable($compare));
    }

    public function providerCompare()
    {
        $object   = new stdClass;
        $prophet  = new Prophet;
        $snapshot = $prophet->prophesize('Totem\\AbstractSnapshot')->reveal();

        return [[$object, new ObjectSnapshot($object), true],
                [$object, new ObjectSnapshot(clone $object), false],
                [$object, $snapshot, false]];
    }

    /** @expectedException InvalidArgumentException */
    public function testConstructWithoutObject()
    {
        new ObjectSnapshot([]);
    }

    /** @dataProvider deepProvider */
    public function testDeepConstructor($value)
    {
        new ObjectSnapshot((object) ['foo' => $value]);
    }

    public function deepProvider()
    {
        return [[(object) ['bar' => 'baz']],
                [['bar' => 'baz']],
                ['fubar']];
    }
}

