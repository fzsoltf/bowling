<?php
ini_set('display_errors', '1');

require_once 'BowlingGameController.php';

$bowlingGameController = new BowlingGameController();
$bowlingGameController->process();
?>