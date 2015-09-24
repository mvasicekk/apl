$(document).ready(function(){
    
    $('#brana1').css({"background-color":"lightblue"});
    
    // pripojeni na websocket
    var sock = undefined;
    startSock();
    setInterval(check,5000);
    $('button[id^=brana]').bind('click',openBrana);
    
});

function isObject(obj) {
  return obj === Object(obj);
}

function openBrana(e){
    var iD = $(this).attr('id');
    var u = $('#userinfo').val();
    var pin = $('#branaPin').val();
    
    console.log(e+" "+iD);
    	// ulozit info o stisknuti do logu
	$.post('./saveToLog.php',
            {
                id:iD,
                u:u,
		pin:pin
            },
            function(data){
                    console.log(data);
		    if((data.pinOk===true) && (sock)){
			var messageToSend = JSON.stringify({msg:"branaButtonClicked",id:iD,loginfo:u});
			sock.send(messageToSend);
		    }
            },
            'json'
        );
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
		    //dej mi poctecni stav bran
		    sock.send('{"msg":"getbranystatus"}');
                  };
        
    sock.onerror = function(){
	    console.log("Websocket error");
	};
	
    
    sock.onmessage = function(evt){
	    var branaInfo = JSON.parse(evt.data);
            console.log(branaInfo);
	    if(isObject(branaInfo)){
		if(branaInfo.msg==="branyStatus"){
		    console.log('zpracovat branyStatus');
		    branaInfo.br.forEach(function(e){
			console.log('id='+e.id+',stav='+e.stav);
			setOnOff(e.id,e.stav);
		    });
		}
		else{
		    setOnOff(branaInfo.brana,branaInfo.stav);
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

/**
 * 
 * @param {type} id
 * @param {type} stav
 * @returns {undefined}
 */
function setOnOff(id, stav) {
    if (stav == 'on') {
	$('#' + id).addClass('on');
	$('#' + id).removeClass('off');
    }
    else {
	$('#' + id).addClass('off');
	$('#' + id).removeClass('on');
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
