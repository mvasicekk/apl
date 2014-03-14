// JavaScript Document

$(document).ready(function(){

	$.datepicker.setDefaults($.datepicker.regional["de"]);
	$(".datepicker" ).datepicker($.datepicker.regional["de"]);

	$('#ex_datum_soll').bind('change',exSoll);
	$('#ex_zeit_soll').bind('blur',exSoll);

	
	$( "#zielort" ).autocomplete({
			source: "getZielorte.php?kd="+$('#kundenr').val(),
			minLength: 0,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
					event.preventDefault();
					this.value = ui.item.label;
					$('#ziel_value').val(ui.item.value);
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": 300,"color":"black","font-size":"12px"}
				);
			},
			change: zielortChange
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
		    
//******************************************************************************

	$('td[id^=td_pal]').click(function(e){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        if($('div.palbemerkungdiv').length!=0){
            $('div.palbemerkungdiv').remove();
	    return;
        }

            $.post(acturl,
            {
                id:id
            },
            function(data){
                updatePalBemerkung(data);
                },
                'json'
                );
    });
	
	
        $('input[id=bemerkung]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

            $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                auftrag:$('#auftragsnr').val()
            },
            function(data){
                updateBemerkungData(data);
                },
                'json'
                );
    });

    $('#teilfilter').click(function(event){
        var filterDiv = "<div id='teilfilterdiv'><table>";
            filterDiv += "<tr><td align='center'><input type='text' id='teil' size='10' maxlength='10'/></td></tr>";
            filterDiv += "<tr><td align='center'><input type='button' id='buttonteil' value='Teil auswaehlen'/></td></tr>";
            filterDiv += "<tr><td align='center'><input type='button' id='buttonabbruch' value='Abbrechen'/></td></tr>";
            filterDiv += "</table></div>";
        if($('#teilfilterdiv').length!=0) $('#teilfilterdiv').remove();
        // pridat dalsi div
        $(filterDiv).appendTo('body');
        $('#teil').get(0).focus();
        // pridam eventhandler pro buttonteil

        $('#buttonteil').click(function(event){
            teil = $('#teil').val();
            auftragsnr=$('#auftragsnr').val();
            //alert('buttonhandler, teil='+teil);
            window.location.href = './exportfuellenTeil.php?teil='+teil+'&auftragsnr='+auftragsnr;
        });

         $('#buttonabbruch').click(function(event){
            if($('#teilfilterdiv').length!=0) $('#teilfilterdiv').remove();
        });
    });

});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------
function zielortChange(event){
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:$(this).attr('id'),
	    val:$('#ziel_value').val()
        },
        function(data){
            updateZielortChange(data);
        },
        'json'
        );        
}

function updateZielortChange(data){
    
}

function exSoll(event){
    var id = $(this).attr('id');
    var acturl = $(this).attr('acturl');

    $.post(acturl,
    {
	id:id,
	val:$(this).val()
    },
    function(data){
	updateExSoll(data);
    },
    'json'
    );
}

function updateExSoll(data){
    if(data.zeit!=null)
	$('#ex_zeit_soll').val(data.zeit);
}


function updatePalBemerkung(data){
//    alert('updatePalBemerkung');
    var buttonOffset = $('#'+data.tdid).offset();
//    alert(data.tdid);
    $(data.div).appendTo('body');
    buttonOffset.top += $('#'+data.tdid).outerHeight();
    $('div.palbemerkungdiv').css({
        "left":buttonOffset.left+"px"
    });
    $('div.palbemerkungdiv').css({
        "top":buttonOffset.top+"px"
    });
    
    var inputy = $('input[id^=bemerkung]');
    
    //na prvni nastavim focus
    if(inputy[1]!=null){
        inputy[1].focus();
//        inputy[1].select();
    }
    $('#bemerkung_'+data.gid).blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        var value = $(this).val();

        $.post(acturl,
        {
            id:id,
	    gid:data.gid,
	    value:value
        },
        function(data){
            updateChangeBemerkung(data);
        },
        'json'
        );
    });
}

function updateChangeBemerkung(data){
    $('div.palbemerkungdiv').remove();
}

function updateBemerkungData(data){
    // potreba
    if(data.affectedrows>0){
    }
}

