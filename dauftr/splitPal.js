$(document).ready(function () {

	
    var container = document.getElementById('ruecktable');
    var containerDA = document.getElementById('dauftrtable');
    
    
    // odeslat pres REST,
    
    var hotOptions = {
	data: [],
	dataSchema: {import: null, teil: null, pal: null, abgnr: null,gutstk:null,aussstk:null,aussart:null,auss_typ:null,gutFlag:null,aussFlag:null},
	startRows: 0,
	startCols: 0,
	fillHandle:false,
	colHeaders: ['Import', 'Teil', 'Pal', 'TaetNr', 'GutStk','AussStk','AussArt','AussTyp','G','A'],
	columns: [
	    {data: 'import', readOnly: true,type:'numeric'},
	    {data: 'teil', readOnly: true,type:'text'},
	    {data: 'pal', readOnly: true,type:'numeric'},
	    {data: 'abgnr', readOnly: true,type:'numeric'},
	    {data: 'gutstk', readOnly: true,type:'numeric'},
	    {data: 'aussstk', readOnly: true,type:'numeric'},
	    {data: 'aussart', readOnly: true,type:'numeric'},
	    {data: 'auss_typ', readOnly: true,type:'numeric'},
	    {data: 'gutFlag', readOnly: false,type:'checkbox',checkedTemplate: '1',uncheckedTemplate: '0'},
	    {data: 'aussFlag', readOnly: false,type:'checkbox',checkedTemplate: '1',uncheckedTemplate: '0'}
	],
	minSpareRows: 0,
	afterChange: function (changes, source) {
	    if(source=='edit'){
		var prop = changes[0][1];
		var row = changes[0][0];
		var oldVal = changes[0][2];
		var newVal = changes[0][3];
		if(newVal=="1"){
		    if(prop=="aussFlag"){
			gutVal = hot.getDataAtRowProp(row,'gutFlag');
			if(gutVal=="1"){
//			    alert('gutValue='+gutVal+', musim nastavit na 0');
			    hot.setDataAtRowProp(row,'gutFlag',"0",'prg_edit');
			}
		    }
		    if(prop=="gutFlag"){
			aussVal = hot.getDataAtRowProp(row,'aussFlag');
			if(aussVal=="1"){
//			    alert('gutValue='+gutVal+', musim nastavit na 0');
			    hot.setDataAtRowProp(row,'aussFlag',"0",'prg_edit');
			}
		    }
		}
	    }
	}
    };
    
    var hotOptionsDA = {
	data: [],
	dataSchema: {auftragsnr: null, teil: null, pal: null, abgnr: null, stk:null,fremdpos:null,kzgut:null,ex:null},
	readOnly:true,
	startRows: 0,
	startCols: 0,
	fillHandle:false,
	colHeaders: ['Import', 'Teil', 'Pal', 'TaetNr', 'ImpStk','FremdPos','KzGut','Ex'],
	columns: [
	    {data: 'auftragsnr', readOnly: true,type:'numeric'},
	    {data: 'teil', readOnly: true,type:'text'},
	    {data: 'pal', readOnly: true,type:'numeric'},
	    {data: 'abgnr', readOnly: true,type:'numeric'},
//	    {data: 'tatkz', readOnly: true,type:'text'},
	    {data: 'stk', readOnly: true,type:'numeric'},
//	    {data: 'fremdauftr', readOnly: true,type:'text'},
	    {data: 'fremdpos', readOnly: true,type:'text'},
	    {data: 'kzgut', readOnly: true,type:'text'},
	    {data: 'ex', readOnly: true,type:'text'}
	],
	minSpareRows: 0
    };

    hot = new Handsontable(container, hotOptions);
    hotDA = new Handsontable(containerDA, hotOptionsDA);
    
    $(window).resize(onWinResize);
    $(window).resize();

    $('#import').change(vonParamChanged);
    $('#pal1').change(vonParamChanged);
    $('#pal2stk').change(pal2stkChanged);
    $('#pal2').change(pal2Changed);
    $('#persnr').change(persnrChanged);
    $('#gosplit').click(gosplitClicked);
    $('#exInfo').hide();
    $('#gosplit').hide();
});


function showSplitGo(){
    var pal2 = parseInt($('#pal2').val());
    var pal2stk = parseInt($('#pal2stk').val());
    var persnr = parseInt($('#persnr').val());
    var isExVisible = $('#exInfo').is(':visible');
    var isPal2ExistsVisible = $('#pal2ExistsInfo').is(':visible');
    
    //if(pal2>0 && pal2stk>0 && persnr>0 && !isExVisible && !isPal2ExistsVisible){
    if(pal2>0 && pal2stk>0 && persnr>0 && !isExVisible){
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

function pal2Changed(){
    //alert('pal2 changed');
    //otestovat, jestli uz takova paleta neexituje
    $.post('./testPalExists.php',
		{
		    id: $(this).attr('id'),
		    import: $('#import').val(),
		    pal2: $('#pal2').val()
		},
	function (data) {
	    updateTestPalExists(data);
	},
	'json'
    );
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function gosplitClicked(event){
    $('#gosplit').hide();
    $.post('./goSplit.php',
		{
		    id: $(this).attr('id'),
		    import: $('#import').val(),
		    pal1: $('#pal1').val(),
		    pal2: $('#pal2').val(),
		    pal2stk: $('#pal2stk').val(),
		    persnr: $('#persnr').val(),
		    drueckHotData:hot.getData()
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
    //$('#dauftrLog').html(data.dauftrLog+"<hr>"+data.drueckLog);
    $('#dauftrLog').html("<h3> hotovo - fertig ! </h3>");
    $('#gosplit').hide();
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

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateTestPalExists(data){
    if(data.exists===true){
	$('#pal2ExistsInfo').show();
    }
    else{
	$('#pal2ExistsInfo').hide();
    }
    showSplitGo();
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
	
	$('#import1').val($('#import').val());
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
 * @param {type} et
 * @returns {undefined}
 */
function updateRMTable(data, et) {

    hotDA.loadData(data.dauftrRows);
    
    if(data.rows && data.rows.length>0){
	hot.loadData(data.rows);
	//pripravim nove cislo palety
	$('#pal2').val(parseInt(data.impal)+9);
	pal2Changed();
    }
    else{
	$('#pal2').val("");
	hot.loadData(null);
    }

    if(data.isEx===true){
	$('#exInfo').show();
    }
    else{
	$('#exInfo').hide();
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
