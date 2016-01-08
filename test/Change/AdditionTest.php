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
    /** @var Addition */
    private $change;

    public function setUp()
    {
        $this->change = new Addition('new');
    }

    public function testOld()
    {
        $this->assertNull($this->change->getOld());
    }

    public function testNew()
    {
        $this->assertSame('new', $this->change->getNew());
    }
}

