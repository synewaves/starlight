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
use Starlight\Component\Http\SessionStorage;

/**
 * Session
 */
class Session implements \Serializable
{
   const FLASH_KEY = '_flash';
   const SAVE_KEY = '_starlight';
   
   protected $storage;
   protected $options = array();
   protected $attributes = array();
   protected $old_flash_data = array();
   protected $is_started = false;
   
   
   /**
    * Constructor
    */
   public function __construct(SessionStorageInterface $storage, array $options = array())
   {
      $this->storage = $storage;
      $this->options = $options;
      $this->attributes = array(self::FLASH_KEY => array());
      $this->is_started = false;  
   }
   
   /**
    * Destructor
    */
   public function __destruct()
   {
      $this->save();
   }
   
   /**
    * Serialize
    * @return string serialized data
    */
   public function serialize()
   {
      return serialize(array($this->storage, $this->options));
   }
   
   /**
    * Unserialize
    * @param string $serialized serialized data
    */
   public function unserialize($serialized)
   {
      list($this->storage, $this->options) = unserialize($serialized);
      $this->attributes = array();
      $this->is_started = false;
   }
   
   /**
    * Save session
    */
   public function save()
   {
      if ($this->isStarted()) {
         if (isset($this->attributes[self::FLASH_KEY])) {
            $this->attributes[self::FLASH_KEY] = array_diff_key($this->attributes[self::FLASH_KEY], $this->old_flash_data);
         }
         $this->storage->write(self::SAVE_KEY, $this->attributes);
      }
   }
   
   /**
    * Start session and storage engine
    */
   public function start()
   {
      if ($this->isStarted()) {
         return;
      }
      
      $this->storage->start();
      $this->attributes = $this->storage->read(self::SAVE_KEY);
      
      if (!isset($this->attributes[self::FLASH_KEY])) {
         $this->attributes[self::FLASH_KEY] = array();
      }
      
      $this->old_flash_data = array_flip(array_keys($this->attributes[self::FLASH_KEY]));
      $this->is_started = true;
   }
   
   /**
    * Check if an attribute exists
    * @param string $name attribute name
    * @return boolean attribute exists
    */
   public function has($name)
   {
      return array_key_exists($name, $this->attributes);
   }
   
   /**
    * Gets an attribute's value
    * @param string $name attribute's name
    * @param mixed $default default value if not found
    * @return mixed
    */
   public function get($name, $default = null)
   {
      return $this->has($name) ? $this->attributes[$name] : $default;
   }
   
   /**
    * Set an attribute's value
    * @param string $name attribute name
    * @param mixed $value attribute value
    */
   public function set($name, $value)
   {
      if (!$this->isStarted()) {
         $this->start();
      }
      
      $this->attributes[$name] = $value;
   }
   
   /**
    * Delete an attribute
    * @param string $name attribute name
    */
   public function delete($name)
   {
      if (!$this->isStarted()) {
         $this->start();
      }
      
      if ($this->has($name)) {
         unset($this->attributes[$name]);
      }
   }
   
   /**
    * Clear all attributes
    */
   public function clear()
   {
      if (!$this->isStarted()) {
         $this->start();
      }
      
      $this->attributes = array();
      $this->clearFlashes();
   }
   
   /**
    * Invalidates the session
    */
   public function invalidate()
   {
      $this->clear();
      $this->storage->regenerate();
   }
   
   /**
    * Migrates the session to a new session id, keeping existing attributes
    */
   public function migrate()
   {
      $this->storage->regenerate();
   }
   
   /**
    * Gets the session id
    * @return string id
    */
   public function id()
   {
      return $this->storage->id();
   }
   
   /**
    * Gets all flash values
    * @return array flash values
    */
   public function getFlashes()
   {
      return $this->attributes[self::FLASH_KEY];
   }

   /**
    * Sets all flash values at once
    * @param array $values flash values
    */
   public function setFlashes($values)
   {
      if (!$this->isStarted()) {
         $this->start();
      }
      
      $this->attributes[self::FLASH_KEY] = $values;
   }
   
   /**
    * Clears all flash data
    */
   public function clearFlashes()
   {
      $this->attributes[self::FLASH_KEY] = array();
   }
   
   /**
    * Gets a flash value
    * @param string $name flash name
    * @param mixed $default default value if missing
    * @return mixed value
    */
   public function getFlash($name, $default = null)
   {
      return $this->hasFlash($name) ? $this->attributes[self::FLASH_KEY][$name] : $default;
   }
   
   /**
    * Sets a flash value
    * @param string $name flash name
    * @param mixed $value value
    */
   public function setFlash($name, $value)
   {
      if (!$this->isStarted()) {
         $this->start();
      }
      
      $this->attributes[self::FLASH_KEY][$name] = $value;
   }
   
   /**
    * Checks if a flash value exists
    * @param string $name flash name
    * @return boolean exists
    */
   public function hasFlash($name)
   {
      return array_key_exists($name, $this->attributes[self::FLASH_KEY]);
   }
   
   /**
    * Deletes a flash value
    * @param string $name flash name
    */
   public function deleteFlash($name)
   {
      if ($this->hasFlash($name)) {
         unset($this->attributes[self::FLASH_KEY][$name]);
      }
   }
   
   /**
    * Is the current session started
    * @return boolean session started
    */
   protected function isStarted()
   {
      return $this->is_started;
   }
}
