/*************************** Main ****************************/

var publicKey;
var privateKey;
var token;
var chosenSet;

var parties;
var votes;
var votesClear;
var votesHashed;
var votesPasswords;
var chosenSetSignatures;

loadParties();

/************************* Functions *************************/

/* Handle file select fields of public and private keys */
function handleFileSelect(evt) {
	var files = evt.target.files; // FileList object

	// files is a FileList of File objects. List some properties.
	var output = [];
	var f = files[0];
	var r = new FileReader();
	r.onload = function (e) {
		var contents = e.target.result;

		if (evt.target.id == 'filesPubKey') {
			publicKey = contents;
			console.log('INFO: Public key loaded successfully');
		} else if (evt.target.id == 'filesPrivKey') {
			privateKey = contents;
			console.log('INFO: Private key loaded successfully');
		}
	};

	r.readAsText(f);
	//testRSA();
}

/* Loads parties information from server */
function loadParties() {
	$.ajax('voting-info.php', {
		success: function (data) {
			parties = data.parties;
			console.log('INFO: Parties loaded');

			generateSets(10);
		},
		error: function () {
			console.log('ERROR: An error ocurred loading parties');
		}
	});
}

/* Generates n sets to be sent */
function generateSets(n) {
	var setsClear = new Array();
	var setsHashed = new Array();
	var setsPass = new Array();

	for (var i = 0; i < n; i++) {
		var votesClearSet = new Array();
		var votesHashedSet = new Array();
		var votesSetPass = new Array();

		for (var key in parties) {
			if (parties.hasOwnProperty(key)) {
				var vote = new Object();
				vote.id = guid();
				vote.party = key;
				vote.name = parties[key];

				var voteE = hashVotes(vote);

				votesClearSet.push(vote);
				votesHashedSet.push(voteE.encrypted);
				votesSetPass.push(voteE.passcode);
			}
		}

		setsClear.push(votesClearSet);
		setsHashed.push(votesHashedSet);
		setsPass.push(votesSetPass);
	}

	votes = setsClear;
	votesHashed = setsHashed;
	votesPasswords = setsPass;
}

/* Hash a vote */
function hashVotes(vote) {
	var passcode = guid();
	var hashed = CryptoJS.HmacMD5(JSON.stringify(vote), passcode);

	var obj = new Object();
	obj.encrypted = hashed + "";
	obj.passcode = passcode;

	return obj;
}

/* Cipher a message with a public key */
function encryptRSA(key, message) {
	var encrypt = new JSEncrypt();
	encrypt.setPublicKey(key);
	return encrypt.encrypt(message);
}

/* Decipher a message with a private key */
function decryptRSA(key, encrypted) {
	var decrypt = new JSEncrypt();
	decrypt.setPrivateKey(key);
	return decrypt.decrypt(encrypted);
}

/* Generates a unique id for each vote */
function guid() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	}

	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

/* Authenticate voter with the electoral committee */
function authenticateVoter() {
	var voterId = $('#voterNumber').val();
	publicKeyField = $('#filesPubKey').val();
	privateKeyField = $('#filesPrivKey').val();

	var text = '<h1 style="text-align: center;">Processing...</h1>';
	text = text + '<p style="text-align: center;">Wait a moment please.</p>';

	$('#content').html(text);

	if (voterId != "" && publicKeyField != "" && privateKeyField != "") {
		var requestChallenge = $.ajax({
			url: "authentication.php",
			type: "POST",
			data: { voter_id: voterId },
			dataType: "html"
		});

		requestChallenge.done(function (msg) {
			$('#status').html('Request as been sent.');

			var challenge = JSON.parse(msg);

			if (!challenge.error) {
				var decrypt = new JSEncrypt();
				decrypt.setPrivateKey(privateKey);

				token = decrypt.decrypt(challenge.secret);

				sendVotes();

				console.log('INFO: token = ' + token);
			} else {
				console.log('ERROR: ' + challenge.error);
			}
		});

		requestChallenge.fail(function (jqXHR, textStatus) {
			$('#status').html('An error occurred: ' + textStatus);
		});
	} else {
		alert("É necessário preencher todos os campos!");
	}
}

/* Send sets of generated votes to server */
function sendVotes() {
	var request = $.ajax({
		url: "votes.php",
		type: "POST",
		data: {
			token: token,
			votes: votesHashed
		},
		dataType: "html"
	});

	request.done(function (msg) {
		var res = JSON.parse(msg);

		if (!res.error) {
			console.log ("INFO: Chosen set = " + res.set);

			chosenSet = res.set;
			sendVerificationVotes(chosenSet);
		} else {
			console.log(res.error);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		console.log('An error occurred: ' + textStatus);
	});
}

/* Sends n-1 sets of votes to server for verification */
function sendVerificationVotes(excludedVote) {
	var safeVoteSets = new Array();
	var safePassSets = new Array();

	for (var i = 0; i < votes.length; i++) {
		var safeVotes = new Array();
		var safePass = new Array();

		if (i != excludedVote) {
			for (var j = 0; j < votes[i].length; j++) {
				safeVotes.push(votes[i][j]);
				safePass.push(votesPasswords[i][j]);
			}
		}

		safeVoteSets.push(safeVotes);
		safePassSets.push(safePass);
	}

	var request = $.ajax({
		url: "votes.php",
		type: "POST",
		data: {
			token: token,
			votesSets: safeVoteSets,
			passSets: safePassSets
		},
		dataType: "html"
	});

	request.done(function (msg) {
		var res = JSON.parse(msg);

		if (res.ok) {
			console.log(res.ok);
			chosenSetSignatures = res.signatures;

			// TODO : Show form to choose the vote.
			var text = '<h1 style="text-align: center;">Pick a vote</h1>';
			text = text + '<div class="btn-group-vertical container" style="padding-left: 0px; padding-right: 30px;">';
			for (var i = 0; i < votes[chosenSet].length; i++) {
				text = text + '<button type="button" class="btn btn-primary" onclick="vote(this.value)" value="' + i + '">' + votes[chosenSet][i].name + '</button>';
			}

			text = text + '</div>';
			$('#content').html(text);
		} else {
			console.log(res.error);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		console.log('An error occurred: ' + textStatus);
	});
}

function vote(id) {
	var text = '<h1 style="text-align: center;">Processing...</h1>';
	text = text + '<p style="text-align: center;">Wait a moment please.</p>';

	$('#content').html(text);

	var finalVote = {};

	finalVote.token = token;
	finalVote.id = id;
	finalVote.signature = chosenSetSignatures[id];
	finalVote.vote = votes[chosenSet][id];

	var finalVoteText = JSON.stringify(finalVote);

	var finalVoteParts = new Array();
	while (finalVoteText.length > 100) {
		finalVoteParts.push(finalVoteText.substring(0,99));
		finalVoteText = finalVoteText.substring(99, finalVoteText.length);
	}
	finalVoteParts.push(finalVoteText);

	for (var i = 0; i < finalVoteParts.length; i++) {
		var res = encryptRSA(publicKey, JSON.stringify(finalVoteParts[i]));
		finalVoteParts[i] = res;
	}

	var request = $.ajax({
		url: "votes.php",
		type: "POST",
		data: {
			vote: finalVoteParts
		},
		dataType: "html"
	});

	request.done(function (msg) {
		var res = JSON.parse(msg);

		if (res.error) {
			console.log(res.error);
		} else {
			console.log('INFO: Voting complete.');

			var text = '<h1 style="text-align: center;">Voting complete!</h1>';
			text = text + '<p style="text-align: center;">Your vote id is <strong>' + votes[chosenSet][id].id + '</strong>. You can save this to check your vote later.</p>';

			$('#content').html(text);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		console.log('An error occurred: ' + textStatus);
	});
}
