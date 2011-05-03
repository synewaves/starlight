<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Dispatcher;
use Starlight\Component\Dispatcher\Context\Context;


abstract class Dispatcher
{
   protected $context;
   
   public function __construct(Context $context)
   {
      $this->context = $context;
   }
   
   abstract public function dispatch();
}
