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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'UniversalClassLoader.php';

$autoloader = new \Starlight\Framework\Support\UniversalClassLoader();
$autoloader->registerNamespaces(array(
   'Starlight' => __DIR__ . '/../../',
));
$autoloader->register();

function dump()
{
   foreach (func_get_args() as $arg) {
      echo '<pre>' . print_r($arg, true) . '</pre>';
      echo '<hr />';
   }
}

// $dispatcher = new \Starlight\Component\Dispatcher\HttpDispatcher();
// $dispatcher->dispatch(new \Starlight\Component\Http\Request());
// dump($dispatcher);

//\Starlight\Framework\Kernel::initialize();

// $r->match('/some/url/to/match', 'controller#hellyeah');

// $route = \Starlight\Component\Routing\Router::match('/pages/*junk/anything/:id', 'pages#view')
//    ->defaults(array('controller' => 'anything'))
//    ->methods(array('post', 'get'))
//    ->constraints(array('id' => '/[0-9]+/i'))
//    ->name('junk_stuff')
//    ->namespaced('admin')
//    ;
//    // ->constraints(function($request){
//    //    return false;
//    // });
// $route = \Starlight\Component\Routing\Router::resources('photos')
//    ->except(array('index'))
//    ;
// 
// dump($route);
