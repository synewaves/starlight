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
 * ResourceRoute
 */
class ResourceRoute implements CompilableInterface
{
   /**
    * RESTful routing map; maps actions to methods
    * @var array
    */
   protected static $resources_map = array(
      'index'   => array('name' => '%p',        'verb' => 'get',    'url' => '(.:format)'),
      'add'     => array('name' => 'add_%s',    'verb' => 'get',    'url' => '/:action(.:format)'),
      'create'  => array('name' => '%p',        'verb' => 'post',   'url' => '(.:format)'),
      'show'    => array('name' => '%s',        'verb' => 'get',    'url' => '/:id(.:format)'),
      'edit'    => array('name' => 'edit_%s',   'verb' => 'get',    'url' => '/:id/:action(.:format)'),
      'update'  => array('name' => '%s',        'verb' => 'put',    'url' => '/:id(.:format)'),
      'destroy' => array('name' => '%s',        'verb' => 'delete', 'url' => '/:id(.:format)'),
   );
   
   /**
    * Default path names
    * @var array
    */
   protected static $resource_names = array(
      'add'    => 'add',
      'edit'   => 'edit',
      'delete' => 'delete',
   );
   
   
   public $resource;
   public $controller;
   public $except;
   public $only;
   public $constraints;
   public $singular;
   public $plural;
   public $path_names;
   public $module;
   public $path_prefix;
   public $name_prefix;
   public $member = array();
   public $collection = array();
   public $map_member_collection_scope = null;
   
   
   /**
    * Constructor
    * @param string $resource resource name
    * @param array $options options hash
    */
   public function __construct($resource = null, array $options = array())
   {
      if (!is_null($resource)) {
         $this->resource = $resource;
         
         $this->controller = $this->plural = Inflector::pluralize($this->resource);
         $this->singular = Inflector::singularize($this->controller);
      
         if (count($options) > 0) {
            foreach ($options as $key => $value) {
               $this->$key($value);
            }
         }
      }
   }
   
   /**
    * Set except routes from $resources_map
    * @param array $except except resources
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function except(array $except)
   {
      $this->only = null;
      $this->except = $except;
      
      return $this;
   }
   
   /**
    * Set only routes from $resources_map
    * @param array $only only resources
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function only(array $only)
   {
      $this->except = null;
      $this->only = $only;
      
      return $this;
   }
   
   /**
    * Set controller
    * @param string $controller controller name
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function controller($controller)
   {
      $this->controller = $controller;
      
      return $this;
   }
   
   /**
    * Set contraints
    * @param mixed $constraints constraints
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function constraints($constraints)
   {
      $this->constraints = $constraints;
      
      return $this;
   }
   
   /**
    * Set name for route paths
    * @param string $name path name
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function name($name)
   {
      $single = explode(' ', strtolower(Inflector::humanize($name)));
      $plural = $single;
      $count = count($single);
      
      $single[$count - 1] = Inflector::singularize($single[$count - 1]);
      $plural[$count - 1] = Inflector::pluralize($plural[$count - 1]);
      
      $this->singular = implode('_', $single);
      $this->plural = implode('_', $plural);
      
      return $this;
   }
   
   /**
    * Set path names for special routes
    * @param array $names path name overrides
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function pathNames(array $names)
   {
      $this->path_names = $names;
      
      return $this;
   }
   
   /**
    * Set module/namespace for resource
    * @param string $module module/namespace name
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function module($module)
   {
      $this->module = $module;
      
      return $this;
   }

   /**
    * Set member routes
    * @param \Closure $callback callback method
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function member(\Closure $callback)
   {
      $this->map_member_collection_scope = 'member';
      $callback($this);
      $this->map_member_collection_scope = null;
      
      return $this;
   }
   
   /**
    * Set collection routes
    * @param \Closure $callback callback method
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function collection(\Closure $callback)
   {
      $this->map_member_collection_scope = 'collection';
      $callback($this);
      $this->map_member_collection_scope = null;
      
      return $this;
   }
   
   /**
    * Set an extra route which responds to GET requests
    * @param string $path path
    * @param string $options options hash
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function get($path, $options = array())
   {
      return $this->mapMethod('get', $path, $options);
   }
   
   /**
   * Set an extra route which responds to PUT requests
   * @param string $path path
   * @param string $options options hash
   * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function put($path, $options = array())
   {
      return $this->mapMethod('put', $path, $options);
   }
   
   /**
   * Set an extra route which responds to POST requests
   * @param string $path path
   * @param string $options options hash
   * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function post($path, $options = array())
   {
      return $this->mapMethod('post', $path, $options);
   }
   
   /**
   * Set an extra route which responds to DELETE requests
   * @param string $path path
   * @param string $options options hash
   * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   public function delete($path, $options = array())
   {
      return $this->mapMethod('delete', $path, $options);
   }

   /**
    * Map extra routes through a common interface
    * @param string $method HTTP method
    * @param string $path path
    * @param array $options options hash
    * @return \Starlight\Component\Routing\ResourceRoute this resource route
    */
   protected function mapMethod($method, $path, $options = array())
   {
      $on = isset($options['on']) ? $options['on'] : $this->map_member_collection_scope;
      if (!$on || ($on != 'member' && $on != 'collection')) {
         throw new \InvalidArgumentException('You must pass a valid "on" option (collection/method) to ' . $method);
      }
      
      array_push($this->$on, array($path, strtoupper($method)));
      
      return $this;
   }
   
   /**
    * Compiles this resource route into individual \Starlight\Component\Routing\Route
    * @return array array of \Starlight\Component\Routing\Route routes
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
      
      if ($this->module) {
         $this->controller = $this->module . '\\' . $this->controller;
      }
      
      if (count($this->member) > 0) {
         foreach ($this->member as $member) {
            list($name, $verb) = $member;
            $generators[$name] = array('name' => $name . '_%s', 'verb' => $verb, 'url' => '/:id/' . $name . '(.:format)');
         }
      }
      
      if (count($this->collection) > 0) {
         foreach ($this->collection as $collection) {
            list($name, $verb) = $collection;
            $generators[$name] = array('name' => $name . '_%p', 'verb' => $verb, 'url' => '/' . $name . '(.:format)');
         }
      }
      
      $routes = array();
      foreach ($generators as $action => $parts) {
         $path = $parts['url'];
         if (strpos($path, ':action') !== false) {
            $path = str_replace(':action', $this->path_names[$action], $path);
         }
         
         $r = new Route($this->path_prefix . '/' . $this->resource . $path, $this->controller . '::' . $action);
         $r->methods(array($parts['verb']));
         
         $name = str_replace('%s', $this->name_prefix . $this->singular, $parts['name']);
         $name = str_replace('%p', $this->name_prefix . $this->plural, $name);
         $r->name($name);
         
         if ($this->constraints) {
            $r->constraints($this->constraints);
         }
         
         $routes[] = $r->compile();
      }
      
      return $routes;
   }
}
