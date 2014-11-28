// JavaScript Document

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
                                                $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
                                                $(window).scrollLeft()) + "px");
    return this;
}

$(document).ready(function(){
    
    	$.datepicker.setDefaults($.datepicker.regional["de"]);
	$(".datepicker" ).datepicker($.datepicker.regional["de"]);
	
	
	$('.kundeTagBox').bind('dblclick',kundeBoxDblClicked);
	$('.draggable').bind('dblclick',draggableImExDblClicked);
	$('.draggable').draggable();
	$('.selectable').selectable();
	$('.draggable').css({"cursor":"pointer"});
	$('.droppable').droppable(
		{
		    accept: ".draggable",
//		    activeClass: "ui-state-highlight",
		    addClasses: false,
		    hoverClass: "drop-hover",
		    drop: function( event, ui ) {
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
});

//events handlers

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
	var acturl = 'updateDrop.php';
	$.post(acturl,
	{
	    target_id:data.kundeBoxId,
	    dropped_id:'',
	    dropInfo:'createdImport',
	    kunde:data.kunde
	},
	function(data){
	    updateDropped(data);
	},
	'json'
	);        
    }
}

function updatedraggableImExDblClicked(data){
    if($('#'+data.divid).length!=0){
            $('#'+data.divid).remove();
	    return;
    }
    
    $('body').append(data.div);
    $('#'+data.divid).hide();
    $('#'+data.divid).center();
    $('#'+data.divid).show('slide');
    
    //chytani udalosti
    $('#'+data.divid).draggable();
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('input[id^=editimexbutton_]').bind('click',editImExButtonClick);
    
}

function updatekundeBoxDblClicked(data){
    if($('#'+data.divid).length!=0){
            $('#'+data.divid).remove();
	    return;
    }
    
    $('body').append(data.div);
    $('#'+data.divid).hide();
    $('#'+data.divid).center();
    $('#'+data.divid).show('slide');
    $('#'+data.divid).draggable();
    $('div[id^=closebutton_]').bind('click',closeButtonClick);
    $('input[id^=erstellenbutton_]').bind('click',erstellenPlanImportButtonClick);
    
}




function updateDropped(data){
//    if(data.imex=='ex'){
	$('#'+data.target_id).html(data.targetTD);
	//$('#'+data.target_id).html('updated');
	//udelat draggable
	$('.draggable').bind('dblclick',draggableImExDblClicked);
	$('.draggable').draggable();
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
