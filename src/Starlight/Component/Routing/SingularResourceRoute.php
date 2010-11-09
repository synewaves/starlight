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
class SingularResourceRoute extends ResourceRoute
{
   /**
    * RESTful routing map; maps actions to methods
    * @var array
    */
   protected static $resources_map = array(
      'index'   => array('name' => '%s',        'verb' => 'get',    'url' => '(.:format)'),
      'add'     => array('name' => 'add_%s',    'verb' => 'get',    'url' => '/:action(.:format)'),
      'create'  => array('name' => '%s',        'verb' => 'post',   'url' => '(.:format)'),
      'show'    => array('name' => '%s',        'verb' => 'get',    'url' => '(.:format)'),
      'edit'    => array('name' => 'edit_%s',   'verb' => 'get',    'url' => '/:action(.:format)'),
      'update'  => array('name' => '%s',        'verb' => 'put',    'url' => '(.:format)'),
      'destroy' => array('name' => '%s',        'verb' => 'delete', 'url' => '(.:format)'),
   );
}
