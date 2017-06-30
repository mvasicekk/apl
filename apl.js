$(document).ready(function(){
// kurzor do vyhledavaciho policka pro zakazku
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
    
    $('div.buttony').each(function(index){
	//zjistim pocet viditelnych inputu
	var visibleInputs = $(this).has('input:button:visible');
	var visibleInputsCount = visibleInputs.length;
	if(visibleInputsCount==0){
	    $(this).hide();
	}
    });
    
    // graf
    makeGraph(31);
});

function updateData(daysBack){
    makeGraph(daysBack);
}

/**
 * vytvoreni grafu
 * 
 * @param {int} daysBack - pocet zpetne zobrazenych v grafu
 * @returns {undefined}
 
 */
function makeGraph(daysBack){
        $.getJSON('./getGraphData.php?daysBack='+daysBack+' ', function(data) {

	//definice okraju,prostoru
	var margin = {left:80,top:10,right:20,bottom:60};
		
	var svg = d3.select('svg');
	var svgWidth = parseInt(svg.style('width'));
	var svgHeight = parseInt(svg.style('height'));
	var graphWidth = svgWidth-margin.left-margin.right;
	var graphHeight = svgHeight-margin.top-margin.bottom;
	
	// ziskani dat
	//var pole = data.leistungTablearray.pole;
	var pole = data.graphTablearray.pole;
	var datumNestedArray = d3.nest()
		.key(function(el){return d3.time.format('%d.%m.%Y').parse(el.datum)})
		.entries(pole);
	
//	datumNestedArray.sort(function(a,b){
//	    if(Date.parse(a.key)>Date.parse(b.key)){
//		return 1;
//	    }
//	    if(Date.parse(a.key)<Date.parse(b.key)){
//		return -1;
//	    }
//	    return 0;
//	});
//	console.log(datumNestedArray);
	
	yDomain = d3.extent(datumNestedArray,function(el){
	    return +el.values[0].celkem;
	});
	//console.log(yDomain);

	var datumArray = datumNestedArray.map(function(d){
	    return Date.parse(d.key);
	});
	//console.log(datumArray);
	
	maxValue = 80000;

	//var x = d3.time.scale().range([0,graphWidth]);
	//var x = d3.time.scale().range([graphWidth,0]);
	var x = d3.time.scale().range([0,graphWidth]);
	xDomain = d3.extent(datumArray, function(d) { return d; }); 
	//console.log(xDomain);
	x.domain(xDomain);
	
	var y = d3.scale.linear().range([graphHeight, 0]);
	y.domain([0,maxValue]);

	// prostor pro graf
	    svg.append('rect').attr({
            width: svgWidth,
            height: svgHeight,
            fill: '#fff',
            stroke: 'none',
            'stroke-width': 0
        });
	
	var graphGroup = svg.append('g')
		.attr('transform','translate('+margin.left+','+margin.top+')');
	
	graphGroup.append('rect').attr({
            fill: 'rgba(255,255,200,0.1)',
            width:  graphWidth,
            height: graphHeight
        });

	var trans = d3.select("body").transition();
	
	var valueline_celkem = d3.svg.line()
		.interpolate("linear")
		.x(function(d){return x(Date.parse(d.key));})
		.y(function(d){return y(+d.values[0].celkem);});
	
	var valueline_pg1 = d3.svg.line()
		.interpolate("linear")
		.x(function(d){return x(Date.parse(d.key));})
		.y(function(d){return y(+d.values[0].pg1);});
	
	var valueline_pg4 = d3.svg.line()
		.interpolate("linear")
		.x(function(d){return x(Date.parse(d.key));})
		.y(function(d){return y(+d.values[0].pg4);});
	
	
	var normy = [
	    {
		hranice:50000,
		trida:'pg1norma',
		label:'Guss'
	    },
	    {
		hranice:17000,
		trida:'pg4norma',
		label:'NE'
	    },
	    {
		hranice:67000,
		trida:'celkemnorma',
		label:'Sum'
	    },
	];
	
	graphGroup.append("path")
	    .attr("class", "line_celkem")
	    .attr("d", valueline_celkem(datumNestedArray));
    /*
	    .on("mouseover",function(d){
		graphGroup.selectAll('circle')
		    .data(datumNestedArray)
		    .enter()
		    .append('circle')
		    .attr("cx",function(d){ return x(Date.parse(d.key));})
		    .attr("cy",function(d){ return y(+d.values[0].celkem);})
		    .attr('r',8)
		    .style('fill','lightyellow')
		    .style('pointer-events','none') // nereagovat na udalosti mysi na vytvorenych krouzcich
						    // pokud mam krouzky blizko u sebe, nereaguje mi totiz celkem_line
		    .style('stroke','black');
	    })
	    .on("mouseout",function(d){
		graphGroup.selectAll('circle')
		.remove();
	    });
	    */
    
    
//	graphGroup.selectAll('circle')
//		.data(datumNestedArray)
//		.enter()
//		.append('circle')
//		.attr("cx",function(d){ return x(Date.parse(d.key));})
//		.attr("cy",function(d){ return y(+d.values[0].celkem);})
//		.attr('r',3);
//	
	graphGroup.append("path")
	    .attr("class", "line_pg1")
	    .attr("d", valueline_pg1(datumNestedArray));
    
	graphGroup.append("path")
	    .attr("class", "line_pg4")
	    .attr("d", valueline_pg4(datumNestedArray));

	normy.forEach(function(element,index){
	    //console.log(element);
	    var pgnorma = [{x:0,y:y(element.hranice)},{x:graphWidth,y:y(element.hranice)}];
	    var normapg = d3.svg.line()
		.x(function(d) { return d.x; })
		.y(function(d) { return d.y; })
		.interpolate("linear");
	
	    graphGroup.append("path")
		.attr("class", element.trida)
		.attr("d", normapg(pgnorma));
	
	    graphGroup.append("text")
	    // text label for the x axis
//	    .attr("x", graphWidth )
//	    .attr("y", y(element.hranice) )
	    .attr("class", element.trida)
	    .attr("dy", "1.2em")
	    //.attr("transform", "rotate(-90)")
	    .style("text-anchor", "middle")
	    .style("fill", "true")
	    .style("font-size", "10px")
	    .attr("transform", "translate(" + graphWidth + "," + y(element.hranice) + ") rotate(-90)")
	    .text(element.label+" "+element.hranice);

	});

//	var t = d3.select("body").transition();
//	t.select('path.line_celkem')
//		.duration(2000)
//		.attr("d", valueline_celkem(datumNestedArray));
	
	// osy
	var xAxis = d3.svg.axis().scale(x)
	    .orient("bottom")
	    .tickFormat(d3.time.format('%y-%m-%d'))
	    .tickSize(-graphHeight)
	    .ticks(12);
    
    	var xAxisOffset = graphHeight+margin.top;
	var xAxisNodes = svg.append("g")
	// Add the X Axis
	.attr("class", "x axis")
	.attr("transform", "translate("  + margin.left + "," + xAxisOffset + ")")
	.style("font-size", "10px")
	.call(xAxis)
	.selectAll("text")  // natoceni textu, aby se mi vesly nazvy mesicu
	.style("text-anchor", "end")
	.attr("dx", "-.8em")
	.attr("dy", ".15em")
	.attr("transform", "rotate(-65)");
	
	xAxisNodes.selectAll('.domain')
		.attr({
			fill:'none',
			'stroke-width':1,
			stroke:'black'
		});
	var xTicks = xAxisNodes.selectAll('.tick line');
	xTicks.attr({
	    fill:'none',
	    'stroke-width':1,
	    stroke:'grey'
	});
	
//	svg.append("text")
//	    // text label for the x axis
//	    .attr("x", margin.left + graphWidth/2 )
//	    .attr("y", graphHeight + margin.bottom )
//	    .attr("dy", "0.5em" )
//	    //.attr("transform", "rotate(-45)")
//	    .style("text-anchor", "middle")
//	    .text("Datum");


	var yAxis = d3.svg.axis().scale(y)
	    .orient("left").ticks(10);
	var yAxisOffset = margin.top;
	var yAxisNodes = svg.append("g")
	// Add the Y Axis
	.attr("class", "y axis")
	.attr("transform", "translate("  + margin.left + "," + yAxisOffset + ")")
	.call(yAxis);
	
	yAxisNodes.selectAll('.domain')
		.attr({
			fill:'none',
			'stroke-width':1,
			stroke:'black'
		});
	var yTicks = yAxisNodes.selectAll('.tick line');
	yTicks.attr({
	    fill:'none',
	    'stroke-width':1,
	    stroke:'black'
	});
	
	svg.append("text")
	    .attr("transform", "rotate(-90)")
	    .attr("y", 0)
	    .attr("x",0 - (graphHeight / 2))
	    .attr("dy", "1em")
	    .style("text-anchor", "middle")
	    .text("VzKd");
	
	
    });
}