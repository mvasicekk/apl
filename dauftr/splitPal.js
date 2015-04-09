$(document).ready(function () {

	
//    var container = document.getElementById('ruecktable');
//    var hot;
//
//    hot = new Handsontable(container, hotOptions);

    var container = document.getElementById('ruecktable');
    
    var hotOptions = {
	data: [],
	dataSchema: {import: null, teil: null, pal: null, abgnr: null,gutstk:null,aussstk:null},
	startRows: 0,
	startCols: 0,
	fillHandle:false,
	colHeaders: ['Import', 'Teil', 'Pal', 'TaetNr', 'GutStk','AussStk'],
	columns: [
	    {data: 'import', readOnly: true,type:'numeric'},
	    {data: 'teil', readOnly: true,type:'numeric'},
	    {data: 'pal', readOnly: true,type:'numeric'},
	    {data: 'abgnr', readOnly: true,type:'numeric'},
	    {data: 'gutstk', readOnly: true,type:'numeric'},
	    {data: 'aussstk', readOnly: true,type:'numeric'}
	],
	minSpareRows: 0
    };

    hot = new Handsontable(container, hotOptions);
    
    $(window).resize(onWinResize);
    $(window).resize();


//    var picker = new Pikaday(
//	    {
//		field: document.getElementById('datum'),
//		format: 'D.M.YYYY'
//	    });


    $('#import').change(vonParamChanged);
    $('#pal1').change(vonParamChanged);
    $('#pal2stk').change(pal2stkChanged);
    $('#persnr').change(persnrChanged);
    $('#gosplit').click(gosplitClicked);

    $('#gosplit').hide();
});


function showSplitGo(){
    var pal2 = parseInt($('#pal2').val());
    var pal2stk = parseInt($('#pal2stk').val());
    var persnr = parseInt($('#persnr').val());
    if(pal2>0 && pal2stk>0 && persnr>0){
	$('#gosplit').show();
    }
    else{
	$('#gosplit').hide();
    }
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function pal2stkChanged(event) {
    showSplitGo();
}


/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function gosplitClicked(event){
    $.post('./goSplit.php',
		{
		    id: $(this).attr('id'),
		    import: $('#import').val(),
		    pal1: $('#pal1').val(),
		    pal2: $('#pal2').val(),
		    pal2stk: $('#pal2stk').val(),
		    persnr: $('#persnr').val()
		},
	function (data) {
	    updateGoSplit(data);
	},
	'json'
    );
}

/**
 * 
 * @param {type} data
 * @returns {Function|undefined}
 */
function updateGoSplit(data){
    $('#dauftrLog').html(data.dauftrLog+"<hr>"+data.drueckLog);
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function persnrChanged(event){
    //test jestli persnr existuje
    $.post('./getPersNr.php',
		{
		    id: $(this).attr('id'),
		    persnr: $(this).val()
		},
	function (data) {
	    updatePersnrChanged(data);
	},
	'json'
	);
}

function updatePersnrChanged(data){
    if(data.rows==null){
	$('#persnr').val("");
    }
    showSplitGo();
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function vonParamChanged(event) {
	
	$.post('./getRM.php',
		{
		    id: $(this).attr('id'),
		    import: $('#import').val(),
		    pal1: $('#pal1').val()
		},
	function (data) {
	    updateRMTable(data);
	},
	'json'
	);
}


/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
//function afterChangeExcelTable(data, et) {
//    console.log(data);
//    var idArrayUpdate = data.idArrayUpdate;
//
//    //updatnu id vlozenych radku
//    for (i = 0; i < idArrayUpdate.length; i++) {
//	var row = idArrayUpdate[i].row;
//	var iId = idArrayUpdate[i].insertId;
//	if (idArrayUpdate[i].typ == 'insert') {
//	    et.setDataAtRowProp(row, 'id_vorschuss', iId, 'update_id_vorschuss');
//	}
//
//	if (idArrayUpdate[i].typ == 'update') {
//	    var row = idArrayUpdate[i].row;
//	    var prop = idArrayUpdate[i].prop;
////	    alert("afterChangeExcelTable, row="+row+",prop="+prop);
//	    if (idArrayUpdate[i].ar <= 0) {
//		//vratit starou hodnotu
//		et.setDataAtRowProp(row, prop, idArrayUpdate[i].oldValue, 'return_oldvalue');
//	    }
//	}
//
////	if(idArrayUpdate[i].typ=='persnr_update'){
////	    var row = idArrayUpdate[i].row;
////	    var prop = idArrayUpdate[i].prop;
////	    et.setDataAtRowProp(row,'name',idArrayUpdate[i].name,'persnr_update');
////	}
//    }
//}
/**
 * 
 * @param {type} data
 * @param {type} et
 * @returns {undefined}
 */
function updateRMTable(data, et) {
    
    if(data.rows && data.rows.length>0){
	hot.loadData(data.rows);
	//pripravim nove cislo palety
	$('#pal2').val(parseInt(data.impal)+9);
    }
    else{
	$('#pal2').val("");
    }

    showSplitGo();
}
//******************************************************************************
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function onWinResize(event) {
    var h = $(window).height();
//		var go = $('#dauftrgrid').offset();
//		var computedGridHeight = h-go.top-5;
//		$('#dauftrgrid').pqGrid('option','height',computedGridHeight);
}
