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


// $context = new \Starlight\Component\Http\HttpContext();
// $dispatcher = new \Starlight\Component\Http\HttpDispatcher($context);
// $dispatcher->dispatch();

$router = new \Starlight\Component\Routing\Router();
$router->draw(function($r){
   // $r->map('/:controller(/:action(/:id))(.:format)', 'session#add');
   // $r->map(':controller/:id', 'session#add')->constraints(array('id' => '[0-9]+'))->namespaced('admin');
   // $r->map('*anything', 'session#add');
   // $r->map('/login/:screenname', 'session#add')
   // ->defaults(array('id' => 27))
   // ->methods(array('get', 'post', 'delete'))
   // ->name('login')
   // ->namespaced('admin')
   // ->constraints(array('id' => '/27/i'))
   // ;
   
   // $r->constraints(array('id' => 27), function($r){
   //    $r->namespaced('admin', function($r){
   //       // $r->map('/login', 'session#new');
   //       // $r->map('/logout', 'session#destroy');
   //       $r->namespaced('anything', function($r){
   //          $r->map('whee', 'session#new');
   //       });
   //    });
   // });
   
   $r->resources('photo')
   ->name('image')
   ->controller('images')
   ->namespaced('admin')
   ;
});
$router->compile();

dump($router);