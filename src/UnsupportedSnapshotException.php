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

namespace Totem;

use InvalidArgumentException;

class UnsupportedSnapshotException extends InvalidArgumentException
{
    /** @var Snapshot */
    private $snapshot;

    /** @var Snapshotter */
    private $snapshotter;

    public function __construct(Snapshotter $snapshotter, Snapshot $snapshot)
    {
        parent::__construct('This snapshot is not supported by this snapshotter');

        $this->snapshot = $snapshot;
        $this->snapshotter = $snapshotter;
    }

    public function getSnapshot(): Snapshot
    {
        return $this->snapshot;
    }

    public function getSnapshotter(): Snapshotter
    {
        return $this->snapshotter;
    }
}

