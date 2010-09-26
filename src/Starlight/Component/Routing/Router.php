<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Routing;
use Starlight\Component\Http\Request;


/**
 * Router
 */
class Router
{
   protected $routes = array();
   protected $compiled = array();
   protected $scopes = array();
   protected $has_compiled = false;
   
   
   /**
    *
    */
   public function draw(\Closure $callback)
   {
      // todo: check for cached version before redrawing these
      $callback($this);
   }
   
   
   /**
    *
    */
   public function map($path, $endpoint)
   {
      $this->routes[] = new Route($path, $endpoint);
      
      return $this->applyScopes($this->routes[count($this->routes) - 1]);
   }
   
   /**
    *
    */
   public function resources($resource)
   {
      $this->routes[] = new Resource($resource);
      
      return $this->applyScopes($this->routes[count($this->routes) - 1]);
   }
   
   /**
    *
    */
   public function resource($resource)
   {
      $this->routes[] = new SingularResource($resource);
      
      return $this->applyScopes($this->routes[count($this->routes) - 1]);
   }
   
   /**
    *
    */
   public function namespaced($namespace, $callback)
   {
      $this->scope('namespace', $namespace, $callback);
   }
   
   /**
    *
    */
   public function constraints($constraints, $callback)
   {
      $this->scope('constraints', $constraints, $callback);
   }
   
   /**
    *
    */
   public function compile()
   {
      if ($this->has_compiled) {
         return;
      }
      
      $this->compiled = array();
      
      foreach ($this->routes as $route) {
         $c = $route->compile();
         if (is_array($c)) {
            $this->compiled = array_merge($this->compiled, $c);
         } else {
            $this->compiled[] = $c;
         }
      }
      
      $this->has_compiled = true;
   }
   
   /**
    *
    */
   public function match(Request $request)
   {
      foreach ($this->compiled as $r) {
         if ($r->match($request)) {
            //
            return;
         }
      }
      
      // nothing matched:
   }
   
   /**
    *
    */
   public function scope($type, $value, $callback)
   {
      $this->addScope($type, $value);
      $callback($this);
      $this->removeScope($type, $value);
   }
   
   /**
    *
    */
   protected function applyScopes($route)
   {
      // scoped namespace
      if (isset($this->scopes['namespace'])) {
         $route->namespaced(implode('/', $this->scopes['namespace']));
      }
      
      // scoped constraints
      if (isset($this->scopes['constraints'])) {
         $constraints = array();
         foreach ($this->scopes['constraints'] as $c) {
            $constraints = array_merge($constraints, $c);
         }
         $route->constraints($constraints);
      }
      
      return $route;
   }
   
   /**
    *
    */
   protected function addScope($key, $value = null)
   {
      if (isset($this->scopes[$key])) {
         $this->scopes[$key][] = $value;
      } else {
         $this->scopes[$key] = array($value);
      }
   }
   
   /**
    *
    */
   protected function removeScope($key, $value)
   {
      if (isset($this->scopes[$key])) {
         $position = array_search($value, $this->scopes[$key]);
         if ($position !== false) {
            unset($this->scopes[$key][$position]);
         }
      }
   }
}
