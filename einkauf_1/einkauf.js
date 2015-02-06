/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// globalni promenne

var hot = null;

$(document).ready(function(){
    $('#grid_array').css("margin-left","2px");
	    
    getData();
    
//    maximizeHandsonTable(hot)
    $(window).resize(maximizeHandsonTable());
    $(window).resize();
    

    var container = document.getElementById('grid_array');
    hot = new Handsontable(container,
    {
      data: null,
      minSpareRows: 0,
      colHeaders: true,
      contextMenu: true
    });

});


//------------------------------------------------------------------------------
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function onWinResize(event){
		var h = $(window).height();
		var go = $('#grid_array').offset();
		var computedGridHeight = h-go.top-5;
//		$('#grid_array').pqGrid('option','height',computedGridHeight);
}

function maximizeHandsonTable(){
    
    var example = document.getElementById('grid_array');
    availableWidth = $(window).width()  + window.scrollX;
    alert($(window).width());
    availableHeight = 200;//$(window).height() - offset.top;// + window.scrollY;

    example.style.width = availableWidth + 'px';
    example.style.height = availableHeight + 'px';
    if(hot!==null) hot.render();
}

/**
 * 
 * @returns {undefined}
 */
function getData(){
    var acturl = './getAnforderungenDataTable.php';
			    $.post(
				acturl,
				{
				    id:'handsontable'

				},
				function(data){
				updateGetAnforderungenDataTable(data);
				},
				'json'
			    );
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateGetAnforderungenDataTable(data){
    einkaufdata = data.ar;
    hot.loadData(einkaufdata);
    maximizeHandsonTable();
}