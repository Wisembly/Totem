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

final class ObjectSnapshotter implements Snapshotter
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

            /** @var object raw object data */
            private $raw;

            /** @var string object spl hash */
            private $oid;

            public function __construct($data)
            {
                if (!is_object($data)) {
                    throw new InvalidArgumentException(sprintf('Expected an object, got %s', gettype($data)));
                }

                $this->data = [];
                $this->raw = $data;
                $this->oid = spl_object_hash($data);

                $export = (array) $data;
                $class = get_class($data);

                foreach ($export as $property => $value) {
                    $property = str_replace(["\x00*\x00", "\x00{$class}\x00"], '', $property); // not accessible properties

                    $this->data[$property] = $value;
                }
            }

            /** {@inheritDoc} */
            public function isComparable(Snapshot $snapshot): bool
            {
                $data = $snapshot->getRaw();

                if (!is_object($data)) {
                    return false;
                }

                return spl_object_hash($data) === $this->oid;
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
        return is_object($data);
    }
}

