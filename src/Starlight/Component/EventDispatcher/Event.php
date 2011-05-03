<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\EventDispatcher;

/**
 * Event
 */
class Event implements EventInterface
{
   /**
    * Is processed flag
    * @var boolean
    */
   protected $processed = false;
   
   /**
    * Subeject
    * @var mixed
    */
   protected $subject;
   
   /**
    * Unique idenfitier
    * @var string
    */
   protected $name;
   
   /**
    * Parameters
    * @var array
    */
   protected $parameters;
   
   
   /**
    * Constructor
    * @param mixed $subject The subject
    * @param string $name The event name
    * @param array $parameters An array of parameters
    */
   public function __construct($subject, $name, $parameters = array())
   {
      $this->subject = $subject;
      $this->name = $name;
      $this->parameters = $parameters;
   }
   
   /**
    * Returns the event's subject
    * @return mixed subject
    */
   public function getSubject()
   {
      return $this->subject;
   }
   
   /**
    * Returns the event's name
    * @return string name
    */
   public function getName()
   {
      return $this->name;
   }
   
   /**
    * Sets the event's processed flag to true
    * 
    * This method must be called by listeners after the listener has processed the event.
    * (This is only used when calling notifyUntil() in the event manager)
    */
   public function setProcessed()
   {
      $this->processed = true;
   }
   
   /**
    * Returns whether the event has been processed by a listener or not
    * @see setProcessed()
    * @return boolean true if the event has been processed
    */
   public function isProcessed()
   {
      return $this->processed;
   }
   
   /**
    * Returns the event's parameters
    * @return array parameters
    */
   public function all()
   {
      return $this->parameters;
   }
   
   /**
    * Returns true if the parameter exists
    * @param string $name The parameter name
    * @return boolean true if the parameter exists
    */
   public function has($name)
   {
      return array_key_exists($name, $this->parameters);
   }
   
   /**
    * Returns a parameter value
    * @param string $name The parameter name
    * @return mixed The parameter value
    * @throws \InvalidArgumentException When parameter doesn't exist
    */
   public function get($name)
   {
      if (!array_key_exists($name, $this->parameters)) {
         throw new \InvalidArgumentException(sprintf('The event "%s" doesn\'t have a "%s" parameter', $this->name, $name));
      }
   
      return $this->parameters[$name];
   }
   
   /**
    * Sets a parameter
    * @param string $name The parameter name
    * @param mixed $value The parameter value
    */
   public function set($name, $value)
   {
      $this->parameters[$name] = $value;
   }
}
