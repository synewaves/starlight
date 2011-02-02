<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Http\SessionStorage;

/**
 * Native Session Storage
 */
class NativeSessionStorage implements SessionStorageInterface
{
   static protected $regenerated = false;
   static protected $started = false;
   protected $options;

   /**
    * Constructor
    *
    * Available options:
    *
    *  * name:     The cookie name (_SESS by default)
    *  * id:       The session id (null by default)
    *  * lifetime: Cookie lifetime
    *  * path:     Cookie path
    *  * domain:   Cookie domain
    *  * secure:   Cookie secure
    *  * httponly: Cookie http only
    *
    * The default values for most options are those returned by the session_get_cookie_params() function
    * @param array $options An associative array of options
    */
   public function __construct(array $options = array())
   {
      $defaults = session_get_cookie_params();
      
      $this->options = array_merge(array(
         'name'     => '_SESS',
         'lifetime' => $defaults['lifetime'],
         'path'     => $defaults['path'],
         'domain'   => $defaults['domain'],
         'secure'   => $defaults['secure'],
         'httponly' => isset($defaults['httponly']) ? $defaults['httponly'] : false,
      ), $options);
   
      session_name($this->options['name']);
   }
   
   /**
    * Starts the session.
    */
   public function start()
   {
      if (self::$started) {
         return;
      }
   
      session_set_cookie_params(
         $this->options['lifetime'],
         $this->options['path'],
         $this->options['domain'],
         $this->options['secure'],
         $this->options['httponly']
      );
   
      // disable native cache limiter as this is managed by HeaderBag directly
      // session_cache_limiter(false);
   
      if (!ini_get('session.use_cookies') && $this->options['id'] && $this->options['id'] != session_id()) {
         session_id($this->options['id']);
      }
   
      session_start();
   
      self::$started = true;
   }
   
   /**
    * Returns the session ID
    * @return mixed The session ID
    * @throws \RuntimeException If the session was not started yet
    */
   public function id()
   {
      if (!self::$started) {
         throw new \RuntimeException('The session must be started before reading its ID');
      }
   
      return session_id();
   }
   
   /**
    * Reads data from this storage
    * The preferred format for a key is directory style so naming conflicts can be avoided.
    * @param string $key A unique key identifying your data
    * @return mixed Data associated with the key
    * @throws \RuntimeException If an error occurs while reading data from this storage
    */
   public function read($key, $default = null)
   {
      return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
   }
   
   /**
    * Removes data from this storage
    * The preferred format for a key is directory style so naming conflicts can be avoided.
    * @param string $key A unique key identifying your data
    * @return mixed Data associated with the key
    * @throws \RuntimeException If an error occurs while removing data from this storage
    */
   public function remove($key)
   {
      $rc = null;
      
      if (isset($_SESSION[$key])) {
         $rc = $_SESSION[$key];
         unset($_SESSION[$key]);
      }
   
      return $rc;
   }
   
   /**
    * Writes data to this storage
    * The preferred format for a key is directory style so naming conflicts can be avoided.
    * @param string $key A unique key identifying your data
    * @param mixed $data Data associated with your key
    * @throws \RuntimeException If an error occurs while writing to this storage
    */
   public function write($key, $data)
   {
      $_SESSION[$key] = $data;
   }
   
   /**
    * Regenerates id that represents this storage
    * @param Boolean $destroy Destroy session when regenerating?
    * @return Boolean True if session regenerated, false if error
    * @throws \RuntimeException If an error occurs while regenerating this storage
    */
   public function regenerate($destroy = false)
   {
      if (self::$regenerated) {
         return;
      }
   
      session_regenerate_id($destroy);
   
      self::$regenerated = true;
   }
}
