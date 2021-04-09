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

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Totem\Exception\IncomparableDataException;

class AbstractSnapshotTest extends TestCase
{
    public function testDiffIncomparable()
    {
        $this->expectException(IncomparableDataException::class);
        $this->getExpectedExceptionMessage('This data is not comparable with the base');

        $snapshot = new Snapshot(['comparable' => false]);
        $snapshot->diff($snapshot);
    }

    public function testComparableDataFailure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('The computed data is not an array, "string" given');

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

    public function testOffsetUnset()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->getExpectedExceptionMessage('A snapshot is frozen by nature');

        $snapshot = new Snapshot(['data' => ['foo' => 'bar']]);
        unset($snapshot['foo']);
    }

    public function testOffsetSet()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->getExpectedExceptionMessage('A snapshot is frozen by nature');

        $snapshot = new Snapshot(['data' => ['foo' => 'bar']]);
        $snapshot[] = 'foo';
    }

    public function testInvalidDataNormalizer()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('The computed data is not an array, "string" given');

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

        self::assertInstanceOf(Snapshot::class, $snapshot);
    }

    public function testWrongSetClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('A Set Class should be instantiable and implement Totem\SetInterface');

        $snapshot = new Snapshot(['data' => []]);
        $snapshot->setSetClass('stdclass');
    }
}

