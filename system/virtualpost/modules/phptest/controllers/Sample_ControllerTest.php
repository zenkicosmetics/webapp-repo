<?php
class Sample_ControllerTest extends PHPUnit_Framework_TestCase {
    public function testingSampleEqual1() {
    	$a = 1;
    	$b = 1;
        $this->assertEquals($a, $b);
    }
}