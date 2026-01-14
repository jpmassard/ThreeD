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

/**
 * Constants of states for media playback
 * @enum {string}
 */
const PLAYER_STATE = {
	IDLE: 'IDLE',
	LOADING: 'LOADING',
	LOADED: 'LOADED',
	PLAYING: 'PLAYING',
	PAUSED: 'PAUSED',
	STOPPED: 'STOPPED',
	ERROR: 'ERROR'
};

class CastPlayer {
	
	constructor () {
		/** @type {PLAYER_STATE} A state for media playback */
		this.playerState = PLAYER_STATE.IDLE;

		/** @type {cast.framework.RemotePlayer} */
		this.remotePlayer = null;
		/** @type {cast.framework.RemotePlayerController} */
		this.remotePlayerController = null;
	};
	
	/**
	 * This method sets up the CastContext, and a few other members
	 * that are necessary to play and control videos on a Cast
	 * device.
	 */
	initialize () {
	
		var options = {};
		var context = cast.framework.CastContext.getInstance();
	
		// Set the receiver application ID
		options.receiverApplicationId = 'C98D45D8';	// Old chromecast version
//		options.receiverApplicationId = 'C8156DF1'; // new CAF version, not suitable
	
		// Auto join policy can be one of the following three:
		// ORIGIN_SCOPED - Auto connect from same appId and page origin
		// TAB_AND_ORIGIN_SCOPED - Auto connect from same appId, page origin, and tab
		// PAGE_SCOPED - No auto connect
		options.autoJoinPolicy = chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED;

		options.androidReceiverCompatible = false;

		context.setOptions(options);

		let credentialsData = new chrome.cast.CredentialsData("{\"userId\": \"abc\"}");
		context.setLaunchCredentialsData(credentialsData);

		this.remotePlayer = new cast.framework.RemotePlayer();
		this.remotePlayerController = new cast.framework.RemotePlayerController(this.remotePlayer);
//		this.remotePlayerController.addEventListener(
//			cast.framework.RemotePlayerEventType.IS_CONNECTED_CHANGED,
//			this.connectStateChanged.bind(this)
//		);

		// Click on a miniature in category page
		$("#thumbnails").click((event) => {
			var castSession = cast.framework.CastContext.getInstance().getCurrentSession();
			if (castSession !== null)
			{
				// find media URL
				var temp =  $(event.target);
				do  
				{
					temp =  temp.parent();
					var currentMediaURL = temp.attr ('href');
				} while (currentMediaURL == undefined);
				// extract image Id from current URL
				var params = currentMediaURL.substring (currentMediaURL.indexOf('?/') + 2);
				var imgID = params.split('/')[0];
				// Get media info from piwigo Web Service
				var url = window.location.protocol + '//' + window.location.host + '/ws.php?format=json&method=pwg.plugins.ThreeD.get_image_info&image_id=' + imgID;
				$.ajax({
					type: "GET",
					url: url,
					dataType: "json",
					success: this.processData.bind(this),
					error: function(){ alert("failed contacting piwigo web service"); }
				});

				// Do not open media locally
				event.preventDefault();
				event.stopPropagation();
			}
		}).bind(this);
	};


//	connectStateChanged (event)
//	{
//		
//	}


	processData(data) {
		if (data.stat == "ok")
		{
			var imgURL = window.location.protocol + '//' + window.location.host + data.result.path.substring(1);
// For local test only...
//			var imgURL = window.location.protocol + '//192.168.1.3' + data.result.path.substring(1);

			var extension = data.result.path.split('.').pop().toUpperCase(); 
			var mimeType;
			if (extension == 'JPS')
				mimeType = 'image/x-jps';
			else if (extension == 'MPO')
				mimeType = 'image/x-mpo';
			else if ((extension == 'JPG' || extension == 'JPEG') && data.result.representative_ext == null)
				mimeType = 'image/jpeg';
			else if (extension == 'MP4')
				mimeType = 'video/mp4';
			
			var mediaInfo = new chrome.cast.media.MediaInfo(imgURL, mimeType);

			// update customData so the receiver can know about 3D or pano media
			var info3D = {
				'is3D' : data.result.is3D,
				'pano_type' : data.result.pano_type,
			};
			mediaInfo.customData = info3D;
			
			var castSession = cast.framework.CastContext.getInstance().getCurrentSession();
			if (castSession) {
				var request = new chrome.cast.media.LoadRequest(mediaInfo);
				castSession.loadMedia(request).then(() => console.log('Load succeed'), (errorCode) => console.log('Error code: ' + errorCode));
				console.log ("Trying to cast " + imgURL );
			}
		}
	};
	

	/**
	 * Set to use the remote picture player
	 */
	RemotePicturePlayer () {
		var request = new chrome.cast.media.LoadRequest(mediaInfo);
		castSession.loadMedia(request).then(() => console.log('Load succeed'), (errorCode) => console.log('Error code: ' + errorCode));
		console.log ("Trying to cast " + imgURL );
	}


	/**
	 * Set to use the remote panorama player
	 */
	RemotePanoPlayer () {
		
	}


	/**
	 * Set to use the remote player
	 */
	RemoteVideoPlayer () {
		var castSession = cast.framework.CastContext.getInstance().getCurrentSession();
	
		// Add event listeners for player changes which may occur outside sender app
		this.remotePlayerController.addEventListener(
			cast.framework.RemotePlayerEventType.IS_PAUSED_CHANGED,
			function() {
				if (this.remotePlayer.isPaused) {
					this.playerHandler.pause();
				} else {
					this.playerHandler.play();
				}
			}.bind(this)
		);
	
		this.remotePlayerController.addEventListener(
			cast.framework.RemotePlayerEventType.IS_MUTED_CHANGED,
			function() {
				if (this.remotePlayer.isMuted) {
					this.playerHandler.mute();
				} else {
					this.playerHandler.unMute();
				}
			}.bind(this)
		);
	
		this.remotePlayerController.addEventListener(
			cast.framework.RemotePlayerEventType.VOLUME_LEVEL_CHANGED,
			function() {
				var newVolume = this.remotePlayer.volumeLevel * FULL_VOLUME_HEIGHT;
				var p = document.getElementById('audio_bg_level');
				p.style.height = newVolume + 'px';
				p.style.marginTop = -newVolume + 'px';
			}.bind(this)
		);
	
		// This object will implement PlayerHandler callbacks with
		// remotePlayerController, and makes necessary UI updates specific
		// to remote playback
		var playerTarget = {};
	
		playerTarget.play = function () {
			if (this.remotePlayer.isPaused) {
				this.remotePlayerController.playOrPause();
			}
	
			var vi = document.getElementById('video_image');
			vi.style.display = 'block';
			var localPlayer = document.getElementById('video_element');
			localPlayer.style.display = 'none';
		}.bind(this);
	
		playerTarget.pause = function () {
			if (!this.remotePlayer.isPaused) {
				this.remotePlayerController.playOrPause();
			}
		}.bind(this);
	
		playerTarget.stop = function () {
			 this.remotePlayerController.stop();
		}.bind(this);
	
		playerTarget.load = function (mediaIndex) {
			console.log('Loading...' + this.mediaContents[mediaIndex]['title']);
			var mediaInfo = new chrome.cast.media.MediaInfo(
				this.mediaContents[mediaIndex]['sources'][0], 'video/mp4');
	
			mediaInfo.metadata = new chrome.cast.media.GenericMediaMetadata();
			mediaInfo.metadata.metadataType = chrome.cast.media.MetadataType.GENERIC;
			mediaInfo.metadata.title = this.mediaContents[mediaIndex]['title'];
			mediaInfo.metadata.images = [
				{'url': MEDIA_SOURCE_ROOT + this.mediaContents[mediaIndex]['thumb']}];
	
			var request = new chrome.cast.media.LoadRequest(mediaInfo);
			request.credentials = 'user-credentials';
			request.atvCredentials = 'atv-user-credentials';
			castSession.loadMedia(request).then(
				this.playerHandler.loaded.bind(this.playerHandler),
				function (errorCode) {
					this.playerState = PLAYER_STATE.ERROR;
					console.log('Remote media load error: ' +
						CastPlayer.getErrorMessage(errorCode));
				}.bind(this));
		}.bind(this);
	
		playerTarget.getCurrentMediaTime = function() {
			return this.remotePlayer.currentTime;
		}.bind(this);
	
		playerTarget.getMediaDuration = function() {
			return this.remotePlayer.duration;
		}.bind(this);
	
		playerTarget.updateDisplayMessage = function () {
			document.getElementById('playerstate').style.display = 'block';
			document.getElementById('playerstatebg').style.display = 'block';
			document.getElementById('video_image_overlay').style.display = 'block';
			document.getElementById('playerstate').innerHTML =
				this.mediaContents[ this.currentMediaIndex]['title'] + ' ' +
				this.playerState + ' on ' + castSession.getCastDevice().friendlyName;
		}.bind(this);
	
		playerTarget.setVolume = function (volumeSliderPosition) {
			// Add resistance to avoid loud volume
			var currentVolume = this.remotePlayer.volumeLevel;
			var p = document.getElementById('audio_bg_level');
			if (volumeSliderPosition < FULL_VOLUME_HEIGHT) {
				var vScale =  this.currentVolume * FULL_VOLUME_HEIGHT;
				if (volumeSliderPosition > vScale) {
					volumeSliderPosition = vScale + (pos - vScale) / 2;
				}
				p.style.height = volumeSliderPosition + 'px';
				p.style.marginTop = -volumeSliderPosition + 'px';
				currentVolume = volumeSliderPosition / FULL_VOLUME_HEIGHT;
			} else {
				currentVolume = 1;
			}
			this.remotePlayer.volumeLevel = currentVolume;
			this.remotePlayerController.setVolumeLevel();
		}.bind(this);
	
		playerTarget.mute = function () {
			if (!this.remotePlayer.isMuted) {
				this.remotePlayerController.muteOrUnmute();
			}
		}.bind(this);
	
		playerTarget.unMute = function () {
			if (this.remotePlayer.isMuted) {
				this.remotePlayerController.muteOrUnmute();
			}
		}.bind(this);
	
		playerTarget.isMuted = function() {
			return this.remotePlayer.isMuted;
		}.bind(this);
	
		playerTarget.seekTo = function (time) {
			this.remotePlayer.currentTime = time;
			this.remotePlayerController.seek();
		}.bind(this);
	
		this.playerHandler.setTarget(playerTarget);
	
		// Setup remote player volume right on setup
		// The remote player may have had a volume set from previous playback
		if (this.remotePlayer.isMuted) {
			this.playerHandler.mute();
		}
		var currentVolume = this.remotePlayer.volumeLevel * FULL_VOLUME_HEIGHT;
		var p = document.getElementById('audio_bg_level');
		p.style.height = currentVolume + 'px';
		p.style.marginTop = -currentVolume + 'px';
	
		this.hideFullscreenButton();
	
		this.playerHandler.play();
	};

};


/**
 * PlayerHandler
 *
 * This is a handler through which the application will interact
 * with both the RemotePlayer and LocalPlayer. Combining these two into
 * one interface is one approach to the dual-player nature of a Cast
 * Chrome application. Otherwise, the state of the RemotePlayer can be
 * queried at any time to decide whether to interact with the local
 * or remote players.
 *
 * To set the player used, implement the following methods for a target object
 * and call setTarget(target).
 *
 * Methods to implement:
 *  - play()
 *  - pause()
 *  - stop()
 *  - seekTo(time)
 *  - load(mediaIndex)
 *  - getMediaDuration()
 *  - getCurrentMediaTime()
 *  - setVolume(volumeSliderPosition)
 *  - mute()
 *  - unMute()
 *  - isMuted()
 *  - updateDisplayMessage()
 */
var PlayerHandler = function (castPlayer){
	this.target = {};
	this.setTarget = function(target) {
		this.target = target;
	};

	this.play = function() {
		if (castPlayer.playerState !== PLAYER_STATE.PLAYING &&
			castPlayer.playerState !== PLAYER_STATE.PAUSED &&
			castPlayer.playerState !== PLAYER_STATE.LOADED) {
			this.load(castPlayer.currentMediaIndex);
			return;
		}

		this.target.play();
		castPlayer.playerState = PLAYER_STATE.PLAYING;
		document.getElementById('play').style.display = 'none';
		document.getElementById('pause').style.display = 'block';
		this.updateDisplayMessage();
	};

	this.pause = function() {
		if (castPlayer.playerState !== PLAYER_STATE.PLAYING) {
			return;
		}

		this.target.pause();
		castPlayer.playerState = PLAYER_STATE.PAUSED;
		document.getElementById('play').style.display = 'block';
		document.getElementById('pause').style.display = 'none';
		this.updateDisplayMessage();
	};

	this.stop = function() {
		this.pause();
		castPlayer.playerState = PLAYER_STATE.STOPPED;
		this.updateDisplayMessage();
	};

	this.load = function(mediaIndex) {
		castPlayer.playerState = PLAYER_STATE.LOADING;

		document.getElementById('media_title').innerHTML =
			castPlayer.mediaContents[castPlayer.currentMediaIndex]['title'];
		document.getElementById('media_subtitle').innerHTML =
			castPlayer.mediaContents[castPlayer.currentMediaIndex]['subtitle'];
		document.getElementById('media_desc').innerHTML =
			castPlayer.mediaContents[castPlayer.currentMediaIndex]['description'];

		this.target.load(mediaIndex);
		this.updateDisplayMessage();
	};

	this.loaded = function() {
		castPlayer.currentMediaDuration = this.getMediaDuration();
		castPlayer.updateMediaDuration();
		castPlayer.playerState = PLAYER_STATE.LOADED;
		if (castPlayer.currentMediaTime > 0) {
			this.seekTo(castPlayer.currentMediaTime);
		}
		this.play();
		castPlayer.startProgressTimer();
		this.updateDisplayMessage();
	};

	this.getCurrentMediaTime = function() {
		return this.target.getCurrentMediaTime();
	};

	this.getMediaDuration = function() {
		return this.target.getMediaDuration();
	};

	this.updateDisplayMessage = function () {
		this.target.updateDisplayMessage();
	};

	this.setVolume = function(volumeSliderPosition) {
		this.target.setVolume(volumeSliderPosition);
	};

	this.mute = function() {
		this.target.mute();
		document.getElementById('audio_on').style.display = 'none';
		document.getElementById('audio_off').style.display = 'block';
	};

	this.unMute = function() {
		this.target.unMute();
		document.getElementById('audio_on').style.display = 'block';
		document.getElementById('audio_off').style.display = 'none';
	};

	this.isMuted = function() {
		return this.target.isMuted();
	};

	this.seekTo = function(time) {
		this.target.seekTo(time);
		this.updateDisplayMessage();
	};
};


