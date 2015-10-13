// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    //$(".datepicker" ).datepicker($.datepicker.regional["de"]);

    $(".datepicker" ).each(function(index){
	var ro = $(this).attr('readonly');
	if(ro!==true){
	    $(this).datepicker($.datepicker.regional["de"]);
	}
    });

//    $('#apl_table').draggable();
//    $('#apl_table').resizable();
    $('#showteildoku').bind('click',showTeilDoku);
    $('#showvpm').bind('click',showVPM);
    $('#showima').bind('click',showIMA);
    $('#showmittel').bind('click',showMittel);
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
//    $('body').delegate('click','div[id^=closebutton_]',closeButtonClick);    

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
    var imarrayValue_gen = $('#ima_imarray_gen').val();
    
    if(id=='ima_select_auftragsnr_e') imarrayValue = $('#ima_imarray_e').val();
    if(id=='ima_select_auftragsnr_gen') imarrayValue = $('#ima_imarray_e').val();
    
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imarrayValue,
	imarray_g:imarrayValue_gen
    },
    function(data){
	updateshowSelectAuftragsnrArray(data);
    },
    'json'
    );    
}

/**
 * 
 */
function emaSelectAuftragsnrArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var imarrayValue = $('#ima_imarray_e').val();
    var emarrayValue_anf = $('#ema_imarray_anf').val();
    var emarrayValue_gem = $('#ema_imarray_gem').val();
    
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imarrayValue,
	imarray_a:emarrayValue_anf,
	imarray_gem:emarrayValue_gem
    },
    function(data){
	updateshowSelectAuftragsnrArray(data);
    },
    'json'
    );    
}

/**
 * 
 */
function emaSelectPalArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var palarrayValue_e = $('#ima_palarray_e').val();
    var palarrayValue_anf = $('#ema_palarray_anf').val();
    var palarrayValue_gem = $('#ema_palarray_gem').val();
    var dauftrIdValue_e = $('#ima_dauftrid_e').val();
    var dauftrIdValue_anf = $('#ema_dauftrid_anf').val();
    var dauftrIdValue_gem = $('#ema_dauftrid_gem').val();

    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	palarrayValue_e:palarrayValue_e,
	palarrayValue_anf:palarrayValue_anf,
	palarrayValue_gem:palarrayValue_gem,
	dauftrIdValue_e:dauftrIdValue_e,
	dauftrIdValue_anf:dauftrIdValue_anf,
	dauftrIdValue_gem:dauftrIdValue_gem
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
 *
 */

function imaSelectPalArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var imarrayValue = $('#ima_imarray').val();
    var palarrayValue = $('#ima_palarray').val();
    
    var dauftrIdValue_e = $('#ima_dauftrid_e').val();
    var dauftrIdValue_gen = $('#ima_dauftrid_gen').val();
    
    var palarrayValue_gen = $('#ima_palarray_gen').val();
    var palarrayValue_gem = $('#ima_palarray_gem').val();
    var imarrayValue_gen = $('#ima_imarray_gen').val();
    
    if(id=='ima_select_pal_e'||id=='ima_select_pal_gen'){
	imarrayValue = $('#ima_imarray_e').val();
	palarrayValue = $('#ima_palarray_e').val();
    }
    
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imarrayValue,
	imarray_g:imarrayValue_gen,
	palarray:palarrayValue,
	palarray_g:palarrayValue_gen,
	palarray_gem:palarrayValue_gem,
	dauftrIdValue_e:dauftrIdValue_e,
	dauftrIdValue_gen:dauftrIdValue_gen
	
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
 *
 */

function imaSelectTatArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var	imVal = $('#ima_imarray').val();
    var	palVal = $('#ima_palarray').val();
    var tatVal = $('#ima_tatarray').val();
    var tatVal_gen = $('#ima_tatarray_gen').val();
    
    if(id=='ima_select_tat_e'||id=='ima_select_tat_gen'){
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
	tatarray:tatVal,
	tatarray_g:tatVal_gen
    },
    function(data){
	updateshowSelectTatArray(data);
    },
    'json'
    );    
}

/**
 * 
 */
function emaSelectTatArray(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    var	imVal = $('#ima_imarray').val();
    var	palVal = $('#ima_palarray').val();
    var tatVal = $('#ima_tatarray_e').val();
    var tatVal_gen = $('#ima_tatarray_gen').val();
    var tatVal_anf = $('#ema_tatarray_anf').val();
    var tatVal_gem = $('#ema_tatarray_gem').val();
    
    $.post(acturl,
    {
	id:id,
	teil:$('#teil').val(),
	imarray:imVal,
	palarray:palVal,
	tatarray:tatVal,
	tatarray_g:tatVal_gen,
	tatarray_a:tatVal_anf,
	tatarray_gem:tatVal_gem
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

function showMittel(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val()
        },
        function(data){
            updateshowMittel(data);
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
	$('#dokuform').draggable();
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
	$('#dokuform').draggable();
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

	$('div[id^=closebutton_]').bind('click',closeButtonClick);
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
//    alert('updateshowSelectAuftragsnrArray');
    
    // zobrazit editovaci div
    if($('#imaselectimform').length!=0){
//	    alert('remove imaselectimform');
            $('#imaselectimform').remove();
	    return;
        }
	
    //spravne umistit vpravo pod tlacitkem
    // pozice tlacitka
    bOffset = $('#'+data.id).offset();
    bWidth = $('#'+data.id).width();
    bHeight = $('#'+data.id).height();
    
    //alert('bOffset.left='+bOffset.left+'bOffset.top='+bOffset.top+'bWidth='+bWidth+'bHeight='+bHeight);
    
    $('body').append(data.formDiv);
    l = bOffset.left;
    t = bOffset.top+bHeight+4;
    
    $('#imaselectimform').css({"left":l+"px"});
    $('#imaselectimform').css({"top":t+"px"});
    
    //pokud je to policko pro genehmigt a pole je aktualne prazdne
    //tak ho rovnou naplnim a zapisu hodnotu i do db pomoci ajaxu
    if(data.e=='gen'){
	var obsahPole = $('#ima_imarray_gen').val();
	if(obsahPole.length==0){
	    $('#ima_imarray_gen').val(data.imarrayboxraw);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }
    if(data.e=='anf'){
	var obsahPole = $('#ema_imarray_anf').val();
	if(obsahPole.length==0){
	    $('#ema_imarray_anf').val(data.imarrayboxraw);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }

    if(data.e=='gem'){
	var obsahPole = $('#ema_imarray_gem').val();
	if(obsahPole.length==0){
	    $('#ema_imarray_gem').val(data.imarrayboxraw_a);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }

    $('input:checkbox[id^=selim]').bind('click',imselectclick);
    updateIMAGenehmigenButton();
}

function updateshowSelectPalArray(data){
    
    // zobrazit editovaci div
    if($('#imaselectpalform').length!=0){
            $('#imaselectpalform').remove();
	    return;
        }
	
    //spravne umistit vpravo pod tlacitkem
    // pozice tlacitka
    bOffset = $('#'+data.id).offset();
    bWidth = $('#'+data.id).width();
    bHeight = $('#'+data.id).height();
    
    l = bOffset.left;
    t = bOffset.top+bHeight+4;
    
    $('body').append(data.formDiv);
    
    
    $('#imaselectpalform').css({"left":l+"px"});
    $('#imaselectpalform').css({"top":t+"px"});
	
    //pokud je to policko pro genehmigt a pole je aktualne prazdne
    //tak ho rovnou naplnim a zapisu hodnotu i do db pomoci ajaxu
    if(data.e=='gen'){
	
	var obsahPoleP = $('#ima_palarray_gen').val();
	var obsahPoleI = $('#ima_dauftrid_gen').val();
	if(obsahPoleP.length==0){
	    $('#ima_palarray_gen').val(data.palarrayboxraw);
	    imaEditFieldChanged(data.id);
	}
	if(obsahPoleI.length==0){
	    $('#ima_dauftrid_gen').val(data.dauftrIdValue_e);
	    imaEditFieldChanged('ima_dauftrid_gen');
	}
    }    
    
    if(data.e=='anf'){
	var obsahPoleP = $('#ema_palarray_anf').val();
	var obsahPoleI = $('#ema_dauftrid_anf').val();
	if(obsahPoleP.length==0){
	    $('#ema_palarray_anf').val(data.palarrayValue_e);
	    imaEditFieldChanged(data.id);
	}
	if(obsahPoleI.length==0){
	    $('#ema_dauftrid_anf').val(data.dauftrIdValue_e);
	    imaEditFieldChanged('ema_dauftrid_anf');
	}
    }    
    
    if(data.e=='gem'){
	var obsahPoleP = $('#ema_palarray_gem').val();
	var obsahPoleI = $('#ema_dauftrid_gem').val();
	if(obsahPoleP.length==0){
	    $('#ema_palarray_gem').val(data.palarrayValue_anf);
	    imaEditFieldChanged(data.id);
	}
	if(obsahPoleI.length==0){
	    $('#ema_dauftrid_gem').val(data.dauftrIdValue_anf);
	    imaEditFieldChanged('ema_dauftrid_gem');
	}
    }    
    $('input:checkbox[id^=selpal]').bind('click',palselectclick);
    updateIMAGenehmigenButton();
}

function updateshowSelectTatArray(data){
    // zobrazit editovaci div
    if($('#imaselecttatform').length!=0){
            $('#imaselecttatform').remove();
	    return;
        }
	
    //spravne umistit vpravo pod tlacitkem
    // pozice tlacitka
    bOffset = $('#'+data.id).offset();
    bWidth = $('#'+data.id).width();
    bHeight = $('#'+data.id).height();
    
    l = bOffset.left;
    t = bOffset.top+bHeight+4;
    
    $('body').append(data.formDiv);
    
    $('#imaselecttatform').css({"left":l+"px"});
    $('#imaselecttatform').css({"top":t+"px"});

    //pokud je to policko pro genehmigt a pole je aktualne prazdne
    //tak ho rovnou naplnim a zapisu hodnotu i do db pomoci ajaxu
    if(data.e=='gen'){
	var obsahPole = $('#ima_tatarray_gen').val();
	if(obsahPole.length==0){
//	    alert(data.palarrayboxraw);
	    $('#ima_tatarray_gen').val(data.tatarrayboxraw);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }    

    if(data.e=='anf'){
	var obsahPole = $('#ema_tatarray_anf').val();
	if(obsahPole.length==0){
//	    alert(data.palarrayboxraw);
	    $('#ema_tatarray_anf').val(data.tatarrayboxrawvzkd);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }    

    if(data.e=='gem'){
	var obsahPole = $('#ema_tatarray_gem').val();
	if(obsahPole.length==0){
//	    alert(data.palarrayboxraw);
	    $('#ema_tatarray_gem').val(data.tatarrayboxraw_a);
	    // TODO vlozit do DB pomoci ajaxu
	    imaEditFieldChanged(data.id);
	}
    }    

    $('input:checkbox[id^=seltat]').bind('click',tatselectclick);
    $('input[id^=seltatvzaby]').bind('change',tatselectclick);
    $('input[id^=seltatvzkd]').bind('change',tatselectclick);
    updateIMAGenehmigenButton();
}

function imselectclick(event){
    //seznam vsech zaskrtnutych checkboxu
    var idcko = $(this).attr('id');
    //musim najit posledni podtrzitko v retezci
    podtrzitkoIndex = idcko.lastIndexOf('_');
    
    //test jestli pred podtrzitkem e
    var e;
    if(podtrzitkoIndex>'selim'.length)
	e = idcko.substr(podtrzitkoIndex-1,1);
    else
	e='';
    
//    alert(e);
//    return;
    var suffix='';
    var selector = 'input:checkbox[id^=selim_]:checked';
    var imfieldid = '#ima_imarray';
    
    if(e=='e'){
	suffix='_e';
	selector = 'input:checkbox[id^=selime_]:checked';
	
    }

    if(e=='n'){
	suffix='_gen';
	selector = 'input:checkbox[id^=selimgen_]:checked';
    }

    if(e=='f'){
	suffix='_anf';
	selector = 'input:checkbox[id^=selimanf_]:checked';
	imfieldid = '#ema_imarray';
    }
    
    if(e=='m'){
	suffix='_gem';
	selector = 'input:checkbox[id^=selimgem_]:checked';
	imfieldid = '#ema_imarray';
    }
    //alert('imselectclick '+$(this).attr('id'));
    var imlist = '';
    $(selector).each(function(){
	imnr = $(this).attr('id').substr(podtrzitkoIndex+1);
	imlist+=imnr+';';
    });
    //alert('e='+e);
//    alert(imlist);
    if(imlist.length>0) imlist = imlist.substring(0,imlist.length-1);
    //alert(e);
    $(imfieldid+suffix).val(imlist);
    //alert(idcko);
    if(e=='e'||e=='n'||e=='f'||e=='m') imaEditFieldChanged(idcko);
    updateIMAGenehmigenButton();
    updateDauftrPositionenErstellen();
}

function palselectclick(event){
    var idcko = $(this).attr('id');
    //musim najit posledni podtrzitko v retezci
    posledniPodtrzitkoIndex = idcko.lastIndexOf('_');
    prvniPodtrzitkoIndex = idcko.indexOf('_');
    druhePodtrzitkoIndex = idcko.indexOf('_',prvniPodtrzitkoIndex+1);
    //test jestli pred podtrzitkem e
    
    var e = idcko.substr(prvniPodtrzitkoIndex-1,1);
    var dauftrid = idcko.substring(prvniPodtrzitkoIndex+1,druhePodtrzitkoIndex);
    var pal = idcko.substr(posledniPodtrzitkoIndex+1);
    
//    alert('dauftrid='+dauftrid+'e='+e+'pal='+pal);
    var suffix='';
    var selector = 'input:checkbox[id^=selpal_]:checked';
    var palfieldid = '#ima_palarray';
    var dauftrIdfieldid = '#ima_dauftrid';
    
    if(e=='e'){
	suffix='_e';
	selector = 'input:checkbox[id^=selpale_]:checked';
    }
    if(e=='n'){
	suffix='_gen';
	selector = 'input:checkbox[id^=selpalgen_]:checked';
    }

    if(e=='f'){
	suffix='_anf';
	selector = 'input:checkbox[id^=selpalanf_]:checked';
	var palfieldid = '#ema_palarray';
	var dauftrIdfieldid = '#ema_dauftrid';
    }

    if(e=='m'){
	suffix='_gem';
	selector = 'input:checkbox[id^=selpalgem_]:checked';
	var palfieldid = '#ema_palarray';
	var dauftrIdfieldid = '#ema_dauftrid';
    }

    //seznam vsech zaskrtnutych checkboxu
    var pallist = '';
    var idlist = '';
    $(selector).each(function(){
	i = $(this).attr('id');
	posledniPodtrzitkoIndex = i.lastIndexOf('_');
	prvniPodtrzitkoIndex = i.indexOf('_');
	druhePodtrzitkoIndex = i.indexOf('_',prvniPodtrzitkoIndex+1);
	dauftrid = i.substring(prvniPodtrzitkoIndex+1,druhePodtrzitkoIndex);
	pal = i.substr(posledniPodtrzitkoIndex+1);
	pallist+=pal+';';
	idlist+=dauftrid+';';
    });
    if(pallist.length>0) pallist = pallist.substring(0,pallist.length-1);
    if(idlist.length>0) idlist = idlist.substring(0,idlist.length-1);
    $(palfieldid+suffix).val(pallist);
    $(dauftrIdfieldid+suffix).val(idlist);
    if(e=='e'||e=='n'||e=='f'||e=='m'){
	imaEditFieldChanged(idcko);
	imaEditFieldChanged((dauftrIdfieldid+suffix).substr(1));
    }
    updateIMAGenehmigenButton();
    updateDauftrPositionenErstellen();
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
    var tatfieldid = '#ima_tatarray';
    
    if(e=='e'){
	suffix='_e';
	esuffix='e';
	selector = 'input:checkbox[id^=seltate_]:checked';
    }

    if(e=='n'){
	suffix='_gen';
	esuffix='gen';
	selector = 'input:checkbox[id^=seltatgen_]:checked';
    }

    if(e=='f'){
	suffix='_anf';
	esuffix='anf';
	selector = 'input:checkbox[id^=seltatanf_]:checked';
	tatfieldid = '#ema_tatarray';
    }
    
    if(e=='m'){
	suffix='_gem';
	esuffix='gem';
	selector = 'input:checkbox[id^=seltatgem_]:checked';
	tatfieldid = '#ema_tatarray';
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
	if(e=='f'||e=='m'){
	    // pribrat hodnotu vzkd
	    vzkd = $('#'+'seltatvzkd'+esuffix+'_'+tatnr).val();
	    //nahradit desetinnou carku desetinnou teckou
	    vzkd = parseFloat(vzkd.replace(',','.'));
	    if(isNaN(vzkd)) vzkd = 0
	    $('#'+'seltatvzkd'+esuffix+'_'+tatnr).val(vzkd);
	}
	if(e=='f'||e=='m'){
	    tatlist+=tatnr+':'+vzaby+':'+vzkd+';';
	}
	else{
	    tatlist+=tatnr+':'+vzaby+';'
	}
    });
    if(tatlist.length>0) tatlist = tatlist.substring(0,tatlist.length-1);
    $(tatfieldid+suffix).val(tatlist);
    if(e=='e'||e=='n'||e=='f'||e=='m') imaEditFieldChanged(idcko);
    updateIMAGenehmigenButton();
    updateDauftrPositionenErstellen();
}

function updateshowEditIMA(data){
    
    if($('#imaeditform').length!=0){
            $('#imaeditform').remove();
	    return;
        }
    $('body').append(data.formDiv);
    $('#imaeditform').draggable();
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
	$('div#imaeditform input[id^=ema_select_auftragsnr]').bind('click',emaSelectAuftragsnrArray);
	$('div#imaeditform input[id^=ima_select_pal]').bind('click',imaSelectPalArray);
	$('div#imaeditform input[id^=ema_select_pal]').bind('click',emaSelectPalArray);
	$('div#imaeditform input[id^=ima_select_tat]').bind('click',imaSelectTatArray);
	$('div#imaeditform input[id^=ema_select_tat]').bind('click',emaSelectTatArray);
	$('div#imaeditform input[id^=imagenehmigtflag_]').bind('click',imaGenehmigtFlagClicked);
	$('div#imaeditform input[id^=imangenehmigtflag_]').bind('click',imaGenehmigtFlagClicked);
	$('div#imaeditform input[id^=ima2ema_]').bind('click',imaGenehmigtFlagClicked);
	$('div#imaeditform input[id^=emagenehmigtflag_]').bind('click',emaGenehmigtFlagClicked);
	$('div#imaeditform input[id^=emangenehmigtflag_]').bind('click',emaGenehmigtFlagClicked);
	$('div#imaeditform input:checkbox[id^=anlage_]').bind('click',imaAnlageChboxClicked);
	$('div#imaeditform input:button[id^=ema_antrag_generieren]').bind('click',emaAntragGenerierenClicked);
	$('div#imaeditform textarea[id^=ema_antrag_text]').bind('blur',emaAntragTextUpdated);
	$('div#imaeditform textarea[id^=ema_genehmigt_bemerkung]').bind('blur',emaAntragTextUpdated);
	$('input[id^=emanr_]').bind('focus',emaNrFocus);
	$('input[id^=emanr_]').bind('blur',emaNrChange);
	// zdatumovani vzbranzch policek
	$(".datepicker" ).each(function(index){
	    var ro = $(this).attr('readonly');
	    if(ro!==true){
		$(this).datepicker($.datepicker.regional["de"]);
	    }
	});
	$('#ema_genehmigt_stamp').bind('change',emaGenehmigtStampChanged);
	$('#ema_genehmigt_user').bind('change',emaGenehmigtUserChanged);
	$('#ema_dauftr_generieren').bind('click',emaDauftrGenerierenClicked);
	$('div[id^=closebutton_]').bind('click',closeButtonClick);
	$('#spinner').hide();
	// update dauftrpositionen erstellen
	updateDauftrPositionenErstellen();
	updateIMAGenehmigenButton();
}


function updateIMAGenehmigenButton(){
    // pokud bude jedno z policek prazdne bude button zakazany
    var im = $('#ima_imarray_gen').val();
    var pal = $('#ima_palarray_gen').val();
    var tat = $('#ima_tatarray_gen').val();
    
    if(im.length==0||pal.length==0||tat.length==0)
	$('input[id^=imagenehmigtflag_]').attr('disabled','disabled');
    else
	$('input[id^=imagenehmigtflag_]').removeAttr('disabled');
}

function updateDauftrPositionenErstellen(){
    // pokud bude jedno z policek prazdne bude button zakazany
    var genUser = $('#ema_genehmigt_user').val();
    var genStamp = $('#ema_genehmigt_stamp').val();
    var im = $('#ema_imarray_gem').val();
    var pal = $('#ema_palarray_gem').val();
    var tat = $('#ema_tatarray_gem').val();
    
    if(genUser.length==0||genStamp.length==0||im.length==0||pal.length==0||tat.length==0){
	$('#ema_dauftr_generieren').attr('disabled','disabled');
	$('input[id^=emagenehmigtflag_]').attr('disabled','disabled');
    }
    else{
	$('#ema_dauftr_generieren').removeAttr('disabled');
	$('input[id^=emagenehmigtflag_]').removeAttr('disabled');
    }
	
    //alert('updateDauftrPositionenErstellen');
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function emaDauftrGenerierenClicked(event){
    var acturl = $(this).attr('acturl');
    
    $.post(acturl,
        {
	    id:$(this).attr('id'),
	    imaid:$('#imaid').val()
        },
        function(data){
            updateEmaDauftrGenerieren(data);
        },
        'json'
        );    
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateEmaDauftrGenerieren(data){
        // zobrazit editovaci div
    if($('#previewtable').length!=0){
//	    alert('remove imaselectimform');
            $('#previewtable').remove();
	    return;
        }
	
    //spravne umistit vpravo pod tlacitkem
    // pozice tlacitka
    bOffset = $('#'+data.id).offset();
    bWidth = $('#'+data.id).width();
    bHeight = $('#'+data.id).height();
    
    //alert('bOffset.left='+bOffset.left+'bOffset.top='+bOffset.top+'bWidth='+bWidth+'bHeight='+bHeight);
    
    $('body').append(data.formDiv);
    divHeight = $('#previewtable').height();
//    l = bOffset.left;
    l = 10;
    b = bOffset.top-divHeight-bHeight;
    
    
    $('#previewtable').css({"left":l+"px"});
    $('#previewtable').css({"top":b+"px"});
    $('#previewtable').draggable();
    //click udalost pro tlacitko pro generovani pozic
    $('#createDauftrPositionen').bind('click',generiereDauftrPositionen);
}

function emaGenehmigtUserChanged(event){
//    alert('emaGenhmigtStampChanged, value='+$(this).val());
    imaEditFieldChanged($(this).attr('id'));
    updateDauftrPositionenErstellen();
}

function emaGenehmigtStampChanged(event){
//    alert('emaGenhmigtStampChanged, value='+$(this).val());
    imaEditFieldChanged($(this).attr('id'));
    updateDauftrPositionenErstellen();
}

function emaAntragTextUpdated(event){
//    var url = $(this).attr('acturl');
//    var value = $(this).val();
//    alert('emaAntragTextUpdated '+url+'\n'+value);
    imaEditFieldChanged($(this).attr('id'));
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function generiereDauftrPositionen(event){
//    alert('generiereDauftrPositionen');
    var url = $(this).attr('acturl');
    var imanr = $('div#imaeditform input#imanr').val();
    $.post(url,
    {
	id:$(this).attr('id'),
	ima:imanr
    },
    function(data){
	updateGeneriereDauftrPositionen(data);
    },
    'json'
    );    
    
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateGeneriereDauftrPositionen(data){

//    alert("dposAr="+data.dposAr
//	+"\ndposNew="+data.dposNew
//	+"\ndauftrInserted="+data.dauftrInserted
//	+"\ndauftrUpdated="+data.dauftrUpdated
//	+"\ndrueckUpdated="+data.drueckUpdated
//	);

	if(data.dauftrInserted>0 || data.dauftrUpdated>0){
	    $('#previewtable').remove();
	    var thIs = $('#ema_dauftr_generieren');
	    var acturl = thIs.attr('acturl');
    
	    $.post(acturl,
	    {
		id:thIs.attr('id'),
		imaid:$('#imaid').val()
	    },
	    function(data){
		updateEmaDauftrGenerieren(data);
	    },
	    'json'
	    );    
	}
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function emaAntragGenerierenClicked(event){
    var url = $(this).attr('acturl');
    //test jestli je vyplnene cislo EMA
    var emanr = $('input[id^=emanr_]').val();
    if(emanr.length==0){
	alert('EMA_Nr fehlt !');
	$('input[id^=emanr_]').css({"border":"2px solid red"});
	return;
    }
    
//    return;
    
    $('#spinner').show();
    $.post(url,
    {
	id:$(this).attr('id'),
	ima:$('div#imaeditform #imanr').val()
    },
    function(data){
	updateemaAntragGenerieren(data);
    },
    'json'
    );    
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateemaAntragGenerieren(data){
    
    var generatorUrl='../Reports/D555_pdf.php?report=D555&imanr='+data.imanr+'&imanr_label=IMANr&tl_tisk=pdf';
//    alert(generatorUrl);
    $.post(generatorUrl,
    {
	id:data.id
    },
    function(data){
	updateAfterEmaGenerator(data);
    },
    'json'
    );    
}

function updateAfterEmaGenerator(data){
    $('#spinner').hide();
    $('#docutablescroller').html(data.filetable);
    $('a.jpg').colorbox({
	    rel:'gal',
	    current:'{current} z/von {total}',
	    maxWidth:'90%',
	    maxHeight:'90%'
	});
    $('div#imaeditform input:checkbox[id^=anlage_]').bind('click',imaAnlageChboxClicked);
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function imaAnlageChboxClicked(event){
//    alert($(this).attr('id'));
    var url = $(this).attr('acturl');

    $.post(url,
    {
	id:$(this).attr('id'),
	ima:$('div#imaeditform #imanr').val(),
	value:$(this).attr('checked')?1:0
    },
    function(data){
	updateImaAnlageChboxClicked(data);
    },
    'json'
    );    
}


function updateImaAnlageChboxClicked(data){
    
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
	$('div#imaeditform input:checkbox[id^=anlage_]').bind('click',imaAnlageChboxClicked);
}

function imaEditFieldChanged(id){
    //alert('fieldChanged:'+id);
    var acturl = './updateDMAField.php';
    var bemerkid = $('textarea[id^=imabemerkung_]').attr('id');
    
    $.post(acturl,
        {
	    id:id,
	    imarray:$('#ima_imarray_e').val(),
	    imarray_g:$('#ima_imarray_gen').val(),
	    imarray_a:$('#ema_imarray_anf').val(),
	    imarray_gem:$('#ema_imarray_gem').val(),
	    palarray:$('#ima_palarray_e').val(),
	    dauftrid:$('#ima_dauftrid_e').val(),
	    dauftrid_g:$('#ima_dauftrid_gen').val(),
	    dauftrid_a:$('#ema_dauftrid_anf').val(),
	    dauftrid_gem:$('#ema_dauftrid_gem').val(),
	    palarray_g:$('#ima_palarray_gen').val(),
	    palarray_a:$('#ema_palarray_anf').val(),
	    palarray_gem:$('#ema_palarray_gem').val(),
	    tatarray:$('#ima_tatarray_e').val(),
	    tatarray_g:$('#ima_tatarray_gen').val(),
	    tatarray_a:$('#ema_tatarray_anf').val(),
	    tatarray_gem:$('#ema_tatarray_gem').val(),
	    ema_antrag_text:$('#ema_antrag_text').val(),
	    ema_genehmigt_bemerkung:$('#ema_genehmigt_bemerkung').val(),
	    ema_genehmigt_stamp:$('#ema_genehmigt_stamp').val(),
	    ema_genehmigt_user:$('#ema_genehmigt_user').val(),
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

function imaGenehmigtFlagClicked(event){
    var acturl = $(this).attr('acturl');
    var id = $(this).attr('id');

    podtrzitkoIndex = id.lastIndexOf('_');
    //alert(podtrzitkoIndex);
    var imaid = id.substr(podtrzitkoIndex+1);
    //alert(imaid);

    $.post(acturl,
        {
	    id:id,
	    imaid:imaid,
	    ch:1,
	    bemerkung_g:$('#ima_genehmigt_bemerkung_'+imaid).val()
        },
        function(data){
            updateGenehmigtFlagClicked(data);
        },
        'json'
        );    
}

function emaGenehmigtFlagClicked(event){
    var acturl = $(this).attr('acturl');
    var id = $(this).attr('id');

    podtrzitkoIndex = id.lastIndexOf('_');
    //alert(podtrzitkoIndex);
    var imaid = id.substr(podtrzitkoIndex+1);
    //alert(imaid);

    $.post(acturl,
        {
	    id:id,
	    imaid:imaid,
	    ch:1,
	    bemerkung_g:$('#ima_genehmigt_bemerkung_'+imaid).val()
        },
        function(data){
            updateEMAGenehmigtFlagClicked(data);
        },
        'json'
        );    
}

function updateEMAGenehmigtFlagClicked(data){
    $('input[id^=imagenehmigtflag_]').attr('disabled','disabled');
    $('input[id^=imangenehmigtflag_]').attr('disabled','disabled');
    $('input[id^=emagenehmigtflag_]').attr('disabled','disabled');
    $('input[id^=emangenehmigtflag_]').attr('disabled','disabled');
    $('#ema_select_auftragsnr_anf').hide();
    $('#ema_select_pal_anf').hide();
    $('#ema_select_tat_anf').hide();
    $('#ema_antrag_text').attr('disabled','disabled');
//    $('#ema_antrag_generieren').hide();
    $('#ema_select_auftragsnr_gem').hide();
    $('#ema_select_pal_gem').hide();
    $('#ema_select_tat_gem').hide();
    $('#ema_genehmigt_bemerkung').attr('disabled','disabled');
    $('#ema_genehmigt_user').attr('disabled','disabled');
    $('#ema_genehmigt_stamp').attr('disabled','disabled');
    $('#ema_dauftr_generieren').hide();
    $('#ima_select_auftragsnr_gen').hide();
    $('#ima_select_pal_gen').hide();
    $('#ima_select_tat_gen').hide();
    $('#ima_select_auftragsnr_e').hide();
    $('#ima_select_pal_e').hide();
    $('#ima_select_tat_e').hide();
//    $('#emapart').hide();
    $('input[id^=imabemerkung_]').attr('readonly','readonly');
    $('input[id^=ima_genehmigt_bemerkung_]').attr('readonly','readonly');
}

function updateGenehmigtFlagClicked(data){
    $('input[id^=imagenehmigtflag_]').attr('disabled','disabled');
    $('input[id^=imangenehmigtflag_]').attr('disabled','disabled');
    $('#ima_select_auftragsnr_gen').hide();
    $('#ima_select_pal_gen').hide();
    $('#ima_select_tat_gen').hide();
    $('#ima_select_auftragsnr_e').hide();
    $('#ima_select_pal_e').hide();
    $('#ima_select_tat_e').hide();
    if(data.ch==2 && data.nicht==2){
	$('#emapart').show();
    }
    else{
	$('#emapart').hide();
    }
    
    $('input[id^=imabemerkung_]').attr('readonly','readonly');
    $('input[id^=ima_genehmigt_bemerkung_]').attr('readonly','readonly');
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
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('#showimanewdiv').bind('click',showImaNewDiv);
    $('#imaform').draggable();
    $('#imanewdiv').hide();
}


function closeButtonClick(){
    var eid = $(this).attr('id');
    var toCloseId = eid.substr(eid.lastIndexOf('_')+1);
    $('#'+toCloseId).remove();
//    alert('id='+eid+'idToClose='+toCloseId);
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateshowMittel(data){
    // zobrazit editovaci div
    if($('#mittelform').length!=0){
            $('#mittelform').remove();
	    if(data.id=='showmittel') return;
        }
    $('body').append(data.formDiv);
    var buttonOffset = $('#showmittel').offset();
    var buttonWidth = $('#showmittel').width();
    var buttonHeight = $('#showmittel').height();
    var divWidth = $('#mittelform').width();
    var divHeight = $('#mittelform').height();

    var divLeft = buttonOffset.left - divWidth/2 + buttonWidth/2;
    var divTop = buttonOffset.top - divHeight - buttonHeight;
    $('#mittelform').css({
        "left":divLeft+"px"
    });
    $('#mittelform').css({
        "top":divTop+"px"
    });

    $('#mittelform').draggable();
    
    $( "#n_mittel_nr" ).autocomplete({
			source: "getArbMittel.php",
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
				    {"color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });

   $('#n_mittel_add').bind('click',mittelAdd);
   $('input[id^=i_mittel_del_]').bind('click',mittelDel);
   $('div[id^=closebutton_]').bind('click',closeButtonClick);
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
    $('#vpmform').draggable();
    //$(".datepicker" ).datepicker($.datepicker.regional["de"]);
    $(".datepicker" ).each(function(index){
	var ro = $(this).attr('readonly');
	if(ro!==true){
	    $(this).datepicker($.datepicker.regional["de"]);
	}
    });

    
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
   $('div[id^=closebutton_]').bind('click',closeButtonClick);
}

/**
 * 
 */
function updateshowTeilDoku(data){
    // doplnit, v pripade ze je input readonly nepovolit nastaveni autocomplete a datepicker
    // zobrazit editovaci div
    if($('#dokuform').length!=0){
            $('#dokuform').remove();
	    if(data.id=='showteildoku') return;
        }
    $('body').append(data.formDiv);
    $('#dokuform').draggable();
    $(".datepicker" ).each(function(index){
	var ro = $(this).attr('readonly');
	if(ro!==true){
	    $(this).datepicker($.datepicker.regional["de"]);
	}
    });

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
		    
    $( "input[id^=r_freigabe_vom_]:not([readonly])" ).autocomplete({
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
   $('div[id^=closebutton_]').bind('click',closeButtonClick);
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

function mittelDel(event){
    element = event.target;
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:id
        },
        function(data){
            updatemittelAdd(data);
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
	    ima_dauftrid:$('#ima_dauftrid').val(),
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

function mittelAdd(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    //alert(acturl);
    $.post(acturl,
        {
            id:id,
	    teil:$('#teil').val(),
	    n_mittel_nr:$('#n_mittel_nr').val(),
	    n_abgnr:$('#n_abgnr').val()
        },
        function(data){
            updatemittelAdd(data);
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

function updatemittelAdd(data){
    element = $('#showmittel');
    var id=element.id;
    var acturl = $(element).attr('acturl');
    $.post(acturl,
        {
            id:'n_mittel_add',
	    teil:$('#teil').val()
        },
        function(data){
            updateshowMittel(data);
        },
        'json'
        );    
}

function updateDkopf(data){

}

