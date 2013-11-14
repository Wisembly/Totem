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

use Totem\Change\Removal;

class RemovalTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $change = new Removal('Old state');

        $this->assertNull($change->getNew());
        $this->assertEquals('Old state', $change->getOld());
    }
}

