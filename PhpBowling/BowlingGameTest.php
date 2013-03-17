<?php
require_once 'BowlingGame.php';

class BowlingGameTest extends PHPUnit_Framework_TestCase
{
	protected $bowlingGame;

	protected function setUp()
	{
		$this->bowlingGame = new BowlingGame();
	}

	public function testAllPinsStanding()
	{
		$numStanding = 0;
		for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
			if ($this->bowlingGame->isPinStanding($pinIndex)) {
				$numStanding++;
			}
		}

		$this->assertEquals(10, $numStanding);
	}

	public function testRollOnePin()
	{
		$pins = array(0 => 1);
		$this->bowlingGame->doRoll($pins);

		$this->assertFalse($this->bowlingGame->isPinStanding(0));
	}
}