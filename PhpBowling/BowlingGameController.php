<?php
/**
 * Bowling game controller
 */
require_once 'BowlingGame.php';

class BowlingGameController {
	private $bowlingGame;
	private $ajaxOn;

	public function __construct() {
		// Initialize game session
		session_start();

		if (!isset($_SESSION['bowlingGame'])) {
			// First run
			$this->bowlingGame = new BowlingGame();
			// Start with non-ajax version
			$this->ajaxOn      = false;
		} else {
			// Not first run, session already exists - let's go on with it
			$this->bowlingGame = $_SESSION['bowlingGame'];
			$this->ajaxOn      = $_SESSION['bowlingGameAjax'];
		}
	}

	public function __destruct() {
		// Actualize session data
		$_SESSION['bowlingGame']     = $this->bowlingGame;
		$_SESSION['bowlingGameAjax'] = $this->ajaxOn;
	}

	/**
	 * Checks if application is in Ajax mode
	 *
	 * @return Ambigous <boolean, unknown>
	 */
	public function isAjaxOn() {
		return $this->ajaxOn;
	}

	/**
	 * Toggles Ajax mode
	 */
	public function toggleAjax() {
		$this->ajaxOn = !$this->ajaxOn;
	}

	/**
	 * Processes requests coming from the view
	 */
	public function process() {
		// Control game flow
		if (isset($_POST['newGameButton'])) {
			// Start new game - no matter in what state the game is
			$this->bowlingGame->reset();
		} else if (isset($_POST['rollButton'])) {
			// Ball rolled if not game over
			if (!$this->bowlingGame->isGameOver()) {
				$this->bowlingGame->doRoll((isset($_POST['pins']) ? (array) $_POST['pins'] : array()));
			}
		} else if (isset($_POST['toggleAjaxButton'])) {
			// Toggle between traditional and ajax version
			$this->toggleAjax();
		}
		if (isset($_POST['ajaxRequest'])) {
			// Create Ajax response in XML format
			$this->createAjaxResponse();
		} else {
			// Output the game view - currently we have one view only
			include 'BowlingGameView.php';
		}
	}

	/**
	 * Creates Ajax response if in Ajax mode
	 */
	private function createAjaxResponse() {
		$xmlStr = chr(60) . "?xml version='1.0' encoding='utf-8'?" . chr(62) . "\n";
		$xmlStr .= "<response>\n";
		$xmlStr .= "<currentFrame>" . (string) ($this->bowlingGame->getCurrentFrameIndex() + 1) . "</currentFrame>\n";
		$xmlStr .= "<frames>\n";
		for ($frameIndex = 0; $frameIndex < 9; $frameIndex++) {
			$xmlStr .= "<frame>\n";
			$xmlStr .= "<firstRoll>" . (string) $this->bowlingGame->getFirstRollInFrame($frameIndex) . "</firstRoll>\n";
			$xmlStr .= "<secondRoll>" . (string) $this->bowlingGame->getSecondRollInFrame($frameIndex) . "</secondRoll>\n";
			$xmlStr .= "<score>" . (string) $this->bowlingGame->getScoreInFrame($frameIndex) . "</score>\n";
			$xmlStr .= "</frame>\n";
		}
		$xmlStr .= "<frame>\n";
		$xmlStr .= "<firstRoll>" . (string) $this->bowlingGame->getFirstRollInFrame(9) . "</firstRoll>\n";
		$xmlStr .= "<secondRoll>" . (string) $this->bowlingGame->getSecondRollInFrame(9) . "</secondRoll>\n";
		$xmlStr .= "<thirdRoll>" . (string) $this->bowlingGame->getThirdRollInFrame(9) . "</thirdRoll>\n";
		$xmlStr .= "<score>" . (string) $this->bowlingGame->getScoreInFrame(9) . "</score>\n";
		$xmlStr .= "</frame>\n";
		$xmlStr .= "</frames>\n";
		$xmlStr .= "<message>" . $this->bowlingGame->getMessage() . "</message>\n";
		$xmlStr .= "<pins>\n";
		for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
			$xmlStr .= "<pin>" . (string) ($this->bowlingGame->isPinStanding($pinIndex) ? 1 : 0) . "</pin>\n";
		}
		$xmlStr .= "</pins>\n";
		$xmlStr .= "<gameOver>" . (string) ($this->bowlingGame->isGameOver() ? 1 : 0) . "</gameOver>\n";
		$xmlStr .= "</response>\n";

		if ($xml = simplexml_load_string($xmlStr)) {
			header("Content-type:text/xml");
			echo $xmlStr;
		} else {
			echo "XML error";
		}
	}
}
?>