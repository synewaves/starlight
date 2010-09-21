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
 * Singular resource
 */
class SingularResource extends Resource
{
   /**
    * RESTful routing map; maps actions to methods
    * @var array
    */
   protected static $resources_map = array(
      'index'   => array('name' => '%s',        'verb' => 'get',    'url' => '/'),
      'add'     => array('name' => 'add_%s',    'verb' => 'get',    'url' => '/:action'),
      'create'  => array('name' => '%s',        'verb' => 'post',   'url' => '/'),
      'show'    => array('name' => '%s',        'verb' => 'get',    'url' => '/'),
      'edit'    => array('name' => 'edit_%s',   'verb' => 'get',    'url' => '/:action'),
      'update'  => array('name' => '%s',        'verb' => 'put',    'url' => '/'),
      'delete'  => array('name' => 'delete_%s', 'verb' => 'get',    'url' => '/:action'),
      'destroy' => array('name' => '%s',        'verb' => 'delete', 'url' => '/'),
   );
};
