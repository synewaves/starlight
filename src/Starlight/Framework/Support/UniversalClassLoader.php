<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace Starlight\Framework\Support;

/**
 * UniversalClassLoader implements a "universal" autoloader for PHP 5.3.
 *
 * It is able to load classes that use either:
 *
 *  * The technical interoperability standards for PHP 5.3 namespaces and
 *    class names (http://groups.google.com/group/php-standards/web/psr-0-final-proposal);
 *
 *  * The PEAR naming convention for classes (http://pear.php.net/).
 *
 * Classes from a sub-namespace or a sub-hierarchy of PEAR classes can be
 * looked for in a list of locations to ease the vendoring of a sub-set of
 * classes for large projects.
 *
 * Example usage:
 *
 *     $loader = new UniversalClassLoader();
 *
 *     // register classes with namespaces
 *     $loader->registerNamespaces(array(
 *       'Starlight\Support' => __DIR__.'/component',
 *       'Starlight' => __DIR__.'/framework',
 *     ));
 *
 *     // register a library using the PEAR naming convention
 *     $loader->registerPrefixes(array(
 *       'Swift_' => __DIR__.'/Swift',
 *     ));
 *
 *     // activate the autoloader
 *     $loader->register();
 *
 * In this example, if you try to use a class in the Starlight\Support
 * namespace or one of its children (Starlight\Support\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 */
class UniversalClassLoader
{
   /**
    * Current namespaces
    * @var array
    */
   protected $namespaces = array();
   
   /**
    * Current prefixes
    * @var array
    */
   protected $prefixes = array();
   
   
   /**
    * Get current namespaces
    *
    * @return array current namespaces
    */
   public function getNamespaces()
   {
      return $this->namespaces;
   }
   
   /**
    * Get current prefixes
    *
    * @return array current prefixes
    */
   public function getPrefixes()
   {
      return $this->prefixes;
   }
   
   /**
    * Registers an array of namespaces
    *
    * @param array $namespaces An array of namespaces (namespaces as keys and locations as values)
    */
   public function registerNamespaces(array $namespaces)
   {
      $this->namespaces = array_merge($this->namespaces, $namespaces);
   }
   
   /**
    * Registers a namespace.
    *
    * @param string $namespace The namespace
    * @param string $path      The location of the namespace
    */
   public function registerNamespace($namespace, $path)
   {
      $this->namespaces[$namespace] = $path;
   }
   
   /**
    * Register with the php autoload subsystem
    */
   public function register()
   {
      spl_autoload_register(array($this, 'load'));
   }

   /**
    * Loads the given class/interface
    *
    * @param string $class fully-qualified class name to load
    */
   public function load($class)
   {
      if (($pos = stripos($class, '\\')) !== false) {
         // namespace
         $namespace = substr($class, 0, $pos);
         foreach ($this->namespaces as $ns => $dir) {
            if (strpos($namespace, $ns) === 0) {
               $file = $dir . DIRECTORY_SEPARATOR . str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
               if (file_exists($file)) {
                  require $file;
               }
               
               return;
            }
         }
      } else {
         // PEAR style
         foreach ($this->prefixes as $prefix => $dir) {
            if (strpos($class, $prefix) === 0) {
               $file = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
               if (file_exists($file)) {
                  require $file;
               }
         
               return;
            }
         }
      }
   }
};
