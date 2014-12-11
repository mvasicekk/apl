// JavaScript Document

function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

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
    var att1 = 'input[name="'+data.summeid+'"]';
    $(att1).val(data.summeplan);
    
    $('#'+data.summetagId).html(data.summetagValue);
    $('#'+data.summestatnrtagId).html(data.summestatnrtagValue);
    $('#'+data.summinAllId).html(data.summinAllValue);

    //spocitat rozdil
    //jednotlive statnr
    var sollProtag = parseInt($('#sollprotagsum_'+data.statnr+'_'+data.datum).html().replace(/\s+/g, ''));
    var sollTag = parseInt($('#solltagsum_'+data.statnr+'_'+data.datum).html().replace(/\s+/g, ''));
    var diff = sollProtag-sollTag;
    diff = number_format(diff,0,',',' ');
    $('#diff_'+data.statnr+'_'+data.datum).html(diff);
    //suma statnr
    var sollProtag = parseInt($('#sollprotagsum_sum_'+data.datum).html().replace(/\s+/g, ''));
    var sollTag = parseInt($('#solltagsum_sum_'+data.datum).html().replace(/\s+/g, ''));
    var diff = sollProtag-sollTag;
    diff = number_format(diff,0,',',' ');
    $('#diff_sum_'+data.datum).html(diff);
    
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

