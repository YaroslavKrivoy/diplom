
// THEMEPUNCH INTERNAL HANDLINGS
if(typeof(console) === 'undefined') {
    var console = {};
    console.log = console.error = console.info = console.debug = console.warn = console.trace = console.dir = console.dirxml = console.group = console.groupEnd = console.time = console.timeEnd = console.assert = console.profile = console.groupCollapsed = function() {};
}

// SANDBOX GREENSOCK

var oldgs = window.GreenSockGlobals;
	oldgs_queue = window._gsQueue;

var punchgs = window.GreenSockGlobals = {};

var punchgsSandbox = {
    oldgs:          oldgs,
    oldgs_queue:    oldgs_queue
}
