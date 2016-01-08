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

interface Snapshot
{
    /**
     * Checks if another snapshot is comparable to this one
     *
     * @return bool
     */
    public function isComparable(Snapshot $snapshot): bool;

    /**
     * Get the raw data associated with the snapshot
     *
     * @return mixed
     */
    public function getRaw();

    /**
     * Get the snapshotted data
     *
     * @return mixed[]
     */
    public function getData(): array;

    /**
     * Data setter
     *
     * This is needed in order to be able to normalize the data inside the
     * snapshotted data (Adding more snapshots on its properties)
     *
     * @return Snapshot
     */
    public function setData(array $data): Snapshot;
}

