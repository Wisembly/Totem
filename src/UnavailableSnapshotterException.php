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

use RuntimeException;

class UnavailableSnapshotterException extends RuntimeException
{
    private $snapshotter;

    public function __construct(Snapshotter $snapshotter, $reason)
    {
        parent::__construct("This snapshotter is not available (reason: {$reason})");

        $this->snapshotter = $snapshotter;
    }

    public function getSnapshotter(): Snapshotter
    {
        return $this->snapshotter;
    }
}

