<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 

error_reporting(-1);

require_once __DIR__ . '/Support/UniversalClassLoader.php';

$autoloader = new \Starlight\Framework\Support\UniversalClassLoader();
$autoloader->registerNamespaces(array(
   'Starlight' => __DIR__ . '/../../',
));
$autoloader->register();


function dump()
{
   foreach (func_get_args() as $arg) {
      echo '<pre>' . htmlspecialchars(print_r($arg, true)) . '</pre>';
      echo '<hr />';
   }
}


$r = new \Starlight\Component\Routing\Router();
$r->draw(function($r){

   $r->map('users', $r->redirect('/users/anything'));
   
})->compile();

echo $r;
dump($r);