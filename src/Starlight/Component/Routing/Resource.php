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
use Starlight\Component\Inflector\Inflector;


/**
 * Route
 */
class Resource implements Compilable
{
   /**
    * RESTful routing map; maps actions to methods
    * @var array
    */
   protected static $resources_map = array(
      'index'   => array('method' => 'get',    'path' => '/'),
      'add'     => array('method' => 'get',    'path' => '/:action'),
      'create'  => array('method' => 'post',   'path' => '/'),
      'show'    => array('method' => 'get',    'path' => '/:id'),
      'edit'    => array('method' => 'get',    'path' => '/:id/:action'),
      'update'  => array('method' => 'put',    'path' => '/:id'),
      'delete'  => array('method' => 'get',    'path' => '/:id/:action'),
      'destroy' => array('method' => 'delete', 'path' => '/:id'),
   );
   
   protected static $resource_names = array(
      'add'    => 'add',
      'edit'   => 'edit',
      'delete' => 'delete',
   );
   
   
   public $resource;
   public $except;
   public $only;
   public $controller;
   public $constraints;
   public $name;
   public $path_names;
   // member, collection, resources (nested)
   
   
   /**
    *
    */
   public function __construct($resource)
   {
      $this->resource = $resource;
      $this->controller = $this->resource;
   }
   
   /**
    *
    */
   public function except(array $except)
   {
      $this->only = null;
      $this->except = $except;
      
      return $this;
   }
   
   /**
    *
    */
   public function only(array $only)
   {
      $this->except = null;
      $this->only = $only;
      
      return $this;
   }
   
   /**
    *
    */
   public function controller($controller)
   {
      $this->controller = $controller;
      
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
    * (as)
    */
   public function name($name)
   {
      $this->name = $name;
      
      return $this;
   }
   
   /**
    *
    */
   public function pathNames(array $names)
   {
      $this->path_names = $names;
      
      return $this;
   }
   
   
   /**
    *
    */
   public function compile()
   {
      $generators = self::$resources_map;
      if ($this->except) {
         $generators = array_diff_key($generators, array_fill_keys($this->except, true));
      } elseif ($this->only) {
         $generators = array_intersect_key($generators, array_fill_keys($this->only, true));
      }
      
      $this->path_names += self::$resource_names;
      
      $routes = array();
      foreach ($generators as $action => $parts) {
         $path = $parts['path'];
         if (strpos($path, ':action') !== false) {
            $path = str_replace(':action', $this->path_names[$action], $path);
         }
         
         $r = new Route('/' . $this->resource . $path, $this->controller . '#' . $action);
         $r->methods(array($parts['method']))->name($this->name);
         $routes[] = $r;
      }
      
      dump($routes);
   }
};
