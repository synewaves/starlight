<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Http;

/**
 * HTTP Request
 */
class Request
{
   public $post;
   public $get;
   public $cookies;
   public $files;
   public $server;
   public $headers;
   
   protected $host;
   protected $port;
   protected $remote_ip;
   
   
   /**
    * Constructor
    * @param array $post POST values
    * @param array $get GET values
    * @param array $cookies COOKIE values
    * @param array $files FILES values
    * @param array $server SERVER values
    */
   public function __construct(array $post = null, array $get = null, array $cookies = null, array $files = null, array $server = null)
   {
      $this->post    = new ParameterBucket($post ?: $_POST);
      $this->get     = new ParameterBucket($get ?: $_GET);
      $this->cookies = new ParameterBucket($cookies ?: $_COOKIE);
      $this->files   = new ParameterBucket($files ?: $_FILES);
      $this->server  = new ParameterBucket($server ?: $_SERVER);
      $this->headers = new HeaderBucket($this->initializeHeaders(), 'request');
   }
   
   /**
    * Clone
    */
   public function __clone()
   {
      $this->post    = clone $this->post;
      $this->get     = clone $this->get;
      $this->cookies = clone $this->cookies;
      $this->files   = clone $this->files;
      $this->server  = clone $this->server;
      $this->headers = clone $this->headers;
   }
   
   /**
    * Get a value from the request
    *
    * Order or precedence: GET, POST, COOKIE
    */
   public function get($key, $default = null)
   {
      return $this->get->get($key, $this->post->get($key, $this->cookies->get($key, $default)));
   }
   
   /**
    * Gets request method, lowercased
    *
    * Method can come from three places in this order:
    * <ol>
    *    <li>$_POST[_method]</li>
    *    <li>$_SERVER[X_HTTP_METHOD_OVERRIDE]</li>
    *    <li>$_SERVER[REQUEST_METHOD]</li>
    * </ol>
    * @return string request method
    */
   public function getMethod()
   {
      return strtolower($this->post->get('_method', $this->server->get('X_HTTP_METHOD_OVERRIDE', $this->server->get('REQUEST_METHOD'))));
   }
   
   /**
    * Is this a DELETE request
    * @return boolean is DELETE request
    */
   public function isDelete()
   {
      return $this->getMethod() == 'delete';
   }
   
   /**
    * Is this a GET request
    * @see head()
    * @return boolean is GET request
    */
   public function isGet()
   {
      return $this->isHead();
   }
   
   /**
    * Is this a POST request
    * @return boolean is POST request
    */
   public function isPost()
   {
      return $this->getMethod() == 'post';
   }
   
   /**
    * Is this a HEAD request
    *
    * Functionally equivalent to get()
    * @return boolean is HEAD request
    */
   public function isHead()
   {
      return $this->getMethod() == 'head' || $this->getMethod() == 'get';
   }
   
   /**
    * Is this a PUT request
    * @return boolean is PUT request
    */
   public function isPut()
   {
      return $this->getMethod() == 'put';
   }
   
   /**
    * Is this an SSL request
    * @return boolean is SSL request
    */
   public function isSsl()
   {
      return strtolower($this->server->get('HTTPS')) == 'on';
   }

   /**
    * Gets server protocol
    * @return string protocol
    */
   public function getProtocol()
   {
      return $this->isSsl() ? 'https://' : 'http://';
   }
   
   /**
    * Gets the server host
    * @return string host
    */
   public function getHost()
   {
      if ($this->host === null) {
         $host = $this->server->get('HTTP_X_FORWARDED_HOST', $this->server->get('HTTP_HOST'));
         $pos = strpos($host, ':');
         if ($pos !== false) {
            $this->host = substr($host, 0, $pos);
            $this->port = $this->port === null ? substr($host, $pos + 1) : null;
         } else {
            $this->host = $host;
         }
      }
      
      return $this->host;
   }
   
   /**
    * Gets the server port
    * @return integer port
    */
   public static function getPort()
   {
      if ($this->port === null) {
         $this->port = $this->server->get('SERVER_PORT', $this->getStandardPort());
      }
      
      return $this->port;
   }

   /**
    * Gets the standard port number for the current protocol
    * @return integer port (443, 80)
    */
   public static function getStandardPort()
   {
      return $this->isSsl() ? 443 : 80;
   }
   
   /**
    * Gets the port string with colon delimiter if not standard port
    * @return string port
    */
   public function getPortString()
   {
      return (in_array($this->getPort(), array(443, 80))) ? '' : ':' . $this->getPort();
   }
   
   /**
    * Gets the host with the port
    * @return string host with port
    */
   public function getHostWithPort()
   {
      return $this->getHost() . $this->getPortString();
   }
   
   /**
    * Gets the client's remote ip
    * @return string IP
    */
   public function getRemoteIp()
   {
      if ($this->remote_ip === null) {
         $remote_ips = null;
         if ($x_forward = $this->server->get('HTTP_X_FORWARDED_FOR')) {
            $remote_ips = array_map('trim', explode(',', $x_forward));
         }
      
         $client_ip = $this->server->get('HTTP_CLIENT_IP');
         if ($client_ip) {
            if (is_array($remote_ips) && !in_array($client_ip, $remote_ips)) {
               // don't know which came from the proxy, and which from the user
             throw new Exception\IpSpoofingException(sprintf("IP spoofing attack?!\nHTTP_CLIENT_IP=%s\nHTTP_X_FORWARDED_FOR=%s", $this->server->get('HTTP_CLIENT_IP'), $this->server->get('HTTP_X_FORWARDED_FOR')));
            }
         
            $this->remote_ip = $client_ip;
         } elseif (is_array($remote_ips)) {
            $this->remote_ip = $remote_ips[0];
         } else {
            $this->remote_ip = $this->server->get('REMOTE_ADDR');
         }
      }
      
      return $this->remote_ip;
   }
   
   /**
    * Check if the request is an XMLHttpRequest (AJAX)
    * @return boolean is XHR (AJAX) request
    */
   public function isXmlHttpRequest()
   {
      return (bool) preg_match('/XMLHttpRequest/i', $this->server->get('HTTP_X_REQUESTED_WITH'));
   }
   
   /**
    * Gets the full server url with protocol and port
    * @return string server path
    */
   public function getServer()
   {
      return $this->getProtocol() . $this->getHostWithPort();
   }
   
   /**
    * Initialize request headers for HeaderBucket
    * @return array headers
    */
   protected function initializeHeaders()
   {
      $headers = array();
      foreach ($this->server->all() as $key => $value) {
         if (strtolower(substr($key, 0, 5)) === 'http_') {
            $headers[substr($key, 5)] = $value;
         }
      }

      return $headers;
   }
};
