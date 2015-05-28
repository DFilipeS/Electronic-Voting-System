/*************************** Main ****************************/

var publicKey;
var privateKey;
var token;

var parties;
var votes;
var votesPasswords;

/*var encrypted = CryptoJS.AES.encrypt("Hello AES!!!", "dani");
var decrypted = CryptoJS.AES.decrypt(encrypted, "dani");

console.log(encrypted.toString());
console.log(decrypted.toString(CryptoJS.enc.Latin1));*/

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

/* Authenticate voter with the electoral committee */
function authenticateVoter() {
	var voterId = $('#voterNumber').val();
	publicKeyField = $('#filesPubKey').val();
	privateKeyField = $('#filesPrivKey').val();

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

function sendVotes() {
	console.log(votes);

	var request = $.ajax({
		url: "votes.php",
		type: "POST",
		data: {
			token: token,
			votes: votes
		},
		dataType: "html"
	});

	request.done(function (msg) {
		var res = JSON.parse(msg);

		if (!res.error) {
			console.log ("INFO: Chosen set = " + res.set);
		} else {
			console.log(res.error);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		console.log('An error occurred: ' + textStatus);
	});
}

/* Generates n sets to be sent */
function generateSets(n) {
	var sets = new Array();
	var setsPass = new Array();

	for (var i = 0; i < n; i++) {
		var votesSet = new Array();
		var votesSetPass = new Array();

		for (var key in parties) {
			if (parties.hasOwnProperty(key)) {
				var vote = new Object();
				vote.id = guid();
				vote.party = key;
				vote.name = parties[key];

				var voteE = hashVotes(vote);
				votesSet.push(voteE.encrypted);
				votesSetPass.push(voteE.passcode);
			}
		}
		sets.push(votesSet);
		setsPass.push(votesSetPass);
	}

	votes = sets;
	votesPasswords = setsPass;
}

/* Hash a set of votes */
function hashVotes(vote) {
	var passcode = guid();

	// Chipher with random passcode and add to KeysSet
	//var encrypted = CryptoJS.AES.encrypt(JSON.stringify(vote), passcode);
	var hashed = CryptoJS.HmacMD5(JSON.stringify(vote), passcode);

	//console.log("encrypted: " + encrypted.toString());

	/*Later to decrypt
	var decrypted = CryptoJS.AES.decrypt(encrypted, passcode);
	console.log("decrypted: "+JSON.parse(decrypted).toString(CryptoJS.enc.Latin1));*/

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
