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

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;

final class RecursiveSnapshotter implements Snapshotter
{
    /** @var \SplPriorityQueue<Snapshotter> */
    private $snapshotters;

    public function __construct()
    {
        $this->snapshotters = new \SplPriorityQueue;
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
        $snapshot = $data instanceof Snapshot
            ? $data
            : $this->getSnapshotter($data)->getSnapshot($data);

        $data = $snapshot->getData();

        foreach ($data as &$value) {
            try {
                $value = $this->getSnapshot($value);
            } catch (UnsupportedDataException $e) {
            }
        }

         // can't do anything else than requiring a setData in the generated
         // snapshot :{
        $snapshot = $snapshot->setData($data);

        return $snapshot;
    }

    public function addSnapshotter(Snapshotter $snapshotter, $priority = 0)
    {
        $this->snapshotters->insert($snapshotter, $priority);
    }

    private function getSnapshotter($data): Snapshotter
    {
        $snapshotters = clone $this->snapshotters;

        foreach ($snapshotters as $snapshotter) {
            if ($snapshotter->supports($data)) {
                return $snapshotter;
            }
        }

        throw new UnsupportedDataException($this, $data);
    }
}

