var parties = {
	0: 'Partido 0',
	1: 'Partido 1',
	2: 'Partido 2',
	3: 'Partido 3'
};

var nparties = 4;

generateSets(10);

/* Generates n sets to be sent */
function generateSets(n) {
	var sets = new Array();
	
	for (var i = 0; i < n; i++) {
		var votesSet = new Array();
		
		for (var j = 0; j < nparties; j++) {
			var vote = new Object();
			vote.id = guid();
			vote.party = j;
			vote.name = parties[j];
			
			votesSet.push(vote);
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