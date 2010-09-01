<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Dispatcher;
use \Starlight\Component\Http as Http;


class HttpDispatcher
{
   protected $request;
   
   public function dispatch(Http\Request $request)
   {
      $this->request = $request;
   }
};
