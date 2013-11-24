// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    
    var inputy = $('.entermove');
    $('input:text[class=entermove]').bind('focus',function(e){
        this.select();
    });
    
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }


    inputy.bind('keypress',function(e){
        var key = e.which;
        if(key==13){
            e.preventDefault();

            var nextIndex = inputy.index(this) + 1;
            if(inputy[nextIndex]!=null){
                var nextBox = inputy[nextIndex];
                nextBox.focus();
                nextBox.select();
            }
        }
    });

//    $('input[id$=time]').css({"background-color":"red"});

    $('input[id$=time]').change(function(e){
	vysledek="00:00";
	hodnota=$(this).val();
	if(hodnota.length==4){
	    hodiny = hodnota.substr(0, 2);
	    minuty = hodnota.substr(2, 2);
	    hodinyInt = parseInt(hodiny);
	    minutyInt = parseInt(minuty);
	    if((hodinyInt>=0) && (hodinyInt<=23) && (minutyInt>=0) && (minutyInt<=59)){
		vysledek = hodinyInt.toString()+":"+minutyInt.toString();
	    }
	}
	$(this).val(vysledek);
    });
    


//    $('#repneu').hide();
    $('#rundneu').keypress(function(event){
        if(event.keyCode==13) $('#rundneu').click();
    });

    $('#im').change(function(e){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
	var v = $(this).val();
        $.post(acturl,
        {
            id:id,
	    v:v
         },
         function(data){
            validateAuftrag(data);
         },
         'json'
         );
	
    });

    $('#ex').change(function(e){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
	var v = $(this).val();
        $.post(acturl,
        {
            id:id,
	    v:v
         },
         function(data){
            validateAuftrag(data);
         },
         'json'
         );
	
    });

    $('#rundneu').click(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        $.post(acturl,
        {
            id:id,
            im:$('#im').val(),
            ex:$('#ex').val(),
	    ab_aby_soll_date:$('#ab_aby_soll_date').val(),
	    ab_aby_soll_time:$('#ab_aby_soll_time').val(),
	    ab_aby_ist_date:$('#ab_aby_ist_date').val(),
	    ab_aby_ist_time:$('#ab_aby_ist_time').val(),
	    proforma:$('#proforma').val(),
	    spediteur_id:$('#spediteur_id').val(),
	    fahrername:$('#fahrername').val(),
	    lkw_kz:$('#lkw_kz').val(),
	    an_kunde_ort:$('#an_kunde_ort').val(),
	    an_kunde_soll_date:$('#an_kunde_soll_date').val(),
	    an_kunde_soll_time:$('#an_kunde_soll_time').val(),
	    an_kunde_ist_date:$('#an_kunde_ist_date').val(),
	    an_kunde_ist_time:$('#an_kunde_ist_time').val(),
	    an_aby_soll_date:$('#an_aby_soll_date').val(),
	    an_aby_soll_time:$('#an_aby_soll_time').val(),
	    an_aby_ist_date:$('#an_aby_ist_date').val(),
	    an_aby_ist_time:$('#an_aby_ist_time').val(),
	    an_aby_nutzlast:$('#an_aby_nutzlast').val(),
	    preis:$('#preis').val(),
	    rabatt:$('#rabatt').val(),
	    betrag:$('#betrag').val(),
	    rechnung:$('#rechnung').val(),
	    bemerkung:$('#bemerkung').val()
         },
         function(data){
            insertRundlaufData(data);
         },
         'json'
         );
    });

});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function validateAuftrag(data){
    if(data.ok==0){
	$('#'+data.id).val("");
    }
}


function insertRundlaufData(data){
    if(data.ar!=1){
	$('#chyba').html('<span style="color:red;">Chyba při ukládání !!! Záznam nebyl uložen.</span>');
    }
    else{
	$('#chyba').html('<span style="color:green;">uloženo.</span>');
	// a vymazat casu u soll
	$('#ab_aby_soll_time').val("");
	$('#an_kunde_soll_time').val("");
	$('#an_aby_soll_time').val("");
	// vymazat import , export
	$('#im').val("");
	$('#ex').val("");
    }
    //nastavit focus na prvni input
    var inputy = $('.entermove');
    
    //na prvni nastavim focus
    if(inputy[0]!=null){
	inputy[0].focus();
	inputy[0].select();
    }
}



function js_validate_float(control)
{

	var hodnota = control.value

	re = /,/
	novahodnota=hodnota.replace(re,".");

	floatvalue = parseFloat(novahodnota);

	if(!isNaN(floatvalue)&&(floatvalue>=0))
	{
		control.value=floatvalue;
		control.style.backgroundColor='';
                return true;
	}
	else
	{
		//chyba validace
		control.style.backgroundColor='red';
		//failed.className='error';
		//failed.value=error_description;
                return false;
	}
}