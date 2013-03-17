<?php

require_once 'C:\xampp\htdocs\simpletest\autorun.php';

require_once 'BowlingGameController.php';

class TestOfBowling extends UnitTestCase {
	private $ctlr;

	function setUp() {
		$this->ctrl = new BowlingGameController();
	}

	function tearDown() {
	}

	function testGameControllerToggleAjax() {
		$ajax1 = $this->ctrl->isAjaxOn();
		$this->ctrl->toggleAjax();
		$ajax2 = $this->ctrl->isAjaxOn();
		$this->assertTrue($ajax2 === !$ajax1);
	}

	function testGameControllerToggleAjax2() {
		$ajax1 = $this->ctrl->isAjaxOn();
		$this->ctrl->toggleAjax();
		$ajax2 = $this->ctrl->isAjaxOn();
		$this->ctrl->toggleAjax();
		$ajax3 = $this->ctrl->isAjaxOn();
		$this->assertTrue($ajax1 === $ajax3 && $ajax2 === !$ajax1);
	}
}
?>