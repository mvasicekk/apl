// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    // posun k dalsimu inputu pomoci enteru
    var inputy = $('.entermove');
    $('input:text[class=entermove]').bind('focus',function(e){
        this.select();
    });
    
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }

    $('input[id=enter]').keypress(function(event){
        if(event.keyCode==13) $('#enter').click();
    });

    inputy.bind('keypress',function(e){
        var key = e.which;
        if(key==13){
            e.preventDefault();

            var nextIndex = inputy.index(this) + 1;
            //alert('this='+this+' nextIndex='+nextIndex);
            if(inputy[nextIndex]!=null){
                var nextBox = inputy[nextIndex];
                //alert(nextBox);
                nextBox.focus();
            //nextBox.select();
            }
        }
    });

    $('input[id=enter]').click(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$(this).val(),
            behaelternr:$('#behaelternr').val(),
            kunde:$('#kunde').val(),
            datum:$('#datum').val(),
            stk:$('#stk').val(),
            bein:$('#bein').val(),
            bezu:$('#bezu').val(),
            lagplatz:$('#lagplatz').val()
        },
        function(data){
            updateEnterData(data);
        },
        'json'
        );
    });


        $('#behaelternr').autocomplete({
        source: "searchBehNr.php",
        minLength: 0,
        autoFocus: true,
        select: function( event, ui ) {
            if(ui.item){
            }
            else{
                // zadat novou masinu ?
                $(this).val('');
            }
        },
        change: function( event, ui ) {
            if(ui.item){
            }
            else{
                $(this).val('');
            }

            var id = $(this).attr('id');
            var acturl = $(this).attr('acturl');
            $.post(acturl,
            {
                id:id,
                behnr:$('#behaelternr').val()
            },
            function(data){
                updateBehNrData(data);
            },
            'json'
            );
        }
    });

    $('input[id=kunde]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$(this).val(),
            behnr:$('#behaelternr').val()
        },
        function(data){
            updateKundeData(data);
        },
        'json'
        );
    });

    $('#bezu').autocomplete({
        source: "searchBehZustand.php?zustand_typ=bezu",
        minLength: 0,
        autoFocus: true,
        select: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                // zadat novou masinu ?
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }
        },
        change: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }
        }
    });

    $('#bein').autocomplete({
        source: "searchBehZustand.php?zustand_typ=bein",
        minLength: 0,
        autoFocus: true,
        select: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                // zadat novou masinu ?
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }
        },
        change: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }

        }
    });

    $('#lagplatz').autocomplete({
        source: "searchLagPlatz.php",
        minLength: 0,
        autoFocus: true,
        select: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                // zadat novou masinu ?
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }
        },
        change: function( event, ui ) {
            if(ui.item){
                $(this).css({
                    "background-color":""
                })
            }
            else{
                $(this).val('');
                $(this).css({
                    "background-color":"red"
                })
                $(this).focus();
                $(this).select();
            }

        }
    });
//
//
//    $('input[id=im]').blur(function(event){
//        var id = $(this).attr('id');
//        var acturl = $(this).attr('acturl');
//
//        $.post(acturl,
//        {
//            id:id,
//            value:$(this).val()
//        },
//        function(data){
//            updateImportData(data);
//        },
//        'json'
//        );
//    });
//
//    $('input[id=ex]').blur(function(event){
//        var id = $(this).attr('id');
//        var acturl = $(this).attr('acturl');
//
//        $.post(acturl,
//        {
//            id:id,
//            value:$(this).val()
//        },
//        function(data){
//            updateExportData(data);
//        },
//        'json'
//        );
//    });
//
//
//
//    $('input[id=kundenach]').blur(function(event){
//        var id = $(this).attr('id');
//        var acturl = $(this).attr('acturl');
//
//        $.post(acturl,
//        {
//            id:id,
//            value:$(this).val()
//        },
//        function(data){
//            updateKundeNachData(data);
//        },
//        'json'
//        );
//    });
//
//    $('input[id=datum]').change(function(event){
//        var id = $(this).attr('id');
//        var acturl = $(this).attr('acturl');
//
//        $.post(acturl,
//        {
//            id:id,
//            value:$(this).val(),
//            im:$('#im').val(),
//            ex:$('#ex').val()
//        },
//        function(data){
//            updateDatumData(data);
//        },
//        'json'
//        );
//    });
//
//
//    $('input[id^=delbehbew_').click(function(event){
//        var id = $(this).attr('id');
//        var acturl = $(this).attr('acturl');
//        $.post(acturl,
//        {
//            id:id,
//            value:$(this).val()
//        },
//        function(data){
//            updateDelBehBew(data);
//        },
//        'json'
//        );
//    });
//
//

});

// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function updateBehNrData(data){

}
function addDelBehBewHandler(){

    $('input[id^=delinv_]').click(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateDelBehBew(data);
                },
                'json'
                );
    });
}

function updateDelBehBew(data){
    // podle obsahu importu nebo exportu vyvolam funkci pro obcerstveni
        id='kunde'
        var acturl = $('#'+id).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$('#'+id).val(),
            behnr:$('#behaelternr').val()
        },
        function(data){
            updateKundeData(data);
        },
        'json'
        );
}

function updateKundeData(data){

    if(data.kundeInfoArray!=null){
        $('#kunde_info').html(data.kundeInfoArray[0].name1);
        updateBewegungDiv(data.divContent);
    }
    else{
        $('#kunde_info').html('Kunde nicht gefunden !!');
        $('#kunde').val('');
    }
}

function updateZustandData(data){
    if(data.found==0){
        //nenasel jsem takovy zustand
        $('#zustand').val('');
        $('#zustand_info').html('Zustand nicht gefunden !!!');
    }
    else{
        $('#zustand_info').html(data.zustand_text);
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

function updateBewegungDiv(content){
        if($('#behtablecontent').length!=0) $('#behtablecontent').remove();
        $('#bewegungendiv').html(content);
        addDelBehBewHandler();
}

function updateImExData(data){
        if(data.auftragArray!=null){
        updateBewegungDiv(data.behtablecontent);
    }
    else{
        // pokud je alespon im nebo ex vyplnene , nebudu mazat obsah divu
        if($('#im').val().length>0 || $('#ex').val().length>0)
            //nedelam nic
            a=1;
        else
            $('#bewegungendiv').html('');
    }
}


function updateImportData(data){
    if(data.auftragArray!=null){
        behInfo = 'Auftragsnr: '+data.auftragArray[0].auftragsnr+', Importdatum: '+data.auftragArray[0].aufdat
        $('#import_info').html(behInfo);
        $('#kundevon').val(data.auftragArray[0].kunde);
        $('#kundenach').val(100);
        $('#zustand').val(1);
        $('#datum').val(data.auftragArray[0].aufdat);
        updateBewegungDiv(data.behtablecontent);
    }
    else{
        $('#import_info').html('');
        $('#bewegungendiv').html('');
    }
}

function updateExportData(data){
    if(data.auftragArray!=null){
        behInfo = 'Auftragsnr: '+data.auftragArray[0].auftragsnr+', Auslieferdatum: '+data.auftragArray[0].ausliefer_datum
        $('#export_info').html(behInfo);
        $('#import_info').html('');
        $('#im').val('');
        $('#kundevon').val(100);
        $('#kundenach').val(data.auftragArray[0].kunde);
        $('#zustand').val(1);
        $('#datum').val(data.auftragArray[0].ausliefer_datum);
        updateBewegungDiv(data.behtablecontent);
    }
    else{
        $('#export_info').html('');
        if($('#im').val().length>0)
            //nedeleam nic
            a=1;
          else
            $('#bewegungendiv').html('');
    }
}

function updateBehaelterNrData(data){
    if(data.artikelArray!=null){
        $('#behaelternr_info').html(data.artikelArray[0].name);
    }
    else{
        $('#behaelternr_info').html('BehaelterNr nicht gefunden !!');
        $('#behaelternr').val('');
        $('#behaelternr').select();
    }
}

function updateEnterData(data){
    if(data.fehler==1){
        $('#enter_info').html('Fehler bei der Eingabe ! Keine DAten nach DB geschrieben !');
    }
    else{
        id='kunde'
        var acturl = $('#'+id).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$('#'+id).val(),
            behnr:$('#behaelternr').val()
        },
        function(data){
            updateKundeData(data);
        },
        'json'
        );
        $('#enter_info').html('&nbsp;');
        $('#stk').val(0);
        $('#behaelternr').focus();
        $('#behaelternr').select();
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
