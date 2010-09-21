<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\Routing;


/**
 * Route
 */
class RouteParser
{
   /**
    * URL path separators (regex escaped)
    * '\' and '.'
    * @var string
    */
   protected static $default_separators = array('\/', '\.');
   
   public $path;
   public $regex;
   public $requirements;
   public $separators;
   public $names = array();
   
   /**
    *
    */
   public function parse($path, array $requirements = array(), array $separators = array())
   {
      $this->path = trim($path);
      $this->requirements = $requirements;
      $this->separators = count($separators) > 0 ? $separators : static::$default_separators;
      
      $this->regex = $this->path;
      $this->regex = str_replace(')', ')?', $this->regex);
      $this->regex = str_replace('/', '\\/', $this->regex);
      $this->regex = str_replace('.', '\\.', $this->regex);
      
      preg_match_all('/((\:|\\*)[a-z]+)/i', $this->regex, $matches, PREG_OFFSET_CAPTURE);
      if (isset($matches[0])) {
         $t_regex = '';
         $t_offset = 0;
         $separators = implode('', $this->separators);
         foreach ($matches[0] as $match) {
            list($key, $offset) = $match;
            
            $t_regex .= substr($this->regex, $t_offset, $offset - $t_offset);
            
            $identifier = substr($key, 0, 1);
            $name = substr($key, 1);
            
            $regex = '';
            if (isset($this->requirements[$name])) {
               $regex = $this->requirements[$name];
            } elseif ($identifier == '*') {
               $regex = '.+';
            } else {
               $regex = '[^' . $separators . ']+';
            }
            
            $t_regex .= '(?<' . $name . '>' . $regex . ')';
            $t_offset = $offset + strlen($key);
            
            $this->names[] = $name;
         }
         
         $t_regex .= substr($this->regex, $t_offset);
         $this->regex = $t_regex;
      }
      
      return '/\\A' . $this->regex . '\\Z/';
   }
};
