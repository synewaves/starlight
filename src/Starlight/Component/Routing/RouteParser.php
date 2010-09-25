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
    * URL path separators
    * '\' and '.'
    * @var string
    */
   protected static $default_separators = array('/', '.');
   
   public $path;
   public $regex;
   public $requirements;
   public $separators;
   public $names = array();
   protected $quoted_separators;
   
   
   /**
    *
    */
   public function parse($path, array $requirements = array(), array $separators = array())
   {
      $this->regex = '';
      $this->path = trim($path);
      $this->requirements = $requirements;
      $this->separators = count($separators) > 0 ? $separators : static::$default_separators;
      $this->quoted_separators = $this->quote(implode('', $this->separators));
      
      $this->regex = $this->reduce();
      
      return $this->regex;
   }
   
   /**
    *
    */
   protected function reduce()
   {
      $balance = 0;
      $prev = $next = $literal = '';
      $length = strlen($this->path);
      $segments = array();
      
      for ($i=0; $i<$length; $i++)
      {
         $curr = $this->path[$i];
         $next = isset($this->path[$i + 1]) ? $this->path[$i + 1] : '';
         $prev = ($i > 0) ? $this->path[$i - 1] : '';
         
         if ($curr == '\\') {
            if (preg_match('/[\(|\)|\:|\*]/', $next)) {
               // escaped special character
               $i++;
               $literal .= $next;
            } else {
               // literal
               $literal .= $curr;
            }
         } else {
            if (preg_match('/[\(|\)]/', $curr)) {
               // parenthesis
               if ($literal != '') {
                  $segments[] = $this->quote($literal);
                  $literal = '';  
               }
               
               if ($curr == '(') {
                  $balance += 1;
                  $segments[] = '(?:';
               } else {
                  $balance -= 1;
                  $segments[] = ')?';
               }
            } elseif (preg_match('/[\:|\*]/', $curr)) {
               // identifier
               preg_match('/(\:|\*){1}[a-z\_]+/i', $this->path, $matches, PREG_OFFSET_CAPTURE, $i);
               if (isset($matches[0]) && count($matches[0]) > 0) {
                  if ($literal != '') {
                     $segments[] = $this->quote($literal);
                     $literal = '';
                  }
                  
                  $id = substr($matches[0][0], 0, 1);
                  $key = substr($matches[0][0], 1);
                  
                  if (isset($this->requirements[$key])) {
                     // custom requirements
                     $regex = $this->requirements[$key];
                  } elseif ($id == '*') {
                     // glob
                     $regex = '.+';
                  } else {
                     // standard segments
                     $regex = '[^' . $this->quoted_separators . ']+';
                  }
                  
                  $segments[] = '(?<' . $key . '>' . $regex . ')';
                  $this->names[] = $key;
                  $i += strlen($key);
               } else {
                  // invalid identifier:
                  $literal .= $curr;
               }
            } else {
               // just normal text
               $literal .= $curr;
            }
         }
      }
      
      if ($literal != '') {
         $segments[] = $this->quote($literal);
      }
      
      if ($balance != 0) {
         throw new \InvalidArgumentException('Optional segment parenthesis are unbalanced.');
      }
      
      return '/\A' . implode('', $segments) . '\Z/';
   }
   
   /**
    *
    */
   protected function quote($string)
   {
      return preg_quote($string, '/');
   }
}
