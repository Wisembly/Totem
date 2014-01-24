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

namespace test\Totem;

use Prophecy\PhpUnit\ProphecyTestCase;

class AbstractChangeTest extends ProphecyTestCase
{
    public function testOld()
    {
        $mock = $this->prophesize('Totem\\AbstractChange');
        $mock->getOld()->willReturn('old');

        $this->assertSame('old', $mock->reveal()->getOld());
    }

    public function testNew()
    {
        $mock = $this->prophesize('Totem\\AbstractChange');
        $mock->getNew()->willReturn('new');

        $this->assertSame('new', $mock->reveal()->getNew());
    }
}

