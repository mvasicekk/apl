// JavaScript Document

function pis(){
    
    // zobrazeni hodin
    var d = Date();
    text = d.toString();
    $('#t3').html(text);
    
}

function pisajax(){
    //alert('pisajax');
    var acturl = './infoabyAjax.php';

    $.post(acturl,
        {
            elementid: ''
        },
        function(data){
                updatePisAjax(data);
        },
        'json'
    );
}

function updatePisAjax(data){
    
    $('#t1').html(data.t1);
    $('#t2').html(data.t2);
    $('#t3').html(data.t3);
    
}

$(document).ready(function(){

    //$('#k1').css({"background-color":"#ddffdd"});
    //v = pis();
    //setInterval(pis, 500);
    
    //setInterval(pisajax, 5000);
    
});
