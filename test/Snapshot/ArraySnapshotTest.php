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

use stdClass,
    ReflectionMethod;

use PHPUnit_Framework_TestCase;

class ArraySnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerCompare
     */
    public function testCompare($compare, $expect)
    {
        $snapshot = new ArraySnapshot([]);
        $this->assertSame($expect, $snapshot->isComparable($compare));
    }

    public function providerCompare()
    {
        $snapshot = $this->getMockBuilder('Totem\\AbstractSnapshot')
                         ->disableOriginalConstructor()
                         ->getMock();

        return [[new ArraySnapshot([]), true],
                [$snapshot, false]];
    }

    /**
     * @dataProvider deepProvider
     */
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

