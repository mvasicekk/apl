// JavaScript Document

$(document).ready(function(){

	$.datepicker.setDefaults($.datepicker.regional["de"]);
	$(".datepicker" ).datepicker($.datepicker.regional["de"]);

//	$('#kunde_von').bind('change', kundeChanged);
//	$('#kunde_bis').bind('change', kundeChanged);

	$('#kunde_von').bind('change', datumChanged);
	$('#kunde_bis').bind('change', datumChanged);
	$('#datum_von').bind('change', datumChanged);
	$('#datum_bis').bind('change', datumChanged);
	
	$(window).bind("resize", updateSize);
	updateSize();
	$('#spinner').hide();
	//$('input[type=text]:first').focus();

});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------
function updateSize(event){
	$('#dispodiv').height(function(index, height) {
	    return window.innerHeight - $(this).offset().top-10;
	});
}

function datumChanged(event){
    var acturl = $(this).attr('acturl');
    $('#spinner').show();
    $.post(acturl,
    {
	id:$(this).attr('id'),
	von:$('#datum_von').val(),
	bis:$('#datum_bis').val(),
	kd_von:$('#kunde_von').val(),
	kd_bis:$('#kunde_bis').val()
    },
    function(data){
	updateDatumChanged(data);
    },
    'json'
    );        
}

function updateDatumChanged(data){
    $('#spinner').hide();
    $('#dispodiv').html(data.divcontent);
//    $('table.dispotable').tinytbl({
//            direction: 'ltr',      // text-direction (default: 'ltr')
//            //thead:     true,       // fixed table thead
//            //tfoot:     true,       // fixed table tfoot
//            cols:      1,          // fixed number of columns
//            width:     'auto',     // table width (default: 'auto')
//            height:    'auto'      // table height (default: 'auto')
//        });
    //pridat udalostni procedury
    $('input[id^=solltag_]').bind('change',sollTagChanged);
}

function sollTagChanged(event){
    //alert('solltag changed');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
    {
	id:$(this).attr('id'),
	val:$(this).val(),
	bis:$('#datum_bis').val(),
	kd_von:$('#kunde_von').val(),
	kd_bis:$('#kunde_bis').val()
    },
    function(data){
	updateSollTagChanged(data);
    },
    'json'
    );        
}

function updateSollTagChanged(data){
    $('#'+data.id).val(data.minuten);
    $('#'+data.summeid).val(data.summeplan);
    $('#'+data.summetagId).html(data.summetagValue);
    $('#'+data.summestatnrtagId).html(data.summestatnrtagValue);
    $('#'+data.summinAllId).html(data.summinAllValue);
    
    $.each( data.zubearbarray, function(i, n){
	$('#'+i).html(n);
	if(n<0) 
	    $('#'+i).addClass('negativ');
	else
	    $('#'+i).removeClass('negativ');
    });
}

function kundeChanged(event){
    var acturl = $(this).attr('acturl');
    $.post(acturl,
    {
	id:$(this).attr('id'),
	kd_von:$('#kunde_von').val(),
	kd_bis:$('#kunde_bis').val()
    },
    function(data){
	updateKundeChanged(data);
    },
    'json'
    );        
}

function updateKundeChanged(data){
    $('#plany').html(data.planydiv);
    $('#dispodiv').html(data.divcontent);
}

