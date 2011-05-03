<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Inflector;


/**
 * Inflector
 */
class Inflector
{
   /**
    * Plural inflections
    * @var array
    */
   protected static $plurals = array();

   /**
    * Singular inflections
    * @var array
    */
   protected static $singulars = array();

   /**
    * Uncountable item reflections
    * @var array
    */
   protected static $uncountables = array();
   
   
   /**
    * Adds a plural inflection
    * @param string $rule regex rule
    * @param string $replacement regex replacement
    */
   public static function plural($rule, $replacement)
   {
      if (!preg_match('/^\//', $rule)) {
         // looks like a string
         // TODO: make string vs regex detection better
         static::$uncountables = array_diff(static::$uncountables, array($rule));
         $rule = '/' . $rule . '/i';
      }
      
      static::$uncountables = array_diff(static::$uncountables, array($replacement));
      array_unshift(static::$plurals, array($rule, $replacement));
   }
   
   /**
    * Adds a singular inflection
    * @param string $rule regex rule
    * @param string $replacement regex replacement
    */
   public static function singular($rule, $replacement)
   {
      if (!preg_match('/^\//', $rule)) {
         // looks like a string
         // TODO: make string vs regex detection better
         static::$uncountables = array_diff(static::$uncountables, array($rule));
         $rule = '/' . $rule . '/i';
      }
      
      static::$uncountables = array_diff(static::$uncountables, array($replacement));
      array_unshift(static::$singulars, array($rule, $replacement));
   }

   /**
    * Adds an irregular inflection
    * @param string $singular singular rule
    * @param string $plural plural rule
    */
   public static function irregular($singular, $plural)
   {
      static::plural('/(' . $singular[0] . ')' . substr($singular, 1) . '$/i', '\1' . substr($plural, 1));
      static::singular('/(' . $plural[0] . ')' . substr($plural, 1) . '$/i', '\1' . substr($singular, 1));
   }

   /**
    * Adds an uncountable word(s)
    * @param string|array single word or array of words
    */
   public static function uncountable($words)
   {
      static::$uncountables += !is_array($words) ? array($words) : $words;
   }
   
   /**
    * Pluralizes a word
    * @param string $str word
    * @return string pluralized word
    */
   public static function pluralize($str)
   {
      $str = strval($str);

      if (!in_array(strtolower($str), static::$uncountables) && $str != '') {
         foreach (static::$plurals as $pair) {
            if (preg_match($pair[0], $str)) {
               return preg_replace($pair[0], $pair[1], $str);
            }
         }
      }
      
      return $str;
   }
   
   /**
    * Singularizes a word
    * @param string $str word
    * @return string singularized word
    */
   public static function singularize($str)
   {
      $str = strval($str);

      if (!in_array(strtolower($str), static::$uncountables) && $str != '') {
         foreach (static::$singulars as $pair) {
            if (preg_match($pair[0], $str)) {
               return preg_replace($pair[0], $pair[1], $str);
            }
         }
      }
      
      return $str;
   }
   
   /**
    * Normalizes a string for a url
    *
    * This removes any non alphanumeric character, dash, space or underscore
    * with supplied separator
    *
    * <code>
    * echo Inflector::normalize('This is an example');
    * > this-is-an-example
    * </code>
    * @param string $str string to normalize
    * @param string $sep string separator
    * @return normalized string
    */
   public static function normalize($str, $sep = '-')
   {
      return strtolower(preg_replace(array('/[^a-zA-Z0-9\_ \-]/', '/\s+/',), array('', $sep), strval($str)));
   }
   
   /**
    * Humanizes an underscored string
    *
    * <code>
    * echo Inflector::humanize('this_is_an_example');
    * > This is an example
    * </code>
    * @param string $str string to humanize (underscored)
    * @return string humanized string
    */
   public static function humanize($str)
   {
      return ucfirst(strtolower(preg_replace('/_/', ' ', preg_replace('/_id$/', '', strval($str)))));
   }
   
   /**
    * Titleizes an underscored string
    *
    * <code>
    * echo Inflector::titleize('this_is_an_example');
    * > This Is An Example
    * </code>
    * @param string $str string to titleize (underscored)
    * @return string titleized string
    */
   public static function titleize($str)
   {
      return ucwords(static::humanize(static::underscore($str)));
   }
   
   /**
    * Creates a table name from a class name
    *
    * <code>
    * echo Inflector::tableize('ExampleWord');
    * > example_words
    * </code>
    * @param string $str string to tableize (underscored)
    * @return string humanized string
    */
   public static function tableize($str)
   {
      return static::pluralize(static::underscore($str));
   }
   
   /**
    * Creates a class name from a table name
    *
    * <code>
    * echo Inflector::classify('example_words');
    * > ExampleWord
    * </code>
    * @param string $str string to classify (camel-cased, pluralized)
    * @return string classified string
    */
   public static function classify($str)
   {
      return static::camelize(static::singularize(preg_replace('/.*\./', '', $str)));
   }
   
   /**
    * Camelizes a string
    *
    * <code>
    * echo Inflector::camelize('This is an example');
    * > ThisIsAnExample
    * </code>
    * @param string $str string to camelize
    * @param boolean $uc_first uppercase first letter
    * @return camelized string
    */
   public static function camelize($str, $uc_first = true)
   {
      $base = str_replace(' ', '', ucwords(str_replace('_', ' ', strval($str))));

      return !$uc_first ? strtolower(substr($base, 0, 1)) . substr($base, 1) : $base;
   }
   
   /**
    * Underscores a camelized string
    *
    * <code>
    * echo Inflector::underscore('thisIsAnExample');
    * > this_is_an_example
    * </code>
    * @param string $str string to underscore (camelized)
    * @return string underscored string
    */
   public static function underscore($str)
   {
      $str = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\\1_\\2', strval($str));
      $str = preg_replace('/([a-z\d])([A-Z])/', '\\1_\\2', $str);
      $str = str_replace('-', '_', $str);

      return strtolower($str);
   }
   
   /**
    * Dasherizes a string
    *
    * <code>
    * echo Inflector::dasherize('this_is_an_example');
    * > this-is-an-example
    * </code>
    * @param string $str string to dasherize
    * @return dasherized string
    */
   public static function dasherize($str)
   {
      return str_replace('_', '-', strval($str));
   }
   
   /**
    * Ordinalizes a number
    *
    * <code>
    * echo Inflector::ordinalize(10);
    * > 10th
    * </code>
    * @param integer $number number
    * @return string ordinalized number
    */
   public static function ordinalize($number)
   {
      if (in_array(($number % 100), array(11, 12, 13))) {
         return $number . 'th';
      } else {
         $mod = $number % 10;
         if ($mod == 1) {
            return $number . 'st';
         } elseif ($mod == 2) {
            return $number . 'nd';
         } elseif ($mod == 3) {
            return $number . 'rd';
         } else {
            return $number . 'th';
         }
      }
   }

   /**
    * Creates a foreign key name from a class name
    *
    * <code>
    * echo Inflector::foreignKey('Example');
    * > example_id
    * </code>
    * @param string $str string to make foreign key for
    * @param boolean $underscore use underscore delimiter
    * @return string foreign key string
    */
   public static function foreignKey($str, $underscore = true)
   {
      return static::underscore($str) . ($underscore ? '_id' : 'id');
   }
}


// include default inflections
require_once __DIR__ . '/inflections.php';
