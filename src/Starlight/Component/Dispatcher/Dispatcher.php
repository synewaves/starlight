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
use Starlight\Component\Dispatcher\Context\Context;


abstract class Dispatcher
{
   public $context;
   
   public function __construct(Context $context)
   {
      $this->context = $context;
   }
   
   abstract public function dispatch();
}
