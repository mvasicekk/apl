$(document).ready(function () {

    var container = document.getElementById('exceltable');
    var hot;

    yellowRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	Handsontable.renderers.TextRenderer.apply(this, arguments);
	td.style.backgroundColor = 'yellow';
    };

    greenRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	Handsontable.renderers.TextRenderer.apply(this, arguments);
	if (!value || value === '') {
	    td.style.background = '#EEE';
	}
	else {
	    td.style.backgroundColor = '#cfc';
	}
    };

    hot = new Handsontable(container, {
	data: [],
	dataSchema: {id_vorschuss: null, persnr: null, datumF: null, vorschuss: null},
	startRows: 1,
	startCols: 4,
	colHeaders: ['id', 'Name', 'PersNr', 'Datum', 'Vorschuss'],
	columns: [
	    {data: 'id_vorschuss', renderer: greenRenderer, readOnly: true},
	    {data: 'name', readOnly: true},
	    {data: 'persnr', type: 'numeric', allowInvalid: false},
	    {data: 'datumF', type: 'date', dateFormat: 'D.M.YYYY', correctFormat: true, firstDay: 1},
	    {data: 'vorschuss', type: 'numeric', allowInvalid: false}
	],
	minSpareRows: 25,
	afterChange: function (changes, source) {
	    // pripojit hodnotu id_vorschuss k poli
	    // nechci informovat o zmene id_vorschuss
//	    alert(source);
	    if (source == "return_oldvalue" || source == 'update_id_vorschuss') {
		return;
	    }

	    if (changes != null && changes.length == 1) {
//		alert('changes.length==1')
		var prop = changes[0][1];
//		alert(prop);
		if (prop == "id_vorschuss") {
		    return;
		}
	    }
	    var sourceDataRowA = [];
	    if (source == 'autofill' || source == 'edit' || source == 'paste') {
		for (i = 0; i < changes.length; i++) {
		    var row = changes[i][0];
		    var column = "id_vorschuss";
		    sourceDataRowA[i] = hot.getSourceDataAtRow(row);
		}
	    }

	    $.post('./tableAfterChange.php',
		    {
			id: $(this).attr('id'),
			changes: changes,
			sourceDataRowA: sourceDataRowA,
			source: source
		    },
	    function (data) {
		afterChangeExcelTable(data, hot);
	    },
		    'json'
		    );
	}
    });


    $(window).resize(onWinResize);
    $(window).resize();


    var picker = new Pikaday(
	    {
		field: document.getElementById('datum'),
		format: 'D.M.YYYY'
	    });


    $('#persnr').change(function (event) {
	$.post('./getVorschuss.php',
		{
		    id: $(this).attr('id'),
		    persnr_val: $('#persnr').val(),
		    datum_val: $('#datum').val()
		},
	function (data) {
	    updateExcelTable(data, hot);
	},
		'json'
		);
    });

    $('#datum').change(function (event) {
	$.post('./getVorschuss.php',
		{
		    id: $(this).attr('id'),
		    persnr_val: $('#persnr').val(),
		    datum_val: $('#datum').val()
		},
	function (data) {
	    updateExcelTable(data, hot);
	},
		'json'
		);
    });


});


/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function afterChangeExcelTable(data, et) {
    console.log(data);
    var idArrayUpdate = data.idArrayUpdate;

    //updatnu id vlozenych radku
    for (i = 0; i < idArrayUpdate.length; i++) {
	var row = idArrayUpdate[i].row;
	var iId = idArrayUpdate[i].insertId;
	if (idArrayUpdate[i].typ == 'insert') {
	    et.setDataAtRowProp(row, 'id_vorschuss', iId, 'update_id_vorschuss');
	}

	if (idArrayUpdate[i].typ == 'update') {
	    var row = idArrayUpdate[i].row;
	    var prop = idArrayUpdate[i].prop;
//	    alert("afterChangeExcelTable, row="+row+",prop="+prop);
	    if (idArrayUpdate[i].ar <= 0) {
		//vratit starou hodnotu
		et.setDataAtRowProp(row, prop, idArrayUpdate[i].oldValue, 'return_oldvalue');
	    }
	}

//	if(idArrayUpdate[i].typ=='persnr_update'){
//	    var row = idArrayUpdate[i].row;
//	    var prop = idArrayUpdate[i].prop;
//	    et.setDataAtRowProp(row,'name',idArrayUpdate[i].name,'persnr_update');
//	}
    }
}
/**
 * 
 * @param {type} data
 * @param {type} et
 * @returns {undefined}
 */
function updateExcelTable(data, et) {
    et.loadData(data.rows);
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
