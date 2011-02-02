<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\EventDispatcher;

/**
 * EventDispatcherInterface describes an event dispatcher class
 * @see http://developer.apple.com/documentation/Cocoa/Conceptual/Notifications/index.html Apple's Cocoa framework
 */
interface EventDispatcherInterface
{
   /**
    * Connects a listener to a given event name
    * Listeners with a higher priority are executed first
    * @param string $name An event name
    * @param mixed $listener A PHP callable
    * @param integer $priority The priority (between -10 and 10 -- defaults to 0)
    */
   function connect($name, $listener, $priority = 0);
   
   /**
    * Disconnects one, or all listeners for the given event name
    * @param string $name An event name
    * @param mixed|null $listener The listener to remove, or null to remove all
    */
   function disconnect($name, $listener = null);
   
   /**
    * Notifies all listeners of a given event
    * @param EventInterface $event An EventInterface instance
    */
   function notify(EventInterface $event);
   
   /**
    * Notifies all listeners of a given event until one processes the event
    * A listener tells the dispatcher that it has processed the event by calling the setProcessed() method on it.
    * It can then return a value that will be fowarded to the caller.
    * @param EventInterface $event An EventInterface instance
    * @return mixed The returned value of the listener that processed the event
    */
   function notifyUntil(EventInterface $event);
   
   /**
    * Filters a value by calling all listeners of a given event
    * @param EventInterface $event An EventInterface instance
    * @param mixed $value The value to be filtered
    * @return mixed The filtered value
    */
   function filter(EventInterface $event, $value);
   
   /**
    * Returns true if the given event name has some listeners
    * @param string $name The event name
    * @return Boolean true if some listeners are connected, false otherwise
    */
   function hasListeners($name);
   
   /**
    * Returns all listeners associated with a given event name
    * @param string $name The event name
    * @return array An array of listeners
    */
   function getListeners($name);
}
