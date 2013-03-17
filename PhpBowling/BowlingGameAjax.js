/**
 * Get the browser XmlHttp object and make it do the work
 */
function getXmlHttp(params) {
	var xmlhttp;

	// Create ajax request
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	// Follow ready state
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			// Document ready
			var response = xmlhttp.responseXML.documentElement;
			if (!response) {
				alert("Response problem");
				return;
			}
			// Valid response (valid?-)
			refreshView(response);
		}
	};
	
	// Add ajax parameter
	params = "ajaxRequest=1&" + params;

	xmlhttp.open("POST", "index.php", true);
	xmlhttp.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader('Content-length', params.length);
	xmlhttp.send(params);
}

/**
 * Start a new game
 */
function newGame() {
	getXmlHttp("newGameButton=1");
}

/**
 * Roll next ball
 */
function roll() {
	params = "rollButton=1";
	for ( var i = 0; i < 10; i++) {
		if (document.getElementById("pins[" + i + "]").checked) {
			params += "&pins[" + i + "]=1";
			document.getElementById("pins[" + i + "]").checked = false;
			document.getElementById("pins[" + i + "]").disabled = true;
		}
	}
	getXmlHttp(params);
}

/**
 * Refresh values in HTML view
 */
function refreshView(response) {
	// Frame index
	var currentFrame = response.getElementsByTagName('currentFrame');
	document.getElementById("currentFrame").innerHTML = currentFrame[0].firstChild.nodeValue;

	// Frame rolls & scores
	var frames = response.getElementsByTagName('frame');
	for ( var i = 0; i < 10; i++) {
		var firstRoll = frames[i].getElementsByTagName('firstRoll');
		var secondRoll = frames[i].getElementsByTagName('secondRoll');
		if (firstRoll[0].firstChild) {
			document.getElementById("firstRoll" + i).innerHTML = firstRoll[0].firstChild.nodeValue;
		} else {
			document.getElementById("firstRoll" + i).innerHTML = '';
		}
		if (secondRoll[0].firstChild) {
			document.getElementById("secondRoll" + i).innerHTML = secondRoll[0].firstChild.nodeValue;
		} else {
			document.getElementById("secondRoll" + i).innerHTML = '';
		}
		if (9 == i) {
			var thirdRoll = frames[i].getElementsByTagName('thirdRoll');
			if (thirdRoll[0].firstChild) {
				document.getElementById("thirdRoll9").innerHTML = thirdRoll[0].firstChild.nodeValue;
			} else {
				document.getElementById("thirdRoll9").innerHTML = '';
			}
		}
		var score = frames[i].getElementsByTagName('score');
		if (score[0].firstChild) {
			document.getElementById("score" + i).innerHTML = score[0].firstChild.nodeValue;
		} else {
			document.getElementById("score" + i).innerHTML = '';
		}
	}

	// Message
	var message = response.getElementsByTagName('message');
	if (message[0].firstChild) {
		document.getElementById("message").innerHTML = message[0].firstChild.nodeValue;
	} else {
		document.getElementById("message").innerHTML = '';
	}

	// Roll form
	var gameOver = response.getElementsByTagName('gameOver');
	if (gameOver[0].firstChild && '1' == gameOver[0].firstChild.nodeValue) {
		document.getElementById("rollForm").style.visibility = 'hidden';
	} else {
		document.getElementById("rollForm").style.visibility = 'visible';
	}
	
	// Pins
	var pins = response.getElementsByTagName('pin');
	for ( var i = 0; i < pins.length; i++) {
		document.getElementById("pins[" + i + "]").checked = false;
		if ('1' == pins[i].firstChild.nodeValue) {
			document.getElementById("pins[" + i + "]").disabled = false;
		} else {
			document.getElementById("pins[" + i + "]").disabled = true;
		}
	}
}

// Check all pins in roll form
function checkAll(pins) {
	for (var i = 0; i < 10; i++) {
		var cb = document.getElementById('pins[' + i + ']');
		if (!cb.disabled) {
			cb.checked = true;
		}
	}
}