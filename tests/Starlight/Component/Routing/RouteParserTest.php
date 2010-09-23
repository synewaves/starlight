<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Starlight\Tests\Component\Routing;
use Starlight\Component\Routing\RouteParser;


/**
 * @group Routing
 */ 
class RouteParserTest extends \PHPUnit_Framework_TestCase
{
   public function setup()
   {
      $this->parser = new RouteParser();
   }
   
   // public function testStaticSegment()
   // {
   //    $this->parser->parse('sample');
   //    
   //    $this->assertEquals('/\Asample\Z/', $this->parser->regex);
   //    $this->assertEquals(array(), $this->parser->names);
   // }
   // 
   // public function testMultipleStaticSegments()
   // {
   //    $this->parser->parse('sample/index.html');
   //    
   //    $this->assertEquals('/\Asample\/index\.html\Z/', $this->parser->regex);
   //    $this->assertEquals(array(), $this->parser->names);
   // }
   // 
   // public function testDynamicSegment()
   // {
   //    $this->parser->parse(':subdomain.example.com');
   //    
   //    $this->assertEquals('/\A(?<subdomain>[^\/\.]+)\.example\.com\Z/', $this->parser->regex);
   //    $this->assertEquals(array('subdomain'), $this->parser->names);
   // }
   // 
   // public function testDynamicSegmentWithLedingUnderscore()
   // {
   //    $this->parser->parse(':_subdomain.example.com');
   //    
   //    $this->assertEquals('/\A(?<_subdomain>[^\/\.]+)\.example\.com\Z/', $this->parser->regex);
   //    $this->assertEquals(array('_subdomain'), $this->parser->names);
   // }
   // 
   // public function testInvalidSegmentNames()
   // {
   //    $this->assertEquals('/\A\:123\.example\.com\Z/', $this->parser->parse(':123.example.com'));
   //    $this->assertEquals('/\A\:\$\.example\.com\Z/', $this->parser->parse(':$.example.com'));
   // }
   // 
   // public function testEscapedDynamicSegment()
   // {
   //    $this->parser->parse('\:subdomain.example.com');
   //    
   //    $this->assertEquals('/\A\:subdomain\.example\.com\Z/', $this->parser->regex);
   //    $this->assertEquals(array(), $this->parser->names);
   // }
   // 
   // public function testDynamicSegmentWithSeparators()
   // {
   //    $this->assertEquals('/\Afoo\/(?<bar>[^\/]+)\Z/', $this->parser->parse('foo/:bar', array(), array('/')));
   // }
   // 
   // public function testDynamicSegmentWithRequirements()
   // {
   //    $this->assertEquals('/\Afoo\/(?<bar>[a-z]+)\Z/', $this->parser->parse('foo/:bar', array('bar' => '[a-z]+'), array('/')));
   // }
   //    
   // public function testDynamicSegmentInsideOptionalSegment()
   // {
   //    $this->assertEquals('/\Afoo(?:\.(?<extension>[^\/\.]+))?\Z/', $this->parser->parse('foo(.:extension)'));
   // }
   //    
   // public function testGlobSegment()
   // {
   //    $this->assertEquals('/\Asrc\/(?<files>.+)\Z/', $this->parser->parse('src/*files'));
   // }
   // 
   // public function testGlobIgnoresSeparators()
   // {
   //    $this->assertEquals('/\Asrc\/(?<files>.+)\Z/', $this->parser->parse('src/*files', array(), array('/', '.', '?')));
   // }
   // 
   // public function testGlobSegmentAtBeginning()
   // {
   //    $this->assertEquals('/\A(?<files>.+)\/foo\.txt\Z/', $this->parser->parse('*files/foo.txt'));
   // }
   // 
   // public function testGlobSegmentInMiddle()
   // {
   //    $this->assertEquals('/\Asrc\/(?<files>.+)\/foo\.txt\Z/', $this->parser->parse('src/*files/foo.txt'));
   // }
   // 
   // public function testMultipleGlobSegments()
   // {
   //    $this->assertEquals('/\Asrc\/(?<files>.+)\/dir\/(?<morefiles>.+)\/foo\.txt\Z/', $this->parser->parse('src/*files/dir/*morefiles/foo.txt'));
   // }
   // 
   // public function testEscapedGlobSegment()
   // {
   //    $this->parser->parse('src/\*files');
   //    
   //    $this->assertEquals('/\Asrc\/\*files\Z/', $this->parser->regex);
   //    $this->assertEquals(array(), $this->parser->names);
   // }
   // 
   // public function testOptionalSegment()
   // {
   //    $this->assertEquals('/\A\/foo(?:\/bar)?\Z/', $this->parser->parse('/foo(/bar)'));
   // }
   // 
   // public function testConsecutiveOptionalSegments()
   // {
   //    $this->assertEquals('/\A\/foo(?:\/bar)?(?:\/baz)?\Z/', $this->parser->parse('/foo(/bar)(/baz)'));
   // }
   // 
   // public function testMultipleOptionalSegments()
   // {
   //    $this->assertEquals('/\A(?:\/foo)?(?:\/bar)?(?:\/baz)?\Z/', $this->parser->parse('(/foo)(/bar)(/baz)'));
   // }
   
   // public function testEscapesOptionalSegmentParenthesis()
   // {
   //    $this->assertEquals('/\A\/foo\(\/bar\)\Z/', $this->parser->parse('/foo\(/bar\)'));
   // }
   
   // public function testEscapesOneOptionalSegmentParenthesis()
   // {
   //    $this->assertEquals('/\A\/foo\((?:\/bar)?\Z/', $this->parser->parse('/foo\((/bar\)'));
   // }
   
   public function testThrowsExceptionIfOptionalSegmentParenthesisAreUnbalanced()
   {
      $this->setExpectedException('InvalidArgumentException');
      // $this->parser->parse('/foo(/bar');
      // $this->parser->parse('/foo((/bar)');
      echo $this->parser->parse('/foo(/bar))');
   }
}


  // def test_raises_regexp_error_if_optional_segment_parenthesises_are_unblanced
  //   assert_raise(RegexpError) { Strexp.compile('/foo((/bar)') }
  //   assert_raise(RegexpError) { Strexp.compile('/foo(/bar))') }
  // end