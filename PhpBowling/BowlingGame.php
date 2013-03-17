<?php
/**
 * Original Bowling Game Kata from Uncle Bob C. Martin's TDD tutorial
 *
 */
class BowlingGame {
	const SPARE_MESSAGE    = 'Spare!';
	const STRIKE_MESSAGE   = 'Strike!';
	const GAMEOVER_MESSAGE = 'Game over!';

	private $frames;
	private $pins;
	private $currentFrameIndex;
	private $gameOver;
	private $message;

	public function __construct() {
		$this->reset();
	}

	/**
	 * Reset whole game
	 */
	public function reset() {
		// Initialize frames
		$this->frames = array();
		// Frames 1-9
		for ($frameIndex = 0; $frameIndex < 9; $frameIndex++) {
			$this->frames[$frameIndex] = array(
					'firstRoll'  => null,
					'secondRoll' => null);
		}
		// Frame 10
		$this->frames[9] = array(
				'firstRoll'  => null,
				'secondRoll' => null,
				'thirdRoll'  => null);

		// Reset pins
		$this->resetPins();

		// Current frame index
		$this->currentFrameIndex = 0;

		// Game over indicator
		$this->gameOver = false;

		// Message to indicate spare & strike & game over events
		$this->message = '';
	}

	/**
	 * Reset (set up) pins
	 */
	private function resetPins() {
		for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
			$this->pins[$pinIndex] = 1;
		}
	}

	/**
	 * Check if pin is standing
	 *
	 * @param int $pinIndex
	 * @return boolean
	 */
	public function isPinStanding($pinIndex) {
		return 1 == $this->pins[$pinIndex];
	}

	/**
	 * Lay pins that have been hit
	 * Check if they were really standing
	 *
	 * @param array $pins
	 * @throws Exception
	 */
	private function layPins($pins) {
		for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
			if (isset($pins[$pinIndex]) && 1 == $pins[$pinIndex]) {
				if (1 == $this->pins[$pinIndex]) {
					// This pin has been knocked down right now
					$this->pins[$pinIndex] = 0;
				} else {
					// This pin has already been knocked down before - roll failure!
					throw new Exception('Roll failure');
				}
			}
		}
	}

	/**
	 * Process current roll
	 *
	 * @param array $pins
	 */
	public function doRoll($pins) {
		// Lay pins hit down
		$this->layPins($pins);

		// Count hits in the current roll
		$hits = 0;
		for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
			if (isset($pins[$pinIndex]) && 1 == $pins[$pinIndex]) {
				$hits++;
			}
		}
		// Set roll result
		if (9 > $this->currentFrameIndex) {
			// First nine frames
			if (null === $this->frames[$this->currentFrameIndex]['firstRoll']) {
				// Hits in the first roll in frame
				$this->frames[$this->currentFrameIndex]['firstRoll'] = $hits;
				if (10 == $hits) {
					// Set strike message
					$this->message = BowlingGame::STRIKE_MESSAGE;
					// Reset pins
					$this->resetPins();
					// Go the next frame if not the last one
					$this->currentFrameIndex++;
				}
			} elseif (null === $this->frames[$this->currentFrameIndex]['secondRoll']) {
				// Hits in the second roll in frame
				$this->frames[$this->currentFrameIndex]['secondRoll'] = $hits;
				if ($this->isSpare($this->currentFrameIndex)) {
					// Set spare message
					$this->message = BowlingGame::SPARE_MESSAGE;
				}
				// Go the next frame if not the last one
				$this->currentFrameIndex++;
				// Reset pins
				$this->resetPins();
			}
		} else {
			// Tenth frame
			if (null === $this->frames[9]['firstRoll']) {
				// Hits in the first roll in frame
				$this->frames[9]['firstRoll'] = $hits;
				if (10 == $hits) {
					// Set strike message
					$this->message = BowlingGame::STRIKE_MESSAGE;
					// Reset pins
					$this->resetPins();
				}
			} elseif (null === $this->frames[9]['secondRoll']) {
				// Hits in the second roll in frame
				$this->frames[9]['secondRoll'] = $hits;
				if (10 == $hits) {
					// Set strike message
					$this->message = BowlingGame::STRIKE_MESSAGE;
					// Reset pins
					$this->resetPins();
				} else if ($this->isSpare(9)) {
					// Set spare message
					$this->message = BowlingGame::SPARE_MESSAGE;
					// Reset pins
					$this->resetPins();
				} else if (10 != $this->frames[9]['firstRoll']) {
					// Game over in the tenth frame
					$this->gameOver = true;
					$this->message = BowlingGame::GAMEOVER_MESSAGE;
				}
			} else {
				// Hits in the third roll in the tenth frame
				$this->frames[9]['thirdRoll'] = $hits;
				// Game over
				$this->gameOver = true;
				$this->message = BowlingGame::GAMEOVER_MESSAGE;
			}
		}
	}

	/**
	 * Get current frame index
	 * @return int
	 */
	public function getCurrentFrameIndex() {
		return $this->currentFrameIndex;
	}

	/**
	 * Get the value of the first roll in frame
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	public function getFirstRollInFrame($frameIndex) {
		return $this->frames[$frameIndex]['firstRoll'];
	}

	/**
	 * Get the value of the second roll in frame
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	public function getSecondRollInFrame($frameIndex) {
		return $this->frames[$frameIndex]['secondRoll'];
	}

	/**
	 * Get the value of the third roll in frame
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	public function getThirdRollInFrame($frameIndex) {
		return $this->frames[$frameIndex]['thirdRoll'];
	}

	/**
	 * Get the actual score in frame
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	public function getScoreInFrame($frameIndex) {
		if (null === $this->frames[$frameIndex]['firstRoll']) {
			// This frame has not been reached yet
			return null;
		} else {
			$score = 0;
			for ($i = 0; $i <= $frameIndex; $i++) {
				if ($this->isStrike($i)) {
					$score += 10 + $this->getStrikeBonus($i);
				} else if ($this->isSpare($i)) {
					$score += 10 + $this->getSpareBonus($i);
				} else {
					$score += $this->getScoreRolledInFrame($i);
				}
			}
			return $score;
		}
	}

	/**
	 * Check if game has finished
	 * @return boolean
	 */
	public function isGameOver() {
		return $this->gameOver;
	}

	/**
	 * Get game message
	 */
	public function getMessage() {
		$message = $this->message;
		if (!$this->gameOver) {
			// If game over, message must be preserved
			$this->message = '';
		}
		return $message;
	}

	/**
	 * Check if frame is spare
	 *
	 * @param int $frameIndex
	 * @return boolean
	 */
	private function isSpare($frameIndex) {
		return 10 != $this->frames[$frameIndex]['firstRoll']
		&& 10 == $this->frames[$frameIndex]['firstRoll'] + $this->frames[$frameIndex]['secondRoll'];
	}

	/**
	 * Check if frame is strike
	 *
	 * @param int $frameIndex
	 * @return boolean
	 */
	private function isStrike($frameIndex) {
		if (9 > $frameIndex) {
			// In the first nine frames it is possible in the first roll only
			return 10 == $this->frames[$frameIndex]['firstRoll'];
		} else {
			// In the tenth frame it can be the second roll as well
			return (null === $this->frames[$frameIndex]['secondRoll'] && 10 == $this->frames[$frameIndex]['firstRoll'])
			|| 10 == $this->frames[$frameIndex]['secondRoll'];
		}
	}

	/**
	 * Get the score resulting from the rolls in frame
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	private function getScoreRolledInFrame($frameIndex) {
		$score = $this->frames[$frameIndex]['firstRoll'] + $this->frames[$frameIndex]['secondRoll'];
		if (9 == $frameIndex) {
			// In the tenth frame there might be a third roll that adds up to the score
			$score += $this->frames[9]['thirdRoll'];
		}
		return $score;
	}

	/**
	 * Get spare bonus
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	private function getSpareBonus($frameIndex) {
		// Take the next roll
		if (9 > $frameIndex) {
			// In the first nine frames the bonus is the first roll of the next frame
			return $this->frames[$frameIndex + 1]['firstRoll'];
		} else {
			// In the tenth frame the bonus is the third roll
			return $this->frames[$frameIndex]['thirdRoll'];
		}
	}

	/**
	 * Get strike bonus
	 *
	 * @param int $frameIndex
	 * @return int
	 */
	private function getStrikeBonus($frameIndex) {
		// Take the next two rolls
		if (8 > $frameIndex) {
			// In the first eight frames the bonus is either the two rolls of the next frame
			// or in case of another strike, the first rolls of the next two frames
			if (10 > $this->frames[$frameIndex + 1]['firstRoll']) {
				return $this->frames[$frameIndex + 1]['firstRoll'] + $this->frames[$frameIndex + 1]['secondRoll'];
			} else {
				return $this->frames[$frameIndex + 1]['firstRoll'] + $this->frames[$frameIndex + 2]['firstRoll'];
			}
		} else if (8 == $frameIndex) {
			// In the ninth frame the bonus is the first and second rolls in the tenth frame
			return $this->frames[9]['firstRoll'] + $this->frames[9]['secondRoll'];
		} else {
			// In the tenth frame the bonus is the second and third rolls
			return $this->frames[$frameIndex]['secondRoll'] + $this->frames[$frameIndex]['thirdRoll'];
		}
	}
}
?>