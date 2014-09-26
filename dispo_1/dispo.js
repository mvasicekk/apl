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
	$('#rm_bis').bind('change', datumChanged);
	$('#disporefresh').bind('click', datumChanged);
	$('#nurMitMin').bind('click', datumChanged);

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
	$('div.ft_container').height($('#dispodiv').height());
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
	kd_bis:$('#kunde_bis').val(),
	rm_bis:$('#rm_bis').val(),
	nurMitMin:$('#nurMitMin').attr('checked')?1:0
    },
    function(data){
	updateDatumChanged(data);
    },
    'json'
    );        
}

function updateDatumChanged(data){
    $('#spinner').hide();
    $('#rm_bis').val(data.rm_bis);
    $('#dispodiv').html(data.divcontent);
    
    $('table.dispotable').fxdHdrCol({
		    fixedCols:  1,
		    width:     "100%",
		    height:    "100%",
		    colModal: data.columns,
//		    colModal: [
//			   { width: 200, align: 'center' },
//			   { width: 500, align: 'center' },
//			   { width: 500, align: 'center' }
//		    ],
		    sort: false
	    });


    $('input[id^=solltag_]').bind('change',sollTagChanged);
    $('input[id^=solltag_]').bind('click',sollTagFocus);
}

function sollTagFocus(event){
    //alert('focus');
    $(this).select();
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
	kd_bis:$('#kunde_bis').val(),
	rm_bis:$('#rm_bis').val()
    },
    function(data){
	updateSollTagChanged(data);
    },
    'json'
    );        
}

function updateSollTagChanged(data){
    $('#'+data.id).val(data.minuten);
    
    // misto na id se odkazuju na atribut name, protoze kvuli ukotvenym radkum/sloupcum
    // mam na jedne strance vice elementu se stejnym id, coz je illegal a vyber
    // potom nefunguje jak ma
    // 
    //$('#'+data.summeid).val(data.summeplan);
    var att1 = 'input[name="'+data.summeid+'"]';
    $(att1).val(data.summeplan);
    
    $('#'+data.summetagId).html(data.summetagValue);
    $('#'+data.summestatnrtagId).html(data.summestatnrtagValue);
    $('#'+data.summinAllId).html(data.summinAllValue);
    
    $.each( data.zubearbarray, function(i, n){
	$('#'+i).html(n);
	
	// taky vyber podle atributu name kvuli nasobnym id na jedne strance
	var att='td[data-name="'+i+'"]';
	$(att).html(n);
	
	var att='input[name="'+i+'"]';
	$(att).val(n);

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

