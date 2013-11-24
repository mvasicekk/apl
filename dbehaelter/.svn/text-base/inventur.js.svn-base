// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    // posun k dalsimu inputu pomoci enteru
    //var inputy = $('input:text');
    var inputy = $('.entermove');
    $('input:text[class=entermove]').bind('focus',function(e){
        this.select();
    });
    
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }
    //inputy.css({'background-color':'green'});
    //alert(inputy);
//    inputy.bind('keypress',function(e){
//        var key = e.which;
//        if(key==13){
//            e.preventDefault();
//
//            var nextIndex = inputy.index(this) + 1;
//            //alert('this='+this+' nextIndex='+nextIndex);
//            if(inputy[nextIndex]!=null){
//                var nextBox = inputy[nextIndex];
//                //alert(nextBox);
//                nextBox.focus();
//                //nextBox.select();
//            }
//        }
//    });


    $('input[id=behaelternr]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                kunde:$('#kunde').val(),
                datum:$('#datum').val()
            },
            function(data){
                    updateBehaelterNrData(data);
                },
                'json'
                );
    });

        $('input[id=kunde]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                behaelternr:$('#behaelternr').val(),
                kunde:$('#kunde').val(),
                datum:$('#datum').val()
            },
            function(data){
                    updateKundeData(data);
                },
                'json'
                );
    });

        $('input[id=kundekontostk]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                behaelternr:$('#behaelternr').val(),
                kunde:$('#kunde').val(),
                datum:$('#datum').val()
            },
            function(data){
                    updateKundeKontoData(data);
                },
                'json'
                );
        });

        $('input[id=datum]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                behaelternr:$('#behaelternr').val(),
                kunde:$('#kunde').val(),
                datum:$('#datum').val(),
                kundekontostk:$('#kundekontostk').val()
            },
            function(data){
                    updateDatumData(data);
                },
                'json'
                );
        });

        $('input[id^=inventur_stk_]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                behaelternr:$('#behaelternr').val(),
                kunde:$('#kunde').val(),
                datum:$('#datum').val()
            },
            function(data){
                    updateKundeKontoData(data);
                },
                'json'
                );
    });


});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function updateKundeKontoData(data){

}

function updateDatumData(data){

        $('input[id^=inventur_stk_]').each(function(){
                $(this).val(0);
            });
        $('#kundekontostk').val(0);

        if(data.stkArray!=null){
            //naplnim hodnoty s kusama
            $.each(data.stkArray,function(index,value){
                if((value.zustand_id==9999) && (value.platz_id=='KDKONTO')){
                    $('#kundekontostk').val(value.stk);
                }
                else{
                    id='inventur_stk_'+value.zustand_id+'_'+value.platz_id;
                    $('#'+id).val(value.stk);
                }
                datum = value.datum;
            });
            $('#datum').val(datum);
        }
}

function updateKundeData(data){

        $('input[id^=inventur_stk_]').each(function(){
                $(this).val(0);
            });
        $('#kundekontostk').val(0);

        //$('#datum').val('');

        if(data.kundeInfoArray!=null){
        $('#kunde_info').html(data.kundeInfoArray[0].name1);
        if(data.stkArray!=null){
            //naplnim hodnoty s kusama
            $.each(data.stkArray,function(index,value){
                if((value.zustand_id==9999) && (value.platz_id=='KDKONTO')){
                    $('#kundekontostk').val(value.stk);
                }
                else{
                    id='inventur_stk_'+value.zustand_id+'_'+value.platz_id;
                    $('#'+id).val(value.stk);
                }
                datum = value.datum;
            });
            $('#datum').val(datum);
        }
    }
    else{
        $('#kunde_info').html('Kunde nicht gefunden !!');
        $('#kunde').val('');
        $('#kunde').select();
    }
}
function updateBehaelterNrData(data){
        $('input[id^=inventur_stk_]').each(function(){
                $(this).val(0);
            });
        $('#kundekontostk').val(0);
    if(data.artikelArray!=null){
        $('#behaelternr_info').html(data.artikelArray[0].name);
                if(data.stkArray!=null){
            //naplnim hodnoty s kusama
            $.each(data.stkArray,function(index,value){
                if((value.zustand_id==9999) && (value.platz_id=='KDKONTO')){
                    $('#kundekontostk').val(value.stk);
                }
                else{
                    id='inventur_stk_'+value.zustand_id+'_'+value.platz_id;
                    $('#'+id).val(value.stk);
                }
                datum = value.datum;
            });
            $('#datum').val(datum);
        }
    }
    else{
        $('#behaelternr_info').html('BehaelterNr nicht gefunden !!');
        $('#behaelternr').val('');
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