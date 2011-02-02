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
 * HTTP Cookie
 */
class Cookie
{
   protected $name;
   protected $value;
   protected $options;
   
   /**
    * Constructor
    *
    * Available options:
    *
    *  * expires:   Expiration date (string, int or \DateTime) (default: 0)
    *  * path:      Path (default: null)
    *  * domain:    Domain (default: null)
    *  * secure:    Secure cookie (default: false)
    *  * http_only: HTTP only (default: true)
    *
    * @param string $name name
    * @param mixed $value value
    * @param array $options options hash
    */
   public function __construct($name, $value, array $options = array())
   {
      $options = array_merge(array(
         'expires' => 0,
         'path' => null,
         'domain' => null,
         'secure' => false,
         'http_only' => true,
      ), $options);
      
      $this->setName($name)
           ->setValue($value)
           ->setExpires($options['expires'])
           ->setPath($options['path'])
           ->setDomain($options['domain'])
           ->setSecure($options['secure'])
           ->setHttpOnly($options['http_only']);
   }
   
   /**
    * Gets the name
    * @return string name
    */
   public function getName()
   {
      return $this->name;
   }
   
   /**
    * Sets the name
    * @param string $name name
    * @throws \InvalidArgumentException if invalid name
    * @return Cookie this instance
    */
   public function setName($name)
   {
      // from PHP source code
      if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
         throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
      }
      
      if (empty($name)) {
         throw new \InvalidArgumentException('The cookie name cannot be empty');
      }
      
      $this->name = $name;
      return $this;
   }
   
   /**
    * Gets the value
    * @return string value
    */
   public function getValue()
   {
      return $this->value;
   }
   
   /**
    * Sets the value
    * @param string $value value
    * @throws \InvalidArgumentException if invalid value
    * @return Cookie this instance
    */
   public function setValue($value)
   {
      if (preg_match("/[,; \t\r\n\013\014]/", $value)) {
         throw new \InvalidArgumentException(sprintf('The cookie value "%s" contains invalid characters.', $name));
      }
      
      $this->value = $value;
      return $this;
   }
   
   /**
    * Gets the expiration date
    * @return int expiration date
    */
   public function getExpires()
   {
      return $this->options['expire'];
   }
   
   /**
    * Sets the expiration date
    * @param string|int|\DateTime $expires expiration date
    * @return Cookie this instance
    */
   public function setExpires($expires)
   {
      if (is_numeric($expires)) {
         $expires = (int) $expires;
      } elseif ($expires instanceof \DateTime) {
         $expires = $expires->getTimestamp();
      } else {
         $o_expires = $expires;
         $expires = strtotime($expires);
         if ($expires === false || $expires == -1) {
            throw new \InvalidArgumentException(sprintf('The "expires" cookie parameter is not valid: "%s".', $o_expires));
         }
      }
      
      $this->options['expires'] = $expires;
      return $this;
   }
   
   /**
    * Gets the path
    * @return string path
    */
   public function getPath()
   {
      return $this->options['path'];
   }
   
   /**
    * Sets the path
    * @param string $path path
    * @return Cookie this instance
    */
   public function setPath($path)
   {
      $this->options['path'] = $path;
      return $this;
   }
   
   /**
    * Gets the domain
    * @return string domain
    */
   public function getDomain()
   {
      return $this->options['domain'];
   }
   
   /**
    * Sets the domain
    * @param string $domain domain
    * @return Cookie this instance
    */
   public function setDomain($domain)
   {
      $this->options['domain'] = $domain;
      return $this;
   }
   
   /**
    * Gets the secure flag
    * @return boolean secure flag
    */
   public function getSecure()
   {
      return (bool) $this->options['secure'];
   }
   
   /**
    * Sets the secure flag
    * @param boolean $secure secure flag
    * @return Cookie this instance
    */
   public function setSecure($secure)
   {
      $this->options['secure'] = (bool) $secure;
      return $this;
   }
   
   /**
    * Gets the http only flag
    * @return boolean http only
    */
   public function getHttpOnly()
   {
      return (bool) $this->options['http_only'];
   }
   
   /**
    * Sets the http only flag
    * @param boolean $http_only http only flag
    * @return Cookie this instance
    */
   public function setHttpOnly($http_only)
   {
      $this->options['http_only'] = (bool) $http_only;
      return $this;
   }
   
   /**
    * Is this cookie going to be cleared
    * @return boolean is cleared
    */
   public function isCleared()
   {
      return $this->options['expires'] < time();
   }
   
   /**
    * Get formatted cookie header value
    * @return string formatted value
    */
   public function __toString()
   {
      $cookie = sprintf('%s=%s', $this->name, urlencode($this->value));
      
      if ($options['expires'] !== null) {
         $date = \DateTime::createFromFormat('U', $options['expires'], new \DateTimeZone('UTC'));
         $cookie .= '; expires=' . substr($date->format('D, d-M-Y H:i:s T'), 0, -5);
      }
      
      if ($options['domain']) {
         $cookie .= '; domain=' . $options['domain'];
      }
      
      if ($options['path'] && $options['path'] !== '/') {
         $cookie .= '; path=' . $options['path'];
      }
      
      if ($options['secure']) {
         $cookie .= '; secure';
      }
      
      if ($options['http_only']) {
         $cookie .= '; httponly';
      }
      
      return $cookie;
   }
}
