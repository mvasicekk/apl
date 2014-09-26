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


    inputy.bind('keydown',function(e){
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

//    $('#repneu').hide();
    $('#repneu').keydown(function(event){
        if(event.keyCode==13) $('#repneu').click();
    });

    $('#repneu').click(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        $.post(acturl,
        {
            id:id,
            invnummer:$('#invnummer').val(),
            persnr_reparatur:$('#persnr_reparatur').val(),
            persnr_ma:$('#persnr_ma').val(),
            datum:$('#datum').val(),
            repzeit:$('#repzeit').val(),
            repbemerkung:$('#repbemerkung').val()
         },
         function(data){
            insertReparaturKopfData(data);
         },
         'json'
         );
    });

    $( "#invnummer" ).autocomplete({
			source: "searchInvnummer.php",
			minLength: 2,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // zadat novou masinu ?
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }
			},
       			change: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }

                                    var id = $(this).attr('id');
                                    var acturl = $(this).attr('acturl');
                                    $.post(acturl,
                                    {
                                            id:id,
                                            invnummer:$('#invnummer').val(),
                                            persnr_reparatur:$('#persnr_reparatur').val(),
                                            persnr_ma:$('#persnr_ma').val(),
                                            datum:$('#datum').val(),
                                            repzeit:$('#repzeit').val(),
                                            repbemerkung:$('#repbemerkung').val()
                                    },
                                    function(data){
                                         updateReparaturKopfData(data);
                                    },
                                    'json'
                                    );
                                }
		});

     $( "#persnr_reparatur" ).autocomplete({
			source: "searchPersnrReparatur.php",
			minLength: 1,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }
                                },
       			change: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }
                                    var id = $(this).attr('id');
                                    var acturl = $(this).attr('acturl');
                                    $.post(acturl,
                                    {
                                            id:id,
                                            invnummer:$('#invnummer').val(),
                                            persnr_reparatur:$('#persnr_reparatur').val(),
                                            persnr_ma:$('#persnr_ma').val(),
                                            datum:$('#datum').val(),
                                            repzeit:$('#repzeit').val(),
                                            repbemerkung:$('#repbemerkung').val()
                                    },
                                    function(data){
                                         updateReparaturKopfData(data);
                                    },
                                    'json'
                                    );
                                }
		});
     $( "#persnr_ma" ).autocomplete({
			source: "searchPersnrReparatur.php",
			minLength: 1,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }
                                },
       			change: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        $(this).val('');
                                        $('#repid').val(0);
                                    }
                                    var id = $(this).attr('id');
                                    var acturl = $(this).attr('acturl');
                                    $.post(acturl,
                                    {
                                            id:id,
                                            invnummer:$('#invnummer').val(),
                                            persnr_reparatur:$('#persnr_reparatur').val(),
                                            persnr_ma:$('#persnr_ma').val(),
                                            datum:$('#datum').val(),
                                            repzeit:$('#repzeit').val(),
                                            repbemerkung:$('#repbemerkung').val()
                                    },
                                    function(data){
                                         updateReparaturKopfData(data);
                                    },
                                    'json'
                                    );
                                }
		});


//
    $('input[id=datum]').change(function(event){
                                    var id = $(this).attr('id');
                                    var acturl = $(this).attr('acturl');
                                    $.post(acturl,
                                    {
                                            id:id,
                                            invnummer:$('#invnummer').val(),
                                            persnr_reparatur:$('#persnr_reparatur').val(),
                                            persnr_ma:$('#persnr_ma').val(),
                                            datum:$('#datum').val(),
                                            repzeit:$('#repzeit').val(),
                                            repbemerkung:$('#repbemerkung').val()
                                    },
                                    function(data){
                                         updateReparaturKopfData(data);
                                    },
                                    'json'
                                    );
    });

    $('input[id=repzeit]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        if($('#repid').val()>0){
            $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                repid:$('#repid').val()
            },
            function(data){
                updateReparaturKopfValues(data);
            },
            'json'
            );
        }
    });

    $('input[id=repbemerkung]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');
        if($('#repid').val()>0){
            $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                repid:$('#repid').val()
            },
            function(data){
                updateReparaturKopfValues(data);
            },
            'json'
            );
        }
    });
});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function updateReparaturKopfValues(data){

}

function updateReparaturKopfData(data){
    $('#ersatzteileeingabetable').html('');
    if(data.inputOK==true){
        if(data.reparaturKopfArray==null){
            // oprava jeste neexistuje, zpristupnim tlacitko pro vytvoreni nove opravy
            $('#repneu').show();
            $('#reparaturIDinfo').html('Neue Reparatur ?');
            $('#repzeit').val(0);
            $('#repbemerkung').val('');
            $('#repid').val(0);
        }
        else{
            $('#repneu').hide();
            //radek pro opravu uz existuje naplnim cas a poznamku
            // pro informaci zobrazim id opravy
            $('#reparaturIDinfo').html('Reparatur ID: '+data.reparaturKopfArray[0].id);
            $('#repzeit').val(data.reparaturKopfArray[0].repzeit);
            $('#repbemerkung').val(data.reparaturKopfArray[0].bemerkung);
            $('#repid').val(data.reparaturKopfArray[0].id);
            //zobrazim pozice pro nahradni dily
            $('#ersatzteileeingabetable').html(data.reparaturPositionenDiv);
            //priradit eventy k inputum
            reparaturPosAnzahlInputEventsAdd();
        }
    }
    else{
        $('#repneu').hide();
    }
}

function insertReparaturKopfData(data){
    $('#ersatzteileeingabetable').html('');
    if(data.reparaturID>0){
            $('#repneu').hide();
            $('#reparaturIDinfo').html('Reparatur ID: '+data.reparaturID);
            $('#repid').val(data.reparaturID);
            $('#ersatzteileeingabetable').html(data.reparaturPositionenDiv);
            reparaturPosAnzahlInputEventsAdd();
    }
}

function reparaturPosAnzahlInputEventsAdd(){
        $('input[id^=etpos_]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateReparaturPosAnzahl(data);
            },
            'json'
            );
    });

    $('input[id^=etalt_]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

            $.post(acturl,
            {
                id:id,
                value:$(this).attr('checked')?1:0
            },
            function(data){
                updateReparaturPosAnzahl(data);
            },
            'json'
            );
    });
    
        $('input[id^=etinvnummer_]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateReparaturPosAnzahl(data);
            },
            'json'
            );
    });
    
        $('input[id^=etbem_]').change(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

            $.post(acturl,
            {
                id:id,
                value:$(this).val()
            },
            function(data){
                updateReparaturPosAnzahl(data);
            },
            'json'
            );
    });
}

function updateReparaturPosAnzahl(data){

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