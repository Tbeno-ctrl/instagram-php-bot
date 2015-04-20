var cookie = require('cookie');
var curl = require('curlrequest')
var argv = require('minimist')(process.argv.slice(2), {
	string: ['mediaId', 'userId']
})

/**
* Extract csrf and mid tokens out of headers
*/
var extractCookies = function(headers) {
	var temp = [];
	var mid = null;
	var csrftoken = null;

	headers = headers.split('\r\n');
	headers.forEach(function(header){
		var cookies = header.split('Set-Cookie: ')

		if(cookies.length == 2)
			temp.push(cookie.parse(cookies[1]))
	})

	for(var key in temp) {
		if('csrftoken' in temp[key]) {
			csrftoken = temp[key].csrftoken
		}

		if('mid' in temp[key]) {
			mid = temp[key].mid
		}
	}

	return {
		cookies: temp,
		mid: mid,
		csrftoken: csrftoken,
		data: headers.slice(-1)[0] 
	};
}

/**
* Merge two json objects properties
*/
var extend = function(obj1, obj2) {
    var temp = {};
    
    for (var attrname in obj1) { 
    	temp[attrname] = obj1[attrname]; 
    }
    
    for (var attrname in obj2) { 
    	temp[attrname] = obj2[attrname]; 
    }
    
    return temp;
}

/**
* Authorize on instagram using username/password 
* and save cookies with tokens
*/
var instagramAuth = function(username, password, callback) {
	//auth data
	this.username = username;
	this.password = password;

	//cookie data
	this.cookies = []
	this.csrftoken = null;
	this.mid = null;

	/**
	*
	* Receiving csrf token and mid token
	*/
	this.init = function()
	{
		curl.request({ 
			url: 'https://instagram.com/accounts/login/',
			include: true 
		}, function (err, headers) {
			var cookies = extractCookies(headers)

			this.cookies = cookies.cookies || []; 
			this.csrftoken = cookies.csrftoken || null; 
			this.mid = cookies.mid || null;

			//authorize after tokens have handled
			this.auth()
		}.bind(this))
	}

	/**
	*
	* Authorization with instagram credentials
	*/
	this.auth = function()
	{
		curl.request({ 
			url: 'https://instagram.com/accounts/login/ajax/',
			include: true,
			headers: { 
				'X-Instagram-AJAX': '1',
				'X-CSRFToken': this.csrftoken,
				'X-Requested-With': 'XMLHttpRequest',
				'Cookie': 'mid=' + this.mid + '; csrftoken=' + this.csrftoken,
				'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
				'Referer': 'https://instagram.com/accounts/login/ajax/?targetOrigin=https%3A%2F%2Finstagram.com'
			},
			data: {
				username: this.username,
				password: this.password
			}
		}, function(err, headers) {
			var cookies = extractCookies(headers)

			this.cookies = cookies.cookies || []; 
			this.csrftoken = cookies.csrftoken || null; 
			this.mid = cookies.mid || null;

			try {
				var status = JSON.parse(cookies.data)
				if(status.status !== 'ok' || status.authenticated !== true)
					throw "Authorization failed"
				
				callback.call(this)
			}
			catch(err) {
				callback.call(this, 'Failed')
			}
		})
	}

	this.init()
}


/**
* Instagram actions
* dependent on auth class
*/
var instagramActions = function(auth) {
	this.cookies = '';
	this.headers = {
		'X-Instagram-AJAX': '1',
		'X-Requested-With': 'XMLHttpRequest',
		'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
		'Referer': 'https://instagram.com/accounts/login/ajax/?targetOrigin=https%3A%2F%2Finstagram.com'
	}

	/**
	* Receiving csrf, mid, session, user tokens
	*/
	this.init = function()
	{
		var cookies = '';

		for(var key in auth.cookies) {
			if('csrftoken' in auth.cookies[key]) {
				cookies += cookie.serialize('csrftoken', auth.cookies[key].csrftoken)+'; '
			}

			if('sessionid' in auth.cookies[key]) {
				cookies += cookie.serialize('sessionid', auth.cookies[key].sessionid)+'; '
			}

			if('ds_user_id' in auth.cookies[key]) {
				cookies += cookie.serialize('ds_user_id', auth.cookies[key].ds_user_id)+'; '
			}

			if('mid' in auth.cookies[key]) {
				cookies += cookie.serialize('mid', auth.cookies[key].mid)+'; '
			}
		}

		this.cookies = cookies
	}

	/**
	*
	*/
	this.setLike = function(media_id, callback) {
		var headers = extend(this.headers, {
			'X-CSRFToken': auth.csrftoken,
			'Cookie': this.cookies
		})

		curl.request({ 
			url: 'https://instagram.com/web/likes/' + media_id + '/like/',
			headers: headers,
			method: 'post'
		}, function(err, headers) {
			try {
				callback.call(this, undefined, JSON.parse(headers))
			}
			catch(err) {
				callback.call(this, 'failed to setLike')
			}
		})
	}

	/**
	*
	*/
	this.setComment = function(media_id, comment, callback) {
		var headers = extend(this.headers, {
			'X-CSRFToken': auth.csrftoken,
			'Cookie': this.cookies
		})

		curl.request({ 
			url: 'https://instagram.com/web/comments/' + media_id + '/add/',
			headers: headers,
			data: {
				comment_text: comment
			}
		}, function(err, headers) {
			try {
				callback.call(this, undefined, JSON.parse(headers))
			}
			catch(err) {
				callback.call(this, 'failed to setComment')
			}
		})
	}

	/**
	*
	*/
	this.setFollow = function(user_id, callback) {
		var headers = extend(this.headers, {
			'X-CSRFToken': auth.csrftoken,
			'Cookie': this.cookies
		})

		curl.request({ 
			url: 'https://instagram.com/web/friendships/' + user_id + '/follow/',
			headers: headers,
			method: 'POST'
		}, function(err, headers) {
			try {
				callback.call(this, undefined, JSON.parse(headers))
			}
			catch(err) {
				callback.call(this, 'failed to setFollow')
			}
		})
	}

	/**
	*
	*/
	this.setUnfollow = function(user_id, callback) {
		var headers = extend(this.headers, {
			'X-CSRFToken': auth.csrftoken,
			'Cookie': this.cookies
		})

		curl.request({ 
			url: 'https://instagram.com/web/friendships/' + user_id + '/unfollow/',
			headers: headers,
			method: 'POST'
		}, function(err, headers) {
			try {
				callback.call(this, undefined, JSON.parse(headers))
			}
			catch(err) {
				callback.call(this, 'failed to setUnfollow')
			}
		})
	}

	this.init()
}


/**
*
* Likes
*
*/
if('username' in argv && 'password' in argv && 'setLike' in argv)
{
	var likes = new instagramAuth(argv.username, argv.password, function(err){

		if(err !== undefined) {
			console.log('unable to login')	
			return false;
		}

		var actions = new instagramActions(this);
		
		var like = actions.setLike(argv.mediaId, function(err, response){
			if(err !== undefined) {
				console.log('unable to setLike')
				return false;
			}
				
			console.log(response)
		})
	})
}

/**
*
* Comment
*
*/
else if('username' in argv && 'password' in argv && 'setComment' in argv && 'commentText' in argv)
{
	var auth = new instagramAuth(argv.username, argv.password, function(err){

		if(err !== undefined) {
			console.log('unable to login')	
			return false;
		}

		var actions = new instagramActions(this);

		var comment = actions.setComment(argv.mediaId, argv.commentText, function(err, response){
			if(err !== undefined) {
				console.log('unable to setComment')
				return false;
			}
				
			console.log(response)
		})
	})
}

/**
*
* Follows
*
*/
else if('username' in argv && 'password' in argv && 'setFollow' in argv)
{
	var follows = new instagramAuth(argv.username, argv.password, function(err){

		if(err !== undefined) {
			console.log('unable to login')	
			return false;
		}

		var actions = new instagramActions(this);

		var follow = actions.setFollow(argv.userId, function(err, response){
			if(err !== undefined) {
				console.log('unable to setFollow')
				return false;
			}
				
			console.log(response)
		})
	})
}

/**
*
* Unfollow
*
*/
else if('username' in argv && 'password' in argv && 'setUnfollow' in argv)
{
	var auth = new instagramAuth(argv.username, argv.password, function(err){

		if(err !== undefined) {
			console.log('unable to login')	
			return false;
		}

		var actions = new instagramActions(this);

		var unfollow = actions.setUnfollow(argv.userId, function(err, response){
			if(err !== undefined) {
				console.log('unable to setUnfollow')
				return false;
			}
			
			console.log(response)
		})
	})
}


/**
*
* Authorization
*
*/
else if('username' in argv && 'password' in argv)
{
	var auth = new instagramAuth(argv.username, argv.password, function(err){

		if(err !== undefined) {
			console.log('failure')
			return false;
		}

		console.log('success')
	});
}