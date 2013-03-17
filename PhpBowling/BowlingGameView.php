<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>The Bowling Game</title>
<script type="text/javascript" src="BowlingGameAjax.js"></script>
</head>
<body>
	<h1>
		The Bowling Game -
		<?php echo ($this->isAjaxOn() ? 'Ajax version' : 'Traditional version'); ?>
	</h1>
	<!-- Main menu form -->
	<form name="mainMenuForm" id="mainMenuForm" method="post" action="">
		<p>
			<input name="newGameButton" id="newGameButton" type="<?php echo ($this->isAjaxOn() ? 'button' : 'submit'); ?>"
				value="New game" <?php echo ($this->isAjaxOn() ? 'onclick="newGame();"' : ''); ?>/>
			<input name="toggleAjaxButton" id="toggleAjaxButton" type="submit"
				value="<?php echo ($this->isAjaxOn() ? 'Traditional' : 'Ajax'); ?>" />
		</p>
	</form>
	<!-- Scoreboard -->
	<table border="1" cellpadding="2" cellspacing="0">
		<tr height="40px">
			<td colspan="21">Current frame: <span id="currentFrame"><?php echo $this->bowlingGame->getCurrentFrameIndex() + 1; ?>
			</span>
			</td>
		</tr>
		<tr height="40px">
			<?php
			// First row
			// First nine frames
			for ($frameIndex = 0; $frameIndex < 9; $frameIndex++) {
				?>
			<td width="40px" id="firstRoll<?php echo $frameIndex; ?>"><?php echo $this->bowlingGame->getFirstRollInFrame($frameIndex); ?>
			</td>
			<td width="40px" id="secondRoll<?php echo $frameIndex; ?>"><?php echo $this->bowlingGame->getSecondRollInFrame($frameIndex); ?>
			</td>
			<?php
			}
			// Tenth frame
			?>
			<td width="40px" id="firstRoll9"><?php echo $this->bowlingGame->getFirstRollInFrame(9); ?>
			</td>
			<td width="40px" id="secondRoll9"><?php echo $this->bowlingGame->getSecondRollInFrame(9); ?>
			</td>
			<td width="40px" id="thirdRoll9"><?php echo $this->bowlingGame->getThirdRollInFrame(9); ?>
			</td>
		</tr>
		<tr height="40px">
			<?php
			// Second row
			// First nine frames
			for ($frameIndex = 0; $frameIndex < 9; $frameIndex++) {
				?>
			<td colspan="2" id="score<?php echo $frameIndex; ?>"><?php echo $this->bowlingGame->getScoreInFrame($frameIndex);?>
			</td>
			<?php
			}
			// Tenth frame
			?>
			<td colspan="3" id="score9"><?php echo $this->bowlingGame->getScoreInFrame(9);?>
			</td>
		</tr>
		<tr>
			<td colspan="21">Message: <span id="message"><?php echo $this->bowlingGame->getMessage(); ?></span>
			</td>
		</tr>
	</table>
	<?php
	// View roll form
	if (!$this->bowlingGame->isGameOver()) {
		// Next roll form
		?>
	<!-- Next roll form -->
	<form name="rollForm" id="rollForm" method="post" action="">
		<p>
			<?php
			for ($pinIndex = 0; $pinIndex < 10; $pinIndex++) {
				?>
			<input name="pins[<?php echo $pinIndex; ?>]"
				id="pins[<?php echo $pinIndex; ?>]" type="checkbox" value="1"
				<?php echo ($this->bowlingGame->isPinStanding($pinIndex) ? '' : 'disabled="disabled "');?> />
			<?php
			}
			?>
			<input name="rollButton" id="rollButton" type="<?php echo ($this->isAjaxOn() ? 'button' : 'submit'); ?>" value="Roll" <?php echo ($this->isAjaxOn() ? 'onclick="roll();"' : ''); ?> />
			<input name="checkAllButton" id="checkAllButton" type="button"
				value="Check all" onclick="checkAll();" />
		</p>
	</form>
	<?php
	}
	?>
</body>
</html>
