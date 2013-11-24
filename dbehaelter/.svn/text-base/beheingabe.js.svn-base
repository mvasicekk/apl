// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    // posun k dalsimu inputu pomoci enteru
    //var inputy = $('input:text');
//    var inputy = $('input[class=entermove]');
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

    $('input[id=im]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateImportData(data);
                },
                'json'
                );
    });

    $('input[id=ex]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateExportData(data);
                },
                'json'
                );
    });

    $('input[id=kundevon]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateKundeVonData(data);
                },
                'json'
                );
    });

    $('input[id=kundenach]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateKundeNachData(data);
                },
                'json'
                );
    });

    $('input[id=datum]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                im:$('#im').val(),
                ex:$('#ex').val()
            },
            function(data){
                    updateDatumData(data);
                },
                'json'
                );
    });

});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------


function updateKundeVonData(data){

    if(data.kundeInfoArray!=null){
        $('#kundevon_info').html(data.kundeInfoArray[0].name1);
    }
    else{
        $('#kundevon_info').html('Kunde nicht gefunden !!');
        $('#kundevon').val('');
    }
}

function updateKundeNachData(data){

    if(data.kundeInfoArray!=null){
        $('#kundenach_info').html(data.kundeInfoArray[0].name1);
    }
    else{
        $('#kundenach_info').html('Kunde nicht gefunden !!');
        $('#kundenach').val('');
    }
}

function updateImportData(data){
    if(data.auftragArray!=null){
        if($('#behtablecontent').length!=0) $('#behtablecontent').remove();
        behInfo = 'Auftragsnr: '+data.auftragArray[0].auftragsnr+', Importdatum: '+data.auftragArray[0].aufdat
        $('#export_info').html('');
        $('#ex').val('');
        $('#import_info').html(behInfo);
        $('#kundevon').val(data.auftragArray[0].kunde);
        $('#kundenach').val(100);
        $('#datum').val(data.auftragArray[0].aufdat);
        $('#beheingabetable').html(data.behtablecontent);
        addEventHandlerrToStkInputs();
    }
    else{
        $('#import_info').html('');
        if($('#behtablecontent').length!=0) $('#behtablecontent').remove();
    }
}

function updateExportData(data){
    if(data.auftragArray!=null){
        if($('#behtablecontent').length!=0) $('#behtablecontent').remove();
        behInfo = 'Auftragsnr: '+data.auftragArray[0].auftragsnr+', Auslieferdatum: '+data.auftragArray[0].ausliefer_datum
        $('#export_info').html(behInfo);
        $('#import_info').html('');
        $('#im').val('');
        $('#kundevon').val(100);
        $('#kundenach').val(data.auftragArray[0].kunde);
        $('#datum').val(data.auftragArray[0].ausliefer_datum);
        $('#beheingabetable').html(data.behtablecontent);
        addEventHandlerrToStkInputs();
    }
    else{
        $('#export_info').html('');
        if($('#im').val()==''){
            if($('#behtablecontent').length!=0) $('#behtablecontent').remove();
        }
    }
}

function addEventHandlerrToStkInputs(){
    $('input[id^=beheingabe_stk_]').change(function(e){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                im:$('#im').val(),
                ex:$('#ex').val(),
                datum:$('#datum').val(),
                von:$('#kundevon').val(),
                nach:$('#kundenach').val()
            },
            function(data){
                    updateStkInput(data);
                },
                'json'
                );
    });

    // pridat pohyb pomoci enter
    var inputy = $('#beheingabetable input[class=entermove]');
    $('input:text[class=entermove]').bind('focus',function(e){
        this.select();
    });

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

}

function updateStkInput(data){
    
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