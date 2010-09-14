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
      echo '<pre>' . print_r($arg, true) . '</pre>';
      echo '<hr />';
   }
}


$dispatcher = new \Starlight\Component\Dispatcher\HttpDispatcher(new \Starlight\Component\Http\HttpContext());
$dispatcher->dispatch();


// $dispatcher = new \Starlight\Component\Dispatcher\HttpDispatcher();
// $dispatcher->dispatch(new \Starlight\Component\Http\Request());
// dump($dispatcher);

//\Starlight\Framework\Kernel::initialize();

// $r->match('/some/url/to/match', 'controller#hellyeah');

// $route = \Starlight\Component\Routing\Router::map('/pages/:id', 'pages#view')
//    // ->defaults(array('controller' => 'anything'))
//    // ->methods(array('post', 'get'))
//    // ->constraints(array('id' => '/[0-9]+/i'))
//    // ->name('junk_stuff')
//    // ->namespaced('admin')
//    ;
//    // ->constraints(function($request){
//    //    return false;
//    // });

// $route = \Starlight\Component\Routing\Router::resources('photos')
//    ->only(array('edit'))
//    ->pathNames(array('edit' => 'editon'))
//    ->controller('images')
//    ->name('images')
//    ;
// 
// $route->compile();

// \Starlight\Component\Routing\Router::compile();
// //\Starlight\Component\Routing\Router::match('/pages/this-is-some-junk/and-some-more-junk/adsf/anything/100asd');
// \Starlight\Component\Routing\Router::match('/pages/100asd');
