$(document).ready(function(){
//    $('input:first').focus();
//    $('input:first').select();

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
	console.log(pole);
	
	var barWidth = 60;
	var barPadding = 3;
	var svgHeight = 300;
	var pg1Array = [];
	pole.forEach(
		function(item){
		    //console.log(item);
		    pg1Array.push(item.pg1);
		});
	
	var maxValue = d3.max(pg1Array);
	//console.log(maxValue);
	var graphGroup = d3.select('svg').append('g');
	
	function xloc(d,i){
	    return i*(barWidth+barPadding);
	}
	
	function yloc(d){
	    return svgHeight-d*(svgHeight/maxValue);
	}
	
	function translator(d,i){
	    return "translate("+xloc(d,i)+","+yloc(d)+")";
	    //return "translate("+xloc(d,i)+","+0+")";
	}
	
	var barGroup = graphGroup.selectAll('g')
		.data(pg1Array)
		.enter()
		.append('g')
		.attr('transform',translator);
	
	barGroup.selectAll("rect")
		.data(pg1Array)
		.enter()
		.append('rect')
		.attr({
		    fill:'steelblue',
		    width:barWidth,
		    height:function(d){return d*(svgHeight/maxValue);}
		});
	var textTranslator = "translate(" + barWidth / 2 + ",10)";
	barGroup.append('text')
	    .text(function(d) { return Math.round(d); })
	    .attr({
		fill: 'white',
		'alignment-baseline': 'before-edge',
		'text-anchor': 'middle',
		transform: textTranslator
	    })
	    .style('font', '10px sans-serif');

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
		    highlighter: {
			show: true,
			sizeAdjust: 7.5
		    },
		    cursor: {
			show: false
		    },
		    seriesColors:["#4bb2c5", "#EAA228", "#c5b47f"],
		    legend:{
			show:true,
			location: 'nw'
		    },
		    canvasOverlay:{
			show:true,
			objects:[
			    {
				dashedHorizontalLine:{
				    name: 'KemperZiel',
				    y: 17000,
				    lineWidth:2,
				    color:'#EAA228',
				    shadow:true
				}
			    },
			    {
				dashedHorizontalLine:{
				    name: 'SummeZiel',
				    y: 75000,
				    lineWidth:2,
				    color:'#c5b47f',
				    shadow:true
				}
			    },
			    {
				dashedHorizontalLine:{
				    name: 'GussZiel',
				    y: 58000,
				    lineWidth:2,
				    color:'#4bb2c5',
				    shadow:true
				}
			    }
			]
		    },
		    title:'',
		    axesDefaults:{
			labelRenderer: $.jqplot.CanvasAxisLabelRenderer
		    },
		    series:[{label:"Guss"},{label:"NE"},{label:"Sum"}],
		    seriesDefaults:{
			rendererOptions:{
			    animation: { 
				show: false
			    }
			}
		    },
		    axes:{
			xaxis:{
			    label:'Tag in akt. Monat',
			    min: 0,
			    max:31,
//			    ticks:[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
			    ticks:[0,5,10,15,20,25,31],
			    tickOptions:{
				formatString:"%d"
			    },
//			    numberTicks:31
			},
			yaxis:{
			    label:'VzKd[min]',
			    min: 0,
			    ticks:[0,5000,10000,17000,25000,30000,40000,50000,58000,70000,75000,85000],
			    tickOptions:{
				formatString:"%d"
			    },
			    max:85000
			}
		    }
		}
	);
    });
});
