$(document).ready(function () {

    var container = document.getElementById('exceltable');
    //var hot;

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
	dataSchema: {
	    id: null
	    ,delstr:null,
	    ab_aby_soll_datetime: null, 
	    imexstr: null, 
//	    dspediteur_id: null, 
	    spedname: null, 
	    fahrername: null, 
	    lkw_kz: null, 
	    naves_kz: null,
	    preis: null,
	    rabatt: null,
	    proforma:null,
	    archiv:null,
	    bemerkung: null
	    
	},
	stretchH: 'last',
	startRows: 1,
	startCols: 4,
	minSpareRows: 0,
	minSpareCols: 0,
	columnSorting:true,
	currentRowClassName: 'currentRow',
	currentColClassName: 'currentCol',
	fillHandle:false,   // zakazat natahovaci tahatko, protoze moc nefunguje
	colHeaders: [
	    'id', 
	    '',
	    'Abfahrt Aby Soll',
	    'ImEx', 
//	    'SpediteurId',
	    'Spediteur', 
	    'Fahrername', 
	    'Lkw KZ',
	    'Anh. KZ',
	    'Preis vereinbart',
	    'Rabatt',
	    'Proforma',
	    'Archiv',
	    'Bemerkung'
	],
	columns: [
	    {data: 'id', renderer: greenRenderer, readOnly: true},
	    {data: 'delstr',readOnly:true,renderer:'html'},
	    {data: 'ab_aby_soll_datetime', readOnly: true},
	    {data: 'imexstr', readOnly:true,renderer:'html'},
//	    {data: 'dspediteur_id', type: 'numeric', readOnly:true},
	    {data: 'spedname', readOnly:true},
	    {data: 'fahrername'},
	    {data: 'lkw_kz'},
	    {data: 'naves_kz'},
	    {data: 'preis', type: 'numeric', allowInvalid: false},
	    {data: 'rabatt', type: 'numeric', allowInvalid: false},
	    {data: 'proforma'},
	    {data: 'archiv', type: 'numeric', allowInvalid: false},
	    {data: 'bemerkung'}
	],
	afterCreateRow: function (index, numberOfRows) {
			    // nedovolit pridat nove radky
			    data.splice(index, numberOfRows);
			},
	//minSpareRows: 25,
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
		    //var row = hot.translateRowIndex(changes[i][0]);
		    var row = changes[i][0];
		    var column = "id_vorschuss";
		    //sourceDataRowA[i] = hot.getSourceDataAtRow(row);
		    sourceDataRowA[i] = hot.getDataAtRow(row);
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


    var pickerVon = new Pikaday(
	    {
		field: document.getElementById('datumVon'),
		format: 'D.M.YYYY'
	    });
	    
    var pickerBis = new Pikaday(
	    {
		field: document.getElementById('datumBis'),
		format: 'D.M.YYYY'
	    });


    
    $('input[id^=datum]').change(function (event) {
	$.post('./getRundlauf.php',
		{
		    id: $(this).attr('id'),
		    datum_val_von: $('#datumVon').val(),
		    datum_val_bis: $('#datumBis').val(),
		    spediteur: $('#spediteur').val()
		},
	function (data) {
	    updateExcelTable(data, hot);
	},
		'json'
		);
    });

    $('select[id=spediteur]').change(function (event) {
	$.post('./getRundlauf.php',
		{
		    id: $(this).attr('id'),
		    datum_val_von: $('#datumVon').val(),
		    datum_val_bis: $('#datumBis').val(),
		    spediteur: $('#spediteur').val()
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
}

/**
 * 
 * @param {type} data
 * @param {type} et
 * @returns {undefined}
 */
function updateExcelTable(data, et) {
    et.loadData(data.rows);
    $('button[id^=delRundlauf_]').unbind('click');
    $('button[id^=delRundlauf_]').bind('click',delRundlaufClicked);
}

function delRundlaufClicked(e) {
    console.log('delRundlauf clicked');
    var d = window.confirm('wirklich loeschen / opravdu smazat ?');
    if (d) {
	$.post('./getRundlauf.php',
		{
		    id: $(this).attr('id'),
		    datum_val_von: $('#datumVon').val(),
		    datum_val_bis: $('#datumBis').val(),
		    spediteur: $('#spediteur').val(),
		    action: 'delRundlauf'
		},
	function (data) {
	    updateExcelTable(data, hot);
	},
		'json'
		);
    }
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
