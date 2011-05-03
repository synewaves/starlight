<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Http;
use Starlight\Component\StdLib\StorageBucket;


/**
 * Wrapper class for request/response headers
 */
class HeaderBucket extends StorageBucket
{
   protected $cookies = array();
   protected $cache_control = array();
   
   /**
    * Returns a header value by name
    * @param string $key The header name
    * @param mixed $default default value if none found
    * @param boolean $first Whether to return the first value or all header values
    * @return string|array The first header value if $first is true, an array of values otherwise
    */
   public function get($key, $default = null, $first = true)
   {
      $key = strtr(strtolower($key), '_', '-');
      
      if (!array_key_exists($key, $this->contents)) {
         if ($default === null) {
            return $first ? null : array();
         } else {
            return $first ? $default : array($default);
         }
      }

      if ($first) {
         return count($this->contents[$key]) ? $this->contents[$key][0] : $default;
      } else {
         return $this->contents[$key];
      }
   }

   /**
    * Sets a header by name
    * @param string $key The key
    * @param string|array $values The value or an array of values
    * @param boolean $replace Whether to replace the actual value of not (true by default)
    * @return HeaderBucket this instance
    */
   public function set($key, $values, $replace = true)
   {
      $key = strtr(strtolower($key), '_', '-');
   
      if (!is_array($values)) {
         $values = array($values);
      }
   
      if ($replace === true || !isset($this->contents[$key])) {
         $this->contents[$key] = $values;
      } else {
         $this->contents[$key] = array_merge($this->contents[$key], $values);
      }
      
      if ($key === 'cache-control') {
         $this->cache_control = $this->parseCacheControl($values[0]);
      }
      
      return $this;
   }
   
   /**
    * Replaces the current contents with a new set
    * @param array $contents contents
    * @return HeaderBucket this instance
    */
   public function replace(array $contents = array())
   {
      $this->contents = array();
      $this->add($contents);
      
      return $this;
   }
   
   /**
    * Adds contents
    * @param array $contents contents
    * @return HeaderBucket this instance
    */
   public function add(array $contents = array())
   {
      foreach ($contents as $key => $value) {
         $this->set($key, $value);
      }
      
      return $this;
   }
   
   /**
    * Returns true if the content is defined
    * @param string $key The key
    * @return boolean true if the contents exists, false otherwise
    */
   public function has($key)
   {
      return parent::has(strtr(strtolower($key), '_', '-'));
   }
   
   /**
    * Returns true if the given HTTP header contains the given value
    * @param string $key The HTTP header name
    * @param string $value The HTTP value
    * @return Boolean true if the value is contained in the header, false otherwise
    */
   public function contains($key, $value)
   {
      return in_array($value, $this->get($key, null, false));
   }
   
   /**
    * Deletes a header
    * @param string $key The HTTP header name
    * @return HeaderBucket this instance
    */
   public function delete($key)
   {
      $key = strtr(strtolower($key), '_', '-');
      
      parent::delete($key);
      
      if ($key == 'cache-control') {
         $this->cache_control = array();
      }
      
      return $this;
   }
   
   /**
    * Sets a cookie
    * @param Cookie $cookie
    * @throws \InvalidArgumentException When the cookie expire parameter is not valid
    * @return HeaderBucket this instance
    */
   public function setCookie(Cookie $cookie)
   {
      $this->cookies[$cookie->getName()] = $cookie;
      return $this;
   }

   /**
    * Removes a cookie from the array, but does not unset it in the browser
    * @param string $name
    * @return HeaderBucket this instance
    */
   public function removeCookie($name)
   {
      unset($this->cookies[$name]);
      return $this;
   }

   /**
    * Whether the array contains any cookie with this name
    * @param string $name
    * @return boolean cookie exists
    */
   public function hasCookie($name)
   {
      return isset($this->cookies[$name]);
   }

   /**
    * Returns a cookie
    * @param string $name
    * @throws \InvalidArgumentException if cookie not found
    * @return Cookie cookie
    */
   public function getCookie($name)
   {
      if (!$this->hasCookie($name)) {
         throw new \InvalidArgumentException(sprintf('There is no cookie with name "%s".', $name));
      }
   
      return $this->cookies[$name];
   }

   /**
    * Returns an array with all cookies
    * @return array all cookies
    */
   public function getCookies()
   {
      return $this->cookies;
   }

   /**
    * Returns the HTTP header value converted to a date
    * @param string $key The parameter key
    * @param \DateTime $default The default value
    * @throws \RuntimeException if cannot parse header
    * @return \DateTime The filtered value
    */
   public function getDate($key, \DateTime $default = null)
   {
      if (($value = $this->get($key)) === null) {
         return $default;
      }
   
      if (($date = \DateTime::createFromFormat(DATE_RFC2822, $value)) === false) {
         throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
      }
   
      return $date;
   }

   /**
    * Adds the cache control headers
    * @param string $key key
    * @param mixed $value value
    * @return HeaderBag this instance
    */
   public function addCacheControlDirective($key, $value = true)
   {
      $this->cache_control[$key] = $value;
      $this->set('Cache-Control', $this->getCacheControlHeader());
      
      return $this;
   }

   /**
    * Is there a cache control directive present
    * @param string $key key
    * @return boolean is present
    */
   public function hasCacheControlDirective($key)
   {
      return array_key_exists($key, $this->cache_control);
   }
   
   /**
    * Gets the cache control directive header
    * @param string $key key
    * @return string cache control header
    */
   public function getCacheControlDirective($key)
   {
      return array_key_exists($key, $this->cache_control) ? $this->cache_control[$key] : null;
   }

   /**
    * Removes the cache control directive header
    * @param string $key key
    * @return HeaderBag this instance
    */
   public function removeCacheControlDirective($key)
   {
      unset($this->cache_control[$key]);
      $this->set('Cache-Control', $this->getCacheControlHeader());
      
      return $this;
   }

   /**
    * Generates the cache control header value
    * @return string header value
    */
   protected function getCacheControlHeader()
   {
      $parts = array();
      ksort($this->cache_control);
      foreach ($this->cache_control as $key => $value) {
         if ($value === true) {
            $parts[] = $key;
         } else {
            if (preg_match('/[^a-zA-Z0-9._-]/', $value)) {
               $value = '"' . $value . '"';
            }
   
            $parts[] = "$key=$value";
         }
      }
   
      return implode(', ', $parts);
   }

   /**
    * Parses a Cache-Control HTTP header
    * @param string $header The value of the Cache-Control HTTP header
    * @return array An array representing the attribute values
    */
   protected function parseCacheControl($header)
   {
      $cache_control = array();
      preg_match_all('/([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?/', $header, $matches, PREG_SET_ORDER);
      foreach ($matches as $match) {
         $cache_control[strtolower($match[1])] = isset($match[2]) && $match[2] ? $match[2] : (isset($match[3]) ? $match[3] : true);
      }
   
      return $cache_control;
   }
}
