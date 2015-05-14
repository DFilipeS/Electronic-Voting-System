var parties;
var publicKey;
var id = "123";

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

var request = $.ajax({
	  url: "authentication.php",
	  type: "POST",
	  data: {id : id},
	  dataType: "html"
});

request.done(function(msg) {
	$('#status').html('Request as been sent.');
});

request.fail(function(jqXHR, textStatus) {
	$('#status').html('An error occurred: ' + textStatus);
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
	testRSA();
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
				
				var voteE = cryptSetAES(vote);
				votesSet.push(voteE.encrypted);
				votesSetPass.push(voteE.passcode);
			}
		}
		sets.push(votesSet);
		setsPass.push(votesSetPass);
	}

	return sets;
}

/* Chipher a vote set */
function cryptSetAES(vote) {
	var passcode = guid();
	
	//Chipher with random passcode and add to KeysSet
	var encrypted = CryptoJS.AES.encrypt(JSON.stringify(vote), passcode);
	console.log("encrypted: "+encrypted.toString());		

	/*Later to decrypt
	var decrypted = CryptoJS.AES.decrypt(encrypted, passcode);
	console.log("decrypted: "+JSON.parse(decrypted).toString(CryptoJS.enc.Latin1));*/
	
	var obj = new Object();
	obj.encrypted = encrypted;
	obj.passcode = passcode;
	
	return obj;
}

/* Test RSA related functions */
function testRSA(){
	var privkey = cryptico.generateRSAKey("password", 1024);
	var pubkey = cryptico.publicKeyString(privkey);   

	var testStr = "ola";
		
	var crypted = cryptRSA(testStr, pubkey);
	decryptRSA(crypted, privkey);
	
	var cryptedAndSigned = cryptAndSignRSA(testStr, pubkey, privkey);
	decryptRSA(cryptedAndSigned, privkey);
}

/* Chipher an object with a public key */
function cryptRSA(obj, pubKey){
	var EncryptionResult = cryptico.encrypt(JSON.stringify(obj), pubKey);
	console.log("cryptRSA: "+ EncryptionResult.cipher);
	
	return EncryptionResult;
}

/*Chipher an object with a public key and sign it with a private key*/
function cryptAndSignRSA(obj, pubKey, signKey){
	var EncryptionResult = cryptico.encrypt(JSON.stringify(obj), pubKey, signKey);
	
	// We can get pubKey  -> EncryptionResult.publickey
	console.log("cryptAndSignRSA: "+ EncryptionResult.cipher);
	
	return EncryptionResult;
}

/* Decrypts an object with a private key*/
function decryptRSA(obj, privKey){
	var DecryptionResult = cryptico.decrypt(obj.cipher, privKey);
	console.log("decryptRSA: "+ JSON.parse(DecryptionResult.plaintext)+"\n");
	
	return JSON.parse(DecryptionResult.plaintext);
}

/* Generates a unique id for each vote */
function guid() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	}

	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

//Keys Hash Map (uid,passcode)
var setKeys = {
	values: new Array(),	
	get: function(key){
		for(var i =0; i< this.values.length; i++){
			if(this.values[i].key == key){
				return this.values[i].value;
			}
		}
		return null;
	},
	add: function(key, value){
		var obj = new Object();
		obj.key = key;
		obj.value = value;	
		
		this.values.push(obj);
	}
}