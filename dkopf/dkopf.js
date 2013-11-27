// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    $('#showteildoku').bind('click',showTeilDoku);
    
    $('input[id=preis_stk_gut]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

    $('input[id=preis_stk_auss]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });
    
    $('input[id^=schwierigkeitsgrad_S]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

//------------------------------------------------------------------------------
    $('input[id=jb_lfd_2]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

    $('input[id=jb_lfd_1]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });
    
        $('input[id=jb_lfd_plus_1]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

    $('input[id=jb_lfd_j]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });
//------------------------------------------------------------------------------
$('input[id=restmengen_verw]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

        $('input[id=fremdauftr_dkopf]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });

        $('input[id=status]').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

         $.post(acturl,
            {
                id:id,
                value:$(this).val(),
                teil:$('#teil').val()
            },
            function(data){
                    updateDkopf(data);
                },
                'json'
                );
    });


});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function showTeilDoku(event){
    element = $('#showteildoku');
    var id=element.attr('id');
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowTeilDoku(data);
        },
        'json'
        );    
}

function updateshowTeilDoku(data){
    // zobrazit editovaci div
    if($('#dokuform').length!=0){
            $('#dokuform').remove();
	    if(data.id=='showteildoku') return;
        }
    $('body').append(data.formDiv);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    
//    $( "input[id^=r_doku_nr_]" ).autocomplete({
//			source: "getDokuTyp.php",
//			minLength: 0,
//                        autoFocus: true,
//			select: function( event, ui ) {
//                                    if(ui.item){
//                                    }
//                                    else{
//                                        // polozka neni v seznamu
//                                    }
//			},
//			open: function(event, ui) {
//				$(this).autocomplete("widget").css(
//				    {"width": 300,"color":"black","font-size":"12px"}
//				);
//			}
//		}).focus(function(){
//		    if ($(this).autocomplete("widget").is(":visible")) {
//			return;
//		    }
//		    $(this).data("autocomplete").search($(this).val());
//		    });

    $( "#n_doku_nr" ).autocomplete({
			source: "getDokuTyp.php",
			minLength: 0,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": 300,"color":"black","font-size":"12px"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
		
    $( "#n_freigabe_vom" ).autocomplete({
			source: "getFreigabeVom.php",
			minLength: 0,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"color":"black","font-size":"12px"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
		    
    $( "input[id^=r_freigabe_vom_]" ).autocomplete({
			source: "getFreigabeVom.php",
			minLength: 0,
//                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"color":"black","font-size":"12px"}
				);
			},
			change: dokuFieldChange
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });		    

   $('#n_doku_add').bind('click',dokuAdd);
   $('input[id^=i_doku_del_]').bind('click',dokuDel);
   $('input[id^=r_einlag_datum_]').bind('blur',dokuFieldChange);
   $('input[id^=r_freigabe_am_]').bind('blur',dokuFieldChange);
   $('input[id^=r_musterplatz_]').bind('blur',dokuFieldChange);
//   $('input[id^=r_freigabe_vom_]').change('click',dokuFieldChange);
   
}

function dokuFieldChange(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    val:$(element).val()
        },
        function(data){
            updatedokuFieldChange(data);
        },
        'json'
        );        
}

function updatedokuFieldChange(data){
    if(data.goUpdate==false){
	$('#'+data.id).val('');
	$('#'+data.id).css({"border-color":"red"});
	$('#'+data.id).focus();
    }
    else{
	$('#'+data.id).css({"border-color":""});
    }
}

function dokuDel(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updatedokuAdd(data);
        },
        'json'
        );        
}

function dokuAdd(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val(),
	    n_doku_nr:$('#n_doku_nr').val(),
	    n_einlag_datum:$('#n_einlag_datum').val(),
	    n_musterplatz:$('#n_musterplatz').val(),
	    n_freigabe_am:$('#n_freigabe_am').val(),
	    n_freigabe_vom:$('#n_freigabe_vom').val()
        },
        function(data){
            updatedokuAdd(data);
        },
        'json'
        );        
}

function updatedokuAdd(data){
    element = $('#showteildoku');
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:'n_doku_add',
	    teil:$('#teil').val()
        },
        function(data){
            updateshowTeilDoku(data);
        },
        'json'
        );    
}

function updateDkopf(data){

}

