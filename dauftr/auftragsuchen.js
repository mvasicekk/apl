// JavaScript Document

$(document).ready(function(){

    
    $('#b_infopanely1').click(function(event){
            var acturl = './infopanelAjax.php';
            var id = $(this).attr('id');

            // test jestli uz nemam tabulku otevrenou
            if($('#panelytable').length!=0){
                $('#panelytable').remove();
                return;
            }
            
            $.post(acturl,
            {
                id: id
            },
            function(data){
                updateInfoPanelClick(data);
            },
            'json'
            );
    });
    
        $('#b_infopanely2').click(function(event){
            var acturl = './infopanelAjax2.php';
            var id = $(this).attr('id');

            // test jestli uz nemam tabulku otevrenou
            if($('#panelytable2').length!=0){
                $('#panelytable2').remove();
                return;
            }
            
            $.post(acturl,
            {
                id: id
            },
            function(data){
                updateInfoPanelClick2(data);
            },
            'json'
            );
    });
    
            $('#b_infopanely3').click(function(event){
            var acturl = './infopanelAjax3.php';
            var id = $(this).attr('id');

            // test jestli uz nemam tabulku otevrenou
            if($('#panelytable3').length!=0){
                $('#panelytable3').remove();
                return;
            }
            
            $.post(acturl,
            {
                id: id
            },
            function(data){
                updateInfoPanelClick3(data);
            },
            'json'
            );
    });
    
});

function updateSaveText(data){
    
}


function updateInfoPanelClick(data){
    
//    alert('updateInfoPanelClick,data.id='+data.id);
    // zjistim si pozici tlacitka
    var buttonOffset = $('#'+data.id).offset();
    
    $(data.divcontent).appendTo('body');
    buttonOffset.top += $('#'+data.id).outerHeight();
    $('#panelytable').css({
        "left":"10px"
    });
    $('#panelytable').css({
        "top":buttonOffset.top+"px"});

    //a pridat zpracovani eventu
    var inputy = $('td input[id^=text]');
    
    //inputy.css({"background-color":"#ddffdd"});
    
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
    inputy.bind('keydown',function(e){
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

}

function updateInfoPanelClick2(data){
    
    // zjistim si pozici tlacitka
    var buttonOffset = $('#'+data.id).offset();
    
    $(data.divcontent).appendTo('body');
    buttonOffset.top += $('#'+data.id).outerHeight();
    $('#panelytable2').css({
        "left":"10px"
    });
    $('#panelytable2').css({
        "top":buttonOffset.top+"px"});

    //a pridat zpracovani eventu
    var inputy = $('td input[id^=text]');
    
    //inputy.css({"background-color":"#ddffdd"});
    
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
    inputy.bind('keydown',function(e){
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

}

function updateInfoPanelClick3(data){
    
    // zjistim si pozici tlacitka
    var buttonOffset = $('#'+data.id).offset();
    
    $(data.divcontent).appendTo('body');
    buttonOffset.top += $('#'+data.id).outerHeight();
    $('#panelytable3').css({
        "left":"10px"
    });
    $('#panelytable3').css({
        "top":buttonOffset.top+"px"});

    //a pridat zpracovani eventu
    var inputy = $('td input[id^=text]');
    
    //inputy.css({"background-color":"#ddffdd"});
    
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
    inputy.bind('keydown',function(e){
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

}

