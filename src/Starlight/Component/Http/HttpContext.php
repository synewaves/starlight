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
use Starlight\Component\Dispatcher\Context\Context;


class HttpContext extends Context
{
   public $request;
   public $response;
   
   
   public function __construct()
   {
      $this->request = new Request();
      $this->response = new Response();
   }
};
