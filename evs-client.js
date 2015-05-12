var parties;
var nparties;

$.ajax('voting-info.php', {
	success: function (data) {
		nparties = data.nparties;
		parties = data.parties;

		$('#status').html(JSON.stringify(generateSets(2)));
	},
	error: function () {
		$('#status').html('An error occurred');
	}
});

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