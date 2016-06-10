// JavaScript Document

$(document).ready(function(){

    $('#fz_drucken').attr('disabled','disabled');
    $('#fz_drucken').click(function(event){
       // projit vsechny radky tabulky a vytvorit pole s parametrama
       pole = '';

       $('#fz_teile tr.teilezeile').each(function(index){
           //alert($(this).html());
           teil = $(this).find('td.teil').html();
           verpackungmenge = $(this).find('td.verpackungmenge').html();
           anzpal = $(this).find('td.anzpal').html();

//           charge = $(this).find('td.charge').html();
           pole += ''+teil+','+verpackungmenge+','+anzpal+';';//+charge+';';
       });

       pal2x = $('#pal2x').attr('checked')?1:0;
       a5 = $('#A5papier').attr('checked')?1:0;
       erstpal = $('#erstpal').val();
       
       document.location.href='../../Reports/D64Y_pdf.php?pole='+pole+'&export='+$('#export').val()+'&export_datum='+$('#export_datum').val().toString()+'&a5='+a5+'&pal2x='+pal2x+'&erstpal='+erstpal;
    });
    
    // fokus na teil

    $('#teil').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$(this).val(),
	    export:$('#export').val()
        },
        function(data){
            updateValidateTeil(data);
        },
        'json'
        );
    });

    $('#export').blur(function(event){
        var id = $(this).attr('id');
        var acturl = $(this).attr('acturl');

        $.post(acturl,
        {
            id:id,
            value:$(this).val()
        },
        function(data){
            updateValidateExport(data);
        },
        'json'
        );
    });

    $('#addteil').click(function(event){
       // vytvorim novy radek v tabulce fz_teile
       teil = $('#teil').val();
       verpackungmenge = $('#verpackungmenge').val();
       anzpal = $('#anzpal').val();
//       charge = $('#charge').val();

       row = '<tr id="r_'+teil+'" class="teilezeile">';
       row+= '<td colspan="2" style="background-color:white;" class="teil">';
       row+= teil;
       row+= '</td>';
       row+= '<td colspan="2" style="background-color:white;text-align:right;" class="verpackungmenge">';
       row+= verpackungmenge;
       row+= '</td>';
       row+= '<td colspan="2" style="background-color:white;text-align:right;" class="anzpal">';
       row+= anzpal;
       row+= '</td>';
//       row+= '<td class="charge">';
//       row+= charge;
//       row+= '</td>';
       row+= '<td>';
       row+= '<input type="button" id="del_'+teil+'" value="-"/>';
       row+= '</td>';

       row+= '</tr>';

       $('#fz_teile tr:last').after(row);

       $('#fz_teile [id^=del_]').click(function(event){
          $(this).parent().parent().remove();
          if($('#fz_teile tr').length==3)
            $('#fz_drucken').attr('disabled','disabled');
       });

       //alert($('#fz_teile tr').length);
       
       if($('#fz_teile tr').length==3)
            $('#fz_drucken').attr('disabled','disabled');
        else
            $('#fz_drucken').attr('disabled','');
    });
});

//------------------------------------------------------------------------------------------------------
// ajax update functions

function updateFzDrucken(data){

}

function updateValidateExport(data){
 if(data.row==null){
     $('#export').val('');
     $('#export_datum').val('');
 }
 else{
     $('#export_datum').val(data.ex_datum_soll);
 }
}

function updateValidateTeil(data){
 if(data.row==null){
     //takovy dil neexistuje
     $('#teil').css({'background-color':'red'});
     $('#teil').val('');
 }
 else{
     $('#teil').css({'background-color':''});
     $('#verpackungmenge').val(data.verpackungmenge);
 }
}
