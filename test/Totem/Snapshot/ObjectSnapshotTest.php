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

use \stdClass;

use \PHPUnit_Framework_TestCase;

use Totem\Snapshot\ObjectSnapshot;

class ObjectSnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Totem\Exception\IncomparableDataException
     */
    public function testDiffWrongOid()
    {
        $snapshot = new ObjectSnapshot(new stdClass);
        $snapshot->diff(new ObjectSnapshot(new stdClass));
    }

    public function testDiff()
    {
        $object = new stdClass;

        $snapshot = new ObjectSnapshot($object);
        $set = $snapshot->diff($snapshot);

        $this->assertInstanceOf('Totem\\Set', $set);
    }

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

        return [[$object, new ObjectSnapshot($object), true],
                [$object, new ObjectSnapshot(clone $object), false],
                [$object, $this->getMock('Totem\\AbstractSnapshot'), false]];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithoutObject()
    {
        new ObjectSnapshot([]);
    }
}

