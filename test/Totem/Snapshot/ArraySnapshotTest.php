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

use Totem\Snapshot\ArraySnapshot;

class ArraySnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Totem\Exception\IncomparableDataException
     */
    public function testDiffWrongArray()
    {
        $snapshot = new ArraySnapshot(['foo', 'bar']);
        $snapshot->diff(new ArraySnapshot(['foo' => 'bar']));
    }

    public function testDiff()
    {
        $snapshot = new ArraySnapshot(['foo' => 'bar']);
        $set = $snapshot->diff($snapshot);

        $this->assertInstanceOf('Totem\\Set', $set);
    }
}

