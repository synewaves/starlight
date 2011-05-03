<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Tests\Http\Cookie;
use Starlight\Component\Http\Cookie;


/**
 */ 
class CookieTest extends \PHPUnit_Framework_TestCase
{
   public function testConstructor()
   {
      $cookie = new Cookie('name', 'value', array(
         'expires' => time(),
         'http_only' => true,
      ));
      
      $this->assertEquals($cookie->getName(), 'name');
      $this->assertEquals($cookie->getValue(), 'value');
      $this->assertTrue($cookie->getHttpOnly());
      $this->assertFalse($cookie->getSecure());
   }
   
   public function testGetSetName()
   {
      $cookie = new Cookie('name', 'value');
      $this->assertEquals($cookie->getName(), 'name');
      
      try {
         $cookie->setName("name\n");
      } catch (\Exception $e) {
         $this->assertInstanceOf('InvalidArgumentException', $e);
      }
      
      try {
         $cookie->setName("");
      } catch (\Exception $e) {
         $this->assertInstanceOf('InvalidArgumentException', $e);
      }
   }
   
   public function testGetSetValue()
   {
      $cookie = new Cookie('name', 'value');
      $this->assertEquals($cookie->getValue(), 'value');
      
      try {
         $cookie->setValue("value\n");
      } catch (\Exception $e) {
         $this->assertInstanceOf('InvalidArgumentException', $e);
      }
   }
   
   public function testGetSetExpires()
   {
      $date = new \DateTime();
      
      $cookie = new Cookie('name', 'value');
      $cookie->setExpires($date);
      $this->assertEquals($date->format('U'), $cookie->getExpires()->format('U'));
      
      $cookie->setExpires($date->format('U'));
      $this->assertEquals($date->format('U'), $cookie->getExpires()->format('U'));
      
      $cookie->setExpires($date->format(DATE_RFC822));
      $this->assertEquals($date->format('U'), $cookie->getExpires()->format('U'));
      
      try {
         $cookie->setExpires('this obviously is not a date');
      } catch (\Exception $e) {
         $this->assertInstanceOf('InvalidArgumentException', $e);
      }
   }
   
   public function testGetSetPath()
   {
      $cookie = new Cookie('name', 'value');
      $cookie->setPath('/');
      
      $this->assertEquals('/', $cookie->getPath());
   }
   
   public function testGetSetDomain()
   {
      $cookie = new Cookie('name', 'value');
      $cookie->setDomain('example.com');
      
      $this->assertEquals('example.com', $cookie->getDomain());
   }
   
   public function testGetSetSecure()
   {
      $cookie = new Cookie('name', 'value');
      
      $cookie->setSecure(true);
      $this->assertTrue($cookie->getSecure());
      
      $cookie->setSecure(0);
      $this->assertFalse($cookie->getSecure());
      
      $cookie->setSecure('true');
      $this->assertTrue($cookie->getSecure());
   }
   
   public function testGetSetHttpOnly()
   {
      $cookie = new Cookie('name', 'value');
      
      $cookie->setHttpOnly(true);
      $this->assertTrue($cookie->getHttpOnly());
      
      $cookie->setHttpOnly(0);
      $this->assertFalse($cookie->getHttpOnly());
      
      $cookie->setHttpOnly('true');
      $this->assertTrue($cookie->getHttpOnly());
   }
   
   public function testIsCleared()
   {
      $cookie = new Cookie('name', 'value');
      
      $cookie->setExpires(new \DateTime('+2 days'));
      $this->assertFalse($cookie->isCleared());
      
      $cookie->setExpires(time() - 20);
      $this->assertTrue($cookie->isCleared());
   }
   
   public function testToString()
   {
      $date = new \DateTime();
      
      $cookie = new Cookie('name', 'value', array(
         'expires' => $date,
         'path' => '/path',
         'domain' => 'example.com',
         'secure' => true,
         'http_only' => true,
      ));
      
      $expected_value = 'name=value; expires=' . $date->format(DATE_COOKIE) . '; domain=example.com; path=/path; secure; httponly';
      $this->assertEquals($expected_value, $cookie->toHeaderValue());
      $this->assertEquals($expected_value, (string) $cookie);
   }
}
