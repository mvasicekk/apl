// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    $('#showteildoku').bind('click',showTeilDoku);
    $('#showvpm').bind('click',showVPM);
    // shows Teil Attachment
    $('#show_att_muster').bind('click',showTeilAtt);
    $('#show_att_empb').bind('click',showTeilAtt);
    $('#show_att_ppa').bind('click',showTeilAtt);
    $('#show_att_gpa').bind('click',showTeilAtt);
    $('#show_att_vpa').bind('click',showTeilAtt);
    $('#show_att_qanf').bind('click',showTeilAtt);
    $('#show_att_zeit').bind('click',showTeilAtt);
    $('#show_att_liefer').bind('click',showTeilAtt);
    $('#show_att_mehr').bind('click',showTeilAtt);
    $('#show_att_rekl').bind('click',showTeilAtt);

    $('#accordion').accordion();
    
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

function showTeilAtt(event){
    element = $(this);
    var id=element.attr('id');
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowTeilAtt(data);
        },
        'json'
        );    
}

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

function showVPM(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowVPM(data);
        },
        'json'
        );    
}

/**
 * vlastni zobrazeni divu s prilohama + odkazy ke stazeni
 * 
 */
function updateshowTeilAtt(data){
    if($('#dokuform').length!=0){
	$('#dokuform').remove();
	if(data.id=='show_att_muster') return;
	if(data.id=='show_att_empb') return;	
	if(data.id=='show_att_gpa') return;	
	if(data.id=='show_att_ppa') return;
	if(data.id=='show_att_vpa') return;
	if(data.id=='show_att_qanf') return;	
	if(data.id=='show_att_zeit') return;	
	if(data.id=='show_att_liefer') return;	
	if(data.id=='show_att_mehr') return;	
	if(data.id=='show_att_rekl') return;
    }
    // data jsou k dispozici
    if(data.docsArray!=null){
	$('body').append(data.formDiv);
	//priradim udalostni procedury pro slozky
	$('a.dir').bind('click',updateFolder);
	$('a.prevdir').bind('click',updateFolder);
	$('a.jpg').colorbox({
	    rel:'gal',
	    current:'{current} z/von {total}',
	    maxWidth:'90%',
	    maxHeight:'90%'
	});
	console.log(data.docsArray);
    }
    else{
	//automaticke zavreni divu pri neaktivite
	//alert('Keine Dateien / žádné soubory');
	$('body').append(data.formDiv);
//	$("#dokuform").fadeIn('slow').animate({opacity: 1.0}, 1500).effect("pulsate", { times: 2 }, 800).fadeOut('slow');
//	if($('#dokuform').length!=0) $('#dokuform').remove();
    }

    ppaDir = $('div[id^=uploader_]').attr('folder');
    upid = $('div[id^=uploader_]').attr('id');
    //alert(ppaDir+':'+upid);
    var uploader = new plupload.Uploader({
	    runtimes: 'html5,flash,browserplus',
	    flash_swf_url: '../plupload/js/plupload.flash.swf',
	    browse_button: 'pickfiles',
	    container: upid,
	    url: '../upload.php?savepath='+ppaDir
	});
    
	uploader.init();
	uploader.bind('FilesAdded', function(up, files) {
	    $.each(files, function(i, file) {
		$('#filelist').append(
		    '<div id="' + file.id + '">' +
		    file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
	    });
	    up.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
	    $('#' + file.id + " b").html(file.percent + "%");
	});

	uploader.bind('Error', function(up, err) {
	    $('#filelist').append("<div>Error: " + err.code +
            ", popis chyby: " + err.message +
            (err.file ? ", soubor: " + err.file.name : "") +
            "</div>"
	    );
	    up.refresh(); // Reposition Flash/Silverlight
	});
	uploader.bind('FileUploaded', function(up, file) {
	    //$('#' + file.id + " b").html("uloženo");
	    $('#' + file.id).remove();
	    console.log('file uploaded');
//	    // pomoci ajaxu udelam zaznam do DB
//	    acturl = '{!$acturl}'; // neni definovano v sablone
//	    zapis do db udelam na pozadi
//	    pokusny zaznam bude mit 87 bytu
//	    $.post(
//		acturl,
//		{
//		    filename:file.name
//		},
//		function(data){
//		    updateSaveDokument(data);
//		},
//		'json'
//	    );
//	tretina scrollbaru pujde nahoru

	});

}


function updateSaveDokument(data){
    console.log(data);
}

function updateFolder(event){
    event.preventDefault();
    var url = $(this).attr('href');
    var rootPath =  parseInt($('#rootPath').val());
    var trida = $(this).attr('class');
    if($(this).hasClass('dir')) rootPath++;
    if($(this).hasClass('prevdir')) rootPath--;
    //alert('klik na slozku'+url);
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
	    url:url,
	    rootPath:rootPath
        },
        function(data){
            updateshowTeilAtt(data);
        },
        'json'
        );    
}

/**
 *
 *
 */

function updateshowVPM(data){
    // zobrazit editovaci div
    if($('#vpmform').length!=0){
            $('#vpmform').remove();
	    if(data.id=='showvpm') return;
        }
    $('body').append(data.formDiv);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    
    $( "#n_vpm_nr" ).autocomplete({
			source: "getVPM.php",
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
				    {"width": "500px","color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
		
   $('#n_vpm_add').bind('click',vpmAdd);
   $('input[id^=i_vpm_del_]').bind('click',vpmDel);
   $('input[id^=r_stk_]').bind('blur',vpmFieldChange);
   $('input[id^=r_bemerkung_]').bind('blur',vpmFieldChange);
}

/**
 * 
 */
function updateshowTeilDoku(data){
    // zobrazit editovaci div
    if($('#dokuform').length!=0){
            $('#dokuform').remove();
	    if(data.id=='showteildoku') return;
        }
    $('body').append(data.formDiv);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    

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


function vpmFieldChange(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    val:$(element).val()
        },
        function(data){
            updatevpmFieldChange(data);
        },
        'json'
        );        
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

function updatevpmFieldChange(data){
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

function vpmDel(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updatevpmAdd(data);
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

function vpmAdd(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    //alert(acturl);
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val(),
	    n_vpm_nr:$('#n_vpm_nr').val(),
	    n_anzahl:$('#n_anzahl').val(),
	    n_bemerkung:$('#n_bemerkung').val()
        },
        function(data){
            updatevpmAdd(data);
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

function updatevpmAdd(data){
    element = $('#showvpm');
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:'n_vpm_add',
	    teil:$('#teil').val()
        },
        function(data){
            updateshowVPM(data);
        },
        'json'
        );    
}

function updateDkopf(data){

}

