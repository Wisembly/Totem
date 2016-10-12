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
 * Array snapshots
 *
 * @internal
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
final class ArraySnapshot extends Snapshot implements MutableSnapshot
{
    public function __construct(array $raw)
    {
        parent::__construct($raw, $raw);
    }

    /** {@inheritDoc} */
    public function isComparable(Snapshot $snapshot): bool
    {
        return $snapshot instanceof ArraySnapshot;
    }

    /** {@inheritDoc} */
    public function isMutable(): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    public function mutate(RecursiveSnapshotter $recursiveSnapshotter): Snapshot
    {
        $clone = clone $this;

        foreach ($clone->data as &$data) {
            try {
                $data = $recursiveSnapshotter->getSnapshot($data);
            } catch (UnsupportedDataException $e) {
            }
        }

        return $clone;
    }
}

