// JavaScript Document

$.fn.center = function() {
    this.css({
        'position': 'fixed',
        'left': '50%',
        'top': '50%'
    });
    this.css({
        'margin-left': -this.outerWidth() / 2 + 'px',
        'margin-top': -this.outerHeight() / 2 + 'px'
    });

    return this;
}

$(document).ready(function(){
    
    	$.datepicker.setDefaults($.datepicker.regional["de"]);
	$(".datepicker" ).datepicker($.datepicker.regional["de"]);
	
	$('.imextable').floatThead();
	$('.kundeTagBox').bind('dblclick',kundeBoxDblClicked);
	$('.datumheader').bind('dblclick',datumHeaderDblClicked);
	$('.lkwdraggable').bind('dblclick',lkwDblClicked);
	$('.draggable').bind('dblclick',draggableImExDblClicked);
	$('.draggable').draggable({zIndex: 999});
	
//	$('.lkwdraggable').draggable({
//	    axis: "y",
//	    start: function(){
//		
//		$(this).css({"z-index":"999"});
//	    },
//	    stop: function(){
//		$(this).css({"background-color":""});
//	    }
//	});
	$('.selectable').selectable();
	$('.draggable').css({"cursor":"pointer"});
	$('.lkwdraggable').css({"cursor":"pointer"});
	$('.droppable').droppable(
		{
		    accept: ".draggable",
//		    activeClass: "ui-state-highlight",
		    addClasses: false,
		    hoverClass: "drop-hover",
		    drop: function( event, ui ) {
			// pokud mam otevreny formular pro lkwedit nebudu drop zpracovavat a necham
			// to lkwedit
//			if($('div[id^=editlkw_]').length!=0){
//			    $(this).draggable( "option", "revert", true );
//			    return;
//			}
//			else{
//			    $(this).draggable( "option", "revert", false );
//			}
			//alert(ui.draggable.attr('id')+' polozen na '+$(this).attr('id'));
			ui.helper.remove();
			var acturl = 'updateDrop.php';
			$.post(acturl,
			    {
				target_id:$(this).attr('id'),
				dropped_id:ui.draggable.attr('id'),
			    },
			    function(data){
				updateDropped(data);
			    },
			    'json'
			);        
		    }
		}
		);
	
	$(window).bind('keydown',keyDownHandler);
});

//events handlers

function keyDownHandler(event){
    if(event.keyCode==27){
//	alert('esc');
	$('div[id^=editdraggableimex_]').remove();
	$('div[id^=newimp_]').remove();
    }
}

function lkwDblClicked(event){
    event.preventDefault();
    event.stopPropagation();
    var id = $(this).attr('id');
    var acturl = 'lkwEdit.php';
    $.post(acturl,
    {
	id:id
    },
    function(data){
	updateLkwDblClicked(data);
    },
    'json'
    );        
    //alert('lkwedit');
}

function draggableImExDblClicked(event){
    event.preventDefault();
    event.stopPropagation();
//    alert($(this).attr('id')+' dbl clicked');
    var id = $(this).attr('id');
    var acturl = 'imExEdit.php';
    $.post(acturl,
    {
	id:id
    },
    function(data){
	updatedraggableImExDblClicked(data);
    },
    'json'
    );        
}

function editImExButtonClick(event){

    var Id = $(this).attr('id');
    var kundeBoxId = Id.substr(Id.indexOf('_')+1);
    var bestellnrVal = $('#'+'bestellnr_'+kundeBoxId).val();
    var auftragsnr = $('#'+'auftragsnr_'+kundeBoxId).val();
    var bemerkung = $('#'+'bemerkung_'+kundeBoxId).val();
    var imsolldate = $('#'+'imsolldate_'+kundeBoxId).val();
    var imsolltime = $('#'+'imsolltime_'+kundeBoxId).val();
    var exsolldate = $('#'+'exsolldate_'+kundeBoxId).val();
    var exsolltime = $('#'+'exsolltime_'+kundeBoxId).val();
    var zielort = $('#'+'zielort_'+kundeBoxId).val();
    var aufdatdate = $('#'+'aufdatdate_'+kundeBoxId).val();
    var aufdattime = $('#'+'aufdattime_'+kundeBoxId).val();
    var ausgeliefertamdate = $('#'+'ausgeliefertamdate_'+kundeBoxId).val();
    var ausgeliefertamtime = $('#'+'ausgeliefertamtime_'+kundeBoxId).val();
    var rechnungam = $('#'+'rechnungam_'+kundeBoxId).val();
    var zielvalue = $('#'+'ziel_value').val();
    
    var acturl = $(this).attr('acturl');
    $.post(acturl,
    {
	id:Id,
	kundeBoxId:kundeBoxId,
	auftragsnr:auftragsnr,
	bestellnr:bestellnrVal,
	bemerkung:bemerkung,
	imsolldate:imsolldate,
	imsolltime:imsolltime,
	exsolldate:exsolldate,
	exsolltime:exsolltime,
	zielort:zielort,
	zielvalue:zielvalue,
	aufdatdate:aufdatdate,
	aufdattime:aufdattime,
	ausgeliefertamdate:ausgeliefertamdate,
	ausgeliefertamtime:ausgeliefertamtime,
	rechnungam:rechnungam
    },
    function(data){
	updateeditImExButtonClick(data);
    },
    'json'
    );        
    
    
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function erstellenPlanImportButtonClick(event){
    var Id = $(this).attr('id');
    
    var kundeBoxId = Id.substr(Id.indexOf('_')+1);
    var imzeitVal = $('#'+'imzeit_'+kundeBoxId).val();
    var imnrVal = $('#'+'imnr_'+kundeBoxId).val();
    var bestellnrVal = $('#'+'bestellnr_'+kundeBoxId).val();
    var bemerkungVal = $('#'+'bemerkung_'+kundeBoxId).val();
    var terminVal = $('#'+'termin_'+kundeBoxId).val();
    var planteilstkVal = $('#'+'planteilstk_'+kundeBoxId).val();
    
    var acturl = $(this).attr('acturl');
    $.post(acturl,
    {
	id:Id,
	kundeBoxId:kundeBoxId,
	imPlanZeit:imzeitVal,
	imNr:imnrVal,
	bestellNr:bestellnrVal,
	bemerkung:bemerkungVal,
	termin:terminVal,
	planTeilStk:planteilstkVal
    },
    function(data){
	updateerstellenPlanImportButtonClick(data);
    },
    'json'
    );        
    
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function datumHeaderDblClicked(event){
    
    event.preventDefault();
    event.stopPropagation();
    var id = $(this).attr('id');
    var acturl = 'lkwEdit.php';
    $.post(acturl,
    {
	id:id,
	newLkw:1
    },
    function(data){
	updateLkwDblClicked(data);
    },
    'json'
    );
    
//    var datumHeaderId = $(this).attr('id');
//    var acturl = 'datumHeaderDblClicked.php';
//    $.post(acturl,
//    {
//	datumHeaderId:datumHeaderId
//    },
//    function(data){
//	updatedatumHeaderDblClicked(data);
////	updatekundeBoxDblClicked(data);
//    },
//    'json'
//    );        
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function kundeBoxDblClicked(event){
    var kundeBoxId = $(this).attr('id');
    var acturl = 'kundeBoxDblClicked.php';
    $.post(acturl,
    {
	kundeBoxId:kundeBoxId
    },
    function(data){
	updatekundeBoxDblClicked(data);
    },
    'json'
    );        

}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateeditImExButtonClick(data) {
//    alert(data.updateIdArray.length);
    $('#editdraggableimex_'+data.kundeBoxId).remove();
    for (var i = 0; i < data.updateIdArray.length; i++) {
	var idToUpdate = data.updateIdArray[i];
//	alert(idToUpdate);
	var acturl = 'updateDrop.php';
	$.post(acturl,
		{
		    target_id: idToUpdate,
		    dropped_id: '',
		    dropInfo: 'editedImEx',
		    kunde: data.kunde
		},
	function (data) {
	    updateDropped(data);
	},
		'json'
		);

    }
}
// update funkce
function updateerstellenPlanImportButtonClick(data){
    $('#newimp_'+data.kundeBoxId).remove();
    //pokud byla vlozena hlavicka zakazky obcerstvim kundebox
    if(data.insertId>0){
	
	for (var i = 0; i < data.updateIdArray.length; i++) {
	    var idToUpdate = data.updateIdArray[i];
	    //	alert(idToUpdate);
	    var acturl = 'updateDrop.php';
	    $.post(acturl,
		{
		    target_id: idToUpdate,
		    dropped_id: '',
		    dropInfo: 'editedImEx',
		    kunde: data.kunde
		},
		function (data) {
		    updateDropped(data);
		},
		'json'
	    );
	}
	
//	var acturl = 'updateDrop.php';
//	$.post(acturl,
//	{
//	    target_id:data.kundeBoxId,
//	    dropped_id:'',
//	    dropInfo:'createdImport',
//	    kunde:data.kunde
//	},
//	function(data){
//	    updateDropped(data);
//	},
//	'json'
//	);        
    }
}

function submitLkwEdit(event){
    if(event.keyCode==13){
	$(this).find('input[id^=savelkwbutton_]').click();
    }
}

function submitImExEdit(event){
    //alert('event.keycode'+event.keyCode+'id='+$(this).attr('id'));
    if(event.keyCode==13){
	$(this).find('input[id^=editimexbutton_]').click();
    }
}

function makeLkwPayloadDraggable(data){
$('div.lkwPayLoad').draggable({
	stop:function(event,ui){
	    var offsetUI = ui.offset;
	    var divOffset = $('#'+data.divid).offset();
	    var divWidth = $('#'+data.divid).outerWidth();
	    var divHeight = $('#'+data.divid).outerHeight();
	    var uiWidth = ui.helper.outerWidth();
	    var uiHeight = ui.helper.outerHeight();
	    
	    var lDif = divOffset.left-offsetUI.left;
	    var rDif = (offsetUI.left+uiWidth)-(divOffset.left+divWidth);
	    var tDif = divOffset.top-offsetUI.top;
	    var bDif = (offsetUI.top+uiHeight)-(divOffset.top+divHeight);
	    
	    if((lDif>uiWidth)||(rDif>uiWidth)||(tDif>uiHeight)||(bDif>uiHeight)){
		var acturl = './deletePayloadId.php';
		$.post(acturl,
		{
		    payloadid:$(this).attr('id'),
		    rid:data.divid
		},
		function(data){
		    updateDeletePayloadId(data);
		},
		'json'
		);        
	    }
	    else{
		//$(this).css({"background-color":""});
	    }
	},
	drag:function(event,ui){
	    var offsetUI = ui.offset;
	    var divOffset = $('#'+data.divid).offset();
	    var divWidth = $('#'+data.divid).outerWidth();
	    var divHeight = $('#'+data.divid).outerHeight();
	    var uiWidth = ui.helper.outerWidth();
	    var uiHeight = ui.helper.outerHeight();
	    
	    var lDif = divOffset.left-offsetUI.left;
	    var rDif = (offsetUI.left+uiWidth)-(divOffset.left+divWidth);
	    var tDif = divOffset.top-offsetUI.top;
	    var bDif = (offsetUI.top+uiHeight)-(divOffset.top+divHeight);
	    
	    if((lDif>uiWidth)||(rDif>uiWidth)||(tDif>uiHeight)||(bDif>uiHeight)){
		$(this).css({"background-color":"red"});
	    }
	    else{
		$(this).css({"background-color":""});
	    }
	    
	    //console.log("lDif:"+lDif+"tDif:"+tDif+"rDif"+rDif+"bDif:"+bDif);
	}
    });
    $('div.lkwPayLoad').css({"cursor":"pointer"});
}

function draggableClick(event) {
    if ($('div[id^=editlkw_]').length > 0) {
	var target_id = $('div[id^=editlkw_]').attr('id');
	var dropped_id = $(this).attr('id');
//	alert('target_id='+target_id+',dropped_id='+dropped_id);
	var acturl = 'updateDropToLkw.php';
	$.post(acturl,
		{
		    target_id: target_id,
		    dropped_id: dropped_id
		},
	function (data) {
	    updateDroppedToLkw(data);
	},
		'json'
		);
    }
}
/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateLkwDblClicked(data) {
    $('body').append(data.div);
    $('#' + data.divid).hide();
    $('#' + data.divid).center();
    $('#' + data.divid).show('slide');
    $('#' + data.divid).draggable();
    // pridam ke vsem draggable imex odchyceni udalosti click
    $('.draggable').unbind('click');
    $('.draggable').on('click',draggableClick);
    makeLkwPayloadDraggable(data);

    $(".datepicker").datepicker($.datepicker.regional["de"]);
    $('#' + data.divid).bind('keypress', submitLkwEdit);
    $('input[id^=savelkwbutton_]').bind('click', saveLkwButtonClick);
    $('input[id^=deletelkwbutton_]').bind('click', deleteLkwButtonClick);
    $('div[id^=closebutton_]').bind('click', closeButtonClick);
}

function deleteLkwButtonClick(e) {
    console.log($(this).attr('id'));
    var url = $(this).attr('acturl');
    console.log(url);
    $.post(url,
	    {
		id: $(this).attr('id'),
		th:$('#th').val()
	    },
    function (data) {
	updatelkwDeleteClick(data);
    },
	    'json'
	    );
}


function updatelkwDeleteClick(data){
    console.log(data);
    $('#'+data.divid).remove();
    
    for(d in data.divsToUpdate){
	console.log(d);
	console.log(data.divsToUpdate[d]);
	$('#tagheader_'+d).html(data.divsToUpdate[d]);
    }
    //$('#'+data.th).html(data.tagDiv);
    
    $('.lkwdraggable').unbind('dblclick');
    $('.lkwdraggable').bind('dblclick',lkwDblClicked);
//    $('.lkwdraggable').draggable({
//	    axis: "y",
//	    start: function(){
//		$(this).css({"z-index":"999"});
//	    },
//	    stop: function(){
//		$(this).css({"background-color":""});
//	    }
//	});
	$('.lkwdraggable').css({"cursor":"pointer"});
}

function updateDeletePayloadId(data){
    $('div.payloadList').html(data.payloadDiv);
    $('div.lkw_'+data.rundlaufid).html(data.lkwDiv);
    console.log(data);
    $('#ab_aby_soll_date_'+data.rundlaufid).val(data.ab_aby_soll_date_vorschlag);
    $('#ab_aby_soll_time_'+data.rundlaufid).val(data.ab_aby_soll_time_vorschlag);
    
    $('#an_kunde_ort_td_'+data.rundlaufid).html(data.anKundeOrtDiv);
    $('#'+data.imexDivToUpdate).removeClass('imauto');
    makeLkwPayloadDraggable(data);
    
}

function saveLkwButtonClick() {
    console.log('saveLkwButtonClick');
    //vytvorit mapu vsech inputu
    var map = {};
    $(this).parent().find('input').each(function () {
	map[$(this).attr("id")] = $(this).val();
    });
    $(this).parent().find('select').each(function () {
	map[$(this).attr("id")] = $(this).val();
    });
    console.log(map);
    var url = $(this).attr('acturl');
    $.post(url,
	    {
		id: $(this).attr('id'),
		params:map,
		th: $('#th').val()
	    },
    function (data) {
	updatelkwDeleteClick(data);
    },
	    'json'
	    );
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updatedraggableImExDblClicked(data){
    $('body').append(data.div);
    
    $('#'+data.divid).center();
    $('#'+data.divid).show();
    
    //nastavit fosuc na vybrany input
    $('#'+data.focusTo).focus();
    $('#'+data.focusTo).select();
    
    //chytani udalosti
    $('#'+data.divid).bind('keypress',submitImExEdit);
    $('#'+data.divid).draggable();
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('input[id^=editimexbutton_]').bind('click',editImExButtonClick);
    $( "input[id^=zielort_]" ).autocomplete({
			source: "../dauftr/getZielorte.php?kd="+data.kunde,
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
//				    {"width": 300,"color":"black","font-size":"12px"}
				    {"color":"black","font-size":"12px"}
				);
			},
			change: zielortChange
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
}


function updatedatumHeaderDblClicked(data){
    $('body').append(data.div);
    $('#'+data.divid).hide();
    $('#'+data.divid).center();
    $('#'+data.divid).show('slide');
    $('#'+data.divid).draggable();
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('input[id^=erstellenbutton_]').bind('click',lkwErstellenClick);
}

function lkwErstellenClick(e) {
    console.log($(this).attr('id'));
    var url = $(this).attr('acturl');
    console.log(url);
    $.post(url,
	    {
		id: $(this).attr('id')
	    },
    function (data) {
	updatelkwErstellenClick(data);
    },
	    'json'
	    );
}


function updatelkwErstellenClick(data){
    
}


function updatekundeBoxDblClicked(data){
//    if($('#'+data.divid).length!=0){
//            $('#'+data.divid).remove();
//	    return;
//    }
    
    $('body').append(data.div);
    $('#'+data.divid).hide();
    $('#'+data.divid).center();
    $('#'+data.divid).show('slide');
    $('#'+data.divid).draggable();
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('input[id^=erstellenbutton_]').bind('click',erstellenPlanImportButtonClick);
    
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function zielortChange(event){
//    var acturl = $(this).attr('acturl');
//    $.post(acturl,
//        {
//            id:$(this).attr('id'),
//	    val:$('#ziel_value').val()
//        },
//        function(data){
//            updateZielortChange(data);
//        },
//        'json'
//        );        
}


function updateDroppedToLkw(data){
    $('#'+data.imexDivToUpdate).addClass('imauto');
    data.imexDivToUpdate = '';
    updateDeletePayloadId(data);
}

function updateDropped(data){
//    if(data.imex=='ex'){
	$('#'+data.target_id).html(data.targetTD);
	//$('#'+data.target_id).html('updated');
	//udelat draggable
	$('.draggable').unbind('dblclick');
	$('.draggable').bind('dblclick',draggableImExDblClicked);
	$('.draggable').draggable({zIndex: 999});
	$('.draggable').css({"cursor":"pointer"});

//    }
}


function closeButtonClick(){
    
    var eid = $(this).attr('id');
    var elementToClose = $(this).parent();
    elementToClose.remove();
    
//    var toCloseId = eid.substr(eid.indexOf('_')+1);
//    var closeId = 'newimp_'+toCloseId;
////    alert(closeId);
//    $('#'+closeId).remove();
//    alert('id='+eid+'idToClose='+toCloseId);
}
