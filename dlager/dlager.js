// JavaScript Document

$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional["de"]);
    $(".datepicker" ).datepicker($.datepicker.regional["de"]);

    var inputy = $('input[class^=entermove]');
    
    $('input:text[class*=entermove]').bind('focus',function(e){
        this.select();
    });
    
    //alert(inputy.toArray());
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }
    //inputy.css({'background-color':'green'});
    //alert(inputy);
    inputy.bind('keydown',function(e){
        var key = e.which;
        if(key==13){
	    if(!$(this).hasClass('submit')){
		e.preventDefault();
            }
	    else{
		$('#umbuchunggo').css({"background-color":"green"});
		if($(this).attr('id')=='umbuchunggo') $('#umbuchunggo').click();
	    }
            var nextIndex = inputy.index(this) + 1;
            //alert('this='+this+' nextIndex='+nextIndex);
            if(inputy[nextIndex]!=null){
                var nextBox = inputy[nextIndex];
                //alert(nextBox);
                nextBox.focus();
                //nextBox.select();
            }
	    return false;
        }
    });

    $('#umbuchunggo').bind('click',umbuchunggo);
    
    $('#teil').val('');
    $('#auftrag_import').val(0);
    $('#pal_import').val(0);
    $('#gut_stk').val(0);
    $('#auss_stk').val(0);
    $('#lager_von').val('');
    $('#lager_nach').val('');
    $('#teil').focus();
    $('#umbuchunggo').css({"background-color":""});
    
    $( "#teil" ).autocomplete({
			source: "getTeil.php",
			minLength: 3,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
					
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": "500px","color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });

    $( "#auftrag_import" ).autocomplete({
//			source: "getImport.php",
			source: function(request, response) {
			    $.ajax({
				url: "getImport.php",
				dataType: "json",
				data: {
				    term : request.term,
				    teil : $('#teil').val()
				},
				success: function(data) {
				response(data);
			    }
			    });
			},
			minLength: 0,
                        autoFocus: false,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": "200px","color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
    $( "#pal_import" ).autocomplete({
//			source: "getImport.php",
			source: function(request, response) {
			    $.ajax({
				url: "getPal.php",
				dataType: "json",
				data: {
				    term : request.term,
				    teil : $('#teil').val(),
				    im : $('#auftrag_import').val()
				},
				success: function(data) {
				response(data);
			    }
			    });
			},
			minLength: 0,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": "200px","color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		    });
		    
    $( "#lager_von,#lager_nach" ).autocomplete({
			source: "getLager.php",
			minLength: 0,
                        autoFocus: true,
			select: function( event, ui ) {
                                    if(ui.item){
					
                                    }
                                    else{
                                        // polozka neni v seznamu
                                    }
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css(
				    {"width": "500px","color":"black","font-size":"12px","height":"200px","overflow-y":"auto"}
				);
			}
		}).focus(function(){
		    if ($(this).autocomplete("widget").is(":visible")) {
			return;
		    }
		    $(this).data("autocomplete").search($(this).val());
		  });
});


// Ajax update Functions
//---------------------------------------------------------------------------------------------------------------------

function umbuchunggo(event){
    var id=$(this).attr('id');
    var acturl = $(this).attr('acturl');
    $.post(acturl,
        {
            id:id,
	    teil : $('#teil').val(),
	    im : $('#auftrag_import').val(),
	    pal : $('#pal_import').val(),
	    gut_stk : $('#gut_stk').val(),
	    auss_stk : $('#auss_stk').val(),
	    lager_von : $('#lager_von').val(),
	    lager_nach : $('#lager_nach').val()
        },
        function(data){
            updateUmbuchunggo(data);
        },
        'json'
        );    

}

function updateUmbuchunggo(data){
	    $('#umbuchunggo').css({"background-color":""});
    	    $('#teil').val('');
	    $('#auftrag_import').val(0);
	    $('#pal_import').val(0);
	    $('#gut_stk').val(0);
	    $('#auss_stk').val(0);
	    $('#lager_von').val('');
	    $('#lager_nach').val('');
	    $('#teil').focus();
}

