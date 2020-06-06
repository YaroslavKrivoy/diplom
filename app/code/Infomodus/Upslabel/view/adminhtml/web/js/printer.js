require(
    [
        'jquery'
    ],
    function ($) {
        /*!
         * @overview  RSVP - a tiny implementation of Promises/A+.
         * @copyright Copyright (c) 2014 Yehuda Katz, Tom Dale, Stefan Penner and contributors
         * @license   Licensed under MIT license
         *            See https://raw.githubusercontent.com/tildeio/rsvp.js/master/LICENSE
         * @version   3.1.0
         */
            function lib$rsvp$utils$$objectOrFunction(x) {
                return typeof x === "function" || typeof x === "object" && x !== null
            }

            function lib$rsvp$utils$$isFunction(x) {
                return typeof x === "function"
            }

            function lib$rsvp$utils$$isMaybeThenable(x) {
                return typeof x === "object" && x !== null
            }

            var lib$rsvp$utils$$_isArray;
            if (!Array.isArray) {
                lib$rsvp$utils$$_isArray = function (x) {
                    return Object.prototype.toString.call(x) === "[object Array]"
                }
            } else {
                lib$rsvp$utils$$_isArray = Array.isArray
            }
            var lib$rsvp$utils$$isArray = lib$rsvp$utils$$_isArray;
            var lib$rsvp$utils$$now = Date.now || function () {
                    return (new Date).getTime()
                };

            function lib$rsvp$utils$$F() {
            }

            var lib$rsvp$utils$$o_create = Object.create || function (o) {
                    if (arguments.length > 1) {
                        throw new Error("Second argument not supported")
                    }
                    if (typeof o !== "object") {
                        throw new TypeError("Argument must be an object")
                    }
                    lib$rsvp$utils$$F.prototype = o;
                    return new lib$rsvp$utils$$F
                };

            function lib$rsvp$events$$indexOf(callbacks, callback) {
                for (var i = 0, l = callbacks.length; i < l; i++) {
                    if (callbacks[i] === callback) {
                        return i
                    }
                }
                return -1
            }

            function lib$rsvp$events$$callbacksFor(object) {
                var callbacks = object._promiseCallbacks;
                if (!callbacks) {
                    callbacks = object._promiseCallbacks = {}
                }
                return callbacks
            }

            var lib$rsvp$events$$default = {
                mixin: function (object) {
                    object["on"] = this["on"];
                    object["off"] = this["off"];
                    object["trigger"] = this["trigger"];
                    object._promiseCallbacks = undefined;
                    return object
                }, on: function (eventName, callback) {
                    if (typeof callback !== "function") {
                        throw new TypeError("Callback must be a function")
                    }
                    var allCallbacks = lib$rsvp$events$$callbacksFor(this), callbacks;
                    callbacks = allCallbacks[eventName];
                    if (!callbacks) {
                        callbacks = allCallbacks[eventName] = []
                    }
                    if (lib$rsvp$events$$indexOf(callbacks, callback) === -1) {
                        callbacks.push(callback)
                    }
                }, off: function (eventName, callback) {
                    var allCallbacks = lib$rsvp$events$$callbacksFor(this), callbacks, index;
                    if (!callback) {
                        allCallbacks[eventName] = [];
                        return
                    }
                    callbacks = allCallbacks[eventName];
                    index = lib$rsvp$events$$indexOf(callbacks, callback);
                    if (index !== -1) {
                        callbacks.splice(index, 1)
                    }
                }, trigger: function (eventName, options, label) {
                    var allCallbacks = lib$rsvp$events$$callbacksFor(this), callbacks, callback;
                    if (callbacks = allCallbacks[eventName]) {
                        for (var i = 0; i < callbacks.length; i++) {
                            callback = callbacks[i];
                            callback(options, label)
                        }
                    }
                }
            };
            var lib$rsvp$config$$config = {instrument: false};
            lib$rsvp$events$$default["mixin"](lib$rsvp$config$$config);
            function lib$rsvp$config$$configure(name, value) {
                if (name === "onerror") {
                    lib$rsvp$config$$config["on"]("error", value);
                    return
                }
                if (arguments.length === 2) {
                    lib$rsvp$config$$config[name] = value
                } else {
                    return lib$rsvp$config$$config[name]
                }
            }

            var lib$rsvp$instrument$$queue = [];

            function lib$rsvp$instrument$$scheduleFlush() {
                setTimeout(function () {
                    var entry;
                    for (var i = 0; i < lib$rsvp$instrument$$queue.length; i++) {
                        entry = lib$rsvp$instrument$$queue[i];
                        var payload = entry.payload;
                        payload.guid = payload.key + payload.id;
                        payload.childGuid = payload.key + payload.childId;
                        if (payload.error) {
                            payload.stack = payload.error.stack
                        }
                        lib$rsvp$config$$config["trigger"](entry.name, entry.payload)
                    }
                    lib$rsvp$instrument$$queue.length = 0
                }, 50)
            }

            function lib$rsvp$instrument$$instrument(eventName, promise, child) {
                if (1 === lib$rsvp$instrument$$queue.push({
                        name: eventName,
                        payload: {
                            key: promise._guidKey,
                            id: promise._id,
                            eventName: eventName,
                            detail: promise._result,
                            childId: child && child._id,
                            label: promise._label,
                            timeStamp: lib$rsvp$utils$$now(),
                            error: lib$rsvp$config$$config["instrument-with-stack"] ? new Error(promise._label) : null
                        }
                    })) {
                    lib$rsvp$instrument$$scheduleFlush()
                }
            }

            var lib$rsvp$instrument$$default = lib$rsvp$instrument$$instrument;

            function lib$rsvp$$internal$$withOwnPromise() {
                return new TypeError("A promises callback cannot return that same promise.")
            }

            function lib$rsvp$$internal$$noop() {
            }

            var lib$rsvp$$internal$$PENDING = void 0;
            var lib$rsvp$$internal$$FULFILLED = 1;
            var lib$rsvp$$internal$$REJECTED = 2;
            var lib$rsvp$$internal$$GET_THEN_ERROR = new lib$rsvp$$internal$$ErrorObject;

            function lib$rsvp$$internal$$getThen(promise) {
                try {
                    return promise.then
                } catch (error) {
                    lib$rsvp$$internal$$GET_THEN_ERROR.error = error;
                    return lib$rsvp$$internal$$GET_THEN_ERROR
                }
            }

            function lib$rsvp$$internal$$tryThen(then, value, fulfillmentHandler, rejectionHandler) {
                try {
                    then.call(value, fulfillmentHandler, rejectionHandler)
                } catch (e) {
                    return e
                }
            }

            function lib$rsvp$$internal$$handleForeignThenable(promise, thenable, then) {
                lib$rsvp$config$$config.async(function (promise) {
                    var sealed = false;
                    var error = lib$rsvp$$internal$$tryThen(then, thenable, function (value) {
                        if (sealed) {
                            return
                        }
                        sealed = true;
                        if (thenable !== value) {
                            lib$rsvp$$internal$$resolve(promise, value)
                        } else {
                            lib$rsvp$$internal$$fulfill(promise, value)
                        }
                    }, function (reason) {
                        if (sealed) {
                            return
                        }
                        sealed = true;
                        lib$rsvp$$internal$$reject(promise, reason)
                    }, "Settle: " + (promise._label || " unknown promise"));
                    if (!sealed && error) {
                        sealed = true;
                        lib$rsvp$$internal$$reject(promise, error)
                    }
                }, promise)
            }

            function lib$rsvp$$internal$$handleOwnThenable(promise, thenable) {
                if (thenable._state === lib$rsvp$$internal$$FULFILLED) {
                    lib$rsvp$$internal$$fulfill(promise, thenable._result)
                } else if (thenable._state === lib$rsvp$$internal$$REJECTED) {
                    thenable._onError = null;
                    lib$rsvp$$internal$$reject(promise, thenable._result)
                } else {
                    lib$rsvp$$internal$$subscribe(thenable, undefined, function (value) {
                        if (thenable !== value) {
                            lib$rsvp$$internal$$resolve(promise, value)
                        } else {
                            lib$rsvp$$internal$$fulfill(promise, value)
                        }
                    }, function (reason) {
                        lib$rsvp$$internal$$reject(promise, reason)
                    })
                }
            }

            function lib$rsvp$$internal$$handleMaybeThenable(promise, maybeThenable) {
                if (maybeThenable.constructor === promise.constructor) {
                    lib$rsvp$$internal$$handleOwnThenable(promise, maybeThenable)
                } else {
                    var then = lib$rsvp$$internal$$getThen(maybeThenable);
                    if (then === lib$rsvp$$internal$$GET_THEN_ERROR) {
                        lib$rsvp$$internal$$reject(promise, lib$rsvp$$internal$$GET_THEN_ERROR.error)
                    } else if (then === undefined) {
                        lib$rsvp$$internal$$fulfill(promise, maybeThenable)
                    } else if (lib$rsvp$utils$$isFunction(then)) {
                        lib$rsvp$$internal$$handleForeignThenable(promise, maybeThenable, then)
                    } else {
                        lib$rsvp$$internal$$fulfill(promise, maybeThenable)
                    }
                }
            }

            function lib$rsvp$$internal$$resolve(promise, value) {
                if (promise === value) {
                    lib$rsvp$$internal$$fulfill(promise, value)
                } else if (lib$rsvp$utils$$objectOrFunction(value)) {
                    lib$rsvp$$internal$$handleMaybeThenable(promise, value)
                } else {
                    lib$rsvp$$internal$$fulfill(promise, value)
                }
            }

            function lib$rsvp$$internal$$publishRejection(promise) {
                if (promise._onError) {
                    promise._onError(promise._result)
                }
                lib$rsvp$$internal$$publish(promise)
            }

            function lib$rsvp$$internal$$fulfill(promise, value) {
                if (promise._state !== lib$rsvp$$internal$$PENDING) {
                    return
                }
                promise._result = value;
                promise._state = lib$rsvp$$internal$$FULFILLED;
                if (promise._subscribers.length === 0) {
                    if (lib$rsvp$config$$config.instrument) {
                        lib$rsvp$instrument$$default("fulfilled", promise)
                    }
                } else {
                    lib$rsvp$config$$config.async(lib$rsvp$$internal$$publish, promise)
                }
            }

            function lib$rsvp$$internal$$reject(promise, reason) {
                if (promise._state !== lib$rsvp$$internal$$PENDING) {
                    return
                }
                promise._state = lib$rsvp$$internal$$REJECTED;
                promise._result = reason;
                lib$rsvp$config$$config.async(lib$rsvp$$internal$$publishRejection, promise)
            }

            function lib$rsvp$$internal$$subscribe(parent, child, onFulfillment, onRejection) {
                var subscribers = parent._subscribers;
                var length = subscribers.length;
                parent._onError = null;
                subscribers[length] = child;
                subscribers[length + lib$rsvp$$internal$$FULFILLED] = onFulfillment;
                subscribers[length + lib$rsvp$$internal$$REJECTED] = onRejection;
                if (length === 0 && parent._state) {
                    lib$rsvp$config$$config.async(lib$rsvp$$internal$$publish, parent)
                }
            }

            function lib$rsvp$$internal$$publish(promise) {
                var subscribers = promise._subscribers;
                var settled = promise._state;
                if (lib$rsvp$config$$config.instrument) {
                    lib$rsvp$instrument$$default(settled === lib$rsvp$$internal$$FULFILLED ? "fulfilled" : "rejected", promise)
                }
                if (subscribers.length === 0) {
                    return
                }
                var child, callback, detail = promise._result;
                for (var i = 0; i < subscribers.length; i += 3) {
                    child = subscribers[i];
                    callback = subscribers[i + settled];
                    if (child) {
                        lib$rsvp$$internal$$invokeCallback(settled, child, callback, detail)
                    } else {
                        callback(detail)
                    }
                }
                promise._subscribers.length = 0
            }

            function lib$rsvp$$internal$$ErrorObject() {
                this.error = null
            }

            var lib$rsvp$$internal$$TRY_CATCH_ERROR = new lib$rsvp$$internal$$ErrorObject;

            function lib$rsvp$$internal$$tryCatch(callback, detail) {
                try {
                    return callback(detail)
                } catch (e) {
                    lib$rsvp$$internal$$TRY_CATCH_ERROR.error = e;
                    return lib$rsvp$$internal$$TRY_CATCH_ERROR
                }
            }

            function lib$rsvp$$internal$$invokeCallback(settled, promise, callback, detail) {
                var hasCallback = lib$rsvp$utils$$isFunction(callback), value, error, succeeded, failed;
                if (hasCallback) {
                    value = lib$rsvp$$internal$$tryCatch(callback, detail);
                    if (value === lib$rsvp$$internal$$TRY_CATCH_ERROR) {
                        failed = true;
                        error = value.error;
                        value = null
                    } else {
                        succeeded = true
                    }
                    if (promise === value) {
                        lib$rsvp$$internal$$reject(promise, lib$rsvp$$internal$$withOwnPromise());
                        return
                    }
                } else {
                    value = detail;
                    succeeded = true
                }
                if (promise._state !== lib$rsvp$$internal$$PENDING) {
                } else if (hasCallback && succeeded) {
                    lib$rsvp$$internal$$resolve(promise, value)
                } else if (failed) {
                    lib$rsvp$$internal$$reject(promise, error)
                } else if (settled === lib$rsvp$$internal$$FULFILLED) {
                    lib$rsvp$$internal$$fulfill(promise, value)
                } else if (settled === lib$rsvp$$internal$$REJECTED) {
                    lib$rsvp$$internal$$reject(promise, value)
                }
            }

            function lib$rsvp$$internal$$initializePromise(promise, resolver) {
                var resolved = false;
                try {
                    resolver(function resolvePromise(value) {
                        if (resolved) {
                            return
                        }
                        resolved = true;
                        lib$rsvp$$internal$$resolve(promise, value)
                    }, function rejectPromise(reason) {
                        if (resolved) {
                            return
                        }
                        resolved = true;
                        lib$rsvp$$internal$$reject(promise, reason)
                    })
                } catch (e) {
                    lib$rsvp$$internal$$reject(promise, e)
                }
            }

            function lib$rsvp$enumerator$$makeSettledResult(state, position, value) {
                if (state === lib$rsvp$$internal$$FULFILLED) {
                    return {state: "fulfilled", value: value}
                } else {
                    return {state: "rejected", reason: value}
                }
            }

            function lib$rsvp$enumerator$$Enumerator(Constructor, input, abortOnReject, label) {
                var enumerator = this;
                enumerator._instanceConstructor = Constructor;
                enumerator.promise = new Constructor(lib$rsvp$$internal$$noop, label);
                enumerator._abortOnReject = abortOnReject;
                if (enumerator._validateInput(input)) {
                    enumerator._input = input;
                    enumerator.length = input.length;
                    enumerator._remaining = input.length;
                    enumerator._init();
                    if (enumerator.length === 0) {
                        lib$rsvp$$internal$$fulfill(enumerator.promise, enumerator._result)
                    } else {
                        enumerator.length = enumerator.length || 0;
                        enumerator._enumerate();
                        if (enumerator._remaining === 0) {
                            lib$rsvp$$internal$$fulfill(enumerator.promise, enumerator._result)
                        }
                    }
                } else {
                    lib$rsvp$$internal$$reject(enumerator.promise, enumerator._validationError())
                }
            }

            var lib$rsvp$enumerator$$default = lib$rsvp$enumerator$$Enumerator;
            lib$rsvp$enumerator$$Enumerator.prototype._validateInput = function (input) {
                return lib$rsvp$utils$$isArray(input)
            };
            lib$rsvp$enumerator$$Enumerator.prototype._validationError = function () {
                return new Error("Array Methods must be provided an Array")
            };
            lib$rsvp$enumerator$$Enumerator.prototype._init = function () {
                this._result = new Array(this.length)
            };
            lib$rsvp$enumerator$$Enumerator.prototype._enumerate = function () {
                var enumerator = this;
                var length = enumerator.length;
                var promise = enumerator.promise;
                var input = enumerator._input;
                for (var i = 0; promise._state === lib$rsvp$$internal$$PENDING && i < length; i++) {
                    enumerator._eachEntry(input[i], i)
                }
            };
            lib$rsvp$enumerator$$Enumerator.prototype._eachEntry = function (entry, i) {
                var enumerator = this;
                var c = enumerator._instanceConstructor;
                if (lib$rsvp$utils$$isMaybeThenable(entry)) {
                    if (entry.constructor === c && entry._state !== lib$rsvp$$internal$$PENDING) {
                        entry._onError = null;
                        enumerator._settledAt(entry._state, i, entry._result)
                    } else {
                        enumerator._willSettleAt(c.resolve(entry), i)
                    }
                } else {
                    enumerator._remaining--;
                    enumerator._result[i] = enumerator._makeResult(lib$rsvp$$internal$$FULFILLED, i, entry)
                }
            };
            lib$rsvp$enumerator$$Enumerator.prototype._settledAt = function (state, i, value) {
                var enumerator = this;
                var promise = enumerator.promise;
                if (promise._state === lib$rsvp$$internal$$PENDING) {
                    enumerator._remaining--;
                    if (enumerator._abortOnReject && state === lib$rsvp$$internal$$REJECTED) {
                        lib$rsvp$$internal$$reject(promise, value)
                    } else {
                        enumerator._result[i] = enumerator._makeResult(state, i, value)
                    }
                }
                if (enumerator._remaining === 0) {
                    lib$rsvp$$internal$$fulfill(promise, enumerator._result)
                }
            };
            lib$rsvp$enumerator$$Enumerator.prototype._makeResult = function (state, i, value) {
                return value
            };
            lib$rsvp$enumerator$$Enumerator.prototype._willSettleAt = function (promise, i) {
                var enumerator = this;
                lib$rsvp$$internal$$subscribe(promise, undefined, function (value) {
                    enumerator._settledAt(lib$rsvp$$internal$$FULFILLED, i, value)
                }, function (reason) {
                    enumerator._settledAt(lib$rsvp$$internal$$REJECTED, i, reason)
                })
            };
            function lib$rsvp$promise$all$$all(entries, label) {
                return new lib$rsvp$enumerator$$default(this, entries, true, label).promise
            }

            var lib$rsvp$promise$all$$default = lib$rsvp$promise$all$$all;

            function lib$rsvp$promise$race$$race(entries, label) {
                var Constructor = this;
                var promise = new Constructor(lib$rsvp$$internal$$noop, label);
                if (!lib$rsvp$utils$$isArray(entries)) {
                    lib$rsvp$$internal$$reject(promise, new TypeError("You must pass an array to race."));
                    return promise
                }
                var length = entries.length;

                function onFulfillment(value) {
                    lib$rsvp$$internal$$resolve(promise, value)
                }

                function onRejection(reason) {
                    lib$rsvp$$internal$$reject(promise, reason)
                }

                for (var i = 0; promise._state === lib$rsvp$$internal$$PENDING && i < length; i++) {
                    lib$rsvp$$internal$$subscribe(Constructor.resolve(entries[i]), undefined, onFulfillment, onRejection)
                }
                return promise
            }

            var lib$rsvp$promise$race$$default = lib$rsvp$promise$race$$race;

            function lib$rsvp$promise$resolve$$resolve(object, label) {
                var Constructor = this;
                if (object && typeof object === "object" && object.constructor === Constructor) {
                    return object
                }
                var promise = new Constructor(lib$rsvp$$internal$$noop, label);
                lib$rsvp$$internal$$resolve(promise, object);
                return promise
            }

            var lib$rsvp$promise$resolve$$default = lib$rsvp$promise$resolve$$resolve;

            function lib$rsvp$promise$reject$$reject(reason, label) {
                var Constructor = this;
                var promise = new Constructor(lib$rsvp$$internal$$noop, label);
                lib$rsvp$$internal$$reject(promise, reason);
                return promise
            }

            var lib$rsvp$promise$reject$$default = lib$rsvp$promise$reject$$reject;
            var lib$rsvp$promise$$guidKey = "rsvp_" + lib$rsvp$utils$$now() + "-";
            var lib$rsvp$promise$$counter = 0;

            function lib$rsvp$promise$$needsResolver() {
                throw new TypeError("You must pass a resolver function as the first argument to the promise constructor")
            }

            function lib$rsvp$promise$$needsNew() {
                throw new TypeError("Failed to construct 'Promise': Please use the 'new' operator, this object constructor cannot be called as a function.")
            }

            function lib$rsvp$promise$$Promise(resolver, label) {
                var promise = this;
                promise._id = lib$rsvp$promise$$counter++;
                promise._label = label;
                promise._state = undefined;
                promise._result = undefined;
                promise._subscribers = [];
                if (lib$rsvp$config$$config.instrument) {
                    lib$rsvp$instrument$$default("created", promise)
                }
                if (lib$rsvp$$internal$$noop !== resolver) {
                    if (!lib$rsvp$utils$$isFunction(resolver)) {
                        lib$rsvp$promise$$needsResolver()
                    }
                    if (!(promise instanceof lib$rsvp$promise$$Promise)) {
                        lib$rsvp$promise$$needsNew()
                    }
                    lib$rsvp$$internal$$initializePromise(promise, resolver)
                }
            }

            var lib$rsvp$promise$$default = lib$rsvp$promise$$Promise;
            lib$rsvp$promise$$Promise.cast = lib$rsvp$promise$resolve$$default;
            lib$rsvp$promise$$Promise.all = lib$rsvp$promise$all$$default;
            lib$rsvp$promise$$Promise.race = lib$rsvp$promise$race$$default;
            lib$rsvp$promise$$Promise.resolve = lib$rsvp$promise$resolve$$default;
            lib$rsvp$promise$$Promise.reject = lib$rsvp$promise$reject$$default;
            lib$rsvp$promise$$Promise.prototype = {
                constructor: lib$rsvp$promise$$Promise,
                _guidKey: lib$rsvp$promise$$guidKey,
                _onError: function (reason) {
                    var promise = this;
                    lib$rsvp$config$$config.after(function () {
                        if (promise._onError) {
                            lib$rsvp$config$$config["trigger"]("error", reason, promise._label)
                        }
                    })
                },
                then: function (onFulfillment, onRejection, label) {
                    var parent = this;
                    var state = parent._state;
                    if (state === lib$rsvp$$internal$$FULFILLED && !onFulfillment || state === lib$rsvp$$internal$$REJECTED && !onRejection) {
                        if (lib$rsvp$config$$config.instrument) {
                            lib$rsvp$instrument$$default("chained", parent, parent)
                        }
                        return parent
                    }
                    parent._onError = null;
                    var child = new parent.constructor(lib$rsvp$$internal$$noop, label);
                    var result = parent._result;
                    if (lib$rsvp$config$$config.instrument) {
                        lib$rsvp$instrument$$default("chained", parent, child)
                    }
                    if (state) {
                        var callback = arguments[state - 1];
                        lib$rsvp$config$$config.async(function () {
                            lib$rsvp$$internal$$invokeCallback(state, child, callback, result)
                        })
                    } else {
                        lib$rsvp$$internal$$subscribe(parent, child, onFulfillment, onRejection)
                    }
                    return child
                },
                "catch": function (onRejection, label) {
                    return this.then(undefined, onRejection, label)
                },
                "finally": function (callback, label) {
                    var promise = this;
                    var constructor = promise.constructor;
                    return promise.then(function (value) {
                        return constructor.resolve(callback()).then(function () {
                            return value
                        })
                    }, function (reason) {
                        return constructor.resolve(callback()).then(function () {
                            throw reason
                        })
                    }, label)
                }
            };
            function lib$rsvp$all$settled$$AllSettled(Constructor, entries, label) {
                this._superConstructor(Constructor, entries, false, label)
            }

            lib$rsvp$all$settled$$AllSettled.prototype = lib$rsvp$utils$$o_create(lib$rsvp$enumerator$$default.prototype);
            lib$rsvp$all$settled$$AllSettled.prototype._superConstructor = lib$rsvp$enumerator$$default;
            lib$rsvp$all$settled$$AllSettled.prototype._makeResult = lib$rsvp$enumerator$$makeSettledResult;
            lib$rsvp$all$settled$$AllSettled.prototype._validationError = function () {
                return new Error("allSettled must be called with an array")
            };
            function lib$rsvp$all$settled$$allSettled(entries, label) {
                return new lib$rsvp$all$settled$$AllSettled(lib$rsvp$promise$$default, entries, label).promise
            }

            var lib$rsvp$all$settled$$default = lib$rsvp$all$settled$$allSettled;

            function lib$rsvp$all$$all(array, label) {
                return lib$rsvp$promise$$default.all(array, label)
            }

            var lib$rsvp$all$$default = lib$rsvp$all$$all;
            var lib$rsvp$asap$$len = 0;
            var lib$rsvp$asap$$toString = {}.toString;
            var lib$rsvp$asap$$vertxNext;

            function lib$rsvp$asap$$asap(callback, arg) {
                lib$rsvp$asap$$queue[lib$rsvp$asap$$len] = callback;
                lib$rsvp$asap$$queue[lib$rsvp$asap$$len + 1] = arg;
                lib$rsvp$asap$$len += 2;
                if (lib$rsvp$asap$$len === 2) {
                    lib$rsvp$asap$$scheduleFlush()
                }
            }

            var lib$rsvp$asap$$default = lib$rsvp$asap$$asap;
            var lib$rsvp$asap$$browserWindow = typeof window !== "undefined" ? window : undefined;
            var lib$rsvp$asap$$browserGlobal = lib$rsvp$asap$$browserWindow || {};
            var lib$rsvp$asap$$BrowserMutationObserver = lib$rsvp$asap$$browserGlobal.MutationObserver || lib$rsvp$asap$$browserGlobal.WebKitMutationObserver;
            var lib$rsvp$asap$$isNode = typeof self === "undefined" && typeof process !== "undefined" && {}.toString.call(process) === "[object process]";
            var lib$rsvp$asap$$isWorker = typeof Uint8ClampedArray !== "undefined" && typeof importScripts !== "undefined" && typeof MessageChannel !== "undefined";

            function lib$rsvp$asap$$useNextTick() {
                var nextTick = process.nextTick;
                var version = process.versions.node.match(/^(?:(\d+)\.)?(?:(\d+)\.)?(\*|\d+)$/);
                if (Array.isArray(version) && version[1] === "0" && version[2] === "10") {
                    nextTick = setImmediate
                }
                return function () {
                    nextTick(lib$rsvp$asap$$flush)
                }
            }

            function lib$rsvp$asap$$useVertxTimer() {
                return function () {
                    lib$rsvp$asap$$vertxNext(lib$rsvp$asap$$flush)
                }
            }

            function lib$rsvp$asap$$useMutationObserver() {
                var iterations = 0;
                var observer = new lib$rsvp$asap$$BrowserMutationObserver(lib$rsvp$asap$$flush);
                var node = document.createTextNode("");
                observer.observe(node, {characterData: true});
                return function () {
                    node.data = iterations = ++iterations % 2
                }
            }

            function lib$rsvp$asap$$useMessageChannel() {
                var channel = new MessageChannel;
                channel.port1.onmessage = lib$rsvp$asap$$flush;
                return function () {
                    channel.port2.postMessage(0)
                }
            }

            function lib$rsvp$asap$$useSetTimeout() {
                return function () {
                    setTimeout(lib$rsvp$asap$$flush, 1)
                }
            }

            var lib$rsvp$asap$$queue = new Array(1e3);

            function lib$rsvp$asap$$flush() {
                for (var i = 0; i < lib$rsvp$asap$$len; i += 2) {
                    var callback = lib$rsvp$asap$$queue[i];
                    var arg = lib$rsvp$asap$$queue[i + 1];
                    callback(arg);
                    lib$rsvp$asap$$queue[i] = undefined;
                    lib$rsvp$asap$$queue[i + 1] = undefined
                }
                lib$rsvp$asap$$len = 0
            }

            function lib$rsvp$asap$$attemptVertex() {
                try {
                    var r = require;
                    var vertx = r("vertx");
                    lib$rsvp$asap$$vertxNext = vertx.runOnLoop || vertx.runOnContext;
                    return lib$rsvp$asap$$useVertxTimer()
                } catch (e) {
                    return lib$rsvp$asap$$useSetTimeout()
                }
            }

            var lib$rsvp$asap$$scheduleFlush;
            if (lib$rsvp$asap$$isNode) {
                lib$rsvp$asap$$scheduleFlush = lib$rsvp$asap$$useNextTick()
            } else if (lib$rsvp$asap$$BrowserMutationObserver) {
                lib$rsvp$asap$$scheduleFlush = lib$rsvp$asap$$useMutationObserver()
            } else if (lib$rsvp$asap$$isWorker) {
                lib$rsvp$asap$$scheduleFlush = lib$rsvp$asap$$useMessageChannel()
            } else if (lib$rsvp$asap$$browserWindow === undefined && typeof require === "function") {
                lib$rsvp$asap$$scheduleFlush = lib$rsvp$asap$$attemptVertex()
            } else {
                lib$rsvp$asap$$scheduleFlush = lib$rsvp$asap$$useSetTimeout()
            }
            function lib$rsvp$defer$$defer(label) {
                var deferred = {};
                deferred["promise"] = new lib$rsvp$promise$$default(function (resolve, reject) {
                    deferred["resolve"] = resolve;
                    deferred["reject"] = reject
                }, label);
                return deferred
            }

            var lib$rsvp$defer$$default = lib$rsvp$defer$$defer;

            function lib$rsvp$filter$$filter(promises, filterFn, label) {
                return lib$rsvp$promise$$default.all(promises, label).then(function (values) {
                    if (!lib$rsvp$utils$$isFunction(filterFn)) {
                        throw new TypeError("You must pass a function as filter's second argument.")
                    }
                    var length = values.length;
                    var filtered = new Array(length);
                    for (var i = 0; i < length; i++) {
                        filtered[i] = filterFn(values[i])
                    }
                    return lib$rsvp$promise$$default.all(filtered, label).then(function (filtered) {
                        var results = new Array(length);
                        var newLength = 0;
                        for (var i = 0; i < length; i++) {
                            if (filtered[i]) {
                                results[newLength] = values[i];
                                newLength++
                            }
                        }
                        results.length = newLength;
                        return results
                    })
                })
            }

            var lib$rsvp$filter$$default = lib$rsvp$filter$$filter;

            function lib$rsvp$promise$hash$$PromiseHash(Constructor, object, label) {
                this._superConstructor(Constructor, object, true, label)
            }

            var lib$rsvp$promise$hash$$default = lib$rsvp$promise$hash$$PromiseHash;
            lib$rsvp$promise$hash$$PromiseHash.prototype = lib$rsvp$utils$$o_create(lib$rsvp$enumerator$$default.prototype);
            lib$rsvp$promise$hash$$PromiseHash.prototype._superConstructor = lib$rsvp$enumerator$$default;
            lib$rsvp$promise$hash$$PromiseHash.prototype._init = function () {
                this._result = {}
            };
            lib$rsvp$promise$hash$$PromiseHash.prototype._validateInput = function (input) {
                return input && typeof input === "object"
            };
            lib$rsvp$promise$hash$$PromiseHash.prototype._validationError = function () {
                return new Error("Promise.hash must be called with an object")
            };
            lib$rsvp$promise$hash$$PromiseHash.prototype._enumerate = function () {
                var enumerator = this;
                var promise = enumerator.promise;
                var input = enumerator._input;
                var results = [];
                for (var key in input) {
                    if (promise._state === lib$rsvp$$internal$$PENDING && Object.prototype.hasOwnProperty.call(input, key)) {
                        results.push({position: key, entry: input[key]})
                    }
                }
                var length = results.length;
                enumerator._remaining = length;
                var result;
                for (var i = 0; promise._state === lib$rsvp$$internal$$PENDING && i < length; i++) {
                    result = results[i];
                    enumerator._eachEntry(result.entry, result.position)
                }
            };
            function lib$rsvp$hash$settled$$HashSettled(Constructor, object, label) {
                this._superConstructor(Constructor, object, false, label)
            }

            lib$rsvp$hash$settled$$HashSettled.prototype = lib$rsvp$utils$$o_create(lib$rsvp$promise$hash$$default.prototype);
            lib$rsvp$hash$settled$$HashSettled.prototype._superConstructor = lib$rsvp$enumerator$$default;
            lib$rsvp$hash$settled$$HashSettled.prototype._makeResult = lib$rsvp$enumerator$$makeSettledResult;
            lib$rsvp$hash$settled$$HashSettled.prototype._validationError = function () {
                return new Error("hashSettled must be called with an object")
            };
            function lib$rsvp$hash$settled$$hashSettled(object, label) {
                return new lib$rsvp$hash$settled$$HashSettled(lib$rsvp$promise$$default, object, label).promise
            }

            var lib$rsvp$hash$settled$$default = lib$rsvp$hash$settled$$hashSettled;

            function lib$rsvp$hash$$hash(object, label) {
                return new lib$rsvp$promise$hash$$default(lib$rsvp$promise$$default, object, label).promise
            }

            var lib$rsvp$hash$$default = lib$rsvp$hash$$hash;

            function lib$rsvp$map$$map(promises, mapFn, label) {
                return lib$rsvp$promise$$default.all(promises, label).then(function (values) {
                    if (!lib$rsvp$utils$$isFunction(mapFn)) {
                        throw new TypeError("You must pass a function as map's second argument.")
                    }
                    var length = values.length;
                    var results = new Array(length);
                    for (var i = 0; i < length; i++) {
                        results[i] = mapFn(values[i])
                    }
                    return lib$rsvp$promise$$default.all(results, label)
                })
            }

            var lib$rsvp$map$$default = lib$rsvp$map$$map;

            function lib$rsvp$node$$Result() {
                this.value = undefined
            }

            var lib$rsvp$node$$ERROR = new lib$rsvp$node$$Result;
            var lib$rsvp$node$$GET_THEN_ERROR = new lib$rsvp$node$$Result;

            function lib$rsvp$node$$getThen(obj) {
                try {
                    return obj.then
                } catch (error) {
                    lib$rsvp$node$$ERROR.value = error;
                    return lib$rsvp$node$$ERROR
                }
            }

            function lib$rsvp$node$$tryApply(f, s, a) {
                try {
                    f.apply(s, a)
                } catch (error) {
                    lib$rsvp$node$$ERROR.value = error;
                    return lib$rsvp$node$$ERROR
                }
            }

            function lib$rsvp$node$$makeObject(_, argumentNames) {
                var obj = {};
                var name;
                var i;
                var length = _.length;
                var args = new Array(length);
                for (var x = 0; x < length; x++) {
                    args[x] = _[x]
                }
                for (i = 0; i < argumentNames.length; i++) {
                    name = argumentNames[i];
                    obj[name] = args[i + 1]
                }
                return obj
            }

            function lib$rsvp$node$$arrayResult(_) {
                var length = _.length;
                var args = new Array(length - 1);
                for (var i = 1; i < length; i++) {
                    args[i - 1] = _[i]
                }
                return args
            }

            function lib$rsvp$node$$wrapThenable(then, promise) {
                return {
                    then: function (onFulFillment, onRejection) {
                        return then.call(promise, onFulFillment, onRejection)
                    }
                }
            }

            function lib$rsvp$node$$denodeify(nodeFunc, options) {
                var fn = function () {
                    var self = this;
                    var l = arguments.length;
                    var args = new Array(l + 1);
                    var arg;
                    var promiseInput = false;
                    for (var i = 0; i < l; ++i) {
                        arg = arguments[i];
                        if (!promiseInput) {
                            promiseInput = lib$rsvp$node$$needsPromiseInput(arg);
                            if (promiseInput === lib$rsvp$node$$GET_THEN_ERROR) {
                                var p = new lib$rsvp$promise$$default(lib$rsvp$$internal$$noop);
                                lib$rsvp$$internal$$reject(p, lib$rsvp$node$$GET_THEN_ERROR.value);
                                return p
                            } else if (promiseInput && promiseInput !== true) {
                                arg = lib$rsvp$node$$wrapThenable(promiseInput, arg)
                            }
                        }
                        args[i] = arg
                    }
                    var promise = new lib$rsvp$promise$$default(lib$rsvp$$internal$$noop);
                    args[l] = function (err, val) {
                        if (err) lib$rsvp$$internal$$reject(promise, err); else if (options === undefined) lib$rsvp$$internal$$resolve(promise, val); else if (options === true) lib$rsvp$$internal$$resolve(promise, lib$rsvp$node$$arrayResult(arguments)); else if (lib$rsvp$utils$$isArray(options)) lib$rsvp$$internal$$resolve(promise, lib$rsvp$node$$makeObject(arguments, options)); else lib$rsvp$$internal$$resolve(promise, val)
                    };
                    if (promiseInput) {
                        return lib$rsvp$node$$handlePromiseInput(promise, args, nodeFunc, self)
                    } else {
                        return lib$rsvp$node$$handleValueInput(promise, args, nodeFunc, self)
                    }
                };
                fn.__proto__ = nodeFunc;
                return fn
            }

            var lib$rsvp$node$$default = lib$rsvp$node$$denodeify;

            function lib$rsvp$node$$handleValueInput(promise, args, nodeFunc, self) {
                var result = lib$rsvp$node$$tryApply(nodeFunc, self, args);
                if (result === lib$rsvp$node$$ERROR) {
                    lib$rsvp$$internal$$reject(promise, result.value)
                }
                return promise
            }

            function lib$rsvp$node$$handlePromiseInput(promise, args, nodeFunc, self) {
                return lib$rsvp$promise$$default.all(args).then(function (args) {
                    var result = lib$rsvp$node$$tryApply(nodeFunc, self, args);
                    if (result === lib$rsvp$node$$ERROR) {
                        lib$rsvp$$internal$$reject(promise, result.value)
                    }
                    return promise
                })
            }

            function lib$rsvp$node$$needsPromiseInput(arg) {
                if (arg && typeof arg === "object") {
                    if (arg.constructor === lib$rsvp$promise$$default) {
                        return true
                    } else {
                        return lib$rsvp$node$$getThen(arg)
                    }
                } else {
                    return false
                }
            }

            var lib$rsvp$platform$$platform;
            if (typeof self === "object") {
                lib$rsvp$platform$$platform = self
            } else if (typeof global === "object") {
                lib$rsvp$platform$$platform = global
            } else {
                throw new Error("no global: `self` or `global` found")
            }
            var lib$rsvp$platform$$default = lib$rsvp$platform$$platform;

            function lib$rsvp$race$$race(array, label) {
                return lib$rsvp$promise$$default.race(array, label)
            }

            var lib$rsvp$race$$default = lib$rsvp$race$$race;

            function lib$rsvp$reject$$reject(reason, label) {
                return lib$rsvp$promise$$default.reject(reason, label)
            }

            var lib$rsvp$reject$$default = lib$rsvp$reject$$reject;

            function lib$rsvp$resolve$$resolve(value, label) {
                return lib$rsvp$promise$$default.resolve(value, label)
            }

            var lib$rsvp$resolve$$default = lib$rsvp$resolve$$resolve;

            function lib$rsvp$rethrow$$rethrow(reason) {
                setTimeout(function () {
                    throw reason
                });
                throw reason
            }

            var lib$rsvp$rethrow$$default = lib$rsvp$rethrow$$rethrow;
            lib$rsvp$config$$config.async = lib$rsvp$asap$$default;
            lib$rsvp$config$$config.after = function (cb) {
                setTimeout(cb, 0)
            };
            var lib$rsvp$$cast = lib$rsvp$resolve$$default;

            function lib$rsvp$$async(callback, arg) {
                lib$rsvp$config$$config.async(callback, arg)
            }

            function lib$rsvp$$on() {
                lib$rsvp$config$$config["on"].apply(lib$rsvp$config$$config, arguments)
            }

            function lib$rsvp$$off() {
                lib$rsvp$config$$config["off"].apply(lib$rsvp$config$$config, arguments)
            }

            if (typeof window !== "undefined" && typeof window["__PROMISE_INSTRUMENTATION__"] === "object") {
                var lib$rsvp$$callbacks = window["__PROMISE_INSTRUMENTATION__"];
                lib$rsvp$config$$configure("instrument", true);
                for (var lib$rsvp$$eventName in lib$rsvp$$callbacks) {
                    if (lib$rsvp$$callbacks.hasOwnProperty(lib$rsvp$$eventName)) {
                        lib$rsvp$$on(lib$rsvp$$eventName, lib$rsvp$$callbacks[lib$rsvp$$eventName])
                    }
                }
            }
            var lib$rsvp$umd$$RSVP = {
                race: lib$rsvp$race$$default,
                Promise: lib$rsvp$promise$$default,
                allSettled: lib$rsvp$all$settled$$default,
                hash: lib$rsvp$hash$$default,
                hashSettled: lib$rsvp$hash$settled$$default,
                denodeify: lib$rsvp$node$$default,
                on: lib$rsvp$$on,
                off: lib$rsvp$$off,
                map: lib$rsvp$map$$default,
                filter: lib$rsvp$filter$$default,
                resolve: lib$rsvp$resolve$$default,
                reject: lib$rsvp$reject$$default,
                all: lib$rsvp$all$$default,
                rethrow: lib$rsvp$rethrow$$default,
                defer: lib$rsvp$defer$$default,
                EventTarget: lib$rsvp$events$$default,
                configure: lib$rsvp$config$$configure,
                async: lib$rsvp$$async
            };
        lib$rsvp$platform$$default["RSVP"] = lib$rsvp$umd$$RSVP
        /*!
         * @overview  SHA-256 implementation in JavaScript
         * @copyright Copyright (c) Chris Veness 2002-2014
         * @license   Licensed under MIT license
         *            See http://www.movable-type.co.uk/scripts/sha1.html
         */
        var Sha256 = {};
        Sha256.hash = function (t) {
            t = t.utf8Encode();
            var r = [1116352408, 1899447441, 3049323471, 3921009573, 961987163, 1508970993, 2453635748, 2870763221, 3624381080, 310598401, 607225278, 1426881987, 1925078388, 2162078206, 2614888103, 3248222580, 3835390401, 4022224774, 264347078, 604807628, 770255983, 1249150122, 1555081692, 1996064986, 2554220882, 2821834349, 2952996808, 3210313671, 3336571891, 3584528711, 113926993, 338241895, 666307205, 773529912, 1294757372, 1396182291, 1695183700, 1986661051, 2177026350, 2456956037, 2730485921, 2820302411, 3259730800, 3345764771, 3516065817, 3600352804, 4094571909, 275423344, 430227734, 506948616, 659060556, 883997877, 958139571, 1322822218, 1537002063, 1747873779, 1955562222, 2024104815, 2227730452, 2361852424, 2428436474, 2756734187, 3204031479, 3329325298],
                e = [1779033703, 3144134277, 1013904242, 2773480762, 1359893119, 2600822924, 528734635, 1541459225];
            t += String.fromCharCode(128);
            for (var n = t.length / 4 + 2, o = Math.ceil(n / 16), a = new Array(o), h = 0; o > h; h++) {
                a[h] = new Array(16);
                for (var S = 0; 16 > S; S++)a[h][S] = t.charCodeAt(64 * h + 4 * S) << 24 | t.charCodeAt(64 * h + 4 * S + 1) << 16 | t.charCodeAt(64 * h + 4 * S + 2) << 8 | t.charCodeAt(64 * h + 4 * S + 3)
            }
            a[o - 1][14] = 8 * (t.length - 1) / Math.pow(2, 32), a[o - 1][14] = Math.floor(a[o - 1][14]), a[o - 1][15] = 8 * (t.length - 1) & 4294967295;
            for (var u, f, c, i, d, R, p, y, x = new Array(64), h = 0; o > h; h++) {
                for (var O = 0; 16 > O; O++)x[O] = a[h][O];
                for (var O = 16; 64 > O; O++)x[O] = Sha256.σ1(x[O - 2]) + x[O - 7] + Sha256.σ0(x[O - 15]) + x[O - 16] & 4294967295;
                u = e[0], f = e[1], c = e[2], i = e[3], d = e[4], R = e[5], p = e[6], y = e[7];
                for (var O = 0; 64 > O; O++) {
                    var T = y + Sha256.Σ1(d) + Sha256.Ch(d, R, p) + r[O] + x[O], s = Sha256.Σ0(u) + Sha256.Maj(u, f, c);
                    y = p, p = R, R = d, d = i + T & 4294967295, i = c, c = f, f = u, u = T + s & 4294967295
                }
                e[0] = e[0] + u & 4294967295, e[1] = e[1] + f & 4294967295, e[2] = e[2] + c & 4294967295, e[3] = e[3] + i & 4294967295, e[4] = e[4] + d & 4294967295, e[5] = e[5] + R & 4294967295, e[6] = e[6] + p & 4294967295, e[7] = e[7] + y & 4294967295
            }
            return Sha256.toHexStr(e[0]) + Sha256.toHexStr(e[1]) + Sha256.toHexStr(e[2]) + Sha256.toHexStr(e[3]) + Sha256.toHexStr(e[4]) + Sha256.toHexStr(e[5]) + Sha256.toHexStr(e[6]) + Sha256.toHexStr(e[7])
        }, Sha256.ROTR = function (t, r) {
            return r >>> t | r << 32 - t
        }, Sha256.Σ0 = function (t) {
            return Sha256.ROTR(2, t) ^ Sha256.ROTR(13, t) ^ Sha256.ROTR(22, t)
        }, Sha256.Σ1 = function (t) {
            return Sha256.ROTR(6, t) ^ Sha256.ROTR(11, t) ^ Sha256.ROTR(25, t)
        }, Sha256.σ0 = function (t) {
            return Sha256.ROTR(7, t) ^ Sha256.ROTR(18, t) ^ t >>> 3
        }, Sha256.σ1 = function (t) {
            return Sha256.ROTR(17, t) ^ Sha256.ROTR(19, t) ^ t >>> 10
        }, Sha256.Ch = function (t, r, e) {
            return t & r ^ ~t & e
        }, Sha256.Maj = function (t, r, e) {
            return t & r ^ t & e ^ r & e
        }, Sha256.toHexStr = function (t) {
            for (var r, e = "", n = 7; n >= 0; n--)r = t >>> 4 * n & 15, e += r.toString(16);
            return e
        }, "undefined" == typeof String.prototype.utf8Encode && (String.prototype.utf8Encode = function () {
            return unescape(encodeURIComponent(this))
        }), "undefined" == typeof String.prototype.utf8Decode && (String.prototype.utf8Decode = function () {
            try {
                return decodeURIComponent(escape(this))
            } catch (t) {
                return this
            }
        })/*, "undefined" != typeof module && module.exports && (module.exports = Sha256), "function" == typeof define && define.amd && define([], function () {
            return Sha256
        })*/;

        /**
         * @version 2.0.3-2;
         * @overview QZ Tray Connector
         * <p/>
         * Connects a web client to the QZ Tray software.
         * Enables printing and device communication from javascript.
         *
         * @requires RSVP
         *     Provides Promises/A+ functionality for API calls.
         *     Can be overridden via <code>qz.api.setPromiseType</code> to remove dependency.
         * @requires Sha256
         *     Provides hashing algorithm for signing messages.
         *     Can be overridden via <code>qz.api.setSha256Type</code> to remove dependency.
         */
        var qzF = function () {

///// POLYFILLS /////

            if (!Array.isArray) {
                Array.isArray = function (arg) {
                    return Object.prototype.toString.call(arg) === '[object Array]';
                };
            }


///// PRIVATE METHODS /////

            var _qz = {
                VERSION: "2.0.3-2",                              //must match @version above
                DEBUG: false,

                log: {
                    /** Debugging messages */
                    trace: function () {
                        if (_qz.DEBUG) {
                            console.log.apply(console, arguments);
                        }
                    },
                    /** General messages */
                    info: function () {
                        console.info.apply(console, arguments);
                    },
                    /** Debugging errors */
                    warn: function () {
                        if (_qz.DEBUG) {
                            console.warn.apply(console, arguments);
                        }
                    },
                    /** General errors */
                    error: function () {
                        console.error.apply(console, arguments);
                    }
                },


                //stream types
                streams: {
                    serial: 'SERIAL', usb: 'USB', hid: 'HID'
                },


                websocket: {
                    /** The actual websocket object managing the connection. */
                    connection: null,

                    /** Default parameters used on new connections. Override values using options parameter on {@link qz.websocket.connect}. */
                    connectConfig: {
                        host: ["localhost", "localhost.qz.io"], //hosts QZ Tray can be running on
                        hostIndex: 0,                           //internal var - index on host array
                        usingSecure: true,                      //boolean use of secure protocol
                        protocol: {
                            secure: "wss://",                   //secure websocket
                            insecure: "ws://"                   //insecure websocket
                        },
                        port: {
                            secure: [8181, 8282, 8383, 8484],   //list of secure ports QZ Tray could be listening on
                            insecure: [8182, 8283, 8384, 8485], //list of insecure ports QZ Tray could be listening on
                            portIndex: 0                        //internal var - index on active port array
                        },
                        keepAlive: 60,                          //time between pings to keep connection alive, in seconds
                        retries: 0,                             //number of times to reconnect before failing
                        delay: 0                                //seconds before firing a connection
                    },

                    setup: {
                        /** Loop through possible ports to open connection, sets web socket calls that will settle the promise. */
                        findConnection: function (config, resolve, reject) {
                            var deeper = function () {
                                config.port.portIndex++;

                                if ((config.usingSecure && config.port.portIndex >= config.port.secure.length)
                                    || (!config.usingSecure && config.port.portIndex >= config.port.insecure.length)) {
                                    if (config.hostIndex >= config.host.length - 1) {
                                        //give up, all hope is lost
                                        reject(new Error("Unable to establish connection with QZ"));
                                        return;
                                    } else {
                                        config.hostIndex++;
                                        config.port.portIndex = 0;
                                    }
                                }

                                // recursive call until connection established or all ports are exhausted
                                _qz.websocket.setup.findConnection(config, resolve, reject);
                            };

                            var address;
                            if (config.usingSecure) {
                                address = config.protocol.secure + config.host[config.hostIndex] + ":" + config.port.secure[config.port.portIndex];
                            } else {
                                address = config.protocol.insecure + config.host[config.hostIndex] + ":" + config.port.insecure[config.port.portIndex];
                            }

                            try {
                                _qz.log.trace("Attempting connection", address);
                                _qz.websocket.connection = new _qz.tools.ws(address);
                            }
                            catch (err) {
                                _qz.log.error(err);
                                deeper();
                                return;
                            }

                            if (_qz.websocket.connection != null) {
                                _qz.websocket.connection.established = false;

                                //called on successful connection to qz, begins setup of websocket calls and resolves connect promise after certificate is sent
                                _qz.websocket.connection.onopen = function (evt) {
                                    _qz.log.trace(evt);
                                    _qz.log.info("Established connection with QZ Tray on " + address);

                                    _qz.websocket.setup.openConnection({resolve: resolve, reject: reject});

                                    if (config.keepAlive > 0) {
                                        var interval = setInterval(function () {
                                            if (!qz.websocket.isActive()) {
                                                clearInterval(interval);
                                                return;
                                            }

                                            _qz.websocket.connection.send("ping");
                                        }, config.keepAlive * 1000);
                                    }
                                };

                                //called during websocket close during setup
                                _qz.websocket.connection.onclose = function () {
                                    // Safari compatibility fix to raise error event
                                    if (typeof navigator !== 'undefined' && navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
                                        _qz.websocket.connection.onerror();
                                    }
                                };

                                //called for errors during setup (such as invalid ports), reject connect promise only if all ports have been tried
                                _qz.websocket.connection.onerror = function (evt) {
                                    _qz.log.trace(evt);
                                    deeper();
                                };
                            } else {
                                reject(new Error("Unable to create a websocket connection"));
                            }
                        },

                        /** Finish setting calls on successful connection, sets web socket calls that won't settle the promise. */
                        openConnection: function (openPromise) {
                            _qz.websocket.connection.established = true;

                            //called when an open connection is closed
                            _qz.websocket.connection.onclose = function (evt) {
                                _qz.log.trace(evt);
                                _qz.log.info("Closed connection with QZ Tray");

                                //if this is set, then an explicit close call was made
                                if (_qz.websocket.connection.promise != undefined) {
                                    _qz.websocket.connection.promise.resolve();
                                }

                                _qz.websocket.callClose(evt);
                                _qz.websocket.connection = null;

                                for (var uid in _qz.websocket.pendingCalls) {
                                    if (_qz.websocket.pendingCalls.hasOwnProperty(uid)) {
                                        _qz.websocket.pendingCalls[uid].reject(new Error("Connection closed before response received"));
                                    }
                                }
                            };

                            //called for any errors with an open connection
                            _qz.websocket.connection.onerror = function (evt) {
                                _qz.websocket.callError(evt);
                            };

                            //send JSON objects to qz
                            _qz.websocket.connection.sendData = function (obj) {
                                _qz.log.trace("Preparing object for websocket", obj);

                                if (obj.timestamp == undefined) {
                                    obj.timestamp = Date.now();
                                }
                                if (obj.promise != undefined) {
                                    obj.uid = _qz.websocket.setup.newUID();
                                    _qz.websocket.pendingCalls[obj.uid] = obj.promise;
                                }

                                try {
                                    if (obj.call != undefined && obj.signature == undefined) {
                                        var signObj = {
                                            call: obj.call,
                                            params: obj.params,
                                            timestamp: obj.timestamp
                                        };

                                        _qz.security.callSign(_qz.tools.hash(_qz.tools.stringify(signObj))).then(function (signature) {
                                            _qz.log.trace("Signature for call", signature);
                                            obj.signature = signature;
                                            _qz.signContent = undefined;

                                            _qz.websocket.connection.send(_qz.tools.stringify(obj));
                                        });
                                    } else {
                                        _qz.log.trace("Signature for call", obj.signature);

                                        //called for pre-signed content and (unsigned) setup calls
                                        _qz.websocket.connection.send(_qz.tools.stringify(obj));
                                    }
                                }
                                catch (err) {
                                    _qz.log.error(err);

                                    if (obj.promise != undefined) {
                                        obj.promise.reject(err);
                                        delete _qz.websocket.pendingCalls[obj.uid];
                                    }
                                }
                            };

                            //receive message from qz
                            _qz.websocket.connection.onmessage = function (evt) {
                                var returned = JSON.parse(evt.data);

                                if (returned.uid == null) {
                                    if (returned.type == null) {
                                        //incorrect response format, likely connected to incompatible qz version
                                        _qz.websocket.connection.close(4003, "Connected to incompatible QZ Tray version");

                                    } else {
                                        //streams (callbacks only, no promises)
                                        switch (returned.type) {
                                            case _qz.streams.serial:
                                                if (!returned.event) {
                                                    returned.event = JSON.stringify({
                                                        portName: returned.key,
                                                        output: returned.data
                                                    });
                                                }

                                                _qz.serial.callSerial(JSON.parse(returned.event));
                                                break;
                                            case _qz.streams.usb:
                                                if (!returned.event) {
                                                    returned.event = JSON.stringify({
                                                        vendorId: returned.key[0],
                                                        productId: returned.key[1],
                                                        output: returned.data
                                                    });
                                                }

                                                _qz.usb.callUsb(JSON.parse(returned.event));
                                                break;
                                            case _qz.streams.hid:
                                                _qz.hid.callHid(JSON.parse(returned.event));
                                                break;
                                            default:
                                                _qz.log.warn("Cannot determine stream type for callback", returned);
                                                break;
                                        }
                                    }

                                    return;
                                }

                                _qz.log.trace("Received response from websocket", returned);

                                var promise = _qz.websocket.pendingCalls[returned.uid];
                                if (promise == undefined) {
                                    _qz.log.warn('No promise found for returned response');
                                } else {
                                    if (returned.error != undefined) {
                                        promise.reject(new Error(returned.error));
                                    } else {
                                        promise.resolve(returned.result);
                                    }
                                }

                                delete _qz.websocket.pendingCalls[returned.uid];
                            };


                            //send up the certificate before making any calls
                            //also gives the user a chance to deny the connection
                            function sendCert(cert) {
                                if (cert === undefined) {
                                    cert = null;
                                }
                                _qz.websocket.connection.sendData({certificate: cert, promise: openPromise});
                            }

                            _qz.security.callCert().then(sendCert).catch(sendCert);
                        },

                        /** Generate unique ID used to map a response to a call. */
                        newUID: function () {
                            var len = 6;
                            return (new Array(len + 1).join("0") + (Math.random() * Math.pow(36, len) << 0).toString(36)).slice(-len)
                        }
                    },

                    dataPromise: function (callName, params, signature, signingTimestamp) {
                        return _qz.tools.promise(function (resolve, reject) {
                            var msg = {
                                call: callName,
                                promise: {resolve: resolve, reject: reject},
                                params: params,
                                signature: signature,
                                timestamp: signingTimestamp
                            };

                            _qz.websocket.connection.sendData(msg);
                        });
                    },

                    /** Library of promises awaiting a response, uid -> promise */
                    pendingCalls: {},

                    /** List of functions to call on error from the websocket. */
                    errorCallbacks: [],
                    /** Calls all functions registered to listen for errors. */
                    callError: function (evt) {
                        if (Array.isArray(_qz.websocket.errorCallbacks)) {
                            for (var i = 0; i < _qz.websocket.errorCallbacks.length; i++) {
                                _qz.websocket.errorCallbacks[i](evt);
                            }
                        } else {
                            _qz.websocket.errorCallbacks(evt);
                        }
                    },

                    /** List of function to call on closing from the websocket. */
                    closedCallbacks: [],
                    /** Calls all functions registered to listen for closing. */
                    callClose: function (evt) {
                        if (Array.isArray(_qz.websocket.closedCallbacks)) {
                            for (var i = 0; i < _qz.websocket.closedCallbacks.length; i++) {
                                _qz.websocket.closedCallbacks[i](evt);
                            }
                        } else {
                            _qz.websocket.closedCallbacks(evt);
                        }
                    }
                },


                printing: {
                    /** Default options used for new printer configs. Can be overridden using {@link qz.configs.setDefaults}. */
                    defaultConfig: {
                        //value purposes are explained in the qz.configs.setDefaults docs

                        colorType: 'color',
                        copies: 1,
                        density: 0,
                        duplex: false,
                        fallbackDensity: 600,
                        interpolation: 'bicubic',
                        jobName: null,
                        margins: 0,
                        orientation: null,
                        paperThickness: null,
                        printerTray: null,
                        rasterize: true,
                        rotation: 0,
                        scaleContent: true,
                        size: null,
                        units: 'in',

                        altPrinting: false,
                        encoding: null,
                        endOfDoc: null,
                        perSpool: 1
                    }
                },


                serial: {
                    /** List of functions called when receiving data from serial connection. */
                    serialCallbacks: [],
                    /** Calls all functions registered to listen for serial events. */
                    callSerial: function (streamEvent) {
                        if (Array.isArray(_qz.serial.serialCallbacks)) {
                            for (var i = 0; i < _qz.serial.serialCallbacks.length; i++) {
                                _qz.serial.serialCallbacks[i](streamEvent);
                            }
                        } else {
                            _qz.serial.serialCallbacks(streamEvent);
                        }
                    }
                },


                usb: {
                    /** List of functions called when receiving data from usb connection. */
                    usbCallbacks: [],
                    /** Calls all functions registered to listen for usb events. */
                    callUsb: function (streamEvent) {
                        if (Array.isArray(_qz.usb.usbCallbacks)) {
                            for (var i = 0; i < _qz.usb.usbCallbacks.length; i++) {
                                _qz.usb.usbCallbacks[i](streamEvent);
                            }
                        } else {
                            _qz.usb.usbCallbacks(streamEvent);
                        }
                    }
                },


                hid: {
                    /** List of functions called when receiving data from hid connection. */
                    hidCallbacks: [],
                    /** Calls all functions registered to listen for hid events. */
                    callHid: function (streamEvent) {
                        if (Array.isArray(_qz.hid.hidCallbacks)) {
                            for (var i = 0; i < _qz.hid.hidCallbacks.length; i++) {
                                _qz.hid.hidCallbacks[i](streamEvent);
                            }
                        } else {
                            _qz.hid.hidCallbacks(streamEvent);
                        }
                    }
                },


                security: {
                    /** Function used to resolve promise when acquiring site's public certificate. */
                    certPromise: function (resolve, reject) {
                        reject();
                    },
                    /** Called to create new promise (using {@link _qz.security.certPromise}) for certificate retrieval. */
                    callCert: function () {
                        return _qz.tools.promise(_qz.security.certPromise);
                    },

                    /** Function used to create promise resolver when requiring signed calls. */
                    signaturePromise: function () {
                        return function (resolve) {
                            resolve();
                        }
                    },
                    /** Called to create new promise (using {@link _qz.security.signaturePromise}) for signed calls. */
                    callSign: function (toSign) {
                        return _qz.tools.promise(_qz.security.signaturePromise(toSign));
                    }
                },


                tools: {
                    /** Create a new promise */
                    promise: function (resolver) {
                        return new RSVP.Promise(resolver);
                    },

                    stringify: function (object) {
                        //old versions of prototype affect stringify
                        var pjson = Array.prototype.toJSON;
                        delete Array.prototype.toJSON;

                        var result = JSON.stringify(object);

                        if (pjson) {
                            Array.prototype.toJSON = pjson;
                        }

                        return result;
                    },

                    hash: typeof Sha256 !== 'undefined' ? Sha256.hash : null,
                    ws: typeof WebSocket !== 'undefined' ? WebSocket : null,

                    absolute: function (loc) {
                        if (document && typeof document.createElement === 'function') {
                            var a = document.createElement("a");
                            a.href = loc;
                            return a.href;
                        }
                        return loc;
                    },

                    /** Performs deep copy to target from remaining params */
                    extend: function (target) {
                        //special case when reassigning properties as objects in a deep copy
                        if (typeof target !== 'object') {
                            target = {};
                        }

                        for (var i = 1; i < arguments.length; i++) {
                            var source = arguments[i];
                            if (!source) {
                                continue;
                            }

                            for (var key in source) {
                                if (source.hasOwnProperty(key)) {
                                    if (target === source[key]) {
                                        continue;
                                    }

                                    if (source[key] && source[key].constructor && source[key].constructor === Object) {
                                        var clone;
                                        if (Array.isArray(source[key])) {
                                            clone = target[key] || [];
                                        } else {
                                            clone = target[key] || {};
                                        }

                                        target[key] = _qz.tools.extend(clone, source[key]);
                                    } else if (source[key] !== undefined) {
                                        target[key] = source[key];
                                    }
                                }
                            }
                        }

                        return target;
                    }
                }
            };


///// CONFIG CLASS ////

            /** Object to handle configured printer options. */
            function Config(printer, opts) {
                /**
                 * Set the printer assigned to this config.
                 * @param {string|Object} newPrinter Name of printer. Use object type to specify printing to file or host.
                 *  @param {string} [newPrinter.name] Name of printer to send printing.
                 *  @param {string} [newPrinter.file] Name of file to send printing.
                 *  @param {string} [newPrinter.host] IP address or host name to send printing.
                 *  @param {string} [newPrinter.port] Port used by &lt;printer.host>.
                 */
                this.setPrinter = function (newPrinter) {
                    if (typeof newPrinter === 'string') {
                        newPrinter = {name: newPrinter};
                    }

                    this.printer = newPrinter;
                };

                /**
                 *  @returns {Object} The printer currently assigned to this config.
                 */
                this.getPrinter = function () {
                    return this.printer;
                };

                /**
                 * Alter any of the printer options currently applied to this config.
                 * @param newOpts {Object} The options to change. See <code>qz.config.setDefaults</code> docs for available values.
                 *
                 * @see qz.config.setDefaults
                 */
                this.reconfigure = function (newOpts) {
                    _qz.tools.extend(this.config, newOpts);
                };

                /**
                 * @returns {Object} The currently applied options on this config.
                 */
                this.getOptions = function () {
                    return this.config;
                };

                // init calls for new config object
                this.setPrinter(printer);
                this.config = opts;
            }

            /**
             * Shortcut method for calling <code>qz.print</code> with a particular config.
             * @param {Array<Object|string>} data Array of data being sent to the printer. See <code>qz.print</code> docs for available values.
             * @param {boolean} [signature] Pre-signed signature of JSON string containing <code>call</code>, <code>params</code>, and <code>timestamp</code>.
             * @param {number} [signingTimestamp] Required with <code>signature</code>. Timestamp used with pre-signed content.
             *
             * @example
             * qz.print(myConfig, ...); // OR
             * myConfig.print(...);
             *
             * @see qz.print
             */
            Config.prototype.print = function (data, signature, signingTimestamp) {
                qz.print(this, data, signature, signingTimestamp);
            };


///// PUBLIC METHODS /////

            /** @namespace qz */
            return {

                /**
                 * Calls related specifically to the web socket connection.
                 * @namespace qz.websocket
                 */
                websocket: {
                    /**
                     * Check connection status. Active connection is necessary for other calls to run.
                     *
                     * @returns {boolean} If there is an active connection with QZ Tray.
                     *
                     * @see connect
                     *
                     * @memberof  qz.websocket
                     */
                    isActive: function () {
                        return _qz.websocket.connection != null && _qz.websocket.connection.established;
                    },

                    /**
                     * Call to setup connection with QZ Tray on user's system.
                     *
                     * @param {Object} [options] Configuration options for the web socket connection.
                     *  @param {string|Array<string>} [options.host=['localhost', 'localhost.qz.io']] Host running the QZ Tray software.
                     *  @param {boolean} [options.usingSecure=true] If the web socket should try to use secure ports for connecting.
                     *  @param {number} [options.keepAlive=60] Seconds between keep-alive pings to keep connection open. Set to 0 to disable.
                     *  @param {number} [options.retries=0] Number of times to reconnect before failing.
                     *  @param {number} [options.delay=0] Seconds before firing a connection.  Ignored if <code>options.retries</code> is 0.
                     *
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.websocket
                     */
                    connect: function (options) {
                        return _qz.tools.promise(function (resolve, reject) {
                            if (qz.websocket.isActive()) {
                                reject(new Error("An open connection with QZ Tray already exists"));
                                return;
                            } else if (_qz.websocket.connection != null) {
                                reject(new Error("The current connection attempt has not returned yet"));
                                return;
                            }

                            if (!_qz.tools.ws) {
                                reject(new Error("WebSocket not supported by this browser"));
                                return;
                            } else if (!_qz.tools.ws.CLOSED || _qz.tools.ws.CLOSED == 2) {
                                reject(new Error("Unsupported WebSocket version detected: HyBi-00/Hixie-76"));
                                return;
                            }

                            //ensure some form of options exists for value checks
                            if (options == undefined) {
                                options = {};
                            }

                            //disable secure ports if page is not secure
                            if (typeof location === 'undefined' || location.protocol !== 'https:') {
                                //respect forcing secure ports if it is defined, otherwise disable
                                if (typeof options.usingSecure === 'undefined') {
                                    _qz.log.trace("Disabling secure ports due to insecure page");
                                    options.usingSecure = false;
                                }
                            }

                            //ensure any hosts are passed to internals as an array
                            if (typeof options.host !== 'undefined' && !Array.isArray(options.host)) {
                                options.host = [options.host];
                            }

                            var attempt = function (count) {
                                var nextAttempt = function () {
                                    if (options && count < options.retries) {
                                        attempt(count + 1);
                                    } else {
                                        _qz.websocket.connection = null;
                                        reject.apply(null, arguments);
                                    }
                                };

                                var delayed = function () {
                                    var config = _qz.tools.extend({}, _qz.websocket.connectConfig, options);
                                    _qz.websocket.setup.findConnection(config, resolve, nextAttempt)
                                };
                                if (count == 0) {
                                    delayed(); // only retries will be called with a delay
                                } else {
                                    setTimeout(delayed, options.delay * 1000);
                                }
                            };

                            attempt(0);
                        });
                    },

                    /**
                     * Stop any active connection with QZ Tray.
                     *
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.websocket
                     */
                    disconnect: function () {
                        return _qz.tools.promise(function (resolve, reject) {
                            if (qz.websocket.isActive()) {
                                _qz.websocket.connection.close();
                                _qz.websocket.connection.promise = {resolve: resolve, reject: reject};
                            } else {
                                reject(new Error("No open connection with QZ Tray"))
                            }
                        });
                    },

                    /**
                     * List of functions called for any connections errors outside of an API call.<p/>
                     * Also called if {@link websocket#connect} fails to connect.
                     *
                     * @param {Function|Array<Function>} calls Single or array of <code>Function({Event} event)</code> calls.
                     *
                     * @memberof qz.websocket
                     */
                    setErrorCallbacks: function (calls) {
                        _qz.websocket.errorCallbacks = calls;
                    },

                    /**
                     * List of functions called for any connection closing event outside of an API call.<p/>
                     * Also called when {@link websocket#disconnect} is called.
                     *
                     * @param {Function|Array<Function>} calls Single or array of <code>Function({Event} event)</code> calls.
                     *
                     * @memberof qz.websocket
                     */
                    setClosedCallbacks: function (calls) {
                        _qz.websocket.closedCallbacks = calls;
                    },

                    /**
                     * @param {string} [hostname] Hostname to try to connect to when determining network interfaces, defaults to "google.com"
                     * @param {number} [port] Port to use with custom hostname, defaults to 443
                     *
                     * @returns {Promise<Object<{ipAddress: String, macAddress: String}>|Error>} Connected system's network information.
                     *
                     * @memberof qz.websocket
                     */
                    getNetworkInfo: function (hostname, port) {
                        return _qz.websocket.dataPromise('websocket.getNetworkInfo', {
                            hostname: hostname,
                            port: port
                        });
                    }

                },


                /**
                 * Calls related to getting printer information from the connection.
                 * @namespace qz.printers
                 */
                printers: {
                    /**
                     * @returns {Promise<string|Error>} Name of the connected system's default printer.
                     *
                     * @memberof qz.printers
                     */
                    getDefault: function () {
                        return _qz.websocket.dataPromise('printers.getDefault');
                    },

                    /**
                     * @param {string} [query] Search for a specific printer. All printers are returned if not provided.
                     *
                     * @returns {Promise<Array<string>|string|Error>} The matched printer name if <code>query</code> is provided.
                     *                                                Otherwise an array of printer names found on the connected system.
                     *
                     * @memberof qz.printers
                     */
                    find: function (query) {
                        return _qz.websocket.dataPromise('printers.find', {query: query});
                    }
                },

                /**
                 * Calls related to setting up new printer configurations.
                 * @namespace qz.configs
                 */
                configs: {
                    /**
                     * Default options used by new configs if not overridden.
                     * Setting a value to NULL will use the printer's default options.
                     * Updating these will not update the options on any created config.
                     *
                     * @param {Object} options Default options used by printer configs if not overridden.
                     *
                     *  @param {string} [options.colorType='color'] Valid values <code>[color | grayscale | blackwhite]</code>
                     *  @param {number} [options.copies=1] Number of copies to be printed.
                     *  @param {number|Array<number>} [options.density=72] Pixel density (DPI, DPMM, or DPCM depending on <code>[options.units]</code>).
                     *      If provided as an array, uses the first supported density found (or the first entry if none found).
                     *  @param {boolean} [options.duplex=false] Double sided printing
                     *  @param {number} [options.fallbackDensity=600] Value used when default density value cannot be read, or in cases where reported as "Normal" by the driver.
                     *  @param {string} [options.interpolation='bicubic'] Valid values <code>[bicubic | bilinear | nearest-neighbor]</code>. Controls how images are handled when resized.
                     *  @param {string} [options.jobName=null] Name to display in print queue.
                     *  @param {Object|number} [options.margins=0] If just a number is provided, it is used as the margin for all sides.
                     *   @param {number} [options.margins.top=0]
                     *   @param {number} [options.margins.right=0]
                     *   @param {number} [options.margins.bottom=0]
                     *   @param {number} [options.margins.left=0]
                     *  @param {string} [options.orientation=null] Valid values <code>[portrait | landscape | reverse-landscape]</code>
                     *  @param {number} [options.paperThickness=null]
                     *  @param {string} [options.printerTray=null] //TODO - string?
                     *  @param {boolean} [options.rasterize=true] Whether documents should be rasterized before printing. Forced TRUE if <code>[options.density]</code> is specified.
                     *  @param {number} [options.rotation=0] Image rotation in degrees.
                     *  @param {boolean} [options.scaleContent=true] Scales print content to page size, keeping ratio.
                     *  @param {Object} [options.size=null] Paper size.
                     *   @param {number} [options.size.width=null] Page width.
                     *   @param {number} [options.size.height=null] Page height.
                     *  @param {string} [options.units='in'] Page units, applies to paper size, margins, and density. Valid value <code>[in | cm | mm]</code>
                     *
                     *  @param {boolean} [options.altPrinting=false] Print the specified file using CUPS command line arguments.  Has no effect on Windows.
                     *  @param {string} [options.encoding=null] Character set
                     *  @param {string} [options.endOfDoc=null]
                     *  @param {number} [options.perSpool=1] Number of pages per spool.
                     *
                     * @memberof qz.configs
                     */
                    setDefaults: function (options) {
                        _qz.tools.extend(_qz.printing.defaultConfig, options);
                    },

                    /**
                     * Creates new printer config to be used in printing.
                     *
                     * @param {string|object} printer Name of printer. Use object type to specify printing to file or host.
                     *  @param {string} [printer.name] Name of printer to send printing.
                     *  @param {string} [printer.file] Name of file to send printing.
                     *  @param {string} [printer.host] IP address or host name to send printing.
                     *  @param {string} [printer.port] Port used by &lt;printer.host>.
                     * @param {Object} [options] Override any of the default options for this config only.
                     *
                     * @returns {Config} The new config.
                     *
                     * @see config.setDefaults
                     *
                     * @memberof qz.configs
                     */
                    create: function (printer, options) {
                        var myOpts = _qz.tools.extend({}, _qz.printing.defaultConfig, options);
                        return new Config(printer, myOpts);
                    }
                },


                /**
                 * Send data to selected config for printing.
                 * The promise for this method will resolve when the document has been sent to the printer. Actual printing may not be complete.
                 * <p/>
                 * Optionally, print requests can be pre-signed:
                 * Signed content consists of a JSON object string containing no spacing,
                 * following the format of the "call" and "params" keys in the API call, with the addition of a "timestamp" key in milliseconds
                 * ex. <code>'{"call":"<callName>","params":{...},"timestamp":1450000000}'</code>
                 *
                 * @param {Object<Config>} config Previously created config object.
                 * @param {Array<Object|string>} data Array of data being sent to the printer. String values are interpreted the same as the default <code>[raw]</code> object value.
                 *  @param {string} data.data
                 *  @param {string} data.type Valid values <code>[html | image | pdf | raw]</code>
                 *  @param {string} [data.format] Format of data provided.<p/>
                 *      For <code>[html]</code> types, valid formats include <code>[file(default) | plain]</code>.<p/>
                 *      For <code>[image]</code> types, valid formats include <code>[base64 | file(default)]</code>.<p/>
                 *      For <code>[pdf]</code> types, valid format include <code>[base64 | file(default)]</code>.<p/>
                 *      For <code>[raw]</code> types, valid formats include <code>[base64 | file | hex | plain(default) | image | xml]</code>.
                 *  @param {Object} [data.options]
                 *   @param {string} [data.options.language] Required with <code>[raw]</code> type <code>[image]</code> format. Printer language.
                 *   @param {number} [data.options.x] Optional with <code>[raw]</code> type <code>[image]</code> format. The X position of the image.
                 *   @param {number} [data.options.y] Optional with <code>[raw]</code> type <code>[image]</code> format. The Y position of the image.
                 *   @param {string|number} [data.options.dotDensity] Optional with <code>[raw]</code> type <code>[image]</code> format.
                 *   @param {string} [data.options.xmlTag] Required with <code>[xml]</code> format. Tag name containing base64 formatted data.
                 *   @param {number} [data.options.pageWidth] Optional with <code>[html]</code> type printing. Width of the web page to render. Defaults to paper width.
                 *   @param {number} [data.options.pageHeight] Optional with <code>[html]</code> type printing. Height of the web page to render. Defaults to adjusted web page height.
                 * @param {boolean} [signature] Pre-signed signature of JSON string containing <code>call</code>, <code>params</code>, and <code>timestamp</code>.
                 * @param {number} [signingTimestamp] Required with <code>signature</code>. Timestamp used with pre-signed content.
                 *
                 * @returns {Promise<null|Error>}
                 *
                 * @see qz.config.create
                 *
                 * @memberof qz
                 */
                print: function (config, data, signature, signingTimestamp) {
                    //change relative links to absolute
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].constructor === Object) {
                            if ((!data[i].format && data[i].type && data[i].type.toUpperCase() !== 'RAW') //unspecified format and not raw -> assume file
                                || (data[i].format && (data[i].format.toUpperCase() === 'FILE'
                                || (data[i].format.toUpperCase() === 'IMAGE' && !(data[i].data.indexOf("data:image/") === 0 && data[i].data.indexOf(";base64,") !== 0))
                                || data[i].format.toUpperCase() === 'XML'))) {
                                data[i].data = _qz.tools.absolute(data[i].data);
                            }
                        }
                    }

                    var params = {
                        printer: config.getPrinter(),
                        options: config.getOptions(),
                        data: data
                    };
                    return _qz.websocket.dataPromise('print', params, signature, signingTimestamp);
                },


                /**
                 * Calls related to interaction with serial ports.
                 * @namespace qz.serial
                 */
                serial: {
                    /**
                     * @returns {Promise<Array<string>|Error>} Communication (RS232, COM, TTY) ports available on connected system.
                     *
                     * @memberof qz.serial
                     */
                    findPorts: function () {
                        return _qz.websocket.dataPromise('serial.findPorts');
                    },

                    /**
                     * List of functions called for any response from open serial ports.
                     * Event data will contain <code>{string} portName</code> for all types.
                     *  For RECEIVE types, <code>{string} output</code>.
                     *  For ERROR types, <code>{string} exception</code>.
                     *
                     * @param {Function|Array<Function>} calls Single or array of <code>Function({string} portName, {string} output)</code> calls.
                     *
                     * @memberof qz.serial
                     */
                    setSerialCallbacks: function (calls) {
                        _qz.serial.serialCallbacks = calls;
                    },

                    /**
                     * @param {string} port Name of port to open.
                     * @param {Object} bounds Boundaries of serial port output.
                     *  @param {string} [bounds.begin=0x0002] Character denoting start of serial response. Not used if <code>width</code is provided.
                     *  @param {string} [bounds.end=0x000D] Character denoting end of serial response. Not used if <code>width</code> is provided.
                     *  @param {number} [bounds.width] Used for fixed-width response serial communication.
                     *
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.serial
                     */
                    openPort: function (port, bounds) {
                        var params = {
                            port: port,
                            bounds: bounds
                        };
                        return _qz.websocket.dataPromise('serial.openPort', params);
                    },

                    /**
                     * Send commands over a serial port.
                     * Any responses from the device will be sent to serial callback functions.
                     *
                     * @param {string} port An open port to send data over.
                     * @param {string} data The data to send to the serial device.
                     * @param {Object} [properties] Properties of data being sent over the serial port.
                     *  @param {string} [properties.baudRate=9600]
                     *  @param {string} [properties.dataBits=8]
                     *  @param {string} [properties.stopBits=1]
                     *  @param {string} [properties.parity='NONE'] Valid values <code>[NONE| EVEN | ODD | MARK | SPACE]</code>
                     *  @param {string} [properties.flowControl='NONE'] Valid values <code>[NONE | XONXOFF_OUT | XONXOFF_IN | RTSCTS_OUT | RTSCTS_IN]</code>
                     *
                     * @returns {Promise<null|Error>}
                     *
                     * @see qz.serial.setSerialCallbacks
                     *
                     * @memberof qz.serial
                     */
                    sendData: function (port, data, properties) {
                        var params = {
                            port: port,
                            data: data,
                            properties: properties
                        };
                        return _qz.websocket.dataPromise('serial.sendData', params);
                    },

                    /**
                     * @param {string} port Name of port to close.
                     *
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.serial
                     */
                    closePort: function (port) {
                        return _qz.websocket.dataPromise('serial.closePort', {port: port});
                    }
                },


                /**
                 * Calls related to interaction with USB devices.
                 * @namespace qz.usb
                 */
                usb: {
                    /**
                     * List of available USB devices. Includes (hexadecimal) vendor ID, (hexadecimal) product ID, and hub status.
                     * If supported, also returns manufacturer and product descriptions.
                     *
                     * @param includeHubs Whether to include USB hubs.
                     * @returns {Promise<Array<Object>|Error>} Array of JSON objects containing information on connected USB devices.
                     *
                     * @memberof qz.usb
                     */
                    listDevices: function (includeHubs) {
                        return _qz.websocket.dataPromise('usb.listDevices', {includeHubs: includeHubs});
                    },

                    /**
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @returns {Promise<Array<string>|Error>} List of available (hexadecimal) interfaces on a USB device.
                     *
                     * @memberof qz.usb
                     */
                    listInterfaces: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('usb.listInterfaces', params);
                    },

                    /**
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param iface Hex string of interface on the USB device to search.
                     * @returns {Promise<Array<string>|Error>} List of available (hexadecimal) endpoints on a USB device's interface.
                     *
                     * @memberof qz.usb
                     */
                    listEndpoints: function (vendorId, productId, iface) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            interface: iface
                        };
                        return _qz.websocket.dataPromise('usb.listEndpoints', params);
                    },

                    /**
                     * List of functions called for any response from open usb devices.
                     * Event data will contain <code>{string} vendorId</code> and <code>{string} productId</code> for all types.
                     *  For RECEIVE types, <code>{Array} output</code> (in hexadecimal format).
                     *  For ERROR types, <code>{string} exception</code>.
                     *
                     * @param {Function|Array<Function>} calls Single or array of <code>Function({Object} eventData)</code> calls.
                     *
                     * @memberof qz.usb
                     */
                    setUsbCallbacks: function (calls) {
                        _qz.usb.usbCallbacks = calls;
                    },

                    /**
                     * Claim a USB device's interface to enable sending/reading data across an endpoint.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param iface Hex string of interface on the USB device to claim.
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.usb
                     */
                    claimDevice: function (vendorId, productId, iface) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            interface: iface
                        };
                        return _qz.websocket.dataPromise('usb.claimDevice', params);
                    },

                    /**
                     * Check the current claim state of a USB device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @returns {Promise<boolean|Error>}
                     *
                     * @since 2.0.2
                     * @memberOf qz.usb
                     */
                    isClaimed: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('usb.isClaimed', params);
                    },

                    /**
                     * Send data to a claimed USB device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param endpoint Hex string of endpoint on the claimed interface for the USB device.
                     * @param data Bytes to send over specified endpoint.
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.usb
                     */
                    sendData: function (vendorId, productId, endpoint, data) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            endpoint: endpoint,
                            data: data
                        };
                        return _qz.websocket.dataPromise('usb.sendData', params);
                    },

                    /**
                     * Read data from a claimed USB device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param endpoint Hex string of endpoint on the claimed interface for the USB device.
                     * @param responseSize Size of the byte array to receive a response in.
                     * @returns {Promise<Array<string>|Error>} List of (hexadecimal) bytes received from the USB device.
                     *
                     * @memberof qz.usb
                     */
                    readData: function (vendorId, productId, endpoint, responseSize) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            endpoint: endpoint,
                            responseSize: responseSize
                        };
                        return _qz.websocket.dataPromise('usb.readData', params);
                    },

                    /**
                     * Provides a continuous stream of read data from a claimed USB device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param endpoint Hex string of endpoint on the claimed interface for the USB device.
                     * @param responseSize Size of the byte array to receive a response in.
                     * @param [interval=100] Frequency to send read data back, in milliseconds.
                     * @returns {Promise<null|Error>}
                     *
                     * @see qz.usb.setUsbCallbacks
                     *
                     * @memberof qz.usb
                     */
                    openStream: function (vendorId, productId, endpoint, responseSize, interval) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            endpoint: endpoint,
                            responseSize: responseSize,
                            interval: interval
                        };
                        return _qz.websocket.dataPromise('usb.openStream', params);
                    },

                    /**
                     * Stops the stream of read data from a claimed USB device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param endpoint Hex string of endpoint on the claimed interface for the USB device.
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.usb
                     */
                    closeStream: function (vendorId, productId, endpoint) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            endpoint: endpoint
                        };
                        return _qz.websocket.dataPromise('usb.closeStream', params);
                    },

                    /**
                     * Release a claimed USB device to free resources after sending/reading data.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @returns {Promise<null|Error>}
                     *
                     * @memberof qz.usb
                     */
                    releaseDevice: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('usb.releaseDevice', params);
                    }
                },


                /**
                 * Calls related to interaction with HID USB devices<br/>
                 * Many of these calls can be accomplished from the <code>qz.usb</code> namespace,
                 * but HID allows for simpler interaction
                 * @namespace qz.hid
                 * @since 2.0.1
                 */
                hid: {
                    /**
                     * List of available HID devices. Includes (hexadecimal) vendor ID and (hexadecimal) product ID.
                     * If available, also returns manufacturer and product descriptions.
                     *
                     * @returns {Promise<Array<Object>|Error>} Array of JSON objects containing information on connected HID devices.
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    listDevices: function () {
                        return _qz.websocket.dataPromise('hid.listDevices');
                    },

                    /**
                     * Start listening for HID device actions, such as attach / detach events.
                     * Reported under the ACTION type in the streamEvent on callbacks.
                     *
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @see qz.hid.setHidCallbacks
                     *
                     * @memberof qz.hid
                     */
                    startListening: function () {
                        return _qz.websocket.dataPromise('hid.startListening');
                    },

                    /**
                     * Stop listening for HID device actions.
                     *
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @see qz.hid.setHidCallbacks
                     *
                     * @memberof qz.hid
                     */
                    stopListening: function () {
                        return _qz.websocket.dataPromise('hid.stopListening');
                    },

                    /**
                     * List of functions called for any response from open usb devices.
                     * Event data will contain <code>{string} vendorId</code> and <code>{string} productId</code> for all types.
                     *  For RECEIVE types, <code>{Array} output</code> (in hexadecimal format).
                     *  For ERROR types, <code>{string} exception</code>.
                     *  For ACTION types, <code>{string} actionType</code>.
                     *
                     * @param {Function|Array<Function>} calls Single or array of <code>Function({Object} eventData)</code> calls.
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    setHidCallbacks: function (calls) {
                        _qz.hid.hidCallbacks = calls;
                    },

                    /**
                     * Claim a HID device to enable sending/reading data across.
                     *
                     * @param vendorId Hex string of HID device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    claimDevice: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('hid.claimDevice', params);
                    },

                    /**
                     * Check the current claim state of a HID device.
                     *
                     * @param vendorId Hex string of HID device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @returns {Promise<boolean|Error>}
                     *
                     * @since 2.0.2
                     * @memberOf qz.hid
                     */
                    isClaimed: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('hid.isClaimed', params);
                    },

                    /**
                     * Send data to a claimed HID device.
                     *
                     * @param vendorId Hex string of USB device's vendor ID.
                     * @param productId Hex string of USB device's product ID.
                     * @param data Bytes to send over specified endpoint.
                     * @param [reportId=0x00] First byte of the data packet signifying the HID report ID.
                     *                        Must be 0x00 for devices only supporting a single report.
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    sendData: function (vendorId, productId, data, reportId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            endpoint: reportId,
                            data: data
                        };
                        return _qz.websocket.dataPromise('hid.sendData', params);
                    },

                    /**
                     * Read data from a claimed HID device.
                     *
                     * @param vendorId Hex string of HID device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @param responseSize Size of the byte array to receive a response in.
                     * @returns {Promise<Array<string>|Error>} List of (hexadecimal) bytes received from the HID device.
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    readData: function (vendorId, productId, responseSize) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            responseSize: responseSize
                        };
                        return _qz.websocket.dataPromise('hid.readData', params);
                    },

                    /**
                     * Provides a continuous stream of read data from a claimed HID device.
                     *
                     * @param vendorId Hex string of UHIDSB device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @param responseSize Size of the byte array to receive a response in.
                     * @param [interval=100] Frequency to send read data back, in milliseconds.
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @see qz.hid.setHidCallbacks
                     *
                     * @memberof qz.hid
                     */
                    openStream: function (vendorId, productId, responseSize, interval) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId,
                            responseSize: responseSize,
                            interval: interval
                        };
                        return _qz.websocket.dataPromise('hid.openStream', params);
                    },

                    /**
                     * Stops the stream of read data from a claimed HID device.
                     *
                     * @param vendorId Hex string of HID device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    closeStream: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('hid.closeStream', params);
                    },

                    /**
                     * Release a claimed HID device to free resources after sending/reading data.
                     *
                     * @param vendorId Hex string of HID device's vendor ID.
                     * @param productId Hex string of HID device's product ID.
                     * @returns {Promise<null|Error>}
                     * @since 2.0.1
                     *
                     * @memberof qz.hid
                     */
                    releaseDevice: function (vendorId, productId) {
                        var params = {
                            vendorId: vendorId,
                            productId: productId
                        };
                        return _qz.websocket.dataPromise('hid.releaseDevice', params);
                    }
                },


                /**
                 * Calls related to signing connection requests.
                 * @namespace qz.security
                 */
                security: {
                    /**
                     * Set promise resolver for calls to acquire the site's certificate.
                     *
                     * @param {Function} promiseCall <code>Function({function} resolve)</code> called as promise for getting the public certificate.
                     *        Should call <code>resolve</code> parameter with the result.
                     *
                     * @memberof qz.security
                     */
                    setCertificatePromise: function (promiseCall) {
                        _qz.security.certPromise = promiseCall;
                    },

                    /**
                     * Set promise creator for calls to sign API calls.
                     *
                     * @param {Function} promiseGen <code>Function({function} toSign)</code> Should return a function, <code>Function({function} resolve)</code>, that
                     *                              will sign the content and resolve the created promise.
                     * @memberof qz.security
                     */
                    setSignaturePromise: function (promiseGen) {
                        _qz.security.signaturePromise = promiseGen;
                    }
                },

                /**
                 * Calls related to compatibility adjustments
                 * @namespace qz.api
                 */
                api: {
                    /**
                     * Show or hide QZ api debugging statements in the browser console.
                     *
                     * @param {boolean} show Whether the debugging logs for QZ should be shown. Hidden by default.
                     *
                     * @memberof qz.api
                     */
                    showDebug: function (show) {
                        _qz.DEBUG = show;
                    },

                    /**
                     * Get version of connected QZ Tray application.
                     *
                     * @returns {Promise<string|Error>} Version number of QZ Tray.
                     *
                     * @memberof qz.api
                     */
                    getVersion: function () {
                        return _qz.websocket.dataPromise('getVersion');
                    },

                    /**
                     * Change the promise library used by QZ API.
                     * Should be called before any initialization to avoid possible errors.
                     *
                     * @param {Function} promiser <code>Function({function} resolver)</code> called to create new promises.
                     *
                     * @memberof qz.api
                     */
                    setPromiseType: function (promiser) {
                        _qz.tools.promise = promiser;
                    },

                    /**
                     * Change the SHA-256 hashing library used by QZ API.
                     * Should be called before any initialization to avoid possible errors.
                     *
                     * @param {Function} hasher <code>Function({function} message)</code> called to create hash of passed string.
                     *
                     * @memberof qz.api
                     */
                    setSha256Type: function (hasher) {
                        _qz.tools.hash = hasher;
                    },

                    /**
                     * Change the WebSocket handler.
                     * Should be called before any initialization to avoid possible errors.
                     *
                     * @param {Function} ws <code>Function({function} WebSocket)</code> called to override the internal WebSocket handler.
                     *
                     * @memberof qz.api
                     */
                    setWebSocketType: function (ws) {
                        _qz.tools.ws = ws;
                    }
                },

                /**
                 * Version of this JavaScript library
                 *
                 * @constant {string}
                 *
                 * @memberof qz
                 */
                version: _qz.VERSION
            };

        }

        var qz = qzF();

        $(document).ready(function () {
            var isQzConnect = false;
            $('.thermal-print-file').click(function (e) {
                var thiz = $(this);
                e.preventDefault();
                if(isQzConnect === false){
                    isQzConnect = qz.websocket.connect({retries: 5, delay: 1}).then(function () {
                        console.log("Connected!");
                    });
                }
                $.ajax({
                    showLoader: true,
                    url: $(this).attr('href'),
                    type: "GET",
                    dataType: 'text'
                }).done(function (raw) {
                    var printerName;
                    if(document.getElementById("thermalPrinterName")){
                        printerName = document.getElementById("thermalPrinterName").value;
                    } else {
                        printerName = thiz.attr('data-printer');
                    }
                    var config = qz.configs.create(printerName);

                    var data = [raw];

                    qz.print(config, data).then(function () {
                        console.log("Sent data to printer");
                    });
                });
                return false;
            });
        });
    });