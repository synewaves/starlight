<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Tests\Http\ParameterBucket;
use Starlight\Component\Http\ParameterBucket;


/**
 */ 
class ParameterBucketTest extends \PHPUnit_Framework_TestCase
{
   public function setup()
   {
      $this->params = array(
         'test_1' => 'value_1',
         'test_2' => 'value_2',
         'test_3' => 'value_3',
      );
   }
   
   public function testAll()
   {
      $this->assertEquals($this->getBucket()->all(), $this->params);
   }
   
   public function testKeys()
   {
      $this->assertEquals($this->getBucket()->keys(), array_keys($this->params));
   }
   
   public function testReplace()
   {
      $bucket = $this->getBucket();
      $new_parameters = array('test_4' => 'value_4');
      $bucket->replace($new_parameters);
      
      $this->assertEquals($bucket->all(), $new_parameters);
      $this->assertNotEquals($bucket->all(), $this->params);
   }
   
   public function testAdd()
   {
      $bucket = $this->getBucket();
      $new_parameters = array('test_4' => 'value_4');
      $bucket->add($new_parameters);
      
      $this->assertEquals($bucket->all(), array_replace($this->params, $new_parameters));
      $this->assertNotEquals($bucket->all(), $this->params);
   }
   
   public function testGet()
   {
      $this->assertEquals($this->getBucket()->get('test_1'), 'value_1');
      $this->assertNull($this->getBucket()->get('test_4'));
      $this->assertEquals($this->getBucket()->get('test_4', 'value_4'), 'value_4');
   }
   
   public function testSet()
   {
      $bucket = $this->getBucket();
      
      $this->assertNull($bucket->get('test_4'));
      $bucket->set('test_4', 'value_4');
      $this->assertEquals($bucket->get('test_4'), 'value_4');
      
      $this->assertEquals($bucket->get('test_2'), 'value_2');
      $bucket->set('test_2', 'value_22');
      $this->assertEquals($bucket->get('test_2'), 'value_22');
   }
   
   public function testHas()
   {
      $this->assertTrue($this->getBucket()->has('test_1'));
      $this->assertFalse($this->getBucket()->has('test_4'));
   }
   
   public function testDelete()
   {
      $bucket = $this->getBucket();
      
      $this->assertTrue($bucket->has('test_1'));
      $bucket->delete('test_1');
      $this->assertFalse($bucket->has('test_1'));
      
      $this->assertFalse($bucket->has('test_4'));
      $bucket->delete('test_4');
      $this->assertFalse($bucket->has('test_4'));
   }
   
   public function testArrayAccess()
   {
      $bucket = $this->getBucket();
      
      $this->assertTrue(isset($bucket['test_1']));
      $this->assertEquals($bucket['test_1'], 'value_1');
      
      $bucket['test_4'] = 'anything';
      $this->assertTrue(isset($bucket['test_4']));
      
      unset($bucket['test_4']);
      $this->assertFalse(isset($bucket['test_4']));
   }
   
   public function testIteratorAggreate()
   {
      $bucket = $this->getBucket();
      
      foreach ($bucket as $key => $value) {
         $this->assertEquals($value, $this->params[$key]);
      }
   }
   
   protected function getBucket()
   {
      return new ParameterBucket($this->params);
   }
};
