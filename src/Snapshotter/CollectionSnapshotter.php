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

namespace Totem\Snapshotter;

use InvalidArgumentException;

use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;
use Totem\UnsupportedSnapshotException;

use Totem\Snapshot\CollectionSnapshot;

final class CollectionSnapshotter implements Snapshotter
{
    /** @var Snapshotter */
    private $snapshotter;

    /** @var PropertyPath */
    private $primary;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(Snapshotter $snapshotter, $primary)
    {
        $this->snapshotter = $snapshotter;
        $this->primary = new PropertyPath($primary);

        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor()
        ;
    }

    /** {@inheritDoc} */
    public function supports($raw): bool
    {
        if (!is_array($raw) && !$raw instanceof Traversable) {
            return false;
        }

        foreach ($raw as $value) {
            if (!$this->snapshotter->supports($value)) {
                return false;
            }

            if (!$this->accessor->isReadable($value, $this->primary)) {
                return false;
            }
        }

        return true;
    }

    /** {@inheritDoc} */
    public function getSnapshot($raw): Snapshot
    {
        if (!$this->supports($raw)) {
            throw new UnsupportedDataException($this, $raw);
        }

        $data = [];
        $link = [];

        foreach ($raw as $key => $value) {
            $primary = $this->accessor->getValue($value, $this->primary);

            $data[$primary] = $this->snapshotter->getSnapshot($value);
            $link[$primary] = $key;
        }

        return new class ($raw, $data, $link) extends Snapshot implements CollectionSnapshot {
            /**
             * Data mapper between the primary key and the real integer key of
             * this collection
             *
             * @var int[]
             */
            private $link;

            public function __construct($raw, array $data, array $link)
            {
                if (!is_array($raw) && !$raw instanceof Traversable) {
                    throw new InvalidArgumentException(sprintf('Expected a traversable, got %s', gettype($raw)));
                }

                parent::__construct($raw, $data);

                $this->link = $link;
            }

            /** {@inheritDoc} */
            public function isComparable(Snapshot $snapshot): bool
            {
                return $snapshot instanceof CollectionSnapshot;
            }

            /** {@inheritDoc} */
            public function getOriginalKey(string $primary): int
            {
                if (!isset($this->link[$primary])) {
                    throw new InvalidArgumentException(sprintf('The primary key "%s" is not in the computed dataset', $primary));
                }

                return $this->link[$primary];
            }
        };
    }

    /**
     * {@inheritDoc}
     *
     * No one messes with my data. NO ONE.
     *
     * @throws UnsupportedSnapshotException always thrown
     */
    public function setData(Snapshot $snapshot, array $data)
    {
        throw new UnsupportedSnapshotException($this, $snapshot);
    }
}

