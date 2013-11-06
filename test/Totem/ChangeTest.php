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

namespace test\Totem\ChangeSet;

use Totem\Change;

class ChangeTest extends \PHPUnit_Framework_TestCase
{
    public function testChange()
    {
        $change = new Change('Old state', 'New state');

        $this->assertEquals('Old state', $change->getOld());
        $this->assertEquals('New state', $change->getNew());
    }
}

