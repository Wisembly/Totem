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

        return new class($raw) extends Snapshot implements ArraySnapshot {
            public function __construct(array $raw)
            {
                parent::__construct($raw, $raw);
            }

            /** {@inheritDoc} */
            public function isComparable(Snapshot $snapshot): bool
            {
                return is_array($snapshot->getRaw());
            }
        };
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        return is_array($data);
    }

    /** {@inheritDoc} */
    public function setData(Snapshot $snapshot, array $data)
    {
        if (!$snapshot instanceof ArraySnapshot) {
            throw new UnsupportedSnapshotException($this, $snapshot);
        }

        // from http://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/
        $callback = function () use ($data) {
            $this->data = $data;
        };

        $callback->call($snapshot);
    }
}

