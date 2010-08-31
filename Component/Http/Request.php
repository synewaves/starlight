<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Component\HTTP;

class Request
{
   public $post;
   public $get;
   public $cookies;
   public $files;
   public $server;
   
   
   public function __construct(array $post = null, array $get = null, array $cookies = null, array $files = null, array $server = null)
   {
      $this->initialize($post, $get, $cookies, $files, $server);
   }
   
   public function initialize(array $post = null, array $get = null, array $cookies = null, array $files = null, array $server = null)
   {
      $this->post = new ParameterContainer($post ?: $_POST);
      $this->get = new ParameterContainer($get ?: $_GET);
      $this->cookies = new ParameterContainer($cookies ?: $_COOKIE);
      $this->files = new ParameterContainer($files ?: $_FILES);
      $this->server = new ParameterContainer($server ?: $_SERVER);
      
   }
};
