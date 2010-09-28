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
   protected static $base_parameter_defaults = array(
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
   
   
   /**
    * Constructor
    * @param string $path url path
    * @param mixed $endpoint route endpoint
    */
   public function __construct($path, $endpoint)
   {
      $this->path = static::normalize($path);
      $this->endpoint = $endpoint;
   }
   
   /**
    * Set route parameter defaults
    * @param array $defaults default values hash
    * @return Route this instance
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
   * Set route constraints
   * @param mixed $constraints constraints (hash or Closure)
   * @return Route this instance
    */
   public function constraints($constraints)
   {
      $this->constraints = $constraints;
      
      return $this;
   }
   
   /**
   * Set HTTP methods/verbs route should respond to
   * @param array $methods HTTP methods
   * @return Route this instance
    */
   public function methods(array $methods)
   {
      $this->methods = $methods;
      
      return $this;
   }
   
   /**
   * Set route name for generated helpers
   * @param string $name route name
   * @return Route this instance
    */
   public function name($name)
   {
      $this->name = $name;
      
      return $this;
   }
   
   /**
    * Compiles the route
    */
   public function compile()
   {
      $parser = new RouteParser();
      $constraints = !is_callable($this->constraints) ? (array) $this->constraints : array();

      $this->regex = $parser->parse($this->path, $constraints);
      $this->parameters = array_merge(static::$base_parameter_defaults, array_fill_keys($parser->names, ''), (array) $this->parameters);
      
      // get endpoint:
      if (strpos($this->endpoint, '#') !== false) {
         list($this->parameters['controller'], $this->parameters['action']) = explode('#', $this->endpoint);
      }

      return $this;
   }
   
   /**
    *
    */
   public function match(Request $request)
   {
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
