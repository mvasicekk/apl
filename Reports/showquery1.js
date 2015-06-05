$(document).ready(function () {
    // get the data
    $.post('./getQueryData.php',
		{
		    sql: $('#sqlField').val(),
		    tabid: $('#tabidField').val()
		},
	function (data) {
	    updateGetQueryData(data);
	},
	'json'
    );

    $('#searchField').hide();
    
    $(window).resize(onWinResize);
    $(window).resize();

});

function updateGetQueryData(data){
    var container = document.getElementById('querytable');
    var cH = true;
    if(data.columnNames.length>0){
	cH = data.columnNames;
    }
    
    var dataKopie = JSON.parse(JSON.stringify(data.data));
    
    var hotOptions = {
	data: dataKopie,
	readOnly:true,
	colHeaders:cH,
	columnSorting:true,
	search: true,
	rowHeaders:true,
	fillHandle:false,
	minSpareRows: 0,
	columns:data.columnsOptions
    };

    if(data.data!==null){
	$('#searchField').show();
	var hot = new Handsontable(container, hotOptions);
	
	var searchFiled2 = document.getElementById('searchField');
	$('#searchField').on('keyup',function(){
	    var v = $(this).val();
	    console.log(v);
	    var nA = new Array();
	    var indexOld = -1;
	    data.data.forEach(function(val,index){
		for(var propertyName in val){
		    var matchValue = new RegExp(v,"gi");
		    if(val[propertyName]!==null){
			var rege = val[propertyName].toString().match(matchValue);
			if(rege!==null){
//			    console.log(rege);
			    if(index!=indexOld){
				indexOld = index;
//				console.log("index="+index+",property="+propertyName+",matchvalue="+matchValue);
				nA.push(val);
			    }
			}
		    }
		}
	    });
	    console.log("pocet radku v nA="+nA.length);
	    hot.loadData(nA);
	});
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
