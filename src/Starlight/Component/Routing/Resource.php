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
   }
   
   /**
    *
    */
   public function except(array $except)
   {
      $this->only = null;
      $this->except = $except;
      
      // array_diff_key(static::$resources_map, array_fill_keys($except, true));
      
      return $this;
   }
   
   /**
    *
    */
   public function only(array $only)
   {
      $this->except = null;
      $this->only = $only;
      
      // array_intersect_key(static::$resources_map, array_fill_keys($except, true));
      
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
   }
};
