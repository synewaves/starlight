<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Tests\Http\HeaderBucket;
use Starlight\Component\Http\HeaderBucket;


/**
 */ 
class HeaderBucketTest extends \PHPUnit_Framework_TestCase
{
   public function testHas()
   {
      $bucket = new HeaderBucket(array('foo' => 'bar', 'fuzz' => 'bizz'));
      $this->assertEquals(true, $bucket->has('foo'));
      $this->assertEquals(true, $bucket->has('FoO'));
      $this->assertEquals(false, $bucket->has('bizz'));
   }
   
   public function testGet()
   {
      $bucket = new HeaderBucket(array('foo' => 'bar', 'fuzz' => 'bizz'));
      $this->assertEquals('bar', $bucket->get('foo'), '->get return current value');
      $this->assertEquals('bar', $bucket->get('FoO'), '->get key in case insensitive');
      $this->assertEquals(array('bar'), $bucket->get('foo', 'nope', false), '->get return the value as array');
   
      // defaults
      $this->assertNull($bucket->get('none'), '->get unknown values returns null');
      $this->assertEquals('default', $bucket->get('none', 'default'), '->get unknown values returns default');
      $this->assertEquals(array('default'), $bucket->get('none', 'default', false), '->get unknown values returns default as array');
   
      $bucket->set('foo', 'bor', false);
      $this->assertEquals('bar', $bucket->get('foo'), '->get return first value');
      $this->assertEquals(array('bar', 'bor'), $bucket->get('foo', 'nope', false), '->get return all values as array');
   }
   
   public function testContains()
   {
      $bucket = new HeaderBucket(array('foo' => 'bar', 'fuzz' => 'bizz'));
      $this->assertTrue($bucket->contains('foo', 'bar'), '->contains first value');
      $this->assertTrue($bucket->contains('fuzz', 'bizz'), '->contains second value');
      $this->assertFalse($bucket->contains('nope', 'nope'), '->contains unknown value');
      $this->assertFalse($bucket->contains('foo', 'nope'), '->contains unknown value');
      
      // Multiple values
      $bucket->set('foo', 'bor', false);
      $this->assertTrue($bucket->contains('foo', 'bar'), '->contains first value');
      $this->assertTrue($bucket->contains('foo', 'bor'), '->contains second value');
      $this->assertFalse($bucket->contains('foo', 'nope'), '->contains unknown value');
   }
   
   public function testDelete()
   {
      $bucket = new HeaderBucket(array('foo' => 'bar', 'fuzz' => 'bizz'));
      $bucket->delete('foo');
      $bucket->delete('FuZZ');
      
      $this->assertFalse($bucket->has('foo'));
      $this->assertFalse($bucket->has('Fuzz'));
   }
   
   public function testCacheControlDirectiveAccessors()
   {
      $bucket = new HeaderBucket();
      $bucket->addCacheControlDirective('public');
      
      $this->assertTrue($bucket->hasCacheControlDirective('public'));
      $this->assertEquals(true, $bucket->getCacheControlDirective('public'));
      $this->assertEquals('public', $bucket->get('cache-control'));
      
      $bucket->addCacheControlDirective('max-age', 10);
      $this->assertTrue($bucket->hasCacheControlDirective('max-age'));
      $this->assertEquals(10, $bucket->getCacheControlDirective('max-age'));
      $this->assertEquals('max-age=10, public', $bucket->get('cache-control'));
      
      $bucket->removeCacheControlDirective('max-age');
      $this->assertFalse($bucket->hasCacheControlDirective('max-age'));
   }
   
   public function testCacheControlDirectiveParsing()
   {
      $bucket = new HeaderBucket(array('cache-control' => 'public, max-age=10'));
      $this->assertTrue($bucket->hasCacheControlDirective('public'));
      $this->assertEquals(true, $bucket->getCacheControlDirective('public'));
      
      $this->assertTrue($bucket->hasCacheControlDirective('max-age'));
      $this->assertEquals(10, $bucket->getCacheControlDirective('max-age'));
      
      $bucket->addCacheControlDirective('s-maxage', 100);
      $this->assertEquals('max-age=10, public, s-maxage=100', $bucket->get('cache-control'));
   }
   
   public function testCacheControlDirectiveOverrideWithReplace()
   {
      $bucket = new HeaderBucket(array('cache-control' => 'private, max-age=100'));
      $bucket->replace(array('cache-control' => 'public, max-age=10'));
      $this->assertTrue($bucket->hasCacheControlDirective('public'));
      $this->assertEquals(true, $bucket->getCacheControlDirective('public'));
      
      $this->assertTrue($bucket->hasCacheControlDirective('max-age'));
      $this->assertEquals(10, $bucket->getCacheControlDirective('max-age'));
   }
}
