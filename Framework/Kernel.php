<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Framework;


/**
 *
 */
class Kernel
{
   public static function initialize()
   {
      $app_base_path = realpath(dirname(__FILE__) . '/../../../') . DIRECTORY_SEPARATOR;
      
      $autoloader = new \Starlight\Framework\Support\UniversalClassLoader();
      $autoloader->registerNamespaces(array(
         'Starlight' => $app_base_path . 'vendor',
      ));
      $autoloader->register();
   }
};