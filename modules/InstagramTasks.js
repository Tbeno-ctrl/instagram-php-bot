var _ = require('underscore')
var ig = require('instagram-node').instagram();
ig.use({ access_token: '30674714.1677ed0.3aaff0aa80f34661846ade00457ac68e' });
var runTerminalCommand = require('./TerminalCommand')

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
var Task = bookshelf.Model.extend({
	tableName: 'tasks',
	hasTimestamps: ['created_at', 'updated_at']
});

var InstagramTasks = {
	setLike: function(subscription, callback) {
		var randomTag = _.sample(subscription.relations.tags.models);

		if(!randomTag) 
			return false;

		ig.tag_media_recent(randomTag.attributes.content, function(err, medias, pagination, remaining, limit){
			if(err) return false;
			
			var media = _.sample(medias)

			if(!media) return false;

			var mediaId = media.id.split('_')[0]
			runTerminalCommand('--username="'+subscription.relations.user.attributes.instagram_username+
				'" --password="'+subscription.relations.user.attributes.instagram_password+
				'" --mediaId="'+mediaId+
				'" --setLike',
				callback)

			Task.forge({
				subscription_id: subscription.id, 
				url: media.link, 
				media: mediaId,
				action: 'setLike', 
				status: 'complete'
			}).save()
		});
	},
	setComment: function(subscription, callback) {
		var randomTag = _.sample(subscription.relations.tags.models);

		if(!randomTag) 
			return false;
		
		ig.tag_media_recent(randomTag.attributes.content, function(err, medias, pagination, remaining, limit){
			if(err) return false;
			
			var media = _.sample(medias)
			var commentText = _.sample(subscription.relations.comments.models)

			if(!media) return false;
			if(!commentText) return false;

			var mediaId = media.id.split('_')[0]
			runTerminalCommand('--username="'+subscription.relations.user.attributes.instagram_username+
				'" --password="'+subscription.relations.user.attributes.instagram_password+
				'" --mediaId="'+mediaId+
				'" --commentText="'+commentText.attributes.content+
				'" --setComment', 
				callback)

			Task.forge({
				subscription_id: subscription.id, 
				url: media.link, 
				media: mediaId,
				text: commentText.attributes.content,
				action: 'setComment', 
				status: 'complete'
			}).save()
		});	
	},
	setFollow: function(subscription, callback) {
		var randomTag = _.sample(subscription.relations.tags.models);

		if(!randomTag) 
			return false;
		
		ig.tag_media_recent(randomTag.attributes.content, function(err, medias, pagination, remaining, limit){
			if(err) return false;
			
			var media = _.sample(medias)

			if(!media) return false;

			var mediaId = media.id.split('_')[0]
			runTerminalCommand('--username="'+subscription.relations.user.attributes.instagram_username+
				'" --password="'+subscription.relations.user.attributes.instagram_password+
				'" --userId="'+media.user.id+
				'" --setFollow', 
				callback)

			Task.forge({
				subscription_id: subscription.id, 
				url: media.link, 
				user: media.user.id,
				action: 'setFollow', 
				status: 'complete'
			}).save()
		});	
	},
	unsetFollow: function(subscription, callback) {
		var randomTag = _.sample(subscription.relations.tags.models);

		if(!randomTag) 
			return false;
		
		ig.tag_media_recent(randomTag.attributes.content, function(err, medias, pagination, remaining, limit){
			if(err) return false;
			
			var media = _.sample(medias)

			if(!media) return false;

			var mediaId = media.id.split('_')[0]
			runTerminalCommand('--username="'+subscription.relations.user.attributes.instagram_username+
				'" --password="'+subscription.relations.user.attributes.instagram_password+
				'" --userId="'+media.user.id+
				'" --unsetFollow', 
				callback)

			Task.forge({
				subscription_id: subscription.id, 
				url: media.link,
				user: media.user.id,
				action: 'unsetFollow', 
				status: 'complete'
			}).save()
		});
	}
}

module.exports = InstagramTasks