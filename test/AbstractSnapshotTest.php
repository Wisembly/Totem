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

use Totem\Snapshot\ArraySnapshot;
use Totem\Snapshot\ObjectSnapshot;

class AbstractSnapshotTest extends PHPUnit_Framework_TestCase
{
    private $snapshot;

    public function setUp()
    {
        $this->snapshot = new class extends AbstractSnapshot {
            public function __construct()
            {
                $this->data = ['foo' => true];
            }
        };

    }

    /**
     * @expectedException        Totem\Exception\IncomparableDataException
     * @expectedExceptionMessage This data is not comparable with the base
     */
    public function testDiffIncomparable()
    {
        $snapshot = new class extends AbstractSnapshot {
            public function isComparable(AbstractSnapshot $snapshot) {
                return false;
            }
        };

        $snapshot->diff($snapshot);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The computed data is not an array, "string" given
     */
    public function testComparableDataFailure()
    {
        $snapshot = new class extends AbstractSnapshot {
            public function __construct()
            {
                $this->data = 'foo';
            }
        };

        $snapshot->getComparableData();
    }

    /**
     * @dataProvider existsProvider
     */
    public function testOffsetExists($key, $expect)
    {
        $this->assertSame($expect, isset($this->snapshot[$key]));
    }

    public function existsProvider()
    {
        return [
            'offset exists' => ['foo', true],
            'offset does not exists' => ['bar', false]
        ];
    }

    /**
     * @expectedException        BadMethodCallException
     * @expectedExceptionMessage A snapshot is frozen by nature
     */
    public function testOffsetUnset()
    {
        unset($this->snapshot['foo']);
    }

    /**
     * @expectedException        BadMethodCallException
     * @expectedExceptionMessage A snapshot is frozen by nature
     */
    public function testOffsetSet()
    {
        $this->snapshot[] = 'foo';
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The computed data is not an array, "string" given
     */
    public function testInvalidDataNormalizer()
    {
        $snapshot = new class extends AbstractSnapshot {
            protected $data = 'foo';
        };

        $refl = new ReflectionMethod(AbstractSnapshot::class, 'normalize');
        $refl->setAccessible(true);
        $refl->invoke($snapshot);
    }

    /** @dataProvider normalizerProvider */
    public function testNormalizer($data, $snapshotClass, $setClass)
    {
        $snapshot = new class($data) extends AbstractSnapshot {
            public function __construct($data)
            {
                $this->data = [$data];
            }

            public function getData()
            {
                return $this->data;
            }

            public function isComparable(AbstractSnapshot $snapshot)
            {
                $refl = new \ReflectionObject($snapshot);

                return $refl->isAnonymous() || parent::isComparable($snapshot);
            }
        };

        $method = new ReflectionMethod(AbstractSnapshot::class, 'normalize');
        $method->setAccessible(true);
        $method->invoke($snapshot);

        $snapshot = $snapshot->getData()[0];

        $property = new ReflectionProperty(AbstractSnapshot::class, 'setClass');
        $property->setAccessible(true);
        $property->setValue($snapshot, $setClass);
        $property = $property->getValue($snapshot);

        $this->assertInstanceOf($snapshotClass, $snapshot);
        $this->assertEquals($setClass, $property);
    }

    public function normalizerProvider()
    {
        $snapshot = new class extends AbstractSnapshot {};

        return [
            'any snapshots' => [$snapshot, get_class($snapshot), Set::class],
            'array snapshots' => [['foo' => 'bar'], ArraySnapshot::class, 'stdClass'],
            'object snapshot' => [(object) ['foo' => 'bar'], ObjectSnapshot::class, 'stdClass']];
    }

    public function testDiff()
    {
        $this->assertInstanceOf(Set::class, $this->snapshot->diff($this->snapshot));
    }

    public function testCorrectSetClass()
    {
        $this->snapshot->setSetClass(Set::class);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage A Set Class should be instantiable and implement Totem\SetInterface
     */
    public function testWrongSetClass()
    {
        $this->snapshot->setSetClass('stdClass');
    }
}

