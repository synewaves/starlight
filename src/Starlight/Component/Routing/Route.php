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
class Route implements Routable, Compilable
{
   /**
    * Base default values for route parameters
    * @var array
    */
   protected static $base_defaults = array(
      'controller' => null,
      'action' => null,
      'id' => null,
   );
   
   
   public $path;
   public $endpoint;
   public $regex;
   public $parameters;
   public $constraints;
   public $methods;
   public $name;
   public $namespace;
   
   
   /**
    *
    */
   public function __construct($path, $endpoint)
   {
      $this->path = static::normalize($path);
      $this->endpoint = $endpoint;
   }
   
   /**
    *
    */
   public function defaults(array $defaults)
   {
      foreach ($defaults as $key => $value) {
         if (trim($this->parameters[$key]) == '') {
            $this->parameters[$key] = $value;
         }
      }
      
      return $this;
   }
   
   /**
    *
    */
   public function constraints($constraints)
   {
      $this->constraints = $constraints;
      
      return $this;
   }
   
   /**
    *
    */
   public function methods(array $methods)
   {
      $this->methods = $methods;
      
      return $this;
   }
   
   /**
    *
    */
   public function name($name)
   {
      $this->name = $name;
      
      return $this;
   }
   
   /**
    *
    */
   public function namespaced($namespace)
   {
      $this->namespace = $namespace;
      
      return $this;
   }
   
   /**
    *
    */
   public function compile()
   {
      $parser = new RouteParser();
      $constraints = !is_callable($this->constraints) ? (array) $this->constraints : array();
      
      if ($this->namespace) {
         $this->path = '/' . $this->namespace . $this->path;
         if ($this->name != '') {
            $this->name = $this->namespace . '_' . $this->name;
         }
         $this->endpoint = $this->namespace . '/' . $this->endpoint;
      }
      
      $this->regex = $parser->parse($this->path, $constraints);
      $this->parameters = array_merge(static::$base_defaults, array_fill_keys($parser->names, ''), (array) $this->parameters);
      list($this->parameters['controller'], $this->parameters['action']) = explode('#', $this->endpoint);
      
      return $this;
   }
   
   /**
    *
    */
   public function match(Request $request)
   {
   }
   
   /**
    *
    */
   protected function normalize($path)
   {
      $path = trim($path, '/');
      $path = '/' . $path;
      
      return $path;
   }
};
