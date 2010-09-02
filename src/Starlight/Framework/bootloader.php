<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'UniversalClassLoader.php';

$autoloader = new \Starlight\Framework\Support\UniversalClassLoader();
$autoloader->registerNamespaces(array(
   'Starlight' => realpath(dirname(__FILE__) . '/../../'),
));
$autoloader->register();

function dump()
{
   foreach (func_get_args() as $arg) {
      echo '<pre>' . print_r($arg, true) . '</pre>';
      echo '<hr />';
   }
}

$dispatcher = new \Starlight\Component\Dispatcher\HttpDispatcher();
$dispatcher->dispatch(new \Starlight\Component\Http\Request());
dump($dispatcher);

//\Starlight\Framework\Kernel::initialize();