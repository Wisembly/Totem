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

use InvalidArgumentException;

use Totem\Snapshot;
use Totem\Snapshotter\RecursiveSnapshotter;

/**
 * Represents a snapshot of a collection
 *
 * A collection is an array of numerical indexes of array elements
 *
 * @internal
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
final class CollectionSnapshot extends Snapshot implements MutableSnapshot
{
    /**
     * Data mapper between the primary key and the real integer key of
     * this collection
     *
     * @var int[]
     */
    private $link;

    /**
     * Indicates if the data are mutable
     *
     * @var bool
     */
    private $mutable;

    public function __construct(iterable $raw, array $data, array $link, bool $mutable = false)
    {
        parent::__construct($raw, $data);

        $this->link = $link;
        $this->mutable = $mutable;
    }

    /** {@inheritDoc} */
    public function isComparable(Snapshot $snapshot): bool
    {
        return $snapshot instanceof CollectionSnapshot;
    }

    /**
     * Returns the original key for the primary key $primary
     *
     * @param mixed $primary Primary key to search
     *
     * @throws InvalidArgumentException primary key not found
     */
    public function getOriginalKey($primary)
    {
        if (!isset($this->link[$primary])) {
            throw new InvalidArgumentException(sprintf('The primary key "%s" is not in the computed dataset', $primary));
        }

        return $this->link[$primary];
    }

    /** {@inheritDoc} */
    public function isMutable(): bool
    {
        return $this->mutable;
    }

    /** {@inheritDoc} */
    public function mutate(RecursiveSnapshotter $recursiveSnapshotter): Snapshot
    {
        if (!$this->isMutable()) {
            throw new ImmutableException($this);
        }

        $clone = clone $this;

        foreach ($clone->data as &$snapshot) {
            $snapshot = $snapshot->mutate($recursiveSnapshotter);
        }

        return $clone;
    }
}

