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
   public function setup()
   {
      $this->headers = array(
         'HTTP_HOST'            => 'localhost:80',
         'HTTP_CONNECTION'      => 'keep-alive',
         'HTTP_REFERER'         => 'http://localhost:80/',
         'HTTP_ACCEPT'          => 'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
         'HTTP_USER_AGENT'      => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.59 Safari/534.3',
         'HTTP_ACCEPT_ENCODING' => 'gzip,deflate,sdch',
         'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
         'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
      );
      
      $this->type = 'request';
      
      $this->normalized_headers = array();
      foreach ($this->headers as $header => $value) {
         $this->normalized_headers[HeaderBucket::normalizeHeaderName($header)] = array($value);
      }
   }
   
   public function testArguments()
   {
      $this->type = 'madeup';
      $this->setExpectedException('InvalidArgumentException');
      
      $this->getBucket();
   }
   
   public function testAll()
   {
      $this->assertEquals($this->getBucket()->all(), $this->normalized_headers);
   }
   
   public function testKeys()
   {
      $this->assertEquals($this->getBucket()->keys(), array_keys($this->normalized_headers));
   }
   
   public function testReplace()
   {
      $bucket = $this->getBucket();
      $this->assertEquals($bucket->all(), $this->normalized_headers);
      
      $new_headers = array(
         'HTTP_HOST' => 'localhost:80',
      );
      $normalized = array(
         'http-host' => array('localhost:80'),
      );
      $bucket->replace($new_headers);
      $this->assertNotEquals($bucket->all(), $this->normalized_headers);
      $this->assertEquals($bucket->all(), $normalized);
   }
   
   public function testGet()
   {
      $this->assertEquals($this->getBucket()->get('HTTP_HOST'), 'localhost:80');
      $this->assertEquals($this->getBucket()->get('HTTP_HOST', false), array('localhost:80'));
      $this->assertNull($this->getBucket()->get('MADE_UP'));
      $this->assertEquals($this->getBucket()->get('MADE_UP', false), array());
   }
   
   public function testSet()
   {
      $bucket = $this->getBucket();
      
      $bucket->set('MADE_UP', 'Value');
      $this->assertEquals($bucket->get('MADE_UP'), 'Value');
      
      $bucket->set('MADE_uP', 'Value 2');
      $this->assertEquals($bucket->get('MADE_UP'), 'Value 2');
      
      $bucket->set('MADE_UP', 'Value 1', false);
      $this->assertEquals($bucket->get('MADE_UP', false), array('Value 2', 'Value 1'));
   }
   
   public function testHas()
   {
      $this->assertTrue($this->getBucket()->has('HTTP_HOST'));
      $this->assertFalse($this->getBucket()->has('MADE_UP'));
   }
   
   public function testContains()
   {
      $this->assertTrue($this->getBucket()->contains('HTTP_HOST', 'localhost:80'));
      $this->assertFalse($this->getBucket()->contains('HTTP_HOST', 'localhost'));
      $this->assertFalse($this->getBucket()->contains('MADE_UP', 'made up value'));
   }
   
   public function testDelete()
   {
      $bucket = $this->getBucket();
      
      $this->assertTrue($bucket->has('HTTP_HOST'));
      $bucket->delete('HTTP_HOST');
      $this->assertFalse($bucket->has('HTTP_HOST'));
      
      $this->assertFalse($bucket->has('MADE_UP'));
      $bucket->delete('MADE_UP');
      $this->assertFalse($bucket->has('MADE_UP'));
   }
   
   public function testSetCookieInvalidKey()
   {
      $this->setExpectedException('InvalidArgumentException');
      $this->getBucket()->setCookie('Invalid_cookie=', 'value');
   }
   
   public function testSetCookieInvalidValue()
   {
      $this->setExpectedException('InvalidArgumentException');
      $this->getBucket()->setCookie('Invalid_cookie', 'value;');
   }
   
   public function testSetCookieEmptyKey()
   {
      $this->setExpectedException('InvalidArgumentException');
      $this->getBucket()->setCookie('', 'value');
   }
   
   public function testSetCookieRequest()
   {
      $this->type = 'request';
      $bucket = $this->getBucket();
      
      $bucket->setCookie('cookie_key', 'cookie_value');
      $this->assertEquals($bucket->get('Cookie'), 'cookie_key=cookie_value');
   }
   
   public function testSetCookieWithExpirationDateInteger()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $expires = new \DateTime('+7 days');
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'expires' => $expires->getTimestamp(),
         'http_only' => false,
      ));
      
      $this->assertRegExp('/^cookie\_key\=cookie\_value\; expires\=/i', $bucket->get('Set-Cookie'));
   }
   
   public function testSetCookieWithExpirationDateString()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $expires = 'January 1, 2000 12:00:00';
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'expires' => $expires,
         'http_only' => false,
      ));
      
      $this->assertRegExp('/^cookie\_key\=cookie\_value\; expires\=/i', $bucket->get('Set-Cookie'));
   }
   
   public function testSetCookieWithExpirationDateInvalidString()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $expires = 'This is not a date';
      
      $this->setExpectedException('InvalidArgumentException');
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'expires' => $expires,
         'http_only' => false,
      ));
   }
   
   public function testSetCookieWithExpirationDateDateTime()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $expires = new \DateTime('+7 days');
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'expires' => $expires,
         'http_only' => false,
      ));
      
      $this->assertRegExp('/^cookie\_key\=cookie\_value\; expires\=/i', $bucket->get('Set-Cookie'));
   }
   
   public function testSetCookieWithPath()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $path = '/some/awesome/path';
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'path' => $path,
         'http_only' => false,
      ));
      $this->assertEquals($bucket->get('Set-Cookie'), 'cookie_key=cookie_value; path=' . $path);
   }
   
   public function testSetCookieWithDomain()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      $domain = 'example.com';
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'domain' => $domain,
         'http_only' => false,
      ));
      $this->assertEquals($bucket->get('Set-Cookie'), 'cookie_key=cookie_value; domain=' . $domain);
   }
   
   public function testSetCookieWithSecure()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'secure' => true,
         'http_only' => false,
      ));
      $this->assertEquals($bucket->get('Set-Cookie'), 'cookie_key=cookie_value; secure');
   }
   
   public function testSetCookieWithHttpOnly()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      
      $bucket->setCookie('cookie_key', 'cookie_value', array(
         'http_only' => true,
      ));
      $this->assertEquals($bucket->get('Set-Cookie'), 'cookie_key=cookie_value; httponly');
   }
   
   public function testExpireCookieInvalidKey()
   {
      $this->setExpectedException('InvalidArgumentException');
      $this->getBucket()->expireCookie('Invalid_cookie=', 'value');
   }
   
   public function testExpireCookieEmptyKey()
   {
      $this->setExpectedException('InvalidArgumentException');
      $this->getBucket()->expireCookie('', 'value;');
   }
   
   public function testExpireCookie()
   {
      $this->type = 'response';
      $bucket = $this->getBucket();
      
      $bucket->expireCookie('cookie_key');
      $this->assertRegExp('/^cookie\_key\=\; expires\=/i', $bucket->get('Set-Cookie'));
   }
   
   public function testExpireCookieRequest()
   {
      $bucket = $this->getBucket();
      
      $bucket->expireCookie('cookie_key');
      $this->assertNull($bucket->get('Set-Cookie'));
   }
   
   public function testArrayAccess()
   {
      $bucket = $this->getBucket();
      
      $this->assertTrue(isset($bucket['HTTP_HOST']));
      $this->assertEquals($bucket['HTTP_HOST'], 'localhost:80');
      
      $bucket['MADE_UP'] = 'anything';
      $this->assertTrue(isset($bucket['MADE_UP']));
      
      unset($bucket['MADE_UP']);
      $this->assertFalse(isset($bucket['MADE_UP']));
   }
   
   public function testIteratorAggreate()
   {
      $bucket = $this->getBucket();
      
      foreach ($bucket as $key => $value) {
         $this->assertEquals($value, $this->normalized_headers[$key]);
      }
   }
   
   public function testNormalizeHeaderName()
   {
      $this->assertEquals(HeaderBucket::normalizeHeaderName('HTTP_HOST'), 'http-host');
      $this->assertEquals(HeaderBucket::normalizeHeaderName('HTTP_hoST'), 'http-host');
      $this->assertEquals(HeaderBucket::normalizeHeaderName('http-host'), 'http-host');
   }
   
   
   protected function getBucket()
   {
      return new HeaderBucket($this->headers, $this->type);
   }
}
