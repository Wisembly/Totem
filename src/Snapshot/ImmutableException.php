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

use BadMethodCallException;
use Totem\Snapshot;

class ImmutableException extends BadMethodCallException
{
    /** @var Snapshot */
    private $snapshot;

    public function __construct(Snapshot $snapshot)
    {
        parent::__construct('This snapshot is not mutable');

        $this->snapshot = $snapshot;
    }

    public function getSnapshot(): Snapshot
    {
        return $this->snapshot;
    }
}

