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

use \ArrayAccess,
    \ArrayIterator,
    \IteratorAggregate,

    \BadMethodCallException,
    \InvalidArgumentException;

use Totem\Exception\IncomparableDataException;

/**
 * Base class for a Snapshot
 *
 * Represent the data fixed at a given time
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
abstract class AbstractSnapshot implements ArrayAccess
{
    /** @var array data stored in an array */
    protected $data = [];

    /** @var mixed raw data stored */
    protected $raw;

    /**
     * Calculate the diff between two snapshots
     *
     * @param self $snapshot Snapshot to compare this one to
     *
     * @return Set Changeset between the two snapshots
     * @throws IncomparableDataException If the two snapshots are not comparable
     */
    public function diff(self $snapshot)
    {
        if (!$this->isComparable($snapshot)) {
            throw new IncomparableDataException;
        }

        return new Set($this, $snapshot);
    }

    /**
     * Get the raw data fed to this snapshot
     *
     * @return mixed
     */
    final public function getRawData()
    {
        return $this->raw;
    }

    /**
     * Get the computed data, transformed into an array by this constructor
     *
     * @return SnapshotInterface[]
     * @throws InvalidArgumentException If the data is not an array
     */
    final public function getComparableData()
    {
        if (!is_array($this->data)) {
            throw new InvalidArgumentException('The computed data is not an array, "' . gettype($this->data) . '" given');
        }

        return $this->data;
    }

    /**
     * Returns the keys of the data
     *
     * @return array
     * @throws InvalidArgumentException If the frozen data is not an array
     */
    final public function getDataKeys()
    {
        return array_keys($this->getComparableData());
    }

    /**
     * Clone this object
     *
     * @codeCoverageIgnore
     */
    final private function __clone() {}

    /** {@inheritDoc} */
    final public function offsetExists($offset)
    {
        return isset($this->getComparableData()[$offset]);
    }

    /** {@inheritDoc} */
    final public function offsetGet($offset)
    {
        return $this->getComparableData()[$offset];
    }

    /**
     * {@inheritDoc}
     *
     * @throws BadMethodCallException if a unset is tried on a snapshot property
     */
    final public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('A snapshot is frozen by nature');
    }

    /**
     * {@inheritDoc}
     *
     * @throws BadMethodCallException if a unset is tried on a snapshot property
     */
    final public function offsetUnset($offset)
    {
        throw new BadMethodCallException('A snapshot is frozen by nature');
    }

    /**
     * Finish data initialization
     *
     * To be called by child classes *after* the data has been initialized
     */
    protected function normalize()
    {
        if (!is_array($this->data)) {
            throw new InvalidArgumentException('The computed data is not an array, "' . gettype($this->data) . '" given');
        }

        foreach ($this->data as &$value) {
            switch (gettype($value)) {
                case 'object':
                    $value = new Snapshot\ObjectSnapshot($value);
                    break;

                case 'array':
                    $value = new Snapshot\ArraySnapshot($value);
                    break;
            }
        }
    }

    /**
     * Check if the two snapshots are comparable
     *
     * @param self $snapshot Snapshot to be compared with
     * @return boolean true if the two snapshots can be processed in a diff, false otherwise
     */
    protected function isComparable(self $snapshot)
    {
        if (!$snapshot instanceof static) {
            return false;
        }

        return gettype($snapshot->raw) === gettype($this->raw);
    }
}

