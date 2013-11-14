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

namespace test\Totem;

use \ReflectionMethod;

use \PHPUnit_Framework_TestCase;

class AbstractSnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        Totem\Exception\IncomparableDataException
     * @expectedExceptionMessage This data is not comparable with the base
     */
    public function testDiffIncomparable()
    {
        $snapshot = new Snapshot(['comparable' => false]);
        $snapshot->diff($snapshot);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The computed data is not an array, "string" given
     */
    public function testComparableDataFailure()
    {
        $snapshot = new Snapshot(['data' => 'foo']);
        $snapshot->getComparableData();
    }

    /**
     * @dataProvider existsProvider
     */
    public function testOffsetExists($key, $expect)
    {
        $snapshot = new Snapshot(['data' => ['foo' => 'bar']]);
        $this->assertSame($expect, isset($snapshot[$key]));
    }

    public function existsProvider()
    {
        return [['foo', true],
                ['bar', false]];
    }

    /**
     * @expectedException        BadMethodCallException
     * @expectedExceptionMessage A snapshot is frozen by nature
     */
    public function testOffsetUnset()
    {
        $snapshot = new Snapshot(['data' => ['foo' => 'bar']]);
        unset($snapshot['foo']);
    }

    /**
     * @expectedException        BadMethodCallException
     * @expectedExceptionMessage A snapshot is frozen by nature
     */
    public function testOffsetSet()
    {
        $snapshot = new Snapshot(['data' => ['foo' => 'bar']]);
        $snapshot[] = 'foo';
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The computed data is not an array, "string" given
     */
    public function testNormalizer()
    {
        $snapshot = new Snapshot(['data' => 'foo']);

        $refl = new ReflectionMethod('Totem\\AbstractSnapshot', 'normalize');
        $refl->setAccessible(true);
        $refl->invoke($snapshot);
    }
}

