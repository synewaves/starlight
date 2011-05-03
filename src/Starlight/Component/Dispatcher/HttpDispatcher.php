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
use Starlight\Component\Dispatcher\Context\HttpContext;
use Starlight\Component\Routing\Router;


/**
 * HTTP Dispatcher
 */
class HttpDispatcher extends Dispatcher
{
   protected $router;
   
   public function __construct(HttpContext $context, Router $router)
   {
      $this->context = $context;
      $this->router = $router;
   }
   
   public function getContext()
   {
      return $this->context;
   }
   
   public function setContext(HttpContext $context)
   {
      $this->context = $context;
      
      return $this;
   }
   
   public function getRouter()
   {
      return $this->router;
   }
   
   public function setRouter(Router $router)
   {
      $this->router = $router;
      
      return $this;
   }
   
   public function dispatch()
   {
      $route = $this->router->match($this->context->getRequest());
      if ($route) {
         $params = array_filter($route->parameters, function($var){ return trim($var) != ''; });
         unset($params['controller'], $params['action']);
         
         if (is_callable($route->endpoint)) {
            call_user_func_array($route->endpoint, array_merge(array($this->context->getRequest()), array_values($params)));
            return true;
         } else {
            // controller pattern
            
         }
      }
      
      return false;
   }
}