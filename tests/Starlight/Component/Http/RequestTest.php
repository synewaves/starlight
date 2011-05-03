<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Tests\Http\Request;
use Starlight\Component\Http\Request;


/**
 */ 
class RequestTest extends \PHPUnit_Framework_TestCase
{
   public function setup()
   {
      $this->post = array();
      $this->get = array();
      $this->cookies = array();
      $this->files = array();
      $this->server = array();
   }
   
   public function testClone()
   {
      $this->post['test'] = 'example';
      $this->cookies['my_cookie'] = 'the value';
      
      $request1 = $this->getRequest();
      $request2 = clone $request1;
      
      $this->assertEquals($request1->post->get('test'), $request2->post->get('test'));
      $this->assertEquals($request1->cookies->get('my_cookie'), $request2->cookies->get('my_cookie'));
      
      $request1->post->set('test', 'something new');
      $this->assertNotEquals($request1->post->get('test'), $request2->post->get('test'));
   }
   
   public function testGet()
   {
      $this->cookies['check'] = 'cookies';
      $this->post['check'] = 'post';
      $this->get['check'] = 'get';
      $this->assertEquals($this->getRequest()->get('check'), 'get');
      
      unset($this->get['check']);
      $this->assertEquals($this->getRequest()->get('check'), 'post');
      
      unset($this->post['check']);
      $this->assertEquals($this->getRequest()->get('check'), 'cookies');
      
      unset($this->cookies['check']);
      $this->assertNull($this->getRequest()->get('check'));
      $this->assertEquals($this->getRequest()->get('check', 'default'), 'default');
   }
   
   public function testGetMethod()
   {
      $this->server['REQUEST_METHOD'] = 'post';
      $this->server['X_HTTP_METHOD_OVERRIDE'] = 'put';
      $this->post['_method'] = 'get';
      $this->assertEquals($this->getRequest()->getMethod(), 'get');
      
      unset($this->post['_method']);
      $this->assertEquals($this->getRequest()->getMethod(), 'put');
      
      unset($this->server['X_HTTP_METHOD_OVERRIDE']);
      $this->assertEquals($this->getRequest()->getMethod(), 'post');
      
      unset($this->server['REQUEST_METHOD']);
      $this->assertNull($this->getRequest()->getMethod());
   }
   
   public function testIsDelete()
   {
      $this->server['REQUEST_METHOD'] = 'delete';
      $this->assertTrue($this->getRequest()->isDelete());
      
      $this->server['REQUEST_METHOD'] = 'post';
      $this->assertFalse($this->getRequest()->isDelete());
   }
   
   public function testIsGet()
   {
      $this->server['REQUEST_METHOD'] = 'get';
      $this->assertTrue($this->getRequest()->isGet());
      
      $this->server['REQUEST_METHOD'] = 'post';
      $this->assertFalse($this->getRequest()->isGet());
   }
   
   public function testIsPost()
   {
      $this->server['REQUEST_METHOD'] = 'post';
      $this->assertTrue($this->getRequest()->isPost());
      
      $this->server['REQUEST_METHOD'] = 'get';
      $this->assertFalse($this->getRequest()->isPost());
   }
   
   public function testIsPut()
   {
      $this->server['REQUEST_METHOD'] = 'put';
      $this->assertTrue($this->getRequest()->isPut());
      
      $this->server['REQUEST_METHOD'] = 'post';
      $this->assertFalse($this->getRequest()->isPut());
   }
   
   public function testIsHead()
   {
      $this->server['REQUEST_METHOD'] = 'head';
      $this->assertTrue($this->getRequest()->isHead());
      
      $this->server['REQUEST_METHOD'] = 'get';
      $this->assertTrue($this->getRequest()->isHead());
      
      $this->server['REQUEST_METHOD'] = 'post';
      $this->assertFalse($this->getRequest()->isHead());
   }
   
   public function testIsSsl()
   {
      $this->server['HTTPS'] = 'On';
      $this->assertTrue($this->getRequest()->isSsl());
      
      $this->server['HTTPS'] = '';
      $this->assertFalse($this->getRequest()->isSsl());
   }
   
   public function testGetProtocol()
   {
      $this->server['HTTPS'] = 'On';
      $this->assertEquals($this->getRequest()->getProtocol(), 'https://');
      
      $this->server['HTTPS'] = '';
      $this->assertEquals($this->getRequest()->getProtocol(), 'http://');
   }
   
   public function testGetHost()
   {
      $this->server['HTTP_HOST'] = 'example.com';
      $this->server['HTTP_X_FORWARDED_HOST'] = 'forwarded.example.com';
      $this->assertEquals($this->getRequest()->getHost(), 'forwarded.example.com');
      
      unset($this->server['HTTP_X_FORWARDED_HOST']);
      $this->assertEquals($this->getRequest()->getHost(), 'example.com');
   }
   
   public function testSetPortFromHost()
   {
      $this->server['HTTP_HOST'] = 'example.com:8080';
      
      $request = $this->getRequest();
      $request->getHost();
      $this->assertEquals($request->getPort(), 8080);
   }
   
   public function testGetPort()
   {
      $this->server['SERVER_PORT'] = '8080';
      $this->assertEquals($this->getRequest()->getPort(), 8080);
   }
   
   public function testGetStandardPort()
   {
      $this->server['HTTPS'] = 'On';
      $this->assertEquals($this->getRequest()->getStandardPort(), 443);
      
      $this->server['HTTPS'] = '';
      $this->assertEquals($this->getRequest()->getStandardPort(), 80);
   }
   
   public function testGetPortString()
   {
      $this->server['SERVER_PORT'] = '8080';
      $this->assertEquals($this->getRequest()->getPortString(), ':8080');
      
      $this->server['SERVER_PORT'] = '80';
      $this->assertEquals($this->getRequest()->getPortString(), '');
      
      $this->server['SERVER_PORT'] = '443';
      $this->assertEquals($this->getRequest()->getPortString(), '');
   }
   
   public function testGetHostWithPort()
   {
      $this->server['HTTP_HOST'] = 'example.com';
      $this->server['SERVER_PORT'] = '8080';
      $this->assertEquals($this->getRequest()->getHostWithPort(), 'example.com:8080');
      
      $this->server['SERVER_PORT'] = '80';
      $this->assertEquals($this->getRequest()->getHostWithPort(), 'example.com');
      
      $this->server['SERVER_PORT'] = '443';
      $this->assertEquals($this->getRequest()->getHostWithPort(), 'example.com');
   }
   
   public function testGetRemoteIp()
   {
      $this->server['REMOTE_ADDR'] = '127.0.0.3';
      $this->assertEquals($this->getRequest()->getRemoteIp(), '127.0.0.3');
      
      $this->server['HTTP_CLIENT_IP'] = '127.0.0.2';
      $this->assertEquals($this->getRequest()->getRemoteIp(), '127.0.0.2');
      
      unset($this->server['HTTP_CLIENT_IP']);
      $this->server['HTTP_X_FORWARDED_FOR'] = '127.0.0.1, 192.168.0.1, 192.168.0.2';
      $this->assertEquals($this->getRequest()->getRemoteIp(), '127.0.0.1');
   }
   
   public function testGetRemoteIpException()
   {
      $this->server['HTTP_CLIENT_IP'] = '127.0.0.2';
      $this->server['HTTP_X_FORWARDED_FOR'] = '127.0.0.1, 192.168.0.1, 192.168.0.2';
      
      try {
         $this->getRequest()->getRemoteIp();
      } catch (\Exception $e) {
         $this->assertInstanceOf('Starlight\\Component\\Http\\Exception\\IpSpoofingException', $e);
      }
   }
   
   public function testIsXmlHttpRequest()
   {
      $this->assertFalse($this->getRequest()->isXmlHttpRequest());
      
      $this->server['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
      $this->assertTrue($this->getRequesT()->isXmlHttpRequest());
   }
   
   public function testGetServer()
   {
      $this->server['HTTP_HOST'] = 'example.com';
      $this->server['SERVER_PORT'] = '8080';
      $this->assertEquals($this->getRequest()->getServer(), 'http://example.com:8080');
      
      $this->server['SERVER_PORT'] = '80';
      $this->assertEquals($this->getRequest()->getServer(), 'http://example.com');
      
      $this->server['HTTPS'] = 'On';
      $this->assertEquals($this->getRequest()->getServer(), 'https://example.com');
      
      $this->server['SERVER_PORT'] = '8080';
      $this->assertEquals($this->getRequest()->getServer(), 'https://example.com:8080');
   }
   
   public function testGetUri()
   {
      $this->server['REQUEST_URI'] = '/src/Starlight/Framework/bootloader.php';
      $this->assertEquals($this->getRequest()->getUri(), '/src/Starlight/Framework/bootloader.php');
      
      $this->server['REQUEST_URI'] = '';
      $this->assertEquals($this->getRequest()->getUri(), '/');
   }
   
   public function testGetRoute()
   {
      $this->server['REQUEST_URI'] = '/photos/1/users';
      $this->assertEquals($this->getRequest()->getRoute(), '/photos/1/users');
      
      $this->server['REQUEST_URI'] = '/photos/1/users?querystring=1';
      $this->assertEquals($this->getRequest()->getRoute(), '/photos/1/users');
      
      $this->server['REQUEST_URI'] = '';
      $this->assertEquals($this->getRequest()->getRoute(), '/');
   }
   
   public function testIsLocalStandard()
   {
      $this->server['REMOTE_ADDR'] = '127.0.0.1';
      $this->assertTrue($this->getRequest()->isLocal());
      
      $this->server['REMOTE_ADDR'] = '127.0.0.2';
      $this->assertFalse($this->getRequest()->isLocal());
   }
   
   public function testIsLocalCustom()
   {
      Request::$local_ips[] = '192.168.0.1';
      
      $this->server['REMOTE_ADDR'] = '127.0.0.1';
      $this->assertTrue($this->getRequest()->isLocal());
      
      $this->server['REMOTE_ADDR'] = '192.168.0.1';
      $this->assertTrue($this->getRequest()->isLocal());
      
      $this->server['REMOTE_ADDR'] = '127.0.0.2';
      $this->assertFalse($this->getRequest()->isLocal());
   }
   
   
   protected function getRequest()
   {
      return new Request($this->post, $this->get, $this->cookies, $this->files, $this->server);
   }
}
