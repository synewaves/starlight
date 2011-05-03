<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Http;
use Starlight\Component\StdLib\StorageBucket;


/**
 * Wrapper class request parameters
 * @see Request
 */
class ParameterBucket extends StorageBucket
{
   /**
    * Returns the alphabetic characters of the parameter value
    * @param string $key The parameter key
    * @param mixed $default The default value
    * @return string The filtered value
    */
   public function getAlpha($key, $default = '')
   {
      return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
   }
   
   /**
    * Returns the alphabetic characters and digits of the parameter value
    * @param string $key The parameter key
    * @param mixed $default The default value
    * @return string The filtered value
    */
   public function getAlnum($key, $default = '')
   {
      return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
   }
   
   /**
    * Returns the digits of the parameter value
    * @param string $key The parameter key
    * @param mixed $default The default value
    * @return string The filtered value
    */
   public function getDigits($key, $default = '')
   {
      return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));
   }
   
   /**
    * Returns the parameter value converted to integer
    * @param string $key The parameter key
    * @param mixed $default The default value
    * @return string The filtered value
    */
   public function getInt($key, $default = 0)
   {
      return (int) $this->get($key, $default);
   }
}
