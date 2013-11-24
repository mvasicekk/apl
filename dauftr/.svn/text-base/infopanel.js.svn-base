// JavaScript Document

function updateSaveText(data){
    
}


$(document).ready(function(){

    var inputy = $('td input[id^=text]');
    
    inputy.css({"background-color":"#ddffdd"});
    
    // posun k dalsimu inputu pomoci enteru
    //var inputy = $('input:text');
    inputy.bind('focus',function(e){
        this.select();
    });
    
    //na prvni nastavim focus
    if(inputy[0]!=null){
        inputy[0].focus();
        inputy[0].select();
    }
    //inputy.css({'background-color':'green'});
    //alert(inputy);
    inputy.bind('keypress',function(e){
        var key = e.which;
        if(key==13){
            e.preventDefault();
            
            var nextIndex = inputy.index(this) + 1;
            //alert('this='+this+' nextIndex='+nextIndex);
            if(inputy[nextIndex]!=null){
                var nextBox = inputy[nextIndex];
                //alert(nextBox);
                nextBox.focus();
                //nextBox.select();
            }
        }
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
