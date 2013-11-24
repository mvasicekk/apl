// JavaScript Document

function formatTime(time) {
    var result = false, m;
    var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
    if ((m = time.match(re))) {
        result = (m[1].length == 2 ? "" : "0") + m[1] + ":" + m[2];
    }
    return result;
}

function updateUmterminieren(data){
    $('#termine_table').html(data.chdiv);
}

function updateBemerkungChanged(data){
    
}

function updateTerminNeuChanged(data){
    if(data.exportinfo==null){
	// zadany export neexistuje nebo nepatri k vybranemu zakaznikovi
	$('#'+data.id).css({"border-color":"red"}).focus().select();
	$('#'+data.id).val('');
    }
    else{
	$('#'+data.id).css({"border-color":""});
	//vypitvat cast za podtrzitkem
	prilepek=data.id.substr(data.id.indexOf('_', 0),8);
	$('#ex_datum_soll_neu'+prilepek).val(data.exportinfo[0].ex_soll_datum);
	$('#ex_time_soll_neu'+prilepek).val(data.exportinfo[0].ex_soll_uhrzeit);
    }
}

function updateKundeChanged(data){
    $('#termine_table').html(data.divcontent);
    if(data.hasRows==1){
	$('#umterminieren_button').show();
	// mam policka u kterych nastavim ajaxevent
	
	$.datepicker.setDefaults($.datepicker.regional["de"]);
	$(".datepicker" ).datepicker($.datepicker.regional["de"]);
    
	$('input[id^=terminneu_]').change(function(event){
	    var acturl = $(this).attr('acturl');
	    var id = $(this).attr('id');
	    var value = $(this).val();
	    var kunde = $('#kunde').val();

	    $.post(acturl,
	    {
		id: id,
		value: value,
		kunde: kunde
	    },
	    function(data){
		updateTerminNeuChanged(data);
	    },
	    'json'
	    );
	});
	
	$('input[id^=bemerkung_]').change(function(event){
	    var acturl = $(this).attr('acturl');
	    var id = $(this).attr('id');
	    var value = $(this).val();
	    var kunde = $('#kunde').val();

	    $.post(acturl,
	    {
		id: id,
		value: value,
		kunde: kunde
	    },
	    function(data){
		updateBemerkungChanged(data);
	    },
	    'json'
	    );
	});

    
	$('input[id^=ex_time_soll_neu_]').blur(function(event){
	    var acturl = $(this).attr('acturl');
	    var id = $(this).attr('id');
	    var value = $(this).val();
	    if($.trim(value).length>0){
		formattedValue = formatTime(value);
		if(formattedValue!=false){
		    $(this).val(formattedValue);
		}
		else{
		    //spatny format casu
		    $(this).css({
			"border-color":"red"
		    }).focus().select();
		    $(this).val('');
		}
	    }
	}
	);
    }
}


$(document).ready(function(){

    var inputy = $('td input[id^=text]');

    //    $('#kunde').focus().select().css({"border-color":"red"});
    $('#umterminieren_button').hide();
    

    $('#kunde').blur(function(event){
            var acturl = $(this).attr('acturl');
            var id = $(this).attr('id');
            var value = $(this).val();

	    $.post(acturl,
            {
                id: id,
                value: value
            },
            function(data){
                updateKundeChanged(data);
            },
            'json'
            );
    });

    $('#umterminieren_button').click(function(event){
            var acturl = $(this).attr('acturl');
            var id = $(this).attr('id');
            var value = $(this).val();
	    var kunde = $('#kunde').val();

	    $(this).hide();
	    //projit vsechny nenulove hodnoty podle input#terminneu_ a vytvorit z nich pole
	    terminOldArray = new Array();
	    terminNeuArray = new Array();
	    exSollDatumArray = new Array();
	    exSollUhrzeitArray = new Array();
	    exBemerkungArray = new Array();
	    
	    citac = 0;
	    $('input[id^=terminneu_]').each(function(){
		exportNeuValue = $.trim($(this).val());
		if(exportNeuValue.length>0){
		    thisId = $(this).attr('id');
		    prilepek= thisId.substr(thisId.indexOf('_', 0),8);
		    exportOldValue = $('#export_old'+prilepek).html();
		    exSollDatumValue = $('#ex_datum_soll_neu'+prilepek).val();
		    exSollUhrzeitValue = $('#ex_time_soll_neu'+prilepek).val();
		    terminOldArray[citac]=(exportOldValue);
		    terminNeuArray[citac]=(exportNeuValue);
		    exSollDatumArray[citac]=(exSollDatumValue);
		    exSollUhrzeitArray[citac]=(exSollUhrzeitValue);
//		    alert(thisId+' '+$(this).val()+' old:'+exportOldValue+','+exSollDatumValue+','+exSollUhrzeitValue);
		    citac++;
		}
	    }
	    );
            $.post(acturl,
            {
                id: id,
		kunde: kunde,
		'terminOldArray[]': terminOldArray,
		'terminNeuArray[]': terminNeuArray,
		'exSollDatumArray[]': exSollDatumArray,
		'exSollUhrzeitArray[]': exSollUhrzeitArray
            },
            function(data){
                updateUmterminieren(data);
            },
            'json'
            );
    });

});
