// JavaScript Document

$(document).ready(function(){

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

    $('select[id$=_behaeltertyp]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');


        $.post(acturl,
        {
            id:id,
            value:$(this).val()
        },
        function(data){
                updateBehTyp(data);
            },
            'json'
            );
    });

    $('input[id$=_abywaage_brutto]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if(js_validate_float(this)){
            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateWaageData(data);
                },
                'json'
                );
        }
    });

        $('input[id$=_stk_laut_waage]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if(js_validate_float(this)){
            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                    updateWaageData(data);
                },
                'json'
                );
        }
    });


        $('input[id*=_abywaage_behaelter_ist]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if(js_validate_float(this)){
            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateWaageData(data);
                },
                'json'
                );
        }
    });

        $('input[id*=_abywaage_kg_stk10]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if(js_validate_float(this)){
            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateWaageData(data);
                },
                'json'
                );
        }
    });

            $('input[id*=_kg_stk_bestellung]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if(js_validate_float(this)){
            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateWaageData(data);
                },
                'json'
                );
        }
    });

    $('td[id*=_auss_soll_kg_brutto]').each(function(){
        //zjistim is id
        id = $(this).attr('id');
        prvniPodtrzitko = id.indexOf('_', 0);
        idDauftr = id.substr(0, prvniPodtrzitko)

        aussSumme = $('#'+idDauftr+'_aussSumme').html();
        auss_abywaage_kg_stk10 = $('#'+idDauftr+'_auss_abywaage_kg_stk10').val();
        auss_abywaage_behaelter_ist = $('#'+idDauftr+'_auss_abywaage_behaelter_ist').val();
        auss_abywaage_brutto = $('#'+idDauftr+'_auss_abywaage_brutto').val();


        auss_soll_kg_brutto = aussSumme*auss_abywaage_kg_stk10+auss_abywaage_behaelter_ist*1;
        auss_soll_kg_brutto = Math.round(auss_soll_kg_brutto*100)/100;

        if(auss_abywaage_kg_stk10>0){
            auss_stk_laut_waage = Math.floor((auss_abywaage_brutto-auss_abywaage_behaelter_ist)/auss_abywaage_kg_stk10);
        }
        else{
            auss_stk_laut_waage = 0;
        }

        $(this).html(auss_soll_kg_brutto);
        $('#'+idDauftr+'_auss_stk_laut_waage').html(auss_stk_laut_waage);
    });


    // vypocet souctu po nahrani stranky
    $('tr.firstteil').each(function(){
        var mojeId = $(this).attr('id');
        posledniPodtrzitko = mojeId.lastIndexOf('_');
        var dauftrid = mojeId.substr(posledniPodtrzitko+1);
        updateSummeZeile(dauftrid);
    });
});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function updateBehTyp(data){
    if(data.affectedrows>0){
        if(data.auss>0){
            id = data.dauftrid+'_auss_abywaage_behaelter_ist';
            behTypId = data.dauftrid+'_auss_behaeltertyp';
        }
        else{
            id = data.dauftrid+'_abywaage_behaelter_ist';
            behTypId = data.dauftrid+'_behaeltertyp';
        }

        behTypValue = $('#'+behTypId).val();
        //alert('behTypId='+behTypId+'behTypValue='+behTypValue)
        // vahu palety menim jen pokud jeste neni zadna vyplnena, tj. v policku je 0
        if(($('#'+id).val()==0)&&(behTypValue!=9999)){
            $('#'+id).val(data.behgewicht);

            var gewValue = $('#'+id).val();

            //var id = $(this).attr('id');
            var acturl = $('#'+id).attr('acturl');

            $.post(acturl,
            {
                id:id,
                //value:data.behgewicht
                value:gewValue
            },
            function(data){
                updateWaageData(data);
            },
            'json'
            );
        }
    }
}

function updateSummeZeile(dauftrid){
            //zkompletuju id pro policko s oznacenim dilu
            teilId = dauftrid+'_teil';
            //vyberu pole s dilem a zjistim kompletni id
            teilIdKomplett = $('td[id^='+teilId+']').attr('id');
            idProRadky = 'platte_'+teilIdKomplett.substr(teilIdKomplett.indexOf('teil',0)).substr(5);
            //$('td[id='+teilIdKomplett+']').css({'background-color':'green'}).html(idProRadky);
            // vyberu vsechny radky
            var importstk = 0;
            var exportstk = 0;
            var export_laut_waage = 0;
            var abywaage_behaelter_ist = 0;
            var ist_kg_netto = 0;
            var soll_kg_brutto = 0;
            var abywaage_brutto = 0;
            var kunde_behaelter_bestellung_netto = 0;

            $('tr[id^='+idProRadky+']').each(function(index){
                //importstk
                $(this).find('td[id$=_importstk]').each(function(){
                    importstk += $(this).html()*1;
                });
                //exportstk
                $(this).find('td[id$=_exportstk]').each(function(){
                    exportstk += $(this).html()*1;
                });
                $(this).find('td[id$=_aussSumme]').each(function(){
                    exportstk += $(this).html()*1;
                });
                //export laut waage, toto veme i zmetky
                //$(this).find('td[id$=_stk_laut_waage]').each(function(){
                $(this).find('input[id$=_stk_laut_waage]').each(function(){
                    export_laut_waage += $(this).val()*1;
                });
                //abywaage_behaelter_ist
                $(this).find('input[id$=_abywaage_behaelter_ist]').each(function(){
                    abywaage_behaelter_ist += $(this).val()*1;
                });
                //ist_kg_netto, toto veme i zmetky
                $(this).find('td[id$=_ist_kg_netto]').each(function(){
                    ist_kg_netto += $(this).html()*1;
                });
                //soll_kg_brutto, toto veme i zmetky
                $(this).find('td[id$=_soll_kg_brutto]').each(function(){
                    soll_kg_brutto += $(this).html()*1;
                });
                //abywaage_brutto
                $(this).find('input[id$=_abywaage_brutto]').each(function(){
                    abywaage_brutto += $(this).val()*1;
                });
                //kunde_behaelter_bestellung_netto
                $(this).find('td[id$=_kunde_behaelter_bestellung_netto]').each(function(){
                    kunde_behaelter_bestellung_netto += $(this).html()*1;
                });
            });
            //alert(importstk);
            $('#'+idProRadky+'_stkimport').html(importstk);
            $('#'+idProRadky+'_stkexport').html(exportstk);
            $('#'+idProRadky+'_stk_laut_waage').html(export_laut_waage);
            $('#'+idProRadky+'_abywaage_behaelter_ist').html(Math.round(abywaage_behaelter_ist*100)/100);
            $('#'+idProRadky+'_ist_kg_netto').html(Math.round(ist_kg_netto*100)/100);
            $('#'+idProRadky+'_soll_kg_brutto').html(Math.round(soll_kg_brutto*100)/100);
            $('#'+idProRadky+'_abywaage_brutto').html(Math.round(abywaage_brutto*100)/100);
            $('#'+idProRadky+'_kunde_behaelter_bestellung_netto').html(Math.round(kunde_behaelter_bestellung_netto*100)/100);

}

function updateWaageData(data){
    // potreba
    if(data.affectedrows>0){
        if(data.auss==0){
            behTypId = data.dauftrid+'_behaeltertyp';
            // radek s dobrymi kusy
            //$('#'+data.dauftrid+'_stk_laut_waage').html(data.stk_laut_waage);
            $('#'+data.dauftrid+'_ist_kg_netto').html(data.ist_kg_netto);
            $('#'+data.dauftrid+'_soll_kg_brutto').html(data.soll_kg_brutto);
            $('#'+data.dauftrid+'_kunde_behaelter_bestellung_netto').html(data.kunde_behaelter_bestellung_netto);
        }
        else{
            //radek s ausschussy
            behTypId = data.dauftrid+'_auss_behaeltertyp';
            //$('#'+data.dauftrid+'_auss_stk_laut_waage').html(data.stk_laut_waage);
            $('#'+data.dauftrid+'_auss_ist_kg_netto').html(data.ist_kg_netto);
            $('#'+data.dauftrid+'_auss_soll_kg_brutto').html(data.soll_kg_brutto);
            $('#'+data.dauftrid+'_auss_kunde_behaelter_bestellung_netto').html(data.kunde_behaelter_bestellung_netto);
        }
        updateSummeZeile(data.dauftrid);
        if(data.updateBehTyp!=null){
            //alert(behTypId+' = '+data.updateBehTyp);
            $('#'+behTypId).val(data.updateBehTyp);
            id = behTypId;
            acturl = $('#'+behTypId).attr('acturl');
            $.post(acturl,
            {
                id:id,
                value:$('#'+id).val()
            },
            function(data){
                //updateBehTyp(data);
            },
            'json'
            );
        }
    }
}

function updateSumme(id){
    $('#'+'platte')
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


