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
use Starlight\Component\Inflector\Inflector;


/**
 * Router
 */
class Router implements CompilableInterface
{
   protected $routes = array();
   protected $current = array();
   protected $current_type = null;
   protected $compiled = array();
   protected $has_compiled = false;
   protected $scopes = array();
   
   
   /**
    * Draw routes - router gateway
    * @param \Closure $callback callback
    * @return \Starlight\Component\Routing\Router this instance
    */
   public function draw(\Closure $callback)
   {
      // TODO: check for cached version before redrawing these
      $callback($this);
      
      return $this;
   }
   
   /**
    * Maps a single route
    * @param string $path url path
    * @param mixed $endpoint controller::action pair or callback
    * @return \Starlight\Component\Routing\Route route
    */
   public function map($path, $endpoint)
   {
      if ($this->current_type == 'resource') {
         throw new \RuntimeException('Cannot use ' . __CLASS__ . '::map within a resource context.');
      }
      
      if (!preg_match('/\(\.:format\)$/', $path)) {
         // auto append format option to path:
         $path .= '(.:format)';
      }
      
      $this->routes[] = new Route($path, $endpoint);
      $this->current[] = count($this->routes) - 1;
      $this->current_type = 'route';
      
      $this->applyScopes();
      
      array_pop($this->current);
      $this->current_type = '';
      
      return $this->routes[count($this->routes) - 1];
   }
   
   /**
    * Maps RESTful resources routes
    */
   public function resources()
   {
      $args = func_get_args();
      $count = count($args);
      $callback = null;
      $options = array();
      
      if (is_callable($args[$count - 1])) {
         $callback = array_pop($args);
         $count--;
      }
      
      if (is_array($args[$count - 1])) {
         $options = array_pop($args);
         $count--;
      }
      
      // map each resource separately (if multiple)
      foreach ($args as $resource) {
         if ($resource == Inflector::pluralize($resource)) {
            $klass = 'Starlight\Component\Routing\ResourceRoute';
         } else {
            $klass = 'Starlight\Component\Routing\SingularResourceRoute';
         }
         
         $this->routes[] = new $klass($resource, $options);
         $this->current[] = count($this->routes) - 1;
         $this->current_type = 'resource';
         
         $route = $this->routes[count($this->routes) - 1];
         
         $this->applyScopes();
         
         if ($callback) {
            $callback($this, $route);
         }
         
         array_pop($this->current);
         $this->current_type = 'route';
      }
      
      return $route;
   }
   
   /**
    * Maps singular RESTful resource
    */
   public function resource()
   {
      return call_user_func_array(array($this, 'resources'), func_get_args());
   }
   
   /**
    *
    */
   public function redirect($path)
   {
      // TOOD: handle inline redirection
      if (is_string($path)) {
         return function() use ($path) {
            return $path;
         };
      } else {
         // already a callback, need to lazy evaluate later:
         return $path;
      }
   }
   
   /**
    * Scope routes
    * @param array $scopes scopes to apply
    * @param \Closure $callback callback
    */
   public function scope(array $scopes, \Closure $callback)
   {
      $this->addScopes($scopes);
      $callback($this);
      $this->removeScopes($scopes);
   }
   
   /**
    * Compile all routes
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
            return $r;
         }
      }
   }
   
   /**
    * Pretty version of routes
    * @return string nice string
    */
   public function __toString()
   {
      $parts = array();
      $mn = $mv = $mp = 0;
      foreach ($this->compiled as $r) {
         $p = array(
            'name' => $r->name,
            'verb' => strtoupper(implode(',', $r->methods)),
            'path' => $r->path,
            'endp' => is_callable($r->endpoint) ? '{callback}' : $r->endpoint,
         );
         
         if (strlen($p['name']) > $mn) {
            $mn = strlen($p['name']);
         }
         if (strlen($p['verb']) > $mv) {
            $mv = strlen($p['verb']);
         }
         if (strlen($p['path']) > $mp) {
            $mp = strlen($p['path']);
         }
         
         $parts[] = $p;
      }
      
      
      $rc = '<pre>';
      foreach ($parts as $p) {
         $rc .= sprintf("%" . $mn . "s %-" . $mv . "s %-" . $mp . "s %s\n", $p['name'], $p['verb'], $p['path'], $p['endp']);
      }
      $rc .= '</pre>';
      
      return $rc;
   }
   
   /**
    * Apply current scopes to currently scoped routes
    */
   protected function applyScopes()
   {
      $count = count($this->current);
      $current = $this->current[$count - 1];
      $previous = isset($this->current[$count - 2]) ? $this->routes[$this->current[$count - 2]] : null;
      
      $this->routes[$current] = $this->applyScope($this->routes[$current], $previous);
   }
   
   /**
    * Apply scopes to single route
    * @param mixed $route resource or route to scope
    * @param \Starlight\Component\Routing\ResourceRoute $nested current parent route (if present)
    * @return mixed resource or route which was scoped
    */
   protected function applyScope($route, $nested = null)
   {
      // nested resource
      if ($nested) {
         $route->path_prefix .= $nested->path_prefix . '/' . $nested->plural . '/:' . $nested->singular . '_id';
         $route->name_prefix .= $nested->name_prefix . $nested->singular . '_';
      }
      
      // constraints
      if (isset($this->scopes['constraints'])) {
         $constraints = array();
         foreach ($this->scopes['constraints'] as $c) {
            $constraints = array_merge($constraints, is_array($c) ? $c : (array) $c);
         }
         $route->constraints($constraints);
      }
      
      // name
      if (isset($this->scopes['name'])) {
         $count = count($this->scopes['name']);
         if ($this->current_type == 'resource') {
            if (!$nested || $count > 1) {
               $route->name_prefix .= $this->scopes['name'][$count - 1] . '_';
            }
         } else {
            $route->name_prefix .= $this->scopes['name'][$count - 1] . '_';
         }
      }
      
      // module
      if (isset($this->scopes['module'])) {
         $route->module(implode('\\', $this->scopes['module']));
      }
      
      // path
      if (isset($this->scopes['path'])) {
         $count = count($this->scopes['path']);
         if ($this->current_type == 'resource') {
            if (!$nested || $count > 1) {
               $route->path_prefix .= '/' . $this->scopes['path'][$count - 1];
            }
         } else {
            $route->path_prefix .= '/' . $this->scopes['path'][$count - 1];
         }
      }
      
      
      if ($this->current_type == 'route') {
         // route only cases
         
         // HTTP methods/verbs
         if (isset($this->scopes['methods'])) {
            // only consider the last on the stack:
            $route->methods($this->scopes['methods'][count($this->scopes['methods']) - 1]);
         }
         
         // parameter defaults
         if (isset($this->scopes['defaults'])) {
            $defaults = array();
            foreach ($this->scopes['defaults'] as $d) {
               $defaults = array_merge($defaults, $d);
            }
            $route->defaults($d);
         }
         
      } elseif ($this->current_type == 'resource') {
         // resource only cases

         // except
         if (isset($this->scopes['except'])) {
            // only consider the last on the stack:
            $route->except($this->scopes['except'][count($this->scopes['except']) - 1]);
         }
         
         // only
         if (isset($this->scopes['only'])) {
            // only consider the last on the stack:
            $route->only($this->scopes['only'][count($this->scopes['only']) - 1]);
         }
         
         // path_names
         if (isset($this->scopes['path_names'])) {
            // only consider the last on the stack:
            $route->pathNames($this->scopes['path_names'][count($this->scopes['path_names']) - 1]);
         }
      }
   
      return $route;
   }
   
   /**
    * Add scopes to parse tree
    * @param array $scopes scopes to add
    */
   protected function addScopes(array $scopes)
   {
      foreach ($scopes as $scope => $options) {
         if ($scope == 'namespace') {
            $this->addScopes(array(
               'name' => $options,
               'path' => $options,
               'module' => $options,
            ));
            continue;
         }
         
         if (isset($this->scopes[$scope])) {
            $this->scopes[$scope][] = $options;
         } else {
            $this->scopes[$scope] = array($options);
         }
      }
   }
   
   /**
    * Remove scopes from parse tree
    * @param array $scopes scopes to remove
    */
   protected function removeScopes(array $scopes)
   {
      foreach ($scopes as $scope => $options) {
         if ($scope == 'namespace') {
            $this->removeScopes(array(
               'name' => $options,
               'path' => $options,
               'module' => $options,
            ));
            continue;
         }
         if (isset($this->scopes[$scope])) {
            if (($position = array_search($options, $this->scopes[$scope])) !== false) {
               unset($this->scopes[$scope][$position]);
               if (count($this->scopes[$scope]) == 0) {
                  unset($this->scopes[$scope]);
               }
            }
         }
      }
   }
}
