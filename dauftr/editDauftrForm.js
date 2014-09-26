$(document).ready(function(){
    
    
    var colM = [
		{ title: "IM", dataIndx: "im",width:70,editable:false,dataType:"integer",className:"im" }
		,{ title: "Teil", align:"left",editable:false,dataIndx: "teil",width:100,dataType:"string",className:"teil" }
		,{ title: "Pal", align:"right",editable:false,dataIndx: "pal",width:50,dataType:"integer" }
		,{ title: "IM-Stk", editable:true,dataIndx: "im_stk",width:50,align:"right",dataType:"integer",className:"editable" }
		,{ title: "Bemerkung", dataIndx: "bemerkung", align: "left",width:280,dataType:"string",className:"editable" }
		,{ title: "Fremdauftr", align:"left",dataIndx: "fremdauftr",width:110,dataType:"string",className:"editable" }	
		,{ title: "Fremdpos", align:"left",dataIndx: "fremdpos",width:110,dataType:"string",className:"editable" }
		,{ title: "Plan", align:"left",dataIndx: "plan",width:80,dataType:"string",className:"editable" }
		,{ title: "ID", align:"right",editable:false,dataIndx: "id",width:100,dataType:"integer",className:'rdonly' }
	    ];
	    
	    var dataModel = {
		location: "remote",                        
		dataType: "JSON",
		method: "GET",
		getUrl : function () {                
		    return { url: 'getDauftrRows.php?import='+$('#import').val()+'&teil='+$('#teil').val()+'&plan='+$('#plan').val()};
		},
		getData: function ( response ) {                
		    return { data: response };                
		}
	    };

	    var obj = {
		width: "100%", 
		height:300,
		title: "DauftrRows", 
		resizable: false, 
		draggable: false,
		dataModel: dataModel,
		colModel: colM,
		bottomVisible: false,
		topVisible:false,
		numberCell: false,
		flexWidth:true,
		flexHeight:false
		,columnBorders:true
		,scrollModel:{pace:"consistent",horizontal:false}
		,selectionModel: { type: 'cell'}
		,wrap:false
		,hoverMode:"cell"
		,editModel: { clicksToEdit: 1, saveKey: 13}
		,roundCorners:false
	    };
	var $grid = $("#dauftrgrid").pqGrid(obj);
	    
	// odchyceni pohybu pomoci sipek
	$grid.on("pqgridcelleditkeydown", function (evt, ui) {
	   var keyCode = evt.keyCode;
	   var rowIndxPage = ui.rowIndxPage;
	   var colIndx = ui.colIndx;
	   if (keyCode === 40 || keyCode === 38) {
		$grid.pqGrid("saveEditCell");
		//$('#sizeinfo').html("row="+rowIndxPage+"col="+colIndx+"key="+keyCode);
	   }
	   if (keyCode === 40) {
	       var dataModel = ui.dataModel.data;
	       //$('#sizeinfo').html("dataModel="+dataModel);
		if (rowIndxPage < dataModel.length - 1) {
		    rowIndxPage++;
		    $grid.pqGrid("setSelection", null);
		    $grid.pqGrid("setSelection", { rowIndx: rowIndxPage });
		    $grid.pqGrid("editCell", { rowIndxPage: rowIndxPage, colIndx: colIndx });
		    evt.preventDefault();
		    return false;
		}
	   }
	   else if (keyCode === 38 && rowIndxPage > 0) {
	    rowIndxPage--;
            $grid.pqGrid("setSelection", null);
            $grid.pqGrid("setSelection", { rowIndx: rowIndxPage });
            $grid.pqGrid("editCell", { rowIndxPage: rowIndxPage, colIndx: colIndx });
            evt.preventDefault();
            return false;
        }
	});
	
	$grid.on("keydown", function (evt) {
	    var keyCode = evt.keyCode;
	    if (keyCode == 38 || keyCode == 40) {
		evt.preventDefault();
		return false;
	    }
	});
    //**************************************************************************
	    $("#dauftrgrid").pqGrid(
		    {
			cellSave: function(event,ui){
			    var dataModel = ui.data;
			    var rowObject = dataModel[ui.rowIndxPage];
			    var id_dauftr = rowObject.id;
			    var dataIndex = ui.dataIndx;
			    var value = rowObject[dataIndex];
			    var acturl = 'saveDauftrForm.php';
			    $.post(
				acturl,
				{
				    id:id_dauftr,
				    index:dataIndex,
				    value:value
				},
				function(data){
				updateSaveDauftrForm(data);
				},
				'json'
			    );
			}
		    }
	    );
	    $('#dauftrgrid').css("margin-left","2px");
	    $(window).resize(onWinResize);
	    $(window).resize();
	    $('#import').change(function(event){
		$('#dauftrgrid').pqGrid( "refreshDataAndView" );
		$('#dauftrgrid').pqGrid( "setSelection", {rowIndx:0} );
	    });
	    $('#teil').change(function(event){
		$('#dauftrgrid').pqGrid( "refreshDataAndView" );
		$('#dauftrgrid').pqGrid( "setSelection", {rowIndx:0} );
	    });
    	    $('#plan').change(function(event){
		$('#dauftrgrid').pqGrid( "refreshDataAndView" );
		$('#dauftrgrid').pqGrid( "setSelection", {rowIndx:0} );
	    });
	    
	    
	    // geolocation
//	    var mapContainer = document.getElementById('map-container');
//	    function successGeoData(position){
//		var successMessage = 'Nasli jsme vasi pozici';
//		successMessage+='\n Delka : '+position.coords.latitude;
//		successMessage+='\n Sirka : '+position.coords.longitude;
//		successMessage+='\n presnost : '+position.coords.accuracy;
//		console.log(successMessage);
//		var successMessageHTML = successMessage.replace('/\n\g','<br />');
//		var currentContent = mapContainer.innerHTML;
//		mapContainer.innerHTML=currentContent+'<br/>'+successMessageHTML;
//	    }
//	    function failGeoData(error){
//		console.log('error code = '+error.code);
//		switch(error.code){
//		    case error.POSITION_UNAVAILABLE:
//			errorMessage = 'nemohu ziskat polohu';
//			break;
//		    case error.PERSMISSION_DENIED:
//			errorMessage = 'uzivatel nepovolil polohu';
//			break;
//		    case error.TIMEOUT:
//			errorMessage = 'Timeout - hledani polohy trvalo dlouho';
//			break;
//		    case error.UNKNOWN_ERROR:
//			errorMessage = 'neznama chyba: '+error.code;
//			break;
//		}
//		console.log(errorMessage);
//		mapContainer.innerHTML = errorMessage;
//	    }
//	    if(navigator.geolocation){
//		var startMessage = 'prohlizec podporuje geolocation API';
//		console.log(startMessage);
//		mapContainer.innerHTML = startMessage;
//		console.log('zjistuji pozici');
//		mapContainer.innerHTML = startMessage + '<br> zjistuji polohu';
//		navigator.geolocation.getCurrentPosition(successGeoData,failGeoData,
//		{
//		    maximumAge:60000,
//		    enableHighAccuracy:true,
//		    timeout:5000
//		}
//		);
//	    }
//	    else{
//		mapContainer.innerHTML = 'prohlizec nepodporuje geolocation API';
//	    }
});
//******************************************************************************
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function onWinResize(event){
		var h = $(window).height();
		var go = $('#dauftrgrid').offset();
		var computedGridHeight = h-go.top-5;
		$('#dauftrgrid').pqGrid('option','height',computedGridHeight);
}


/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateSaveDauftrForm(data){
    
}