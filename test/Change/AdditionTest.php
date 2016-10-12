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

namespace Totem\Change;

class AdditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage An addition does not have an old state
     */
    public function testOldTriggersException()
    {
        $change = new Addition('new');
        $change->getOld();
    }
}

