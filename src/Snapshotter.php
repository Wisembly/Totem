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

interface Snapshotter
{
    /**
     * Builds and return a normalized snapshot
     *
     * @param mixed $raw Data to snapshot
     *
     * @return Snapshot
     * @throws UnsupportedDataException Data not supported by this snapshotter
     */
    public function getSnapshot($raw): Snapshot;

    /**
     * Check if this snapshot supports the given $data
     *
     * @return bool
     */
    public function supports($raw): bool;

    /**
     * Set the data of a compatible snapshot
     *
     * @param array $data Data to override
     * @return void
     * @throws UnsupportedSnapshotException
     */
    public function setData(Snapshot $snapshot, array $data);
}

