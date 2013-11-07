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
    \ReflectionProperty;

use \PHPUnit_Framework_TestCase;

use Totem\Snapshot\AbstractSnapshot;

class AbstractSnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Totem\Exception\IncomparableDataException
     */
    public function testDiffIncomparable()
    {
        $snapshot = $this->getMockBuilder('Totem\\AbstractSnapshot')
                         ->setMethods(['isComparable'])
                         ->getMock();

        $snapshot->expects(self::once())
                 ->method('isComparable')
                 ->with(self::isInstanceOf('Totem\\AbstractSnapshot'))
                 ->will(self::returnValue(false));

        $snapshot->diff($snapshot);
    }
}

