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
    
    // graf
    $.getJSON('./getGraphData.php', function(data) {
	var pole = data.leistungTablearray.pole;
	var hodnoty_pg1 = [];
	var hodnoty_pg4 = [];
	var hodnoty_celkem = [];
	var ticks = [];
	for (var i = 0; i < pole.length; i++) {
	    hodnoty_pg1[i]=parseInt(pole[pole.length-i-1].pg1);
	    hodnoty_pg4[i]=parseInt(pole[pole.length-i-1].pg4);
	    hodnoty_celkem[i]=parseInt(pole[pole.length-i-1].celkem);
	    ticks[i]=i;
	}
	ticks[ticks.length]=ticks.length;
	ticks[ticks.length+1]=ticks.length+1;
	
	$.jqplot(
		'myChart', 
		[hodnoty_pg1,hodnoty_pg4,hodnoty_celkem],
		{
		    title:'VzKd - PG1,PG4,Sum',
		    axesDefaults:{
			labelRenderer: $.jqplot.CanvasAxisLabelRenderer
		    },
		    seriesDefaults:{
			rendererOptions:{
			    animation: { 
				show: true
			    }
			}
		    },
		    axes:{
			xaxis:{
			    label:'Tag in akt. Monat',
			    min: 0
//			    max: hodnoty_celkem.length+2,
//			    ticks:ticks
//			    renderer: $.jqplot.CategoryAxisRenderer
			},
			yaxis:{
			    label:'VzKd[min]',
			    min: 0
			}
		    }
		}
	);
		    
    });
    
    
    
});
