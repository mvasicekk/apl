$(document).ready(function(){
    $('input:first').focus();
    $('input:first').select();

    // schovam sekce s tlacitkama, ktere neobsahuji zadna viditelna tlacitka
    // vybrat vsechny fieldsety na uvodni strance
    $('div#tlacitka fieldset').each(function(index){
	//zjistim pocet viditelnych inputu
	var visibleInputs = $(this).has('input:button:visible');
	var visibleInputsCount = visibleInputs.length;
	if(visibleInputsCount==0){
	    $(this).hide();
	}
    });
});
