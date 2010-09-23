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
      
      $this->segments = $this->reduce($this->path);
      $this->regex = '/\A' . $this->expand($this->segments) . '\Z/';
      
      return $this->regex;
   }
   
   protected function reduce($path)
   {
      $segments = array();
      
      $start = 0;
      $length = strlen($path);
      
      $open = strpos($path, '(');
      $close = $last_close = $last_open = -1;
      
      // if (($open !== false && $close_test === false) || ($open === false && $close_test !== false)) {
      //    // something is unbalanced:
      //    throw new \InvalidArgumentException('Optional segment parenthesis are unbalanced.');
      // }
      
      // if ($open === false) {
      //    // check for unbalanced-ness:
      //    $c = $this->findMatchingParen(0, $path);
      //    if ($close !== false) {
      //       throw new \InvalidArgumentException('Optional segment parenthesis are unbalanced.');
      //    }
      //    
      //    $segments[] = $path;
      // } elseif ($start != $open) {
      //    $segments[] = substr($path, $start, $open);
      // }
      
      // while ($open !== false && $open <= $end) {
      //    if (($close = $this->findMatchingParen($open, $path)) !== false) {
      //       $segment = array(
      //          'content_before' => '',
      //          'content_after' => '',
      //          'children' => array(),
      //       );
      //       
      //       $segment_content = substr($path, $open + 1, $close - $open - 1);
      //       $segment_inner_paren_open = strpos($segment_content, '(', 0);
      //       if ($segment_inner_paren_open !== false) {
      //          $segment_inner_paren_close = $this->findMatchingParen($segment_inner_paren_open, $segment_content);
      //          if ($segment_inner_paren_close !== false) {
      //             $segment['content_before'] = substr($segment_content, 0, $segment_inner_paren_open);
      //             $segment['content_after'] = substr($segment_content, $segment_inner_paren_close + 1);
      //             $segment['children'] = $this->reduce(substr($segment_content, $segment_inner_paren_open, strlen($segment_content) - $segment_inner_paren_close - 1));
      //          } else {
      //             throw new \InvalidArgumentException('Optional segment parenthesis are unbalanced.');
      //          }
      //       } else {
      //          $segment['content_before'] = $segment_content;
      //       }
      //       
      //       $segments[] = $segment;
      //       $last_open = $open;
      //       $last_close = $close;
      //       $open = strpos($path, '(', $close + 1);
      //    } else {
      //       throw new \InvalidArgumentException('Optional segment parenthesis are unbalanced.');
      //    }
      // }

      return $segments;
   }
   
   
   protected function findMatchingParen($open, $path)
   {
      $close = false;
      $balance = -1;
      $len = strlen($path);
      
      for ($i=$open+1; $i<$len; $i++) {
         $char = $path{$i};
         if ('(' == $char) {
            $balance -= 1;
         }
         if (')' == $char) {
            $balance += 1;
         }
         if (0 == $balance) {
            $close = $i;
            $i = $len;
         }
      }
      
      return $close;
   }
   
   
   protected function expand($segments)
   {
      dump($this->path);
      dump($segments); die;
      
      $regex = '';
      
      foreach ($segments as $segment) {
         if (is_array($segment)) {
            $regex .= '(?:';
            $regex .= $this->parseSegmentPart($segment['content_before']);
            $regex .= $this->expand($segment['children']);
            $regex .= $this->parseSegmentPart($segment['content_after']);
            $regex .= ')?';
         } else {
            $regex .= $this->parseSegmentPart($segment);
         }
      }
      
      return $regex;
   }

   
   protected function parseSegmentPart($path)
   {
      $parsed = '';
      
      preg_match_all('/((\:|\*){1}[a-z\_]+)/i', $path, $matches, PREG_OFFSET_CAPTURE);
      if (isset($matches[0]) && count($matches[0]) > 0) {
         
         $t_regex = '';
         $t_offset = 0;
      
         foreach ($matches[0] as $segment) {
            list($key, $offset) = $segment;
            
            if (($offset > 0 && substr($path, $offset - 1, 1) == '\\')) {
               $t_regex .= $this->quote(substr($path, $t_offset, $offset - $t_offset - 1));
               $t_regex .= '\\' . substr($path, $offset, 1);
               $t_regex .= $this->quote(substr($path, $offset + 1, strlen($key) - 1));
            } else {
               $t_regex .= $this->quote(substr($path, $t_offset, $offset - $t_offset));
               
               $identifier = substr($key, 0, 1);
               $name = substr($key, 1);
               
               $regex = '';
               if (isset($this->requirements[$name])) {
                  // custom requirements
                  $regex = $this->requirements[$name];
               } elseif ($identifier == '*') {
                  // glob character
                  $regex = '.+';
               } else {
                  // standard segment
                  $regex = '[^' . $this->quoted_separators . ']+';
               }
               
               $t_regex .= '(?<' . $name . '>' . $regex . ')';
               $this->names[] = $name;
            }
            
            $t_offset = $offset + strlen($key);
         }
   
         $t_regex .= $this->quote(substr($path, $t_offset), '/');
         $parsed = $t_regex;
         
      } else {
         $parsed = $this->quote($path);
      }
      
      return $parsed;
   }
   
   protected function quote($string)
   {
      return preg_quote($string, '/');
   }
}
