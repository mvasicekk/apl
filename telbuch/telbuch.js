// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    var inputy = $('#suchen');
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }

    $(window).bind('load resize',resizeAdressen);
    $('#suchen').bind('keyup',searchAdress);
    $('#printbutton').bind('click',printButton);
    $('#adressen').hide();

});


function deleteAdress(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updateDelete(data);
        },
        'json'
        );
}


function printButton(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updatePrintButton(data);
        },
        'json'
        );
}

function editAdress(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updateEdit(data);
        },
        'json'
        );
}

function saveAdress(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    
    var data = $('input').serializeArray();
    
    $.post(acturl,
        {
            id:id,
	    data:data
        },
        function(data){
            updateSave(data);
        },
        'json'
        );
}

function searchAdress(event){
        var id = $('#suchen').attr('id');
        var acturl = $('#suchen').attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$('#suchen').val()
        },
        function(data){
            updateSuchen(data);
        },
        'json'
        );
}

function showKategorien(event){
        var acturl = $(this).attr('acturl');
        $.post(acturl,
        {
            code:$('#code').val()
        },
        function(data){
            updateShowKategorien(data);
        },
        'json'
        );
}

function updateShowKategorien(data){
    if($('#kategoriendiv').length!=0){
            $('#kategoriendiv').remove();
	    return;
        }
	
    var bPos = $('#kategorien').offset();
    var bHeight = $('#kategorien').outerHeight();
    $('body').append(data.div);
    $('#kategoriendiv').css({"top":bPos.top+bHeight, "left":bPos.left});
    $('input[id^=kat_]').bind('click',updateKategorien);
}

function updateKategorien(event){
        var acturl = $(this).attr('acturl');
	
        $.post(acturl,
        {
	    id:$(this).attr('id'),
            checked:$(this).attr('checked')
        },
        function(data){
            updateUpKategorien(data);
        },
        'json'
        );
}

function updatePrintKategorien(event){
        var acturl = $(this).attr('acturl');
	
        $.post(acturl,
        {
	    id:$(this).attr('id'),
            checked:$(this).attr('checked')
        },
        function(data){
            updatePrintUpKategorien(data);
        },
        'json'
        );
}

function updateUpKategorien(data){
    $('#katseznam').html(data.aikString);
}

function updatePrintUpKategorien(data){
}

function updatePrintButton(data){
    if($('#printdiv').length!=0){
            $('#printdiv').remove();
	    return;
        }
	
    var bPos = $('#printbutton').offset();
    var bHeight = $('#printbutton').outerHeight();
    var bWidth = $('#printbutton').outerWidth();
    $('body').append(data.div);
    $('#printdiv').css({"top":bPos.top+bHeight, "right":287});
    $('input[id^=printkat_]').bind('click',updatePrintKategorien);
    $('input[id^=column_]').bind('click',updatePrintKategorien);
}

function updateEdit(data){
    // zobrazit editovaci div
    if($('#editform').length!=0){
            $('#editform').remove();
        }

    
    adressenPos = $('#adressen').offset();
    $('body').append(data.formDiv);
    $('#editform').css({"top":adressenPos.top+30, "left":adressenPos.left+50});
//    $('#editform').css('height',500);
    $('#editform').css('width',$('#adressen').width()-100);
    
    $('input[id^=save_]').bind('click',saveAdress);
    $('input[id^=savenew_]').bind('click',saveAdress);
    $('input[id=abbr]').bind('click',abbrAdress);
    $('input[id=kategorien]').bind('click',showKategorien);
    
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
}


function abbrAdress(){
    if($('#editform').length!=0){
	$('#editform').remove();
    }
    if($('#kategoriendiv').length!=0){
	$('#kategoriendiv').remove();
    }

}

function updateSave(data){
    if($('#editform').length!=0){
            $('#editform').remove();
        }
    if($('#kategoriendiv').length!=0){
            $('#kategoriendiv').remove();
        }
}

function updateDelete(data){
    if(data.ar>0){
	//obarvim radek cervene a zmizim editovaci a deletovaci tlacitka
	id = data.adressId;
	$('#adressrow_'+id).attr('class','rowdeleted');
	$('input[id=deladress_'+id+']').hide();
	$('input[id=editadress_'+id+']').hide();
    }
}

function updateSuchen(data){
    if($('#editform').length!=0){
            $('#editform').remove();
        }

    if($('#kategoriendiv').length!=0){
            $('#kategoriendiv').remove();
        }

    if(data.adressenCount>0){
        $('#adressen').show();
        $('#adressen').html(data.content);
	// pridat event handlery
	//handler pro delete
	$('input[id^=deladress_]').bind('click',deleteAdress);
	//handler pro edit
	$('input[id^=editadress_]').bind('click',editAdress);
        resizeAdressen();
    }
    else{
        $('#adressen').hide();
    }
}

function resizeAdressen(){
    adressenPos = $('#adressen').offset();
    windowHeight = $(window).height();
    if($('#adressen').height()>(windowHeight-adressenPos.top-20))
        $('#adressen').css('height',windowHeight-adressenPos.top-20);
    else
        $('#adressen').height('');
}
