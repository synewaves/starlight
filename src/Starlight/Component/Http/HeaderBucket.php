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
 * Wrapper class for request/response headers
 */
class HeaderBucket
{
   /**
    * Headers
    * @var array
    */
   protected $headers;
   
   // /**
   //  * Wrapper class for request/response headers
   //  */
   // protected $cache_control;
   
   /**
    * Type (request, response)
    * @var string
    */
   protected $type;


   /**
    * Constructor
    * @param array $headers An array of HTTP headers
    * @param string $type The type (null, request, or response)
    */
   public function __construct(array $headers = array(), $type = null)
   {
      $this->replace($headers);

      if ($type !== null && !in_array($type, array('request', 'response'))) {
         throw new \InvalidArgumentException(sprintf('The "%s" type is not supported by the HeaderBucket.', $type));
      }
      $this->type = $type;
   }

   /**
    * Returns the headers
    * @return array An array of headers
    */
   public function all()
   {
      return $this->headers;
   }

   /**
    * Returns the parameter keys
    * @return array An array of parameter keys
    */
   public function keys()
   {
      return array_keys($this->headers);
   }

   /**
    * Replaces the current HTTP headers by a new set
    * @param array $headers An array of HTTP headers
    */
   public function replace(array $headers = array())
   {
      $this->cache_control = null;
      $this->headers = array();
      foreach ($headers as $key => $values) {
         $this->set($key, $values);
      }
   }

   /**
    * Returns a header value by name
    * @param string $key The header name
    * @param Boolean $first Whether to return the first value or all header values
    * @return string|array The first header value if $first is true, an array of values otherwise
    */
   public function get($key, $first = true)
   {
      $key = self::normalizeHeaderName($key);

      if (!array_key_exists($key, $this->headers)) {
         return $first ? null : array();
      }

      if ($first) {
         return count($this->headers[$key]) ? $this->headers[$key][0] : '';
      } else {
         return $this->headers[$key];
      }
   }

   /**
    * Sets a header by name
    * @param string $key The key
    * @param string|array $values The value or an array of values
    * @param boolean $replace Whether to replace the actual value of not (true by default)
    */
   public function set($key, $values, $replace = true)
   {
      $key = self::normalizeHeaderName($key);

      if (!is_array($values)) {
         $values = array($values);
      }

      if ($replace === true || !isset($this->headers[$key])) {
         $this->headers[$key] = $values;
      } else {
         $this->headers[$key] = array_merge($this->headers[$key], $values);
      }
   }

   /**
    * Returns true if the HTTP header is defined
    * @param string $key The HTTP header
    * @return Boolean true if the parameter exists, false otherwise
    */
   public function has($key)
   {
      return array_key_exists(self::normalizeHeaderName($key), $this->headers);
   }

   /**
    * Returns true if the given HTTP header contains the given value
    * @param string $key   The HTTP header name
    * @param string $value The HTTP value
    * @return Boolean true if the value is contained in the header, false otherwise
    */
   public function contains($key, $value)
   {
      return in_array($value, $this->get($key, false));
   }

   /**
    * Deletes a header
    * @param string $key The HTTP header name
    */
   public function delete($key)
   {
      unset($this->headers[self::normalizeHeaderName($key)]);
   }

   // /**
   // * Returns an instance able to manage the Cache-Control header.
   // *
   // * @return CacheControl A CacheControl instance
   // */
   // public function getCacheControl()
   // {
   //    if (null === $this->cacheControl) {
   //       $this->cacheControl = new CacheControl($this, $this->get('Cache-Control'), $this->type);
   //    }
   // 
   //    return $this->cacheControl;
   // }
   
   /**
    * Set cookie variable
    *
    * Available options:
    *
    * <ul>
    *    <li><b>expires</b> <i>(integer)</i>: cookie expiration time (default: 0 [end of session])</li>
    *    <li><b>path</b> <i>(string)</i>: path cookie is valid for (default: '')</li>
    *    <li><b>domain</b> <i>(string)</i>: domain cookie is valid for (default: '')</li>
    *    <li><b>secure/b> <i>(boolean)</i>: should cookie only be used on a secure connection (default: false)</li>
    *    <li><b>http_only</b> <i>(string)</i>: cookie only valid over http? [not supported by all browsers] (default: true)</li>
    *    <li><b>encode</b> <i>(boolean)</i>: urlencode cookie value (default: true)</li>
    * </ul>
    * @param string $key cookie key
    * @param mixed $value value
    * @param array $options options hash (see above)
    */
   public function setCookie($name, $value, $options = array())
   {
      $default_options = array(
         'expires' => null,
         'path' => '',
         'domain' => '',
         'secure' => false,
         'http_only' => true,
      );
      $options = array_merge($default_options, $options);
      
      if (preg_match("/[=,; \t\r\n\013\014]/", $key)) {
         throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $key));
      }
   
      if (preg_match("/[,; \t\r\n\013\014]/", $value)) {
         throw new \InvalidArgumentException(sprintf('The cookie value "%s" contains invalid characters.', $value));
      }
   
      if (!$name) {
         throw new \InvalidArgumentException('The cookie name cannot be empty');
      }
      
      $cookie = sprintf('%s=%s', $name, urlencode($value));
      
      if ($this->type == 'request') {
         $this->set('Cookie', $cookie);
         return;
      }
      
      if ($options['expires'] !== null) {
         if (is_numeric($options['expires'])) {
            $options['expires'] = (int) $options['expires'];
         } elseif ($options['expires'] instanceof \DateTime) {
            $options['expires'] = $options['expires']->getTimestamp();
         } else {
            $expires = strtotime($options['expires']);
            if ($expires === false || $expires == -1) {
               throw new \InvalidArgumentException(sprintf('The "expires" cookie parameter is not valid.', $expires));
            }
         }
         
         $cookie .= '; expires=' . substr(\DateTime::createFromFormat('U', $expires, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'), 0, -5);
      }
      
      if ($options['domain']) {
         $cookie .= '; domain=' . $options['domain'];
      }
      
      if ($options['path'] !== '/') {
         $cookie .= '; path=' . $path;
      }
      
      if ($options['secure']) {
         $cookie .= '; secure';
      }
      
      if ($options['httponly']) {
         $cookie .= '; httponly';
      }
      
      $this->set('Set-Cookie', $cookie, false); 
   }
   
   /**
    * Expire a cookie variable
    * @param string $key cookie key
    */
   public function expireCookie($key)
   {
      if ($this->type == 'request') {
         return;
      }

      if (preg_match("/[=,; \t\r\n\013\014]/", $key)) {
         throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $key));
      }
      
      if (!$name) {
         throw new \InvalidArgumentException('The cookie name cannot be empty');
      }
      
      $cookie = sprintf('%s=; expires=', $name, substr(\DateTime::createFromFormat('U', time() - 3600, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'), 0, -5));
      
      $this->set('Set-Cookie', $cookie, false);
   }

   /**
    * Normalizes an HTTP header name
    * @param string $key The HTTP header name
    * @return string The normalized HTTP header name
    */
   static public function normalizeHeaderName($key)
   {
      return strtr(strtolower($key), '_', '-');
   }
};
