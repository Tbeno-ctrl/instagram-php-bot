/**
* Setup
*/
var moment = require("moment")
var exec = require("child_process").exec;
var knex = require('knex')({
	client: 'mysql',
	connection: {
		host     : '127.0.0.1',
		user     : 'root',
		password : 'vgtL1adL0p',
		database : 'tamer',
		charset  : 'utf8'
	}
});
var bookshelf = require('bookshelf')(knex);
var _ = require('underscore')
var kue = require('kue')
var jobs = kue.createQueue();
kue.app.listen(3000);

//self modules
var InstagramTasks = require('./modules/InstagramTasks')

jobs.on( 'error', function( err ) {
  console.log( 'Oops... ', err );
});

/**
* Models
*/
var User = bookshelf.Model.extend({
	tableName: 'users',
	subscription: function() {
		return this.hasOne(Subscription);
	}
});

var Subscription = bookshelf.Model.extend({
	tableName: 'subscriptions',
	subscription: function() {
		return this.belongsTo(User);
	},
	locations: function() {
		return this.belongsToMany(Location, 'location_subscription');
	},
	tags: function() {
		return this.belongsToMany(Tag, 'subscription_tag');
	},
	comments: function() {
		return this.belongsToMany(Comment, 'comment_subscription');
	},
	user: function() {
		return this.belongsTo(User)
	}
});

var Tag = bookshelf.Model.extend({
	tableName: 'tags'
});

var Location = bookshelf.Model.extend({
	tableName: 'locations'
});

var Comment = bookshelf.Model.extend({
	tableName: 'comments'
});

var Sessionlog = bookshelf.Model.extend({
	tableName: 'sessionlogs'
});

var Task = bookshelf.Model.extend({
	tableName: 'tasks',
	hasTimestamps: ['created_at', 'updated_at']
});

/**
* Action
*/

var QueueSubscriptionTask = {
	setLikeInterval: 3.5 * 60000,
	setCommentInterval: 8 * 60000,
	setFollowInterval: 7 * 60000,

	setLike: function(subscription) {
		var job_name = 'subscription_' + subscription.id + '_setLike';

		jobs.process(job_name, function(job, done) {
			// repeat this job later
			jobs.create(job_name).delay(this.setLikeInterval).save();

			InstagramTasks.setLike(subscription, done)
		}.bind(this));

		this.runTask(job_name, 'setLike')
	},
	setComment: function(subscription) {
		var job_name = 'subscription_' + subscription.id + '_setComment';

		jobs.process(job_name, function(job, done) {
			// repeat this job later
			jobs.create(job_name).delay(this.setCommentInterval).save();

			InstagramTasks.setComment(subscription, done)
		}.bind(this));

		this.runTask(job_name, 'setComment')
	},
	setFollow: function(subscription) {
		var job_name = 'subscription_' + subscription.id + '_setFollow';

		jobs.process(job_name, function(job, done) {
			// repeat this job later
			jobs.create(job_name).delay(this.setFollowInterval).save();

			InstagramTasks.setFollow(subscription, done)
		}.bind(this));

		this.runTask(job_name, 'setFollow')
	},
	unsetFollow: function(subscription) {
		
	},
	runTask: function(name, action) {
		kue.Job.rangeByType(name, 'delayed', 0, 10, '', function(err, delayedJobs) {
			if(err) {
				console.log(err)
				return false;
			}

			if(!delayedJobs.length) {
				jobs.create(name).save(function(){
					
				});
			} else {
				var createdAt = new Date(parseInt(delayedJobs[0].created_at));
				var millisecondsAgo = parseInt(new Date() - createdAt);

				var interval = (this[action + 'Interval'] || 60 * 1000) + 10000;

				if(millisecondsAgo > interval) {
					delayedJobs[0].remove(function(){

					});
				}
			}

			// Start checking for delayed jobs. This defaults to checking every 5 seconds
			jobs.promote();
		}.bind(this));
	}
}

var QueueSubscription = function(subscription)
{
	if(subscription.attributes.likes_enabled)
		QueueSubscriptionTask.setLike(subscription)

	if(subscription.attributes.comments_enabled)
		QueueSubscriptionTask.setComment(subscription)
	
	if(subscription.attributes.follows_enabled)
		QueueSubscriptionTask.setFollow(subscription)
	
	if(subscription.attributes.unfollows_enabled)
		QueueSubscriptionTask.unsetFollow(subscription)
}

var getAvailableSubscriptions = function()
{
	new Subscription().fetchAll({
		withRelated: ['tags', 'comments', 'locations', 'user']
	}).then(function(subscriptions){
		_.each(subscriptions.models, function(subscription){
			QueueSubscription(subscription)
		})
	});
}

setInterval(getAvailableSubscriptions, 500)