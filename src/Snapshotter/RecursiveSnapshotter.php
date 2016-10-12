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

namespace Totem\Snapshotter;

use Ds\PriorityQueue;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;
use Totem\UnsupportedSnapshotException;

final class RecursiveSnapshotter implements Snapshotter
{
    /** @var PriorityQueue<Snapshotter> */
    private $snapshotters;

    public function __construct()
    {
        $this->snapshotters = new PriorityQueue;
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        try {
            $this->getSnapshotter($data);
            return true;
        } catch (UnsupportedDataException $e) {
            return false;
        }
    }

    /** {@inheritDoc} */
    public function getSnapshot($data): Snapshot
    {
        $snapshot = $this->getSnapshotter($data)->getSnapshot($data);

        if ($snapshot instanceof Snapshot\MutableSnapshot && $snapshot->isMutable()) {
            $snapshot = $snapshot->mutate($this);
        }

        return $snapshot;
    }

    public function addSnapshotter(Snapshotter $snapshotter, $priority = 0)
    {
        $this->snapshotters->push($snapshotter, $priority);
    }

    private function getSnapshotter($data): Snapshotter
    {
        // gotta copy the queue because it is destructive
        foreach ($this->snapshotters->copy() as $snapshotter) {
            if ($snapshotter->supports($data)) {
                return $snapshotter;
            }
        }

        throw new UnsupportedDataException($this, $data);
    }
}

