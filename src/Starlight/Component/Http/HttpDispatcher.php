<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Http;
use Starlight\Component\Dispatcher\Dispatcher;


class HttpDispatcher extends Dispatcher
{
   public function dispatch()
   {
      dump($this->context->request);
   }
}
