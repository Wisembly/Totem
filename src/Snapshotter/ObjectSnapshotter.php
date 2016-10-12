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
use Totem\UnsupportedSnapshotException;

use Totem\Snapshot\ObjectSnapshot;

final class ObjectSnapshotter implements Snapshotter
{
    /** {@inheritDoc} */
    public function getSnapshot($raw): Snapshot
    {
        if (!$this->supports($raw)) {
            throw new UnsupportedDataException($this, $raw);
        }

        $data = [];
        $export = (array) $raw;
        $class = get_class($raw);

        foreach ($export as $property => $value) {
            $property = str_replace(["\x00*\x00", "\x00{$class}\x00"], '', $property); // not accessible properties
            $data[$property] = $value;
        }

        return new ObjectSnapshot($raw, $data);
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        return is_object($data);
    }
}

