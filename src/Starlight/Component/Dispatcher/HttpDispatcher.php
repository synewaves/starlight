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
            // lambda/closure
            $params = array_merge($this->determineParameters($route->endpoint), $params);
            call_user_func_array($route->endpoint, array_merge(array($this->context->getRequest()), array_values($params)));
            return true;
         } else {
            // controller pattern
            $controller = $route->parameters['controller'];
            $action = $route->parameters['action'];
            
            $params = array_merge($this->determineParameters(array($controller, $action)), $params);
            
            $klass = new $controller();
            call_user_func_array(array($klass, $action), array_values($params));
            return true;
         }
      }
      
      return false;
   }
   
   protected function determineParameters($method)
   {
      $klass = null;
      if (is_array($method)) {
         $klass = $method[0];
         $method = $method[1];
      }
      
      $ref = !$klass ? new \ReflectionFunction($method) : new \ReflectionMethod($klass, $method);
      $params = array();
      foreach ($ref->getParameters() as $i => $param) {
         if (!$klass && $i==0) { continue; }
         if ($param->isOptional()) {
            $params[$param->getName()] = $param->getDefaultValue();
         } else {
            $params[$param->getName()] = null;
         }
      }
      
      return $params;
   }
}