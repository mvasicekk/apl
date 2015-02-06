// JavaScript Document

$(document).ready(function(){


    $('input[id^=text1_]').bind('change',text1Updatet);
    
    $('#rechnung_aby_teilen_form').hide();


    //alert($('#hat_ma_rechnung').val());
    if($('#hat_ma_rechnung').val()!=0){
          $('#td_rechnung_drueck_auswahl').show();
    }
    else{
        $('#td_rechnung_drueck_auswahl').hide();
    }
    
    $('#bt_markieren_run').hide();
    
    $('#bt_rechnung_teilen').click(function(){
        if($('#rechnung_aby_teilen_form').is(':visible')){
            $('#rechnung_aby_teilen_form').hide();
            $('#flag_teilen').val('0');
        }
        else
            $('#rechnung_aby_teilen_form').show();
    });

    //alert($('input[name=dt]').val());

    $('#bt_markieren').click(function(){
        var abgnrVon = parseInt($('#abgnr_von').val());
        var abgnrBis = parseInt($('#abgnr_bis').val());
        //alert('von='+abgnrVon+' bis='+abgnrBis);
	var pocetOznacenych = 0;
        // projit vsechny radky tabulky
        $('table.formulartable tr[class=rechnung_table_position]').css({'background-color':''});
        $('table.formulartable tr[class=rechnung_table_position]').each(function(index){
            var rowId = $(this).attr('id');
            //alert('rowid='+rowId);
            var rowAbgnr = parseInt(rowId.substring(rowId.lastIndexOf('_')+1));
            //css({'background-color':'red'});
            //alert('rowAbgnr='+rowAbgnr);
            if((rowAbgnr>=abgnrVon) && (rowAbgnr<=abgnrBis)){
              $(this).css({'background-color':'yellow'});
	      pocetOznacenych++;
            }
        });

        // enable teilen button
	if($('#rechnr_ma').val()!="00000000"){
	    if(pocetOznacenych>0){
		$('#bt_markieren_run').attr('value','Teilen ( '+pocetOznacenych+' )');
		$('#bt_markieren_run').show();
	    }
	    else{
		$('#bt_markieren_run').attr('value','Teilen ( '+pocetOznacenych+' )');
		$('#bt_markieren_run').show();
	    }
	}
        
        //bind ajaxfunction to run button
        $('#bt_markieren_run').click(function(){
            var acturl = $(this).attr('acturl');
            //alert(acturl);
            var id = $(this).attr('id');

            $.post(acturl,
            {
                id:id,
                abgnr_von:$('#abgnr_von').val(),
                abgnr_bis:$('#abgnr_bis').val(),
                rechnr_regular:$('#rechnr_regular').val(),
                rechnr_ma:$('#rechnr_ma').val()
        },
        function(data){
                updateRechnungTeilen(data);
            },
            'json'
            );
        });
    });

    $('#bt_abbrechen').click(function(){
            $('table.formulartable tr[class=rechnung_table_position]').css({'background-color':''});
            $('#rechnung_aby_teilen_form').hide();
	    $('#bt_markieren_run').hide();
            $('#flag_teilen').val('0');
        });
});


// Ajax update Functions

function text1Updatet(event){
    var Id  = $(this).attr('id');
    var acturl = $(this).attr('acturl');
    
    $.post(acturl,
	{
	id:Id,
        value:$(this).val()
        },
        function(data){
		updateDrechUpdate(data);
            },
            'json'
    );
}


function updateDrechUpdate(data){
    
}

function updateRechnungTeilen(data){
    //alert('affected_rows='+data.ar);
    $('#rechnung_aby_teilen_form').hide('slow');
    $('#td_rechnung_drueck_auswahl').show();
    $('#bt_rechnung_teilen').attr('disabled', 'disabled');
    $('#hat_ma_rechnung').val(1);
}