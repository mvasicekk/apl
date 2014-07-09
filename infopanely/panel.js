// JavaScript Document

$(document).ready(function(){
    
    var inputy = $('td input[id^=text]');
    inputy.bind('focus',function(e){
        this.select();
    });
    
        inputy.change(function(event){
            var acturl = $(this).attr('acturl');
            var id = $(this).attr('id');
            var value = $(this).val();

            $.post(acturl,
            {
                id: id,
                value: value
            },
            function(data){
                updateSaveText(data);
            },
            'json'
            );
	});
    
});


function updateSaveText(data){
    
}

