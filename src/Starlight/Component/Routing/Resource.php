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
 * Resource
 */
class Resource implements Compilable
{
   /**
    * RESTful routing map; maps actions to methods
    * @var array
    */
   protected static $resources_map = array(
      'index'   => array('name' => '%p',        'verb' => 'get',    'url' => '/'),
      'add'     => array('name' => 'add_%s',    'verb' => 'get',    'url' => '/:action'),
      'create'  => array('name' => '%p',        'verb' => 'post',   'url' => '/'),
      'show'    => array('name' => '%s',        'verb' => 'get',    'url' => '/:id'),
      'edit'    => array('name' => 'edit_%s',   'verb' => 'get',    'url' => '/:id/:action'),
      'update'  => array('name' => '%s',        'verb' => 'put',    'url' => '/:id'),
      'delete'  => array('name' => 'delete_%s', 'verb' => 'get',    'url' => '/:id/:action'),
      'destroy' => array('name' => '%s',        'verb' => 'delete', 'url' => '/:id'),
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
   public $namespace;
   // member, collection, resources (nested)
   
   
   /**
    *
    */
   public function __construct($resource)
   {
      $this->resource = $resource;
      $this->controller = Inflector::pluralize($this->resource);
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
      $single = explode(' ', strtolower(Inflector::humanize($name)));
      $plural = $single;
      $count = count($single);
      
      $single[$count - 1] = Inflector::singularize($single[$count - 1]);
      $plural[$count - 1] = Inflector::pluralize($plural[$count - 1]);
      
      $this->name = array(
         implode('_', $single),
         implode('_', $plural),
      );
      
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
      $generators = static::$resources_map;
      if ($this->except) {
         $generators = array_diff_key($generators, array_fill_keys($this->except, true));
      } elseif ($this->only) {
         $generators = array_intersect_key($generators, array_fill_keys($this->only, true));
      }
      
      if (is_array($this->path_names)) {
         $this->path_names += static::$resource_names;
      } else {
         $this->path_names = static::$resource_names;
      }
      
      $routes = array();
      foreach ($generators as $action => $parts) {
         $path = $parts['url'];
         if (strpos($path, ':action') !== false) {
            $path = str_replace(':action', $this->path_names[$action], $path);
         }
         
         $r = new Route('/' . $this->resource . $path, $this->controller . '#' . $action);
         $r->methods(array($parts['verb']));
         
         $single = isset($this->name[0]) ? $this->name[0] : Inflector::singularize($this->resource);
         $plural = isset($this->name[1]) ? $this->name[1] : Inflector::pluralize($this->resource);
         
         $name = str_replace('%s', $single, $parts['name']);
         $name = str_replace('%p', $plural, $name);
         $r->name($name);
         
         if ($this->constraints) {
            $r->constraints($this->constraints);
         }
         
         if ($this->namespace) {
            $r->namespaced($this->namespace);
         }
         
         $routes[] = $r->compile();
      }

      return $routes;
   }
};
