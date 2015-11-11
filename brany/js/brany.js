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
			console.log('pin was ok sending message to nodered');
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
	    console.log("Websocket error");
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
		    stavyPinu[branaInfo.brana] = branaInfo.stav;
		    //setOnOff(branaInfo.brana,branaInfo.stav);
		}
		updateBranyClasses();
	    }
    };
    
    sock.onclose = function(evt){
	console.log('connection closed');
	$('#sock_status').html('closed');
	$('#sock_status').css({"background-color":"red"});
	//reconnect
	check();
    };
}


function updateBranyClasses(){
    console.log(stavyPinu);

    //brana2
    if(stavyPinu.pi_32=='off' && stavyPinu.pi_36=='off'){
	bId = 'brana2Button';
	$('#' + bId).addClass('closed');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('open');
    }
    else if (stavyPinu.pi_32=='on' && stavyPinu.pi_36=='on'){
	bId = 'brana2Button';
	$('#' + bId).addClass('open');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('closed');
    }
    else
    {
	bId = 'brana2Button';
	$('#' + bId).addClass('opening');
	$('#' + bId).removeClass('open');
	$('#' + bId).removeClass('closed');
    }
    

    //brana1
    if(stavyPinu.pi_38=='off' && stavyPinu.pi_40=='off'){
	bId = 'brana1Button';
	$('#' + bId).addClass('closed');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('open');
    }
    else if (stavyPinu.pi_38=='on' && stavyPinu.pi_40=='on'){
	bId = 'brana1Button';
	$('#' + bId).addClass('open');
	$('#' + bId).removeClass('opening');
	$('#' + bId).removeClass('closed');
    }
    else
    {
	bId = 'brana2Button';
	$('#' + bId).addClass('opening');
	$('#' + bId).removeClass('open');
	$('#' + bId).removeClass('closed');
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
 * @param {type} id
 * @param {type} stav
 * @returns {undefined}
 */
function setOnOff(id, stav) {
    console.log(Date());
    console.log('id='+id+' stav='+stav);
    
    var buttonId = '';
    
    //brana2
    if(id=='pi_36' && stav=='off'){
	//brana se zavrela
	var buttonId = 'brana2Button';
	$('#' + buttonId).addClass('closed');
	$('#' + buttonId).removeClass('opening');
	$('#' + buttonId).removeClass('open');
    }

    if(id=='pi_32' && stav=='on'){
	//brana se zcela otevrela
	var buttonId = 'brana2Button';
	$('#' + buttonId).addClass('open');
	$('#' + buttonId).removeClass('opening');
	$('#' + buttonId).removeClass('closed');
    }
    
    //brana1
    if(id=='pi_40' && stav=='off'){
	//brana se zavrela
	var buttonId = 'brana1Button';
	$('#' + buttonId).addClass('closed');
	$('#' + buttonId).removeClass('opening');
	$('#' + buttonId).removeClass('open');
    }
    
    if(id=='pi_38' && stav=='on'){
	//brana se zcela otevrela
	var buttonId = 'brana1Button';
	$('#' + buttonId).addClass('open');
	$('#' + buttonId).removeClass('opening');
	$('#' + buttonId).removeClass('closed');
    }
//    
//    
//    
//    
//    if(id=='pi_40'){
//	//brana1 zavreno
//	buttonId = 'brana1Button';
//    }
//    if(id=='pi_36'){
//	//brana2 zavreno
//	buttonId = 'brana2Button';
//    }
//    
//    if (stav == 'on') {
//	$('#' + buttonId).addClass('on');
//	$('#' + buttonId).removeClass('off');
//    }
//    else {
//	$('#' + buttonId).addClass('off');
//	$('#' + buttonId).removeClass('on');
//    }
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
