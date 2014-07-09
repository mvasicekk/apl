// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    $('#showteildoku').bind('click',showTeilDoku);
    $('#showvpm').bind('click',showVPM);
    $('#showima').bind('click',showIMA);
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
    $(window).bind('resize',documentResized);
    $(window).bind('keydown',keyDownHandler);
    

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

    // nastaveni velikosti tabulky s operacema
    documentResized();
});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------
function keyDownHandler(event){
    if(event.keyCode==27){
	// jen v pripade, ze neni zobrazen overlay s obrazkem
	var cboxDisplay = $('div#cboxOverlay').css('display');
	//alert('cboxDisplay='+cboxDisplay);
	if(cboxDisplay=='none'){
	    $('#dokuform').remove();
	    $('#vpmform').remove();
	    $('#imaform').remove();
	    $('#imaeditform').remove();
	}
    }
}

function documentResized(event){
//        var buttonOffset = $('#'+data.tdid).offset();
//    alert(data.tdid);
//    $(data.div).appendTo('body');
//    buttonOffset.top += $('#'+data.tdid).outerHeight();
//    $('div.palbemerkungdiv').css({
//        "left":buttonOffset.left+"px"
//    });
//    $('div.palbemerkungdiv').css({
//        "top":buttonOffset.top+"px"
//    });


    var teloOffset = $('#formular_telo').offset();
    var teloHeight = $('#formular_telo').outerHeight();
    var aplTableNewTop = teloOffset.top+teloHeight;
    
    $('#apl_table').css({
        "top":aplTableNewTop+"px"
    });
//    alert('documentResized telo: left:'+teloOffset.left+'top:'+teloOffset.top+'outerHeight:'+teloHeight);
}


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

function showImaNewDiv(event){
    //alert('showimanewdiv');
    $('#imanewdiv').toggle(500);
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


/**
 *
 *
 *
 */

function imaSelectAuftragsnrArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var imarrayValue = $('#ima_imarray').val();
    if(id=='ima_select_auftragsnr_e') imarrayValue = $('#ima_imarray_e').val();
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imarrayValue
    },
    function(data){
	updateshowSelectAuftragsnrArray(data);
    },
    'json'
    );    
}

/**
 *
 *
 *
 */

function imaSelectTatArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var	imVal = $('#ima_imarray').val();
    var	palVal = $('#ima_palarray').val();
    var tatVal = $('#ima_tatarray').val();
    
    if(id=='ima_select_tat_e'){
	imVal = $('#ima_imarray_e').val();
	palVal = $('#ima_palarray_e').val();
	tatVal = $('#ima_tatarray_e').val();
    }

    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imVal,
	palarray:palVal,
	tatarray:tatVal
    },
    function(data){
	updateshowSelectTatArray(data);
    },
    'json'
    );    
}

/**
 *
 *
 *
 */

function imaSelectPalArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    imarrayValue = $('#ima_imarray').val();
    palarrayValue = $('#ima_palarray').val();
    
    if(id=='ima_select_pal_e'){
	imarrayValue = $('#ima_imarray_e').val();
	palarrayValue = $('#ima_palarray_e').val();
    }
    
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imarrayValue,
	palarray:palarrayValue
    },
    function(data){
	updateshowSelectPalArray(data);
    },
    'json'
    );    
}
/**
 *
 *
 */

function showIMA(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowIMA(data);
        },
        'json'
        );    
}

function imaEdit(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowEditIMA(data);
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

function removeSelectForms(){
    if($('#imaselectimform').length!=0)	$('#imaselectimform').remove();
    if($('#imaselectpalform').length!=0) $('#imaselectpalform').remove();
    if($('#imaselecttatform').length!=0) $('#imaselecttatform').remove();
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


function updateshowSelectAuftragsnrArray(data){
    // zobrazit editovaci div
    if($('#imaselectimform').length!=0){
            $('#imaselectimform').remove();
	    return;
        }
    $('body').append(data.formDiv);
    $('input:checkbox[id^=selim]').bind('click',imselectclick);
}

function updateshowSelectPalArray(data){
    // zobrazit editovaci div
    if($('#imaselectpalform').length!=0){
            $('#imaselectpalform').remove();
	    return;
        }
    $('body').append(data.formDiv);
    $('input:checkbox[id^=selpal]').bind('click',palselectclick);
}

function updateshowSelectTatArray(data){
    // zobrazit editovaci div
    if($('#imaselecttatform').length!=0){
            $('#imaselecttatform').remove();
	    return;
        }
    $('body').append(data.formDiv);
    $('input:checkbox[id^=seltat]').bind('click',tatselectclick);
    $('input[id^=seltatvzaby]').bind('change',tatselectclick);
}

function imselectclick(event){
    //seznam vsech zaskrtnutych checkboxu
    var idcko = $(this).attr('id');
    //musim najit posledni podtrzitko v retezci
    podtrzitkoIndex = idcko.lastIndexOf('_');
    //test jestli pred podtrzitkem e
    var e = idcko.substr(podtrzitkoIndex-1,1);
    var suffix='';
    var selector = 'input:checkbox[id^=selim_]:checked';
    if(e=='e'){
	suffix='_e';
	selector = 'input:checkbox[id^=selime_]:checked';
    }

    //alert('imselectclick '+$(this).attr('id'));
    var imlist = '';
    $(selector).each(function(){
	imnr = $(this).attr('id').substr(podtrzitkoIndex+1);
	imlist+=imnr+';';
    });
    if(imlist.length>0) imlist = imlist.substring(0,imlist.length-1);
    //alert(e);
    $('#ima_imarray'+suffix).val(imlist);
    if(e=='e') imaEditFieldChanged(idcko);
}

function palselectclick(event){
    var idcko = $(this).attr('id');
    //musim najit posledni podtrzitko v retezci
    podtrzitkoIndex = idcko.lastIndexOf('_');
    //test jestli pred podtrzitkem e
    var e = idcko.substr(podtrzitkoIndex-1,1);
    var suffix='';
    var selector = 'input:checkbox[id^=selpal_]:checked';
    if(e=='e'){
	suffix='_e';
	selector = 'input:checkbox[id^=selpale_]:checked';
    }

    //seznam vsech zaskrtnutych checkboxu
    var imlist = '';
    $(selector).each(function(){
	imnr = $(this).attr('id').substr(podtrzitkoIndex+1);
	imlist+=imnr+';';
    });
    if(imlist.length>0) imlist = imlist.substring(0,imlist.length-1);
    $('#ima_palarray'+suffix).val(imlist);
    if(e=='e') imaEditFieldChanged(idcko);
}


function tatselectclick(event){
    var idcko = $(this).attr('id');
    //musim najit posledni podtrzitko v retezci
    podtrzitkoIndex = idcko.lastIndexOf('_');
    //test jestli pred podtrzitkem e
    var e = idcko.substr(podtrzitkoIndex-1,1);
    var suffix='';
    var esuffix='';
    var selector = 'input:checkbox[id^=seltat_]:checked';
    if(e=='e'){
	suffix='_e';
	esuffix='e';
	selector = 'input:checkbox[id^=seltate_]:checked';
    }

    //alert('suffix='+suffix+'\nesuffix='+esuffix+'\nselector='+selector);
    
    //seznam vsech zaskrtnutych checkboxu
    var tatlist = '';
    $(selector).each(function(){
	idcko = $(this).attr('id');
	podtrzitkoIndex = idcko.lastIndexOf('_');
	tatnr = idcko.substr(podtrzitkoIndex+1);
	//alert('tatnr = '+tatnr);
	//pribrat hodnotu vzaby
	vzaby = $('#'+'seltatvzaby'+esuffix+'_'+tatnr).val();
	//nahradit desetinnou carku desetinnou teckou
	vzaby = parseFloat(vzaby.replace(',','.'));
	if(isNaN(vzaby)) vzaby = 0
	$('#'+'seltatvzaby'+esuffix+'_'+tatnr).val(vzaby);
	tatlist+=tatnr+':'+vzaby+';'
    });
    if(tatlist.length>0) tatlist = tatlist.substring(0,tatlist.length-1);
    $('#ima_tatarray'+suffix).val(tatlist);
    if(e=='e') imaEditFieldChanged(idcko);
}

function updateshowEditIMA(data){
    if($('#imaeditform').length!=0){
            $('#imaeditform').remove();
	    return;
        }
    $('body').append(data.formDiv);

    // zapnuti colorboxu pro obrazky
    $('a.jpg').colorbox({
	    rel:'gal',
	    current:'{current} z/von {total}',
	    maxWidth:'90%',
	    maxHeight:'90%'
	});

    //inicializace uploaderu
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
	    getNewFileTable(ppaDir,data.teil,data.imaid);
	});
	
	//----------------------------------------------------
	// navazani udalosti
	$('div#imaeditform input[id^=imabemerkung_]').bind('change',imaBemerkungChanged);
	$('div#imaeditform input[id^=ima_select_auftragsnr]').bind('click',imaSelectAuftragsnrArray);
	$('div#imaeditform input[id^=ima_select_pal]').bind('click',imaSelectPalArray);
	$('div#imaeditform input[id^=ima_select_tat]').bind('click',imaSelectTatArray);
	$('input[id^=emanr_]').bind('focus',emaNrFocus);
	$('input[id^=emanr_]').bind('blur',emaNrChange);
}


function emaNrChange(event){
    //alert('emaNrChange');
    var url = $(this).attr('changeurl');

    $.post(url,
    {
	id:$(this).attr('id'),
	value:$(this).val()
    },
    function(data){
	updateEmaNrChange(data);
    },
    'json'
    );    
}

function updateEmaNrChange(data){
    //$('#'+data.id).val(data.newValue);
        if((data.ar>0)&&(data.valueOK==true)){
	$('#r_emanr_'+data.imaid).val(data.value);
    }
}

function emaNrFocus(event){
    var url = $(this).attr('focusurl');

    $.post(url,
    {
	id:$(this).attr('id'),
	value:$(this).val()
    },
    function(data){
	updateEmaNrFocus(data);
    },
    'json'
    );    
}

function updateEmaNrFocus(data){
    $('#'+data.id).val(data.newValue);
}

function getNewFileTable(ppaDir,teil,imaid){
    var acturl = './getNewFileTable.php';

    $.post(acturl,
    {
	id:id,
	ppaDir:ppaDir,
	teil:teil,
	imaid:imaid
    },
    function(data){
	updateNewFileTable(data);
    },
    'json'
    );    
}

function updateNewFileTable(data){
    $('#dokutable_edit').html(data.formDiv);
    	$('a.jpg').colorbox({
	    rel:'gal',
	    current:'{current} z/von {total}',
	    maxWidth:'90%',
	    maxHeight:'90%'
	});

}

function imaEditFieldChanged(id){
    //alert('fieldChanged:'+id);
    var acturl = './updateDMAField.php';
    var bemerkid = $('input[id^=imabemerkung_]').attr('id');
    
    $.post(acturl,
        {
	    id:id,
	    imarray:$('#ima_imarray_e').val(),
	    palarray:$('#ima_palarray_e').val(),
	    tatarray:$('#ima_tatarray_e').val(),
	    bemerkungid:bemerkid
        },
        function(data){
            updateEditFieldChanged(data);
        },
        'json'
        );    
}

function updateEditFieldChanged(data){
    
}

/**
 *
 *
 *
 */

function imaBemerkungChanged(event){
    var acturl = $(this).attr('acturl');
    var id = $(this).attr('id');
    
    $.post(acturl,
        {
	    id:id,
	    value:$(this).val()
        },
        function(data){
            updateBemerkungChanged(data);
        },
        'json'
        );    
}


/**
 *
 *
 *
 */

function updateBemerkungChanged(data){
    if(data.ar>0){
	$('#r_bemerkung_'+data.imaid).val(data.value);
    }
}

/**
 *
 *
 *
 */

function updateshowIMA(data){
    // zobrazit editovaci div
    if($('#imaform').length!=0){
            $('#imaform').remove();
	    if($('#imaeditform').length!=0){
		$('#imaeditform').remove();
	    }
	    if(data.id=='showima') return;
        }
    $('body').append(data.formDiv);
    $('#ima_add').bind('click',imaAdd);
    $('#ima_select_auftragsnr').bind('click',imaSelectAuftragsnrArray);
    $('#ima_select_pal').bind('click',imaSelectPalArray);
    $('#ima_select_tat').bind('click',imaSelectTatArray);
    $('input[id^=i_ima_edit_]').bind('click',imaEdit);
    $('#showimanewdiv').bind('click',showImaNewDiv);
    $('#imanewdiv').hide();
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

function imaAdd(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    //alert(acturl);
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val(),
	    imanr:$('#imanr').val(),
	    ima_imarray:$('#ima_imarray').val(),
	    ima_palarray:$('#ima_palarray').val(),
	    ima_tatarray:$('#ima_tatarray').val(),
	    bemerkung:$('#imabemerkung').val()
        },
        function(data){
            updateimaAdd(data);
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

function updateimaAdd(data){
    removeSelectForms();
    element = $('#showima');
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:'ima_add',
	    teil:$('#teil').val()
        },
        function(data){
            updateshowIMA(data);
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

