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

use Totem\Snapshot;
use Totem\Snapshotter\RecursiveSnapshotter;

/**
 * Indicates a snapshot that can be mutated through the RecursiveSnapshotter
 *
 * Usually used with the RecursiveSnapshotter snapshotter
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface MutableSnapshot
{
    /**
     * @return bool
     *      Should always return true if this interface is implemented, except
     *      cases such as the collection snapshot which depends on a context.
     */
    public function isMutable(): bool;

    /**
     * Mutate the current snapshot, returning a new instance.
     *
     * @return Snapshot
     */
    public function mutate(RecursiveSnapshotter $recursiveSnapshotter): Snapshot;
}

