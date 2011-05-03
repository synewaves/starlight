<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Tests\Component\Inflector;
use Starlight\Component\Inflector\Inflector;


/**
 */ 
class InflectorTest extends \PHPUnit_Framework_TestCase
{
   protected static $singular_to_plural = array(
      'search'      => 'searches',
      'switch'      => 'switches',
      'fix'         => 'fixes',
      'box'         => 'boxes',
      'process'     => 'processes',
      'address'     => 'addresses',
      'case'        => 'cases',
      'stack'       => 'stacks',
      'wish'        => 'wishes',
      'fish'        => 'fish',
      'category'    => 'categories',
      'query'       => 'queries',
      'ability'     => 'abilities',
      'agency'      => 'agencies',
      'movie'       => 'movies',
      'archive'     => 'archives',
      'index'       => 'indices',
      'wife'        => 'wives',
      'safe'        => 'saves',
      'half'        => 'halves',
      'move'        => 'moves',
      'salesperson' => 'salespeople',
      'person'      => 'people',
      'spokesman'   => 'spokesmen',
      'man'         => 'men',
      'woman'       => 'women',
      'basis'       => 'bases',
      'diagnosis'   => 'diagnoses',
      'diagnosis_a' => 'diagnosis_as',
      'datum'       => 'data',
      'medium'      => 'media',
      'analysis'    => 'analyses',
      'node_child'  => 'node_children',
      'child'       => 'children',
      'experience'  => 'experiences',
      'day'         => 'days',
      'comment'     => 'comments',
      'foobar'      => 'foobars',
      'newsletter'  => 'newsletters',
      'old_news'    => 'old_news',
      'news'        => 'news',
      'series'      => 'series',
      'species'     => 'species',
      'quiz'        => 'quizzes',
      'perspective' => 'perspectives',
      'ox'          => 'oxen',
      'photo'       => 'photos',
      'buffalo'     => 'buffaloes',
      'tomato'      => 'tomatoes',
      'dwarf'       => 'dwarves',
      'elf'         => 'elves',
      'information' => 'information',
      'equipment'   => 'equipment',
      'bus'         => 'buses',
      'status'      => 'statuses',
      'status_code' => 'status_codes',
      'mouse'       => 'mice',
      'louse'       => 'lice',
      'house'       => 'houses',
      'octopus'     => 'octopi',
      'virus'       => 'viri',
      'alias'       => 'aliases',
      'portfolio'   => 'portfolios',
      'vertex'      => 'vertices',
      'matrix'      => 'matrices',
      'matrix_fu'   => 'matrix_fus',
      'axis'        => 'axes',
      'testis'      => 'testes',
      'crisis'      => 'crises',
      'rice'        => 'rice',
      'shoe'        => 'shoes',
      'horse'       => 'horses',
      'prize'       => 'prizes',
      'edge'        => 'edges',
      'cow'         => 'cattle',
      'database'    => 'databases',
   );
   
   protected static $string_to_normalized = array(
      'Donald E. Knuth'                     => 'donald-e-knuth',
      'Random text with *(bad)* characters' => 'random-text-with-bad-characters',
      'Allow_Under_Scores'                  => 'allow_under_scores',
      'Trailing bad characters!@#'          => 'trailing-bad-characters',
      '!@#Leading bad characters'           => 'leading-bad-characters',
      'Squeeze   separators'                => 'squeeze-separators',
   );
   
   protected static $string_to_normalized_no_sep = array(
      'Donald E. Knuth'                     => 'donaldeknuth',
      'Random text with *(bad)* characters' => 'randomtextwithbadcharacters',
      'Trailing bad characters!@#'          => 'trailingbadcharacters',
      '!@#Leading bad characters'           => 'leadingbadcharacters',
      'Squeeze   separators'                => 'squeezeseparators',
   );
   
   protected static $string_to_normalized_with_underscore = array(
      'Donald E. Knuth'                     => 'donald_e_knuth',
      'Random text with *(bad)* characters' => 'random_text_with_bad_characters',
      'Trailing bad characters!@#'          => 'trailing_bad_characters',
      '!@#Leading bad characters'           => 'leading_bad_characters',
      'Squeeze   separators'                => 'squeeze_separators',
   );
   
   protected static $string_to_title = array(
      'starlight_base'      => 'Starlight Base',
      'StarlightBase'       => 'Starlight Base',
      'starlight base code' => 'Starlight Base Code',
      'Starlight base Code' => 'Starlight Base Code',
      'Starlight Base Code' => 'Starlight Base Code',
      'starlightbasecode'   => 'Starlightbasecode',
      'Starlightbasecode'   => 'Starlightbasecode',
      'Person\'s stuff'     => 'Person\'s Stuff',
      'person\'s Stuff'     => 'Person\'s Stuff',
      'Person\'s Stuff'     => 'Person\'s Stuff',
   );
   
   protected static $camel_to_underscore = array(
      'Product'               => 'product',
      'SpecialGuest'          => 'special_guest',
      'ApplicationController' => 'application_controller',
      'Area51Controller'      => 'area51_controller',
   );
   
   protected static $camel_to_underscore_wo_rev = array(
      'HTMLTidy'          => 'html_tidy',
      'HTMLTidyGenerator' => 'html_tidy_generator',
      'FreeBSD'           => 'free_bsd',
      'HTML'              => 'html',
   );
   
   protected static $class_to_fk_underscore = array(
      'Person' => 'person_id',
   );
   
   protected static $class_to_fk_no_underscore = array(
      'Person' => 'personid',
   );
   
   protected static $class_to_table = array(
      'PrimarySpokesman' => 'primary_spokesmen',
      'NodeChild'        => 'node_children',
   );
   
   protected static $underscore_to_human = array(
      'employee_salary' => 'Employee salary',
      'employee_id'     => 'Employee',
      'underground'     => 'Underground',
   );
   
   protected static $ordinals = array(
      '0' => '0th',
      '1' => '1st',
      '2' => '2nd',
      '3' => '3rd',
      '4' => '4th',
      '5' => '5th',
      '6' => '6th',
      '7' => '7th',
      '8' => '8th',
      '9' => '9th',
      '10' => '10th',
      '11' => '11th',
      '12' => '12th',
      '13' => '13th',
      '14' => '14th',
      '20' => '20th',
      '21' => '21st',
      '22' => '22nd',
      '23' => '23rd',
      '24' => '24th',
      '100' => '100th',
      '101' => '101st',
      '102' => '102nd',
      '103' => '103rd',
      '104' => '104th',
      '110' => '110th',
      '111' => '111th',
      '112' => '112th',
      '113' => '113th',
      '1000' => '1000th',
      '1001' => '1001st',
   );
   
   protected static $underscores_to_dashes = array(
      'street'                => 'street',
      'street_address'        => 'street-address',
      'person_street_address' => 'person-street-address',
   );
   
   protected static $underscores_to_lower_camel = array(
      'product'                => 'product',
      'special_guest'          => 'specialGuest',
      'application_controller' => 'applicationController',
      'area51_controller'      => 'area51Controller',
   );
   
   
   public function testPluralizePlurals()
   {
      $this->assertEquals('plurals', Inflector::pluralize('plurals'));
      $this->assertEquals('Plurals', Inflector::pluralize('Plurals'));
   }
   
   public function testPluralizeEmptyString()
   {
      $this->assertEquals('', Inflector::pluralize(''));
   }
   
   public function testSingularToPlural()
   {
      foreach (self::$singular_to_plural as $singular => $plural) {
         $this->assertEquals($plural, Inflector::pluralize($singular));
         $this->assertEquals(ucfirst($plural), Inflector::pluralize(ucfirst($singular)));
         $this->assertEquals($singular, Inflector::singularize($plural));
         $this->assertEquals(ucfirst($singular), Inflector::singularize(ucfirst($plural)));
      }
   }
   
   public function testCamelize()
   {
      foreach (self::$camel_to_underscore as $camel => $underscore) {
         $this->assertEquals($camel, Inflector::camelize($underscore));
      }
   }
   
   public function testOverwritePreviousInflectors()
   {
      $this->assertEquals('series', Inflector::singularize('series'));
      Inflector::singular('series', 'serie');
      $this->assertEquals('serie', Inflector::singularize('series'));
      Inflector::uncountable('series');
      
      $this->assertEquals('series', Inflector::pluralize('series'));
      Inflector::plural('series', 'seria');
      $this->assertEquals('seria', Inflector::pluralize('series'));
      Inflector::uncountable('series');
      
      $this->assertEquals('dies', Inflector::pluralize('die'));
      Inflector::irregular('die', 'dice');
      $this->assertEquals('dice', Inflector::pluralize('die'));
      $this->assertEquals('die', Inflector::singularize('dice'));
   }
   
   public function testTitleize()
   {
      foreach (self::$string_to_title as $before => $title) {
         $this->assertEquals($title, Inflector::titleize($before));
      }
   }
   
   public function testNormalize()
   {
      foreach (self::$string_to_normalized as $string => $normalized) {
         $this->assertEquals($normalized, Inflector::normalize($string));
      }
   }
   
   public function testNormalizeWithNoSeparator()
   {
      foreach (self::$string_to_normalized_no_sep as $string => $normalized) {
         $this->assertEquals($normalized, Inflector::normalize($string, ''));
      }
   }
   
   public function testNormalizeWithUnderscore()
   {
      foreach (self::$string_to_normalized_with_underscore as $string => $normalized) {
         $this->assertEquals($normalized, Inflector::normalize($string, '_'));
      }
   }
   
   public function testCamelizeLowercasesFirstLetter()
   {
      $this->assertEquals('capitalPlayersLeague', Inflector::camelize('Capital_players_League', false));
   }
   
   public function testUnderscore()
   {
      foreach (self::$camel_to_underscore as $camel => $underscore) {
         $this->assertEquals($underscore, Inflector::underscore($camel));
      }
      
      foreach (self::$camel_to_underscore_wo_rev as $camel => $underscore) {
         $this->assertEquals($underscore, Inflector::underscore($camel));
      }
   }
   
   public function testforeignKey()
   {
      foreach (self::$class_to_fk_underscore as $klass => $fk) {
         $this->assertEquals($fk, Inflector::foreignKey($klass));
      }
      
      foreach (self::$class_to_fk_no_underscore as $klass => $fk) {
         $this->assertEquals($fk, Inflector::foreignKey($klass, false));
      }
   }
   
   public function testTableize()
   {
      foreach (self::$class_to_table as $klass => $table) {
         $this->assertEquals($table, Inflector::tableize($klass));
      }
   }
   
   public function testClassify()
   {
      foreach (self::$class_to_table as $klass => $table) {
         $this->assertEquals($klass, Inflector::classify($table));
         $this->assertEquals($klass, Inflector::classify('prefix.' . $table));
      }
   }
   
   public function testHumanize()
   {
      foreach (self::$underscore_to_human as $underscore => $human) {
         $this->assertEquals($human, Inflector::humanize($underscore));
      }
   }
   
   public function testOrdinal()
   {
      foreach (self::$ordinals as $number => $ordinal) {
         $this->assertEquals($ordinal, Inflector::ordinalize($number));
      }
   }
   
   public function testDasherize()
   {
      foreach (self::$underscores_to_dashes as $underscored => $dashes) {
         $this->assertEquals($dashes, Inflector::dasherize($underscored));
      }
   }
   
   public function testUnderscoreAsReverseOfDasherize()
   {
      foreach (self::$underscores_to_dashes as $underscored => $dashes) {
         $this->assertEquals($underscored, Inflector::underscore(Inflector::dasherize($underscored)));
      }
   }
   
   public function testUnderscoreToLowerCamel()
   {
      foreach (self::$underscores_to_lower_camel as $underscored => $lower_camel) {
         $this->assertEquals($lower_camel, Inflector::camelize($underscored, false));
      }
   }
}
