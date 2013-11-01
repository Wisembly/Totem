<?php
/**
 * This file is part of the Link Set package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Baptiste Clavié <clavie.b@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace LinkSet;

/**
 * Represents a change
 *
 * @author Rémy Gazelot <rgazelot@gmail.com>
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Change 
{
    private $old; // old state
    private $new; // new state

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
