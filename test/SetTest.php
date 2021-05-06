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

use BadMethodCallException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Totem\Snapshot\ArraySnapshot;
use Totem\Snapshot\ObjectSnapshot;
use Totem\Snapshot\CollectionSnapshot;

class SetTest extends TestCase
{
    /**
     * @dataProvider invalidEntryProvider
     */
    public function testSetChangesWithChangedStructure($old, $new, $class)
    {
        $set = new Set();
        $set->compute(new Snapshot(['data' => $old]), new Snapshot(['data' => $new]));

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

        $set = new Set();
        $set->compute($old, $new);

        $this->assertFalse($set->hasChanged('foo'));
        $this->assertFalse(isset($set['foo']));
        $this->assertTrue($set->hasChanged('baz'));
        $this->assertTrue(isset($set['baz']));
    }

    public function testGetChangeWithInvalidProperty()
    {
        $this->expectException(OutOfBoundsException::class);

        $old = new Snapshot(['data' => ['foo' => 'bar']]);

        $set = new Set();
        $set->compute($old, $old);

        $set->getChange('foo');
    }

    // @todo to break up
    public function testGetChange()
    {
        $o = [new stdClass(), (object) ['foo' => 'bar']];

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

        $set = new Set();
        $set->compute(new Snapshot(['data' => $old]), new Snapshot(['data' => $new]));

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
        $set = new Set();
        $set->compute(new Snapshot(['data' => ['foo']]), new Snapshot(['data' => ['bar']]));

        $this->assertInstanceOf('ArrayIterator', $set->getIterator());
    }

    public function testForbidenSetter()
    {
        $this->expectException(BadMethodCallException::class);

        $set = new Set();
        $old = new Snapshot();
        $set->compute($old, $old);

        $set[] = 'baz';
    }

    public function testForbidenUnsetter()
    {
        $this->expectException(BadMethodCallException::class);

        $set = new Set();
        $old = new Snapshot();
        $set->compute($old, $old);

        unset($set[0]);
    }

    public function testHasChangedNotComputedShouldThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The changeset was not computed yet !');

        $set = new Set();
        $set->hasChanged('foo');
    }

    public function testNotComputedCountShouldThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The changeset was not computed yet !');

        $set = new Set();
        count($set);
    }

    public function testNotComputedIteratorShouldThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The changeset was not computed yet !');

        $set = new Set();
        $set->getIterator();
    }

    public function testAlreadyComputedSetShouldNotRecompute()
    {
        $old = new Snapshot(['data' => ['foo']]);
        $new = new Snapshot(['data' => ['bar']]);

        $set = new Set($old, $new); // implicitly compute the set in the constructor

        $this->assertCount(1, $set);
        $this->assertTrue($set->hasChanged(0));
        $this->assertEquals('foo', $set->getChange(0)->getOld());
        $this->assertEquals('bar', $set->getChange(0)->getNew());

        $set->compute($old, $new);
    }

    public function testComputeCollections()
    {
        $old = $new = [['foo' => 'bar', 'baz' => 'fubar'], ['foo' => 'baz', 'baz' => 'fubar']];
        $new[0]['baz'] = 'fubaz';

        $old = new CollectionSnapshot($old, '[foo]');
        $new = new CollectionSnapshot($new, '[foo]');

        $set = new Set();
        $set->compute($old, $new);

        $this->assertContainsOnly('integer', array_keys(iterator_to_array($set)));
    }

    /** @dataProvider unaffectedSnapshotComputerProvider */
    public function testUnaffectedCollections(AbstractSnapshot $origin, AbstractSnapshot $upstream)
    {
        $set = new Set();
        $set->compute($origin, $upstream);

        $this->assertNotContainsOnly('integer',array_keys(iterator_to_array($set)));
    }

    public function unaffectedSnapshotComputerProvider()
    {
        $old = ['foo' => 'bar', 'baz' => 'fubar'];

        return [[new CollectionSnapshot([$old], '[foo]'), new ArraySnapshot($old)],
                [new ArraySnapshot($old), new CollectionSnapshot([$old], '[foo]')],
                [new ArraySnapshot($old), new ArraySnapshot(array_merge($old, ['baz' => 'fubaz']))]];
    }
}

