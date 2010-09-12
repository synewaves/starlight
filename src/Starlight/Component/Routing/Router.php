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
 * Router
 */
class Router
{
   protected static $routes = array();
   protected static $resources = array();
   
   
   public static function match($path, $endpoint)
   {
      static::$routes[] = new Route($path, $endpoint);
      
      return static::$routes[count(static::$routes) - 1];
   }
   
   
   public static function resources($resource)
   {
      static::$resources[] = new Resource($resource);
      
      return static::$resources[count(static::$resources) - 1];
   }
};
