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
 * Represents a snapshot of a collection
 *
 * A collection is an array of numerical indexes of array elements
 *
 * @internal
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface CollectionSnapshot
{
    /**
     * Returns the original key for the primary key $primary
     *
     * @param mixed $primary Primary key to search
     *
     * @throws InvalidArgumentException primary key not found
     */
    public function getOriginalKey(string $primary): int;
}

