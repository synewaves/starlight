<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Starlight\Component\Dispatcher\Context;
use Starlight\Component\Http\Request;
use Starlight\Component\Http\Response;


/**
 * HTTP Context
 */
class HttpContext extends Context
{
   protected $request;
   protected $response;
   
   
   public function __construct(Request $request = null)
   {
      $this->request = !is_null($request) ? $request : new Request();
      $this->response = new Response();
   }
   
   public function getRequest()
   {
      return $this->request;
   }
   
   public function setRequest(Request $request)
   {
      $this->request = $request;
      
      return $this;
   }
   
   public function getResponse()
   {
      return $this->response;
   }
   
   public function setResponse(Response $response)
   {
      $this->response = $response;
      
      return $this;
   }
}
