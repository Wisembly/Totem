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

use \stdClass;

use \PHPUnit_Framework_TestCase;

use Totem\Set,
    Totem\Snapshot\ObjectSnapshot;

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
        $this->assertTrue(isset($set['foo']));
        $this->assertFalse($set->hasChanged('baz'));
        $this->assertFalse(isset($set['baz']));
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

    // @todo to break up
    public function testGetChange()
    {
        $old = $new = ['foo'   => 'foo',
                       'bar'   => ['foo', 'bar'],
                       'baz'   => new stdClass,
                       'qux'   => 'foo',
                       'fubar' => (object) ['foo' => 'bar'],
                       'fubaz' => ['foo', 'bar']];

        $new['foo']     = 'bar';
        $new['bar']     = ['foo', 'baz'];
        $new['baz']     = clone $old['fubar'];
        $new['qux']     = 42;
        $new['fubaz'][] = 'baz';

        $set = new Set($old, $new);

        $this->assertInstanceOf('Totem\\Change', $set->getChange('foo'));
        $this->assertInstanceOf('Totem\\Set', $set->getChange('bar'));
        $this->assertInstanceOf('Totem\\Change', $set['foo']);
    }

    public function testGetters()
    {
        $old = ['foo', 'bar'];
        $set = new Set($old, $old);

        $this->assertSame($old, $set->getOld());
        $this->assertSame($old, $set->getNew());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenSetter()
    {
        $old = ['foo'];
        $set = new Set($old, $old);

        $set[] = 'baz';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenUnsetter()
    {
        $old = ['foo'];
        $set = new Set($old, $old);

        unset($set[0]);
    }

}

