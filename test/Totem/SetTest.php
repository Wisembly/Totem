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

use Totem\Set;

class ChangeSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @dataProvider invalidEntryProvider
     */
    public function testSetChangesWithInvalidEntity($old, $new)
    {
        new Set($old, $new);
    }

    public function invalidEntryProvider()
    {
        return [[['foo'], ['bar' => 'baz']],
                [['foo'], ['bar', 'baz']],
                [['foo', 'bar'], ['baz']],
                [['foo' => 'bar'], ['baz']]];
    }

    public function testHasChanged()
    {
        $old = $new = ['foo' => 'bar',
                       'baz' => 'fubar'];

        $new['foo'] = 'fubaz';

        $set = new Set($old, $new);

        $this->assertTrue($set->hasChanged('foo'));
        $this->assertFalse($set->hasChanged('baz'));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetChangeWithInvalidProperty()
    {
        $old = ['foo' => 'bar',
                'baz' => 'fubar'];

        $set = new Set($old, $old);
        $set->getChange('foo');
    }

    public function testGetChange()
    {
        $old = $new = ['foo' => 'foo',
                       'bar' => ['foo', 'bar']];

        $new['foo'] = 'bar';
        $new['bar'] = ['foo', 'baz'];

        $set = new Set($old, $new);

        $this->assertInstanceOf('Totem\\Change', $set->getChange('foo'));
        $this->assertInstanceOf('Totem\\Set', $set->getChange('bar'));
    }
}

