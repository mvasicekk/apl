$(document).ready(function(){
    
    $('#brana1').css({"background-color":"lightblue"});
    
    // pripojeni na websocket
    var sock = undefined;
    startSock();
    setInterval(check,5000);
    $('button[id^=brana]').bind('click',openBrana);
    
});


var stavyPinu = {
    pi_32:undefined,	//brana2 otevreno = on, magnet na kridlech vrat
    pi_36:undefined,	//brana2 zavreno = off, magnet na stredu kridel
    pi_38:undefined,	//brana1 otevreno = on
    pi_40:undefined	//brana1 zavreno = off
};


function isObject(obj) {
  return obj === Object(obj);
}

function openBrana(e){
    var iD = $(this).attr('id');
    var u = $('#userinfo').val();
    var pin = $('#branaPin').val();
    $('#branaPin').val('');
    
    //console.log(e+" "+iD);
    	// ulozit info o stisknuti do logu
	$.post('./saveToLog.php',
            {
                id:iD,
                u:u,
		pin:pin
            },
            function(data){
                    //console.log(data);
		    if((data.pinOk===true) && (sock)){
			var messageToSend = JSON.stringify({msg:"branaButtonClicked",id:iD,loginfo:u});
			sock.send(messageToSend);
			//console.log('pin was ok sending message to nodered');
		    }
            },
            'json'
        );
	$(this).blur();
	updateBranyClasses();
}

function startSock(){
    sock = new WebSocket('ws://172.16.1.236:1880/ws/brany');
    
    sock.onopen = function(){ 
                    //console.log("Connected to websocket");
                    //console.log("Sending ping..");
                    sock.send('{"msg":"ping"}');
                    //console.log("Ping sent..");
		    $('#sock_status').html('connected');
		    $('#sock_status').css({"background-color":"green"});
		    //dej mi poctecni stav bran
		    sock.send('{"msg":"getbranystatus"}');
                  };
        
    sock.onerror = function(){
	    //console.log("Websocket error");
	};
	
    
    sock.onmessage = function(evt){
	    var branaInfo = JSON.parse(evt.data);
            //console.log(branaInfo);
	    if(isObject(branaInfo)){
		if(branaInfo.msg==="branyStatus"){
		    //console.log('zpracovat branyStatus');
		    branaInfo.br.forEach(function(e){
			//console.log('id='+e.id+',stav='+e.stav);
			stavyPinu[e.id] = e.stav;
			//setOnOff(e.id,e.stav);
		    });
		}
		else{
		    //console.log('zmena stavu pinu');
		    //console.log(branaInfo.stav);
		    stavyPinu[branaInfo.brana] = branaInfo.stav;
		    if(branaInfo.stav!==undefined){
			//window.location.reload();
			refreshWebcam('brana1img');
			refreshWebcam('brana2img');
		    }
		    //setOnOff(branaInfo.brana,branaInfo.stav);
		}
		updateBranyClasses();
	    }
    };
    
    sock.onclose = function(evt){
	//console.log('connection closed');
	$('#sock_status').html('closed');
	$('#sock_status').css({"background-color":"red"});
	//reconnect
	check();
    };
}


var refreshWebcam = function (imgid) {
	console.log('refreshWebcam:'+imgid);
	var proxyUrl = './proxy.php';
            // webcam link is appended with a timestamp to prevent caching
	    if(imgid=='brana1img'){
		var webcamImg = 'http://a:a@172.16.1.102/Streaming/channels/801/picture' + '?' + (new Date()).getTime();
	    }
	    if(imgid=='brana2img'){
		var webcamImg = 'http://a:a@172.16.1.102/Streaming/channels/401/picture' + '?' + (new Date()).getTime();
	    }

/*
	    $.ajaxDigest(webcamImg, {
		//csurl:webcamImg,
		username: 'admin',
		password: '12345'
	    }).done(function(data, textStatus, jqXHR) {
		alert('Retrieved data!');
	    }).fail(function(jqXHR, textStatus, errorThrown) {
		alert('Request failed :(');
	    });
*/	    
	    
	    $.ajax({
                url: proxyUrl,
                type: 'GET',
		data:{csurl:webcamImg},
                success: function () {
                    $('#'+imgid).attr('src', webcamImg);
                    console.log('successfully loaded ' + webcamImg);
                }
            });
	    
        };
	
function updateBranyClasses(){
    
    if(stavyPinu.pi_32=='off' && stavyPinu.pi_36=='off'){
	bId = 'brana2Button';
	$('#' + bId).addClass('closed');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('open');
	//refreshWebcam('brana2img');
	//window.location.reload();
    }
    else if (stavyPinu.pi_32=='on' && stavyPinu.pi_36=='on'){
	bId = 'brana2Button';
	$('#' + bId).addClass('open');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('closed');
	//refreshWebcam('brana2img');
	//window.location.reload();
    }
    else
    {
	bId = 'brana2Button';
	$('#' + bId).addClass('opening');
	$('#' + bId).removeClass('open');
	$('#' + bId).removeClass('closed');
	//refreshWebcam('brana2img');
	//window.location.reload();
    }
    

    //brana1
    if(stavyPinu.pi_38=='off' && stavyPinu.pi_40=='off'){
	bId = 'brana1Button';
	$('#' + bId).addClass('closed');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('open');
	//refreshWebcam('brana1img');
	//window.location.reload();
    }
    else if (stavyPinu.pi_38=='on' && stavyPinu.pi_40=='on'){
	bId = 'brana1Button';
	$('#' + bId).addClass('open');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('closed');
	//refreshWebcam('brana1img');
	//window.location.reload();
    }
    else
    {
	bId = 'brana1Button';
	$('#' + bId).addClass('opening');
	$('#' + bId).removeClass('open');
	$('#' + bId).removeClass('closed');
	//refreshWebcam('brana1img');
	//window.location.reload();
    }
    
    // pokud jsou zabezpeceny schovam div s tlacitkama
    if(stavyPinu.pi_11=='off'){
	$('#brana2Button').attr('disabled','disabled');
	$('#brana1Button').attr('disabled','disabled');
    }
    else{
	$('#brana2Button').removeAttr('disabled');
	$('#brana1Button').removeAttr('disabled');
    }
}

/**
 * 
 * @returns {undefined}
 */
function check(){
//    console.log('checking socket connection');
    if(!sock||sock.readyState===3){
	startSock();
    }
}
