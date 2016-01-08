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

namespace Totem\Change;

use Totem\ChangeInterface;

/**
 * Represents a modification
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Modification implements ChangeInterface
{
    /** @var mixed */
    private $old;

    /** @var mixed */
    private $new;

    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    public function getOld()
    {
        return $this->old;
    }

    public function getNew()
    {
        return $this->new;
    }
}

