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
abstract class AbstractSnapshot
{
    /** @var array data stored in an array */
    protected $data = [];

    /** @var float Time when this snapshot was taken */
    private $time;

    public function __construct() {
        $this->time = microtime(true); // @todo use Datetime instead ?
    }

    /**
     * Check if the two snapshots are comparable
     *
     * @param self $snapshot Snapshot to be compared with
     * @return boolean true if the two snapshots can be processed in a diff, false otherwise
     */
    abstract public function isComparable(self $snapshot);

    /** Clone this object */
    final private function __clone() {}

    /**
     * Calculate the diff between two snapshots
     *
     * @param self $snapshot Snapshot to compare this one to
     *
     * @return Set Changeset between the two snapshots
     * @throws IncomparableDataException If the two snapshots are not comparable
     */
    final public function diff(self $snapshot)
    {
        if (!$this->isComparable($snapshot)) {
            throw new IncomparableDataException('this object is not comparable with the base');
        }

        $data = [$this->data, $snapshot->data];

        if ($snapshot->time < $this->time) {
            $data = [$snapshot->data, $this->data];
        }

        return new Set($data[0], $data[1]);
    }
}
