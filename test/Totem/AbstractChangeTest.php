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

use \PHPUnit_Framework_TestCase;

class AbstractChangeTest extends PHPUnit_Framework_TestCase
{
    /** @var Totem\AbstractChange */
    private $mock;

    public function setUp()
    {
        $this->mock = $this->getMockForAbstractClass('Totem\\AbstractChange', ['old', 'new']);
    }

    public function testOld()
    {
        $this->assertSame('old', $this->mock->getOld());
    }

    public function testNew()
    {
        $this->assertSame('new', $this->mock->getNew());
    }
}

