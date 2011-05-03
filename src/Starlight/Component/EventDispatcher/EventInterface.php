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
 * EventInterface
 */
interface EventInterface
{
   /**
    * Returns the event's subject
    * @return mixed subject
    */
   function getSubject();
   
   /**
    * Returns the event's name
    * @return string name
    */
   function getName();
   
   /**
    * Sets the event's processed flag to true
    * 
    * This method must be called by listeners after the listener has processed the event.
    * (This is only used when calling notifyUntil() in the event manager)
    */
   function setProcessed();
   
   /**
    * Returns whether the event has been processed by a listener or not
    * @see setProcessed()
    * @return boolean true if the event has been processed
    */
   function isProcessed();
   
   /**
    * Returns the event's parameters
    * @return array parameters
    */
   function all();
   
   /**
    * Returns true if the parameter exists
    * @param string $name The parameter name
    * @return boolean true if the parameter exists
    */
   function has($name);
   
   /**
    * Returns a parameter value
    * @param string $name The parameter name
    * @return mixed The parameter value
    * @throws \InvalidArgumentException When parameter doesn't exist
    */
   function get($name);
   
   /**
    * Sets a parameter
    * @param string $name The parameter name
    * @param mixed $value The parameter value
    */
   function set($name, $value);
}
