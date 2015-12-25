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

use ReflectionMethod;
use ReflectionProperty;

use PHPUnit_Framework_TestCase;

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
    public function testInvalidDataNormalizer()
    {
        $snapshot = new Snapshot(['data' => 'foo']);

        $refl = new ReflectionMethod('Totem\\AbstractSnapshot', 'normalize');
        $refl->setAccessible(true);
        $refl->invoke($snapshot);
    }

    /** @dataProvider normalizerProvider */
    public function testNormalizer($data, $snapshotClass, $setClass = null)
    {
        $snapshot = new Snapshot;
        $setClass = $setClass ?: 'stdClass';

        $dataProperty = new ReflectionProperty('Totem\\AbstractSnapshot', 'data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue($snapshot, [$data]);

        $setClassProperty = new ReflectionProperty('Totem\\AbstractSnapshot', 'setClass');
        $setClassProperty->setAccessible(true);
        $setClassProperty->setValue($snapshot, $setClass);

        $method = new ReflectionMethod('Totem\\AbstractSnapshot', 'normalize');
        $method->setAccessible(true);
        $method->invoke($snapshot);

        $this->assertInstanceOf($snapshotClass, $dataProperty->getValue($snapshot)[0]);
        $this->assertEquals($setClass, $setClassProperty->getValue($dataProperty->getValue($snapshot)[0]));
    }

    public function normalizerProvider()
    {
        return [[new Snapshot, 'Totem\\Snapshot', 'Totem\\Set'],
                [['foo' => 'bar'], 'Totem\\Snapshot\\ArraySnapshot'],
                [(object) ['foo' => 'bar'], 'Totem\\Snapshot\\ObjectSnapshot']];
    }

    public function testDiff()
    {
        $snapshot = new Snapshot(['data' => []]);

        $this->assertInstanceOf('Totem\\Set', $snapshot->diff($snapshot));
    }

    public function testCorrectSetClass()
    {
        $snapshot = new Snapshot(['data' => []]);
        $snapshot->setSetClass('Totem\\Set');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage A Set Class should be instantiable and implement Totem\SetInterface
     */
    public function testWrongSetClass()
    {
        $snapshot = new Snapshot(['data' => []]);
        $snapshot->setSetClass('stdclass');
    }
}

