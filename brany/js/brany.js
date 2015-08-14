$(document).ready(function(){
    
    $('#brana1').css({"background-color":"lightblue"});
    
    // pripojeni na websocket
    var sock = undefined;
    startSock();
    setInterval(check,5000);
    $('button[id^=pi_]').bind('click',openBrana);
    
});

function isObject(obj) {
  return obj === Object(obj);
}

function openBrana(e){
    var iD = $(this).attr('id');
    console.log(e+" "+iD);
    if(sock){
	sock.send(JSON.stringify({id:iD,message:"clicked"}));
    }
}

function startSock(){
    sock = new WebSocket('ws://172.16.1.236:1880/ws/brany');
    
    
    sock.onopen = function(){ 
                    console.log("Connected to websocket");
                    console.log("Sending ping..");
                    sock.send('{"msg":"ping"}');
                    console.log("Ping sent..");
		    $('#sock_status').html('connected');
		    $('#sock_status').css({"background-color":"green"});
                  };
        
    sock.onerror = function(){
	    console.log("Websocket error");
	};
	
    
    sock.onmessage = function(evt){
	    var branaInfo = JSON.parse(evt.data);
            console.log(branaInfo);
	    if(isObject(branaInfo)){
		if(branaInfo.stav=='on'){
		    $('#'+branaInfo.brana).addClass('on');
		    $('#'+branaInfo.brana).removeClass('off');
		}
		else{
		    $('#'+branaInfo.brana).addClass('off');
		    $('#'+branaInfo.brana).removeClass('on');
		}
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

function check(){
//    console.log('checking socket connection');
    if(!sock||sock.readyState===3){
	startSock();
    }
}
