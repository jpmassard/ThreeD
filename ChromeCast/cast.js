/* 
 * Copyright 2017 JP Massard. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
**/

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
      castSession = null;
      console.log('CastContext: CastSession disconnected');
   break;
  }
}

$( "#thumbnails" ).click(function ( event ) {
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
    
    $.ajax({
    type: "GET",
    url: window.location.protocol + '//' + window.location.host + '/ws.php?format=json&method=pwg.images.getInfo&image_id=' + imgID,
    dataType: "json",
    success: processData,
    error: function(){ alert("failed contacting piwigo web service"); }
    });

    event.preventDefault();
  }
});

function processData(data)
{
  if (data.stat == "ok")
  {
    var imgURL = data.result.element_url;
    
    var extension = getFileExtension (imgURL).toUpperCase();
    var mimeType;
    if (extension == 'JPS')
      mimeType = 'image/x-jps';
    else if (extension == 'MPO')
      mimeType = 'image/x-mpo';
    else if ((extension == 'JPG' || extension == 'JPEG') && data.result.representative_ext == null)
      mimeType = 'image/jpeg';
    else if (extension == 'MP4')
      mimeType = 'video/mp4';

    console.log ("Casting " + imgURL );
    var mediaInfo = new chrome.cast.media.MediaInfo(imgURL, mimeType);
    var request = new chrome.cast.media.LoadRequest(mediaInfo);
    castSession.loadMedia(request).then( onMediaLoaded, mediaError);
  }
}

function onMediaLoaded()
{
  console.log('Load succeed');
}

function mediaError(errorCode)
{
  console.log('Error code: ' + errorCode);
}

function getFileExtension (filename)
{
  return filename.split('.').pop();
}