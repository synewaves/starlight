<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\StdLib;


/**
 * Generic storage bucket
 */
class StorageBucket implements \ArrayAccess, \IteratorAggregate
{
   /**
    * Bucket contents
    * @var array
    */
   protected $contents;
   
   
   /**
    * Constructor
    * @param array $contents contents
    */
   public function __construct(array $contents = array())
   {
      $this->replace($contents);
   }
   
   /**
    * Returns the contents
    * @return array contents
    */
   public function all()
   {
      return $this->contents;
   }
   
   /**
    * Returns the contents keys
    * @return array contents keys
    */
   public function keys()
   {
      return array_keys($this->contents);
   }
   
   /**
    * Replaces the current contents with a new set
    * @param array $contents contents
    * @return StorageBucket this instance
    */
   public function replace(array $contents = array())
   {
      $this->contents = $contents;
      
      return $this;
   }
   
   /**
    * Adds contents
    * @param array $contents contents
    * @return StorageBucket this instance
    */
   public function add(array $contents = array())
   {
      $this->contents = array_replace($this->contents, $contents);
      
      return $this;
   }
   
   /**
    * Returns content by name
    * @param string $key The key
    * @param mixed $default default value
    * @return mixed value
    */
   public function get($key, $default = null)
   {
      return array_key_exists($key, $this->contents) ? $this->contents[$key] : $default;
   }
   
   /**
    * Sets content by name
    * @param string $key The key
    * @param mixed $value value
    * @return StorageBucket this instance
    */
   public function set($key, $value)
   {
      $this->contents[$key] = $value;
      
      return $this;
   }
   
   /**
    * Returns true if the content is defined
    * @param string $key The key
    * @return boolean true if the contents exists, false otherwise
    */
   public function has($key)
   {
      return array_key_exists($key, $this->contents);
   }
   
   /**
    * Deletes a content
    * @param string $key key
    * @return StorageBucket this instance
    */
   public function delete($key)
   {
      if ($this->has($key)) {
         unset($this->contents[$key]);
      }
      
      return $this;
   }
   
   // --------------------------
   // ArrayAccess implementation
   // --------------------------
   
   public function offsetExists($offset)
   {
      return $this->has($offset);
   }
   
   public function offsetGet($offset)
   {
      return $this->get($offset);
   }
   
   public function offsetSet($offset, $value)
   {
      return $this->set($offset, $value);
   }
   
   public function offsetUnset($offset)
   {
      return $this->delete($offset);
   }
   
   // --------------------------------
   // IteratorAggregate implementation
   // --------------------------------
   
   public function getIterator()
   {
      return new \ArrayIterator($this->all());
   }
}
