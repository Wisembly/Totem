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

use \ArrayAccess,
    \BadMethodCallException,
    \InvalidArgumentException;

/**
 * Base class for a Change
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 * @author Rémy Gazelot <rgazelot@gmail.com>
 */
abstract class AbstractChange
{
    /** old state */
    private $old;

    /** new state */
    private $new;

    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    /** {@inheritDoc} */
    public function getOld()
    {
        return $this->old;
    }

    /** {@inheritDoc} */
    public function getNew()
    {
        return $this->new;
    }
}

