<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Routing;
use Starlight\Component\Http\Request;


/**
 * Routable interface
 */
interface RoutableInterface
{
   /**
    * Match a request
    * @param \Starlight\Component\Http\Request $request current request
    */
   public function match(Request $request);
}
