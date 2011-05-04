<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
      'add'     => array('name' => 'add_%s',    'verb' => 'get',    'url' => '/:action(.:format)'),
      'create'  => array('name' => '%s',        'verb' => 'post',   'url' => '(.:format)'),
      'show'    => array('name' => '%s',        'verb' => 'get',    'url' => '(.:format)'),
      'edit'    => array('name' => 'edit_%s',   'verb' => 'get',    'url' => '/:action(.:format)'),
      'update'  => array('name' => '%s',        'verb' => 'put',    'url' => '(.:format)'),
      'destroy' => array('name' => '%s',        'verb' => 'delete', 'url' => '(.:format)'),
   );
}
