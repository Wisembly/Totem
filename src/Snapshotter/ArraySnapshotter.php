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

use InvalidArgumentException;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;
use Totem\UnsupportedSnapshotException;

use Totem\Snapshot\ArraySnapshot;

final class ArraySnapshotter implements Snapshotter
{
    /** {@inheritDoc} */
    public function getSnapshot($raw): Snapshot
    {
        if (!$this->supports($raw)) {
            throw new UnsupportedDataException($this, $raw);
        }

        return new ArraySnapshot($raw);
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        return is_array($data);
    }
}

