/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){

    //alert('ready');
    // posun k dalsimu inputu pomoci enteru
    //var inputy = $('input:text');
    var inputy = $('[class*=entermove]');
    //inputy.css({"background-color":"yellow"});
    
    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    $('input:text').bind('focus',function(e){
        this.select();
    });
    
    inputy.bind('keydown',function(e){
	//alert('keydown');
        var key = e.which;
        if(key==13){
	    if(!$(this).hasClass('submit')){
		e.preventDefault();
            }
            //e.preventDefault();
            var nextIndex = inputy.index(this) + 1;
            if(inputy[nextIndex]!=null){
                var nextBox = inputy[nextIndex];
                nextBox.focus();
            }
        }
    });
    
    
    var colM = [
		{ title: "ID", dataIndx: "id",width:10,editable:false }
		,{ title: "Datum", align:"left",editable:false,dataIndx: "stamp",width:130 }
		,{ title: "AnfTyp", align:"left",editable:false,dataIndx: "anftyp",width:60 }
		,{ title: "Artikel", dataIndx: "artikel",width:135,editable:false }
		,{ title: "Anzahl", dataIndx: "anzahl", align: "right",width:10,editable:false }
		,{ title: "User", dataIndx: "login", align: "left" ,width:10,editable:false}
		,{ title: "Bemerkung", dataIndx: "bemerkung", align: "left",width:170,editable:false }
		,{ title: "ab Datum", dataIndx: "abdatum", align: "left",width:75,editable:false }
		,{ title: "prio", dataIndx: "prio", align: "right",width:10,editable:false }
		,{ title: "verantw.", dataIndx: "erledigt", align: "left",width:70,editable:true,className:"editable" }
		,{ title: "Status", dataIndx: "status", align: "left",width:90,className:"editable" }
		,{ title: "lief. Datum", dataIndx: "lieferdatum", align: "left",width:75,className:"editable" }
//		,{ title: "test spojeni sloupcu", colModel:[{title:"neco"},{title:"neco1"}],width:100}
	    ];
	    
	    var dataModel = {
		location: "remote",                        
		dataType: "JSON",
		method: "GET",
		getUrl : function () {                
		    return { url: 'getanforderungen.php?import='+$('#import').val()};
		},
		getData: function ( response ) {                
		    return { data: response };                
		}
	    };

	    var obj = {
		width: "100%",
		height:300,
		title: "EinkaufAnforderungen", 
		resizable: false, 
		draggable: false,
		dataModel: dataModel,
		colModel: colM,
		bottomVisible: false,
		numberCell: false,
		flexWidth:true,
		flexHeight:false
		,columnBorders:true
		,scrollModel:{pace:"consistent",horizontal:false}
		,wrap:false
		,roundCorners:false
	    };
	    $("#grid_array").pqGrid(obj);
	    
	    $("#grid_array").pqGrid(
		    {
			cellSave: function(event,ui){
			    var dataModel = ui.data;
			    var rowObject = dataModel[ui.rowIndxPage];
			    var id_dauftr = rowObject.id;
			    var dataIndex = ui.dataIndx;
			    var value = rowObject[dataIndex];
			    var acturl = 'saveEinkaufForm.php';
			    $.post(
				acturl,
				{
				    id:id_dauftr,
				    index:dataIndex,
				    value:value
				},
				function(data){
				updateSaveEinkaufForm(data);
				},
				'json'
			    );
			}
		    }
	    );
	    $('#grid_array').css("margin-left","2px");
    	    $(window).resize(onWinResize);
	    $(window).resize();
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
		$('#grid_array').pqGrid('option','height',computedGridHeight);
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function updateSaveEinkaufForm(data){
    
}