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

use \InvalidArgumentException;

use Totem\Exception\IncomparableDataException;

/**
 * Base class for a Snapshot
 *
 * Represent the data fixed at a given time
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Snapshot
{
    /** @var array data stored in an array */
    protected $data = [];

    /** @var mixed raw data stored */
    protected $raw;

    public function __construct($raw)
    {
        $this->raw  = $raw;
        $this->data = (array) $raw;
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

    /**
     * Clone this object
     *
     * @codeCoverageIgnore
     */
    final private function __clone() {}

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
            throw new IncomparableDataException('this data is not comparable with the base');
        }

        return new Set($this->data, $snapshot->data);
    }

    /**
     * Get the computed data, transformed into an array by this constructor
     *
     * @return array comparable data
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
     * Get the raw data fed to this snapshot
     *
     * @return mixed
     */
    final public function getRawData()
    {
        return $this->raw;
    }
}

