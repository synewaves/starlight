<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Tests\Component\Routing;
use Starlight\Component\Routing\Route;


/**
 */ 
class RouteTest extends \PHPUnit_Framework_TestCase
{
   public function testRouteIsNormalized()
   {
      $route = new Route('some/path', 'controller::action');
      $this->assertEquals('/some/path', $route->path);
   }
   
   public function testSetDefaults()
   {
      $route = $this->getRoute();
      
      $defaults = array('id' => 24);
      $return = $route->defaults($defaults);
      
      $this->assertEquals($defaults, $route->parameters);
      $this->assertEquals($return, $route);
   }
   
   public function testDefaultsDontOverrideSetParameters()
   {
      $route = $this->getRoute();
      
      $defaults = array('id' => 24);
      $route->parameters = array('id' => 25);
      $return = $route->defaults($defaults);
      
      $this->assertEquals(25, $route->parameters['id']);
      $this->assertEquals($return, $route);
   }
   
   public function testSetConstraints()
   {
      $route = $this->getRoute();
      
      $constraints = array('id' => '[0-9]+');
      $return = $route->constraints($constraints);
      
      $this->assertEquals($constraints, $route->constraints);
      $this->assertEquals($return, $route);
   }
   
   public function testSetMethods()
   {
      $route = $this->getRoute();
      
      $methods = array('get', 'post');
      $return = $route->methods($methods);
      
      $this->assertEquals($methods, $route->methods);
      $this->assertEquals($return, $route);
   }
   
   public function testSetName()
   {
      $route = $this->getRoute();
      
      $name = 'login';
      $return = $route->name($name);
      
      $this->assertEquals($name, $route->name);
      $this->assertEquals($return, $route);
   }
   
   public function testDetermineControllerActionFromEndpoint()
   {
      $route = $this->getRoute(array('endpoint' => 'UsersController#view'));
      $route->compile();
      
      $this->assertEquals('UsersController', $route->parameters['controller']);
      $this->assertEquals('view', $route->parameters['action']);
   }
   
   protected function getRoute(array $options = array())
   {
      $defaults = array(
         'path' => '/:controller/:action/:id',
         'endpoint' => 'controller::action',
      );
      $options = array_merge($defaults, $options);
      
      return new Route($options['path'], $options['endpoint']);
   }
}
