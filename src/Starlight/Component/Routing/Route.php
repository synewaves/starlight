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


/**
 * Route
 */
class Route implements Routable, Compilable
{
   /**
    * URL path separators regex
    * '\' and '.'
    * @var string
    */
   protected static $separators = array('\/', '\.');
   
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
      $this->path = $path;
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
      // 
      
      return $this;
   }
   
   /**
    *
    */
   public function compile()
   {
      $elements = preg_split('/([' . implode(static::$separators) . '])/i', trim($this->path, '/'), -1, PREG_SPLIT_DELIM_CAPTURE);
      if (count($elements) == 0) {
         return;
      }
      
      array_unshift($elements, '/');
      $patterns = array();
      $count = count($elements);
      $names = array();
      
      for ($i=0; $i<$count; $i = $i+2) {
         $sep = $elements[$i];
         $elm = $elements[$i+1];
         
         if (preg_match('/^\*(.+)$/', $elm, $match)) {
            // glob character:
            $patterns[] = '(?:\\' . $sep . '(.*+))?';
            $names[$match[1]] = null;
         } elseif (preg_match('/^:(.+)$/', $elm, $match)) {
            // named element:
            $patterns[] = '(?:\\' . $sep . '([^\/\.]+))?';
            $names[$match[1]] = null;
         } else {
            // normal element:
            $patterns[] = '\\' . $sep . $elm;
         }
      }
      
      if ($this->namespace) {
         array_unshift($patterns, '\\/' . $this->namespace);
         if ($this->name != '') {
            $this->name = $this->namespace . '_' . $this->name;
         }
      }
      
      $this->regex = '/^' . implode($patterns) . '\/?$/i';
      $this->parameters = static::$base_defaults + $names;
      list($this->parameters['controller'], $this->parameters['action']) = explode('#', $this->endpoint);
      
      return $this;
   }
   
   /**
    *
    */
   public function match($context)
   {
      // if (preg_match($this->regex, $path, $matches)) {
      //    dump($matches);
      // }
   }
};
