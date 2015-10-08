var castBtn, session, currentMedia;

window['__onGCastApiAvailable'] = function (loaded, errorInfo) {
    castBtn = document.getElementById('cast_btn_launch');
    if (loaded) {
        initializeCastApi();
    	castBtn.style.visibility= "visible";
        
    } else {
    	castBtn.style.visibility= "hidden";
    }
}

function initializeCastApi() {
    var sessionRequest = new chrome.cast.SessionRequest("C8156DF1");
    var apiConfig = new chrome.cast.ApiConfig(sessionRequest, sessionListener, receiverListener);
    chrome.cast.initialize(apiConfig, onInitSuccess, onError);
}

function onError(message) {
    castBtn.style.visibility= "hidden";
    alert(message);
}

function onInitSuccess() {
    if (castBtn) {
        castBtn.addEventListener('click', toggleCast, false);
    }
}

function toggleCast() {
    if (session) {
        stopCast();
    } else {
        chrome.cast.requestSession(onRequestSessionSuccess, onLaunchError);
    }
}

function sessionListener(e) {
    if (castBtn) {
        session = e;
        session.addMediaListener(onMediaDiscovered.bind(this, 'addMediaListener'));
        if (session.media.length !== 0) {
            onMediaDiscovered('onRequestSessionSuccess', session.media[0]);
        }
    }
}

function onRequestSessionSuccess(e) {
    session = e;
    castMedia();
}

function receiverListener(e) {
	if( e === chrome.cast.ReceiverAvailability.AVAILABLE) {
	    // console.log('receiverListener');
	}
}

function onStopCast() {
    castBtn.classList.remove('cast-btn-on');
    session = undefined;
}

function stopCast() {
    session.stop(onStopCast, onError);
}


// TODO : add other medias support
function castMedia() {
    var imgURL, mediaInfo, request;
    imgURL = window.location.protocol + '//' + window.location.host + '/action.php?id=' + window.location.search.substring(2, window.location.search.indexOf('/', 2)) + '&part=e&download';
    mediaInfo = new chrome.cast.media.MediaInfo(imgURL, 'image/x-jps');
    request = new chrome.cast.media.LoadRequest(mediaInfo);
    session.loadMedia(request, onMediaDiscovered.bind(this, 'loadMedia'), onMediaError);
}

function onMediaDiscovered(how, media) {
    currentMedia = media;
    media.addUpdateListener(onMediaStatusUpdate);
    castMedia ();
    castBtn.classList.add('cast-btn-on');
}

function onMediaStatusUpdate(e) {
// TODO
	console.log('status');
}

function onMediaError() {
    alert('ChromeCast MediaError');
    stopCast();
}

function onLaunchError() {
    alert('Cant open ChromeCast session');
}


