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
   protected static $compiled = array();
   
   
   public static function map($path, $endpoint)
   {
      static::$routes[] = new Route($path, $endpoint);
      
      return static::$routes[count(static::$routes) - 1];
   }
   
   
   public static function resources($resource)
   {
      static::$routes[] = new Resource($resource);
      
      return static::$routes[count(static::$routes) - 1];
   }
   
   public static function compile()
   {
      static::$compiled = array();
      
      foreach (self::$routes as $route) {
         $c = $route->compile();
         if (is_array($c)) {
            static::$compiled  += $c;
         } else {
            static::$compiled [] = $c;
         }
      }
   }
   
   public static function match($url)
   {
      foreach (static::$compiled as $r) {
         if ($r->match($url)) {
            //
            return;
         }
      }
      
      // nothing matched:
   }
};
