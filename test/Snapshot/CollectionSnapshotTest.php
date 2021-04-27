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

namespace Totem\Snapshot;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CollectionSnapshotTest extends TestCase
{
    public function testSnapshotNotArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'An array or a Traversable was expected to take a snapshot of a collection, "string" given'
        );

        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => 'Totem\\Snapshot']);
    }

    public function testSnapshotClassNotLoadable()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The snapshot class "Totem\Fubar" does not seem to be loadable');

        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => 'Totem\\Fubar']);
    }

    /**
     * @dataProvider snapshotClassWrongReflectionProvider
     */
    public function testSnapshotWrongReflection($class)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A Snapshot Class should be instantiable and extends abstract class Totem\AbstractSnapshot'
        );

        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => $class]);
    }

    public function snapshotClassWrongReflectionProvider()
    {
        return [['Totem\\AbstractSnapshot'],
                ['stdClass']];
    }

    public function testNonCollection()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The given array / Traversable is not a collection as it contains non numeric keys'
        );

        new CollectionSnapshot(new ArrayObject(['foo' => 'bar']), 'bar');
    }

    public function testKeyNotReadable()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The key "baz" is not defined or readable in one of the elements of the collection'
        );

        new CollectionSnapshot([['foo' => 'bar']], 'baz');
    }

    /** @dataProvider allValidProvider */
    public function testAllValid($hasSnapshot)
    {
        $options = [];
        $class   = 'Totem\\Snapshot\\ArraySnapshot';

        if (true === $hasSnapshot) {
            $class                    = 'Totem\\Snapshot';
            $options['snapshotClass'] = 'Totem\\Snapshot';
        }

        $snapshot = new CollectionSnapshot([['foo' => 'bar', 'baz' => 'fubar']], '[foo]', $options);

        $refl = new ReflectionProperty('Totem\\AbstractSnapshot', 'data');
        $refl->setAccessible(true);

        $this->assertArrayHasKey('bar', $refl->getValue($snapshot));
        $this->assertInstanceOf($class, $refl->getValue($snapshot)['bar']);
    }

    public function allValidProvider()
    {
        return [[true],
                [false]];
    }

    public function testOriginalKeyNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The primary key "baz" is not in the computed dataset');

        $snapshot = new CollectionSnapshot([['foo' => 'bar']], '[foo]');
        $snapshot->getOriginalKey('baz');
    }

    public function testOriginalKey()
    {
        $snapshot = new CollectionSnapshot([['foo' => 'bar']], '[foo]');

        $this->assertSame(0, $snapshot->getOriginalKey('bar'));
    }
}

