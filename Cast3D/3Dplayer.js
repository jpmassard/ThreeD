// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

'use strict';

// Create the namespace
window.player3D = window.player3D || {};

/**
 * The amount of time in a given state before the player goes idle.
 */
player3D.IDLE_TIMEOUT = {
  LAUNCHING: 30 * 1000,    // 30 seconds
  LOADING: 3000 * 1000,    // 50 minutes
  PAUSED: 600 * 1000,       // 10 minutes normally, use 30 seconds for demo
  STALLED: 30 * 1000,      // 30 seconds
  DONE: 300 * 1000,         // 5 minutes normally, use 30 seconds for demo
  IDLE: 300 * 1000          // 5 minutes normally, use 30 seconds for demo
};

/**
 * Describes the type of media being played
 *
 * @enum {string}
 */
player3D.Type = {
  IMAGE: 'image',
  VIDEO: 'video',
  IMAGE3D: 'image3D'
};

/**
 * Describes the state of the player
 *
 * @enum {string}
 */
player3D.State = {
  LAUNCHING: 'launching',
  LOADING: 'loading',
  BUFFERING: 'buffering',
  PLAYING: 'playing',
  PAUSED: 'paused',
  STALLED: 'stalled',
  DONE: 'done',
  IDLE: 'idle'
};

/**
 * If running in Chrome, then run only the player 
 * If running on a Chromecast then we get everything and remote control
 * should work.
 *
 * In the Chromecast case, we:
 * - Get and start the CastReceiver
 * - Setup the slideshow channel
 * - Start the player (this)
 *
 * @param {Element} the element to attach the player
 * @constructor
 * @export
 */
window.onload = function() {
	var userAgent = window.navigator.userAgent;
	var playerDiv = document.getElementById('player');

	// If you want to do some development using Chrome or Edge browser, and then run on
	// a Chromecast you can check the userAgent to see what you are running on, then
	// you would only initialize the receiver code when you are actually on a
	// Chromecast device.
	window.player = new player3D.CastPlayer(playerDiv);
//	if (userAgent.indexOf('CrKey') > -1 || userAgent.indexOf('TV') > -1) {
		window.castreceiver = cast.receiver.CastReceiverManager.getInstance();
		window.castreceiver.onSenderDisconnected = function(event) {
			if(window.castreceiver.getSenders().length == 0 && 	event.reason == cast.receiver.system.DisconnectReason.REQUESTED_BY_SENDER) {
				window.close();
			}
		}
		window.castreceiver.start(window.castreceiver);
//	}
}

/**
 * Cast player constructor - This does the following:
 * - Bind a listener to visibilitychange
 * - Set the default state
 * - Bind event listeners for img & video tags
 *      error, stalled, waiting, playing, pause, ended, timeupdate, seeking, & seeked
 * - Find and remember the various elements
 * - Create the MediaManager and bind to onLoad & onStop</li>
 *
 * @param {Element} the element to attach the player
 * @constructor
 * @export
 */
player3D.CastPlayer = function(element) {

	/**
	* The DOM element the player is attached.
	* @private {Element}
	*/
	this.element_ = element;

	// We want to know when the user changes from watching our content to watching
	// another element, such as broadcast TV, or another HDMI port.  This will only
	// fire when CEC supports it in the TV.
	this.element_.ownerDocument.addEventListener('webkitvisibilitychange', this.onVisibilityChange_.bind(this), false);

	/**
	* The current state of the player
	* @private {player3D.State}
	*/
	this.state_;
	this.setState_(player3D.State.LAUNCHING);

	/**
	 * The image element.
	 * @private {HTMLImageElement}
	 */
	this.imageElement_ = this.element_.querySelector('img');	// @type {HTMLImageElement}
	this.imageElement_.addEventListener('error', this.onError_.bind(this), false);
	
	/**
	 * The image3D element.
	 * @private {HTMLCanvasElement}
	 */
	this.image3DElement_ = this.element_.querySelector('canvas');	// @type {HTMLCanvasElement}    
	this.image3DElement_.addEventListener('error', this.onError_.bind(this), false);
	
	/**
	 * The media element
	 * @private {HTMLMediaElement}
	 */
	this.mediaElement_ = this.element_.querySelector('video');	//** @type {HTMLMediaElement}
	this.mediaElement_.addEventListener('error', this.onError_.bind(this), false);
	this.mediaElement_.addEventListener('stalled', this.onStalled_.bind(this), false);
	this.mediaElement_.addEventListener('waiting', this.onBuffering_.bind(this), false);
	this.mediaElement_.addEventListener('playing', this.onPlaying_.bind(this), false);
	this.mediaElement_.addEventListener('pause', this.onPause_.bind(this), false);
	this.mediaElement_.addEventListener('ended', this.onEnded_.bind(this), false);
	this.mediaElement_.addEventListener('timeupdate', this.onProgress_.bind(this), false);
	this.mediaElement_.addEventListener('seeking', this.onSeekStart_.bind(this), false);
	this.mediaElement_.addEventListener('seeked', this.onSeekEnd_.bind(this), false);
	
	this.progressBarInnerElement_ = this.element_.querySelector('.controls-progress-inner');
	this.progressBarThumbElement_ = this.element_.querySelector('.controls-progress-thumb');
	this.curTimeElement_ = this.element_.querySelector('.controls-cur-time');
	this.totalTimeElement_ = this.element_.querySelector('.controls-total-time');

	/**
	* The remote media object
	* @private {cast.receiver.MediaManager}
	*/
	this.mediaManager_ = new cast.receiver.MediaManager(this.mediaElement_);
	this.mediaManager_.onLoad = this.onLoad_.bind(this);
	this.mediaManager_.onStop = this.onStop_.bind(this);

};

/**
 * Sets the amount of time before the player is considered idle.
 *
 * @param {number} t the time in milliseconds before the player goes idle
 * @private
 */
player3D.CastPlayer.prototype.setIdleTimeout_ = function(t) {
	clearTimeout(this.idle_);
	if (t) {
		this.idle_ = setTimeout(this.onIdle_.bind(this), t);
	}
};


/**
 * Sets the type of player
 *
 * @param {string} mimeType the mime type of the content
 * @private
 */
player3D.CastPlayer.prototype.setContentType_ = function(mimeType) {
	if (mimeType.indexOf('image/') == 0) {
		this.type_ = player3D.Type.IMAGE;
		if (mimeType.indexOf('x-jps') > -1 || mimeType.indexOf('x-mpo') > -1 ) {
		this.type_ = player3D.Type.IMAGE3D;
		}
	} else if (mimeType.indexOf('video/') == 0) {
		this.type_ = player3D.Type.VIDEO;
	}
};


/**
 * Sets the state of the player
 *
 * @param {player3D.State} state the new state of the player
 * @param {boolean=} crossfade true if should cross fade between states
 * @param {number=} delay the amount of time (in ms) to wait
 */
player3D.CastPlayer.prototype.setState_ = function(state, crossfade, delay) {
	var self = this;
	clearTimeout(self.delay_);
	if (delay) {
		var func = function() { self.setState_(state, crossfade); };
		self.delay_ = setTimeout(func, delay);
	} else {
		if (!crossfade) {
			self.state_ = state;
			self.element_.className = 'player ' + (self.type_ || '') + ' ' + state;
			self.setIdleTimeout_(player3D.IDLE_TIMEOUT[state.toUpperCase()]);
			console.log('setState(%o)', state);
		} else {
			player3D.fadeOut_(self.element_, 0.75, function() {
				self.setState_(state, false);
				player3D.fadeIn_(self.element_, 0.75);
			});
		}
	}
};


/**
 * Callback called when media has stalled
 *
 */
player3D.CastPlayer.prototype.onStalled_ = function() {
	console.log('onStalled');
	this.setState_(player3D.State.BUFFERING, false);
	if (this.mediaElement_.currentTime) {
		this.mediaElement_.load();  // see if we can restart the process
	}
};

/**
 * Callback called when media is buffering
 *
 */
player3D.CastPlayer.prototype.onBuffering_ = function() {
	console.log('onBuffering');
	if (this.state_ != player3D.State.LOADING) {
		this.setState_(player3D.State.BUFFERING, false);
	}
};

/**
 * Callback called when media has started playing
 *
 */
player3D.CastPlayer.prototype.onPlaying_ = function() {
	console.log('onPlaying');
	var isLoading = this.state_ == player3D.State.LOADING;
	var xfade = isLoading;
	var delay = !isLoading ? 0 : 3000;      // 3 seconds
	this.setState_(player3D.State.PLAYING, xfade, delay);
};

/**
 * Callback called when media has been paused
 *
 */
player3D.CastPlayer.prototype.onPause_ = function() {
	console.log('onPause');
	if (this.state_ != player3D.State.DONE) {
		this.setState_(player3D.State.PAUSED, false);
	};
this.updateProgress_();
};


/**
 * Callback called when media has been stopped
 *
 */
player3D.CastPlayer.prototype.onStop_ = function() {
	console.log('onStop');
	var self = this;
	player3D.fadeOut_(self.element_, 0.75, function() {
		self.mediaElement_.pause();
		self.mediaElement_.removeAttribute('src');
		self.imageElement_.removeAttribute('src');
		self.setState_(player3D.State.DONE, false);
		player3D.fadeIn_(self.element_, 0.75);
	});
};


/**
 * Callback called when media has ended
 *
 */
player3D.CastPlayer.prototype.onEnded_ = function() {
	console.log('onEnded');
	this.setState_(player3D.State.DONE, true);
};

/**
 * Callback called when media position has changed
 *
 */
player3D.CastPlayer.prototype.onProgress_ = function() {
	this.updateProgress_();
};

/**
 * Updates the current time and progress bar elements
 *
 */
player3D.CastPlayer.prototype.updateProgress_ = function() {
	var curTime = this.mediaElement_.currentTime;
	var totalTime = this.mediaElement_.duration;
	if (!isNaN(curTime) && !isNaN(totalTime)) {
		var pct = 100 * (curTime / totalTime);
		this.curTimeElement_.innerText = player3D.formatDuration_(curTime);
		this.totalTimeElement_.innerText = player3D.formatDuration_(totalTime);
		this.progressBarInnerElement_.style.width = pct + '%';
		this.progressBarThumbElement_.style.left = pct + '%';
	}
};

/**
 * Callback called when user starts seeking
 *
 */
player3D.CastPlayer.prototype.onSeekStart_ = function() {
	console.log('onSeekStart');
	clearTimeout(this.seekingTimeout_);
	this.element_.classList.add('seeking');
};

/**
 * Callback called when user stops seeking
 *
 */
player3D.CastPlayer.prototype.onSeekEnd_ = function() {
	console.log('onSeekEnd');
	clearTimeout(this.seekingTimeout_);
	this.seekingTimeout_ = player3D.addClassWithTimeout_(this.element_, 'seeking', 3000);
};

/**
 * Callback called when media volume has changed - we rely on the pause timer
 * to get us to the right state.  If we are paused for too long, things will
 * close. Otherwise, we can come back, and we start again.
 *
 */
player3D.CastPlayer.prototype.onVisibilityChange_ = function() {
	console.log('onVisibilityChange');
	if (document.webkitHidden) {
		this.mediaElement_.pause();
	} else {
		this.mediaElement_.play();
	}
};

/**
 * Callback called when player enters idle state 
 *
 */
player3D.CastPlayer.prototype.onIdle_ = function() {
	console.log('onIdle');
	if (this.state_ != player3D.State.IDLE) {
		this.setState_(player3D.State.IDLE, true);
	} else {
		window.close();
	}
};

/**
 * Called to handle an error when the media could not be loaded.
 * cast.MediaManager in the Receiver also listens for this event, and it will
 * notify any senders. We choose to just enter the done state, bring up the
 * finished image and let the user either choose to do something else.  We are
 * trying not to put up errors on the second screen.
 *
 */
player3D.CastPlayer.prototype.onError_ = function() {
	console.log('onError');
	this.setState_(player3D.State.DONE, true);
};

/**
 * Called to handle a load request
 * TODO() handle errors better here (i.e. missing contentId, contentType, etc)
 *
 * @param {cast.receiver.MediaManager.Event} event the load event
 */
player3D.CastPlayer.prototype.onLoad_ = function(event) {
	var self = this;

	var title = player3D.getValue_(event.data, ['media', 'metadata', 'title']);
	var titleElement = self.element_.querySelector('.media-title');
	player3D.setInnerText_(titleElement, title);

	var subtitle = player3D.getValue_(event.data, ['media', 'metadata', 'subtitle']);
	var subtitleElement = self.element_.querySelector('.media-subtitle');
	player3D.setInnerText_(subtitleElement, subtitle);

	var artwork = player3D.getValue_(event.data, ['media', 'metadata', 'images', 0, 'url']);
	var artworkElement = self.element_.querySelector('.media-artwork');
	player3D.setBackgroundImage_(artworkElement, artwork);
	
	var autoplay = player3D.getValue_(event.data, ['autoplay']);
	var contentId = player3D.getValue_(event.data, ['media', 'contentId']);
	var contentType = player3D.getValue_(event.data, ['media', 'contentType']);

	self.setContentType_(contentType);
	self.setState_(player3D.State.LOADING, false);
	switch (self.type_) {
	case player3D.Type.IMAGE:
		self.imageElement_.onload = function() {
			self.setState_(player3D.State.PAUSED, false);
		};
		self.imageElement_.src = contentId || '';
		self.mediaElement_.removeAttribute('src');
		break;
	case player3D.Type.VIDEO:
		self.imageElement_.onload = null;
		self.imageElement_.removeAttribute('src');
		self.mediaElement_.autoplay = autoplay || true;
		self.mediaElement_.src = contentId || '';
		break;
	case player3D.Type.IMAGE3D:
		var img = new Image();
		img.src = contentId;
		img.onload = function() {
		  var ctx = self.image3DElement_.getContext('2d');
		  ctx.mozImageSmoothingEnabled = false;
		  self.image3DElement_.width=img.width/2;
		  self.image3DElement_.height=img.height;
		  ctx.drawImage(img, 0, 0, img.width/2, img.height);
		};
		self.imageElement_.removeAttribute('src');
		self.mediaElement_.removeAttribute('src');
		self.setState_(player3D.State.PAUSED, false);
		break;
	}
};

/**
 * Get a value from an object multiple levels deep.
 *
 * @param {Object} obj The object.
 * @param {Array} keys The keys keys.
 * @returns {R} the value of the property with the given keys
 * @template R
 */
player3D.getValue_ = function(obj, keys) {
	for (var i = 0; i < keys.length; i++) {
		if (obj === null || obj === undefined) {
	  		return '';	// default to an empty string
		} else {
	  		obj = obj[keys[i]];
		}
	}
	return obj;
};

/**
 * Sets the inner text for the given element.
 *
 * @param {Element} element The element.
 * @param {string} text The text.
 */
player3D.setInnerText_ = function(element, text) {
	element.innerText = text || '';
};

/**
 * Sets the background image for the given element.
 *
 * @param {Element} element The element.
 * @param {string} url The image url.
 */
player3D.setBackgroundImage_ = function(element, url) {
	element.style.backgroundImage = (url ? 'url("' + url + '")' : 'none');
	element.style.display = (url ? '' : 'none');
};

/**
 * Formats the given duration
 *
 * @param {number} dur the duration (in seconds)
 * @return {string} the time (in HH:MM:SS)
 */
player3D.formatDuration_ = function(dur) {
	function digit(n) { return ('00' + Math.round(n)).slice(-2); }
	var hr = Math.floor(dur / 3600);
	var min = Math.floor(dur / 60) % 60;
	var sec = dur % 60;
	if (!hr) {
		return digit(min) + ':' + digit(sec);
	} else {
		return digit(hr) + ':' + digit(min) + ':' + digit(sec);
	}
};

/**
 * Adds the given className to the given element for the specified amount of
 * time
 *
 * @param {Element} element the element to add the given class
 * @param {string} className the class name to add to the given element
 * @param {number} timeout the amount of time (in ms) the class should be
 *                 added to the given element
 * @return {number} returns a numerical id, which can be used later with
 *                  window.clearTimeout()
 */
player3D.addClassWithTimeout_ = function(element, className, timeout) {
	element.classList.add(className);
	return setTimeout(function() {
		element.classList.remove(className);
	}, timeout);
};

/**
 * Causes the given element to fade in
 *
 * @param {Element} element the element to fade in
 * @param {number} time the amount of time (in seconds) to transition
 * @param {function()=} doneFunc the function to call when complete
 */
player3D.fadeIn_ = function(element, time, doneFunc) {
	player3D.fadeTo_(element, '', time, doneFunc);
};

/**
 * Causes the given element to fade out
 *
 * @param {Element} element the element to fade out
 * @param {number} time the amount of time (in seconds) to transition
 * @param {function()=} doneFunc the function to call when complete
 */
player3D.fadeOut_ = function(element, time, doneFunc) {
	player3D.fadeTo_(element, 0, time, doneFunc);
};

/**
 * Causes the given element to fade to the given opacity
 *
 * @param {Element} element the element to fade in/out
 * @param {string|number} opacity the opacity to transition to
 * @param {number} time the amount of time (in seconds) to transition
 * @param {function()=} doneFunc the function to call when complete
 */
player3D.fadeTo_ = function(element, opacity, time, doneFunc) {
	var listener = null;
	listener = function() {
		element.style.webkitTransition = '';
		element.removeEventListener('webkitTransitionEnd', listener, false);
		if (doneFunc) {
			doneFunc();
		}
	};
	element.addEventListener('webkitTransitionEnd', listener, false);
	element.style.webkitTransition = 'opacity ' + time + 's';
	element.style.opacity = opacity;
};
