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

/**
 * Just a blank interface, so that generated snapshots by the ObjectSnapshotter
 * are marked and identifiable
 *
 * @internal
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface ObjectSnapshot
{
    /**
     * Returns the object hash of the snapshotted object
     *
     * @return string
     */
    public function getObjectId();
}

