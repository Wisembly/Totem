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

final class ArraySnapshotter implements Snapshotter
{
    /** {@inheritDoc} */
    public function getSnapshot($data): Snapshot
    {
        if (!$this->supports($data)) {
            throw new UnsupportedDataException($this, $data);
        }

        return new class($data) implements Snapshot {
            /** @var mixed[] snapshotted data */
            private $data;

            /** @var array raw array data */
            private $raw;

            public function __construct(array $data)
            {
                $this->data = $data;
                $this->raw = $data;
            }

            /** {@inheritDoc} */
            public function isComparable(Snapshot $snapshot): bool
            {
                return is_array($snapshot->getRaw());
            }

            /** {@inheritDoc} */
            public function getRaw()
            {
                return $this->raw;
            }

            /** {@inheritDoc} */
            public function getData(): array
            {
                return $this->data;
            }

            /** {@inheritDoc} */
            public function setData(array $data): Snapshot
            {
                $snapshot = clone $this;
                $snapshot->data = $data;

                return $snapshot;
            }
        };
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        return is_array($data);
    }
}

