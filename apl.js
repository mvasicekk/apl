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

//	var pg1Array = [];
	var margin = {left:80,top:10,right:10,bottom:10};
//	pole.forEach(
//		function(item){
//		    //console.log(item);
//		    pg1Array.push(item.celkem);
//		});
	var pg1Array = pole.map(function(d){
	    return +d.celkem;
	});
	
	var datumArray = pole.map(function(d){
	    return d.datum;
	});
	
	console.log(pg1Array);
	var maxValue = d3.max(pg1Array);
	maxValue = 85000;
	
	var zoomBehavior = d3.behavior.zoom()
		.scaleExtent([0.1,10])
		.on('zoom',onZoom);
	
	//console.log(maxValue);
	var svg = d3.select('svg');
	svg.call(zoomBehavior);
	
	function onZoom(){
	    svg.attr('transform','translate(' + d3.event.translate +
	    ')' +' scale('+d3.event.scale+')');
	};
	
	var svgWidth = parseInt(svg.style('width'));
	var svgHeight = parseInt(svg.style('height'));
	var bands = d3.scale.ordinal()
		.domain(datumArray)
		.rangeRoundBands([0,svgWidth-margin.left-margin.right],0.1);
	console.log('bands');
	console.log(bands.range());
	console.log(bands.rangeBand());
	
	var barWidthWithPadding = (svgWidth-margin.left-margin.right)/pg1Array.length;
	var barPadding = 5;
	var barWidth = barWidthWithPadding - barPadding;
	
	var totalWidth = svgWidth;
	var totalHeight = svgHeight;
	
	var yScale = d3.scale.linear()
		.domain([0,maxValue])
		.range([0,(totalHeight-margin.top-margin.bottom)]);
	
	console.log('svg width='+svgWidth);
	
	svg.append('rect').attr({
            width: totalWidth,
            height: totalHeight,
            fill: 'lightyellow',
            stroke: 'black',
            'stroke-width': 1
        });
	
	var graphGroup = svg.append('g')
		.attr('transform','translate('+margin.left+','+margin.top+')');
	
	graphGroup.append('rect').attr({
            fill: 'rgba(0,0,0,0.1)',
            width:  totalWidth - (margin.left + margin.right),
            height: totalHeight - (margin.bottom + margin.top)
        });
	
	function translator(d,i){
	    return "translate("+bands.range()[i]+","+((totalHeight-margin.top-margin.bottom)-yScale(d))+")";
	    //return "translate("+xloc(d,i)+","+0+")";
	}
	
	var barGroup = graphGroup.selectAll('g')
		.data(pg1Array)
		.enter()
		.append('g')
		.attr('transform',translator);
	
	barGroup.append('rect')
		.attr({
		    fill:'steelblue',
		    width:barWidth,
		    height:function(d){return yScale(d);}
		})
		.on('mouseenter',function(d,i){
		    d3.select(this).attr({'stroke':'red','stroke-width':'2px'});
		})
		.on('mouseout',function(d,i){
		    d3.select(this).attr({'stroke':'none','stroke-width':'0px'});
		});
	
	
	var textTranslator = "translate(" + bands.rangeBand() / 2 + ",10)";
	barGroup.append('text')
	    .text(function(d) { return Math.round(d); })
	    .attr({
		fill: 'white',
		dx:10,
		dy:0,
		'text-anchor':'start',
		transform: 'rotate(60)'
	    })
	    .style('font', '10px sans-serif')
	    .style('font-weight', 'bold');

	// osy
	var axisGroup = svg.append('g');
	
	var scale = d3.scale
		.linear()
		.domain([maxValue,0])
		.range([0,totalHeight-margin.top-margin.bottom]);
	var axis = d3.svg.axis()
		.orient('left')
		.scale(scale);
	var axisNodes = axisGroup.call(axis);
	var domain = axisNodes.selectAll('.domain');
	domain.attr({
	    fill:'none',
	    'stroke-width':1,
	    stroke:'black'
	});
	var ticks = axisNodes.selectAll('.tick line');
	ticks.attr({
	    fill:'none',
	    'stroke-width':1,
	    stroke:'black'
	});
	axisGroup.attr('transform','translate('+margin.left+','+margin.top+')');
	
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
