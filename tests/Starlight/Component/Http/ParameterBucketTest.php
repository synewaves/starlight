<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
         'test_4' => 12321,
         'test_5' => '12341',
         'test_6' => 'value_1matt23_again',
      );
   }
   
   public function testGetAlpha()
   {
      $this->assertEquals($this->getBucket()->getAlpha('test_1'), 'value');
      $this->assertEquals($this->getBucket()->getAlpha('test_4'), '');
      $this->assertEquals($this->getBucket()->getAlpha('test_5'), '');
      $this->assertEquals($this->getBucket()->getAlpha('test_6'), 'valuemattagain');
   }
   
   public function testGetAlnum()
   {
      $this->assertEquals($this->getBucket()->getAlnum('test_1'), 'value1');
      $this->assertEquals($this->getBucket()->getAlnum('test_4'), '12321');
      $this->assertEquals($this->getBucket()->getAlnum('test_5'), '12341');
      $this->assertEquals($this->getBucket()->getAlnum('test_6'), 'value1matt23again');
   }
   
   public function testGetDigits()
   {
      $this->assertEquals($this->getBucket()->getDigits('test_1'), '1');
      $this->assertEquals($this->getBucket()->getDigits('test_4'), '12321');
      $this->assertEquals($this->getBucket()->getDigits('test_5'), '12341');
      $this->assertEquals($this->getBucket()->getDigits('test_6'), '123');
   }
   
   public function testGetInt()
   {
      $this->assertEquals($this->getBucket()->getInt('test_1'), 0);
      $this->assertEquals($this->getBucket()->getInt('test_4'), 12321);
      $this->assertEquals($this->getBucket()->getInt('test_5'), 12341);
      $this->assertEquals($this->getBucket()->getInt('test_6'), 0);
   }
   
   protected function getBucket()
   {
      return new ParameterBucket($this->params);
   }
}
