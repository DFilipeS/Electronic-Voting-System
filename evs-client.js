var parties;
var publicKey;

$.ajax('voting-info.php', {
	success: function (data) {
		parties = data.parties;

		//$('#status').html(JSON.stringify(generateSets(2)));
		$('#status').html('');
	},
	error: function () {
		$('#status').html('An error occurred');
	}
});

/*var encrypted = CryptoJS.AES.encrypt("Hello AES!!!", "dani");
var decrypted = CryptoJS.AES.decrypt(encrypted, "dani");

console.log(encrypted.toString());
console.log(decrypted.toString(CryptoJS.enc.Latin1));*/

function handleFileSelect(evt) {
	var files = evt.target.files; // FileList object
    
	// files is a FileList of File objects. List some properties.
	var output = [];
	var f = files[0];
	var r = new FileReader();
	r.onload = function (e) {
		var contents = e.target.result;
		publicKey = contents;
		
		console.log(contents);
		$('#status').html('Public key loaded successfully');
	};
	
	r.readAsText(f);
}

/* Generates n sets to be sent */
function generateSets(n) {
	var sets = new Array();

	for (var i = 0; i < n; i++) {
		var votesSet = new Array();

		for (var key in parties) {
			if (parties.hasOwnProperty(key)) {
				var vote = new Object();
				vote.id = guid();
				vote.party = key;
				vote.name = parties[key];

				votesSet.push(vote);
			}
		}

		sets.push(votesSet);
	}

	return sets;
}

/* Generates a unique id for each vote */
function guid() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	}

	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}