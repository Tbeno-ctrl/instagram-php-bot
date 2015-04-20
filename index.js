// node request.js --username="evointeractive" --password="\$uccess" --setLike --mediaId="949537093535163002"

var ig = require('instagram-node').instagram();
ig.use({ access_token: '30674714.1677ed0.3aaff0aa80f34661846ade00457ac68e' });
var exec = require("child_process").exec;


// * likes - 2 minutes
setInterval(function(){
	var time = new Date();
	var now = time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds();

	ig.tag_media_recent('happy', function(err, medias, pagination, remaining, limit){
		var media_id = medias[0].id.split('_')[0]

		exec('node request.js --username="evointeractive" --password="success" --setLike --mediaId="' + media_id + '"', function(err, stdout, stderr){
			console.log('Like: ', media_id, stdout, ': ' + now)
		})
	})
}, 3.5 * 60000)

// * comments - 6 minute
setInterval(function(){
	var time = new Date();
	var now = time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds();

	ig.tag_media_recent('happy', function(err, medias, pagination, remaining, limit){
		var media_id = medias[0].id.split('_')[0]

		exec('node request.js --username="evointeractive" --password="success" --setComment --commentText="Nice photo ..." --mediaId="' + media_id + '"', function(err, stdout, stderr){
			console.log('Comment: ', media_id, stdout, ': ' + now)
		})
	})
}, 8 * 60000)

// * follow  - 5 min
setInterval(function(){
	var time = new Date();
	var now = time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds();

	ig.tag_media_recent('happy', function(err, medias, pagination, remaining, limit){
		var user_id = medias[0].user.id

		exec('node request.js --username="evointeractive" --password="success" --setFollow --userId="' + user_id + '"', function(err, stdout, stderr){
			console.log('Follow: ', user_id, stdout, ': ' + now)
		})
	})
}, 7 * 60000)