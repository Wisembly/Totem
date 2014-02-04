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

namespace Totem;

use \stdClass;

use \PHPUnit_Framework_TestCase;

use Totem\Set,
    Totem\Snapshot\ArraySnapshot,
    Totem\Snapshot\ObjectSnapshot;

class SetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidEntryProvider
     */
    public function testSetChangesWithChangedStructure($old, $new, $class)
    {
        $set = new Set(new Snapshot(['data' => $old]), new Snapshot(['data' => $new]));

        $this->assertInstanceOf('Totem\\Change\\' . $class, $set->getChange('1'));
    }

    public function invalidEntryProvider()
    {
        return [[['foo'], ['foo', 'bar'], 'Addition'],
                [['foo', 'bar'], ['foo'], 'Removal']];
    }

    public function testHasChanged()
    {
        $old = new Snapshot(['data' => ['foo' => 'bar', 'baz' => 'fubar']]);
        $new = new Snapshot(['data' => ['foo' => 'bar', 'baz' => 'fubaz']]);

        $set = new Set($old, $new);

        $this->assertFalse($set->hasChanged('foo'));
        $this->assertFalse(isset($set['foo']));
        $this->assertTrue($set->hasChanged('baz'));
        $this->assertTrue(isset($set['baz']));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetChangeWithInvalidProperty()
    {
        $old = new Snapshot(['data' => ['foo' => 'bar']]);

        $set = new Set($old, $old);
        $set->getChange('foo');
    }

    // @todo to break up
    public function testGetChange()
    {
        $o = [new stdClass, (object) ['foo' => 'bar']];

        $old = $new = ['foo'    => 'foo',
                       'bar'    => new ArraySnapshot(['foo', 'bar']),
                       'baz'    => new ObjectSnapshot($o[0]),
                       'qux'    => 'foo',
                       'fubar'  => new ObjectSnapshot($o[1]),
                       'fubaz'  => new ArraySnapshot(['foo', 'bar']),
                       'fuqux'  => new ArraySnapshot(['foo']),
                       'kludge' => new ArraySnapshot(['foo']),
                       'xyzzy'  => new ArraySnapshot(['foo'])];

        $o[1]->foo = 'baz';

        $new['foo']    = 'bar';
        $new['bar']    = new ArraySnapshot(['foo', 'baz']);
        $new['baz']    = new ObjectSnapshot($o[0]);
        $new['qux']    = 42;
        $new['fubar']  = new ObjectSnapshot($o[1]);
        $new['fubaz']  = new ArraySnapshot(['foo', 'bar', 'baz']);
        $new['fuqux']  = new ObjectSnapshot((object) []);
        $new['kludge'] = 42;
        $new['xyzzy']  = (object) [];

        $set = new Set(new Snapshot(['data' => $old]), new Snapshot(['data' => $new]));

        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('fuqux'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('foo'));
        $this->assertInstanceOf('Totem\\Set', $set->getChange('bar'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set['foo']);
        $this->assertInstanceOf('Totem\\Set', $set->getChange('fubar'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('fubar')->getChange('foo'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('kludge'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('xyzzy'));
    }

    public function testIterator()
    {
        $set = new Set(new Snapshot(['data' => ['foo']]), new Snapshot(['data' => ['bar']]));

        $this->assertInstanceOf('ArrayIterator', $set->getIterator());
    }

    public function testGetters()
    {
        $old = new Snapshot(['data' => ['foo'], 'raw' => 'foo']);
        $set = new Set($old, $old);

        $this->assertSame('foo', $set->getOld());
        $this->assertSame('foo', $set->getNew());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenSetter()
    {
        $old = new Snapshot;
        $set = new Set($old, $old);

        $set[] = 'baz';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenUnsetter()
    {
        $old = new Snapshot;
        $set = new Set($old, $old);

        unset($set[0]);
    }

}

