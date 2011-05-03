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
 * Route
 */
class Route implements RoutableInterface, CompilableInterface
{
   /**
    * Base default values for route parameters
    * @var array
    */
   protected static $base_parameter_defaults = array(
      'controller' => null,
      'action' => null,
      'id' => null,
   );
   
   
   public $path;
   public $endpoint;
   public $regex;
   public $parameters = array();
   public $constraints;
   public $methods = array();
   public $name;
   public $module;
   public $path_prefix;
   public $name_prefix;
   
   
   /**
    * Constructor
    * @param string $path url path
    * @param mixed $endpoint route endpoint - "controller::action" or a valid callback
    */
   public function __construct($path, $endpoint)
   {
      $this->path = static::normalize($path);
      $this->endpoint = $endpoint;
   }
   
   /**
    * Set route parameter defaults
    * @param array $defaults default values hash
    * @return \Starlight\Component\Routing\Route this instance
    */
   public function defaults(array $defaults)
   {
      foreach ($defaults as $key => $value) {
         if (!isset($this->parameters[$key]) || trim($this->parameters[$key]) == '') {
            $this->parameters[$key] = $value;
         }
      }
      
      return $this;
   }
   
   /**
    * Set route constraints
    * @param mixed $constraints constraints (hash or \Closure)
    * @return \Starlight\Component\Routing\Route this instance
    */
   public function constraints($constraints)
   {
      $this->constraints = $constraints;
      
      return $this;
   }
   
   /**
    * Set HTTP methods/verbs route should respond to
    * @param array $methods HTTP methods
    * @return \Starlight\Component\Routing\Route this instance
    */
   public function methods($methods)
   {
      if (!is_array($methods)) {
         $methods = array($methods);
      }
      
      $this->methods = $methods;
      
      return $this;
   }
   
   /**
    * Set route name for generated helpers
    * @param string $name route name
    * @return \Starlight\Component\Routing\Route this instance
    */
   public function name($name)
   {
      $this->name = $name;
      
      return $this;
   }
   
   /**
    * Set module/namespace for the controller
    * @param string $module module/namespace
    * @return \Starlight\Component\Routing\Route this instance
    */
   public function module($module)
   {
      $this->module = $module;
      
      return $this;
   }
   
   /**
    * Compiles the route
    * @return \Starlight\Component\Routing\Route this compiled instance
    */
   public function compile()
   {
      $parser = new RouteParser();
      
      $regex_constraints = $other_constraints = array();
      if (is_array($this->constraints)) {
         foreach ($this->constraints as $k => $c) {
            if (!is_callable($c)) {
               $regex_constraints[$k] = $c;
            } else {
               $other_constraints[] = $c;
            }
         }   
      }

      if ($this->path_prefix) {
         $this->path = $this->path_prefix . $this->path;
      }

      $this->regex = $parser->parse($this->path, $regex_constraints);
      $this->parameters = array_merge(static::$base_parameter_defaults, array_fill_keys($parser->names, ''), (array) $this->parameters);
      $this->constraints = $other_constraints;
      
      // get endpoint if string:
      if (is_string($this->endpoint)) {
         if (strpos($this->endpoint, '::') !== false) {
            // apply module:
            if ($this->module) {
               $this->endpoint = $this->module . '\\' . $this->endpoint;
            }
            
            list($this->parameters['controller'], $this->parameters['action']) = explode('::', $this->endpoint);
         }
      } elseif (!is_callable($this->endpoint)) {
         // should be a callback
         throw new \InvalidArgumentException('Route endpoint is invalid');
      }
      
      // set name/prefix if available:
      if ($this->name && $this->name_prefix) {
         $this->name = $this->name_prefix . $this->name;
      }

      return $this;
   }
   
   /**
    * Match a request
    * @param \Starlight\Component\Http\Request $request current request
    * @return boolean route matches request
    */
   public function match(Request $request)
   {      
      $is_match = preg_match($this->regex, $request->getBaseUri(), $matches);
      if ($is_match) {
         if (!in_array($request->getMethod(), $this->methods)) {
            return false;
         }
         
         foreach ($this->constraints as $c) {
            if (!call_user_func($c, $request)) {
               return false;
            }
         }
         
         foreach ($matches as $k => $v) {
            if (array_key_exists($k, $this->parameters)) {
               $this->parameters[$k] = $v;
            }
         }
      }
      
      return $is_match;
   }
   
   /**
    *
    */
   public function dispatch(Request $request)
   {
      $p = array_filter($this->parameters, function($var){ return trim($var) != ''; });
      unset($p['controller'], $p['action']);
      
      if (is_callable($this->endpoint)) {
         call_user_func_array($this->endpoint, array_merge(array($request), array_values($p)));
         return true;
      } else {
         // controller pattern
         
      }
   }
   
   /**
    * Normalizes path - removes trailing slashes and prepends single slash
    * @param string $path original path
    * @return string normalized path
    */
   protected function normalize($path)
   {
      $path = trim($path, '/');
      $path = '/' . $path;
      
      return $path;
   }
}
