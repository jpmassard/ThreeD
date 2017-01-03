var castSession;

window['__onGCastApiAvailable'] = function (loaded) {
    if(typeof(Storage) === "undefined") {
      alert ("Cannot access Session Storage");
      return;
    }
    castSession = null;
    if (!loaded) 
        return;
    initializeCastApi();
    cast.framework.CastContext.getInstance().addEventListener(
      cast.framework.CastContextEventType.SESSION_STATE_CHANGED,
      onSessionStateChanged);
};

function initializeCastApi() {
    cast.framework.CastContext.getInstance().setOptions({
    receiverApplicationId: "C8156DF1",
    autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
    });
}

function onSessionStateChanged (event)
{   
  switch (event.sessionState) {
    case cast.framework.SessionState.SESSION_STARTED:
      castSession = cast.framework.CastContext.getInstance().getCurrentSession();
      console.log('CastContext: CastSession connected');
      break;
    case cast.framework.SessionState.SESSION_RESUMED:
      break;
    case cast.framework.SessionState.SESSION_ENDED:
      // sessionStorage.removeItem ("castSession");
      castSession = null;
      console.log('CastContext: CastSession disconnected');
   break;
  }
}

$( "#thumbnails" ).click(function ( event ) {
  // var castSession= $.parseJSON(sessionStorage.getItem ("castSession"));
  console.log (castSession);
  if (castSession !== null)
  {
    var temp =  $(event.target);
    do  
    {
      temp =  temp.parent();
      var currentMediaURL = temp.attr ('href');
    } while (currentMediaURL == undefined);
    var params = currentMediaURL.substring (currentMediaURL.indexOf('?/') + 2);
    var imgID = params.split('/')[0];
    var imgURL = window.location.protocol + '//' + window.location.host + '/action.php?id=' + imgID + '&part=e&download';

    console.log ("Casting " + imgURL );
    var mediaInfo = new chrome.cast.media.MediaInfo(imgURL, 'image/x-jps');
    var request = new chrome.cast.media.LoadRequest(mediaInfo);
    castSession.loadMedia(request).then( onMediaLoaded, mediaError);
    event.preventDefault();
  }
});

function onMediaLoaded()
{
  console.log('Load succeed');
}

function mediaError(errorCode)
{
  console.log('Error code: ' + errorCode);
}
