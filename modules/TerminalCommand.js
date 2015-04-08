var _ = require('underscore')
var exec = require("child_process").exec;

var TerminalCommand = function(arguments, callback) {
	var command = 'node request.js --max-old-space-size=512 '+arguments
	
	try {
		exec(command, function(err, stdout, stderr){
			console.log(command)
			console.log(stdout, stderr)
			console.log('----- ***** -----')

			callback()
		})
	} catch (err) {
		console.log(err)
	}
}

module.exports = TerminalCommand
