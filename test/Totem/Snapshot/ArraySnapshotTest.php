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
    Totem\Snapshot\ArraySnapshot;

class ArraySnapshotTest extends ProphecyTestCase
{
    /** @dataProvider providerCompare */
    public function testCompare(AbstractSnapshot $compare, $expect)
    {
        $snapshot = new ArraySnapshot([]);
        $this->assertSame($expect, $snapshot->isComparable($compare));
    }

    public function providerCompare()
    {
        $prophet  = new Prophet;
        $snapshot = $prophet->prophesize('Totem\\AbstractSnapshot')
                            ->reveal();

        return [[new ArraySnapshot([]), true],
                [$snapshot, false]];
    }

    /** @dataProvider deepProvider */
    public function testDeepConstructor($value)
    {
        new ArraySnapshot(['foo' => $value]);
    }

    public function deepProvider()
    {
        return [[(object) ['bar' => 'baz']],
                [['bar' => 'baz']],
                ['fubar']];
    }
}

