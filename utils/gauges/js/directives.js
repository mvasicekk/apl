/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var gApp = angular.module('gaugesApp')
	.directive('barsView', function () {
	    
	    return {
		restrict: 'E',
		scope:{data:'='},
		link: renderBarsView
	    }
	});

gApp.directive('halfdonutView', function ($window) {
	    
	    return {
		restrict: 'E',
		scope:{data:'=',vzkd:'=',dt:'='},
		link: function preRenderDonutView($scope, $elements, $attrs) {
		    angular.element($window).bind('resize',function(){
		    renderDonutView($scope, $elements, $attrs);
		});
		renderDonutView($scope, $elements, $attrs);
		}
	    }
	});

//function preRenderDonutView($scope, $elements, $attrs) {
//    angular.element($window).bind('resize',function(){
//	renderDonutView($scope, $elements, $attrs);
//    });
//    renderDonutView($scope, $elements, $attrs);
//}

function renderDonutView($scope, $elements, $attrs) {

    var width = $attrs.width;
    var height = $attrs.height;
    
    var data = $scope.data;
    var vzkdData = $scope.vzkd;
    var dt = $scope.dt;

    // pokud uz jsem nejaky svg mel, tak ho smazu
    var svg = d3.select('svg').remove();
    var svg = d3.select($elements[0]).append("svg");
    //console.log(data);
    // nastavit sirku svg elementu
    svg.attr(
	    {
		width: width,
		height: height
	    });

    // zjistim sirku vysku svg elementu v pixelech
    var svgWidth = parseInt(svg.style('width'));
    
    height = svgWidth/2;
    
    svg.attr(
	    {
		width: width,
		height: height
	    });

    var svgHeight = parseInt(svg.style('height'));

    //1/5 vysky si zaberu pro legendu
    var legendHeight = (1/5)*svgHeight;
    var radiusMax = Math.min(svgWidth/2, svgHeight-legendHeight);
    
    var legendMargin = (svgWidth-2*radiusMax)/2;
    var legendWidth = 2*radiusMax;
    

    var colors = d3.scale.ordinal()
	    .domain([0, 1, 2, 3, 4, 5])
	    .range(['#FFC8FA', '#D2BEFF', '#F5B95F', '#FFFFB4', '#FFAAAA', '#00FF00']);

    var colorsVzKd = [d3.rgb('#fcc'), d3.rgb('#fff')];

    
    

    //var radiusMax = svgWidth / 2;
    var donutWidthProzent = 10;
    var donutInnerRadius = radiusMax-donutWidthProzent/100*radiusMax;
    var donutVzkdPadding = 20;
    
    // obdelniky pro jednotlive statnr
    console.log('data');
    console.log(data);
    var sirkaStatnrRect = legendWidth/data.length;
    
    posunLegendy = svgHeight-legendHeight;
    var legenda = svg.append('g')
	    .attr('transform', 'translate(' + 0 + ',' + posunLegendy + ')');
    
    legenda.selectAll('rect.statLegendBox')
	    .data(data)
	    .enter()
	    .append('rect')
	    .attr('class','statLegendBox')
	    .attr('x',function(d,i){ return legendMargin+i*sirkaStatnrRect})
	    .attr('y',0)
	    .attr('width',sirkaStatnrRect)
	    .attr('height',legendHeight)
	    .attr('fill',function(d,i){ return colors(i)})
	    .attr('stroke','#F00')
	    .attr('stroke-width','1')
	    .attr('opacity','1');
    legenda.selectAll('text.statLegendText')
    	    .data(data)
	    .enter()
	    .append('text')
	    .attr('class','statLegendText')
    	    .attr('x',function(d,i){ return legendMargin+i*sirkaStatnrRect+sirkaStatnrRect/2})
	    .attr('y',legendHeight/4)
	    .attr('dy',0)
    	    .attr('text-anchor', 'middle')
	    .attr('fill','#000')
	    .text(function(d){
		var cas = d.statnr;
		return cas;
	    });
	    var fontSize = sirkaStatnrRect / 8;
    legenda.selectAll('text.statVzkdText')
    	    .data(data)
	    .enter()
	    .append('text')
	    .attr('class','statVzkdText')
    	    .attr('x',function(d,i){ return legendMargin+i*sirkaStatnrRect+sirkaStatnrRect/2})
	    .attr('y',legendHeight/2)
	    .attr('dy','1em')
    	    .attr('text-anchor', 'middle')
	    .attr('font-size', fontSize)
	    .attr('fill','#000');    	    


    
    var graph = svg.append('g')
	    .attr('transform', 'translate(' + svgWidth / 2 + ',' + posunLegendy + ')');

    
    // ramecek s legendou
    
    // oblouk pro donut pro plan jednotlivych statnr
    var arc = d3.svg.arc()
	    .outerRadius(radiusMax)
	    .innerRadius(donutInnerRadius);

    // oblouk pro donut pro skutecne vzkd jednotlivych statnr
    var inPadding = 3;
    var arcStatAktual = d3.svg.arc()
	    .outerRadius(radiusMax - inPadding)
	    .innerRadius(donutInnerRadius + inPadding);

    // oblouk pro skutecne vzkd v sume
    var arcVzKd = d3.svg.arc()
	    .outerRadius(donutInnerRadius - donutVzkdPadding)
	    .innerRadius(50);

    // useky pro planovane vzkd pro jednotlive statnr
    var pie = d3.layout.pie()
	    .startAngle(-Math.PI / 2)
	    .endAngle(Math.PI / 2)
	    .padAngle(0.00)
	    .value(function (d) {
		return d.plan
	    })
	    .sort(null);

    // useky pro sumu skutecnych vzkd
    var pieVzKd = d3.layout.pie()
	    .startAngle(-Math.PI / 2)
	    .endAngle(Math.PI / 2)
	    .value(function (d) {
		return d
	    })
	    .sort(null);


    var path = graph.selectAll('path .stat')
	    .data(pie(data))
	    .enter()
	    .append('path')
	    .attr('class', 'stat')
	    .attr('d', arc)
	    .attr('stroke', '#ccc')
	    .attr('stroke-width', '3')
	    .attr('fill', function (d, i) {
		return '#EEEEEE';
		//return d3.rgb(colors(i)).brighter(0.65);
	    });

    dataStatAktual = pie(data).map(function (v) {
	var scale = d3.scale.linear().domain([0, v.value]).range([v.startAngle, v.endAngle]);
	return {
	    startAngle: v.startAngle,
	    endAngle: v.startAngle,
	    value: 0,
	    padAngle: 0, //v.padAngle,
	    data: v.data
	};
    });
    // upravit koncovy uhel podle aktualnich hodnot vzkd
    var pathStat = graph.selectAll('path .stataktual')
	    .data(dataStatAktual)
	    .enter()
	    .append('path')
	    .attr('class', 'stataktual')
	    .attr('d', arcStatAktual)
	    .attr('fill', function (d, i) {
		return colors(i);
	    });

// podklad pro budik s aktualnim vzkd, planovanou hodnotu rozdelim na 16 casti ( rozplanovat na 16 hodin ) a zobrazim	    
//    var vzkd16hoursData = d3.range(16).map(function(v,i){
//	return {plan:1,hodina:i+1}
//    });
    var vzkd16hoursData = [
	{
	    plan:0,
	    hodina:'05:30'
	},
	{
	    plan:10-5.5,
	    hodina:'10:00'
	},
	{
	    plan:14-10,
	    hodina:'14:00'
	},
	{
	    plan:18-14,
	    hodina:'18:00'
	},
	{
	    plan:22.5-18,
	    hodina:'22:30'
	}
    ];
    
    var vzkd16hoursPieData = pie(vzkd16hoursData);
    console.log(vzkd16hoursPieData);
    //console.log(vzkd16hoursData);
    var pathVzKd16Hours = graph.selectAll('path .vzkd16hour')
	    .data(vzkd16hoursPieData)
	    .enter()
	    .append('path')
	    .attr('class', 'vzkd16hour')
	    .attr('d', arcVzKd)
	    .attr('stroke','#faa')
	    .attr('stroke-width','2')
	    .attr('fill','none');
//	    .attr('fill', function (d, i) {
//		var c = ['#ddd', '#eee'];
//		return c[1];
//	    });

    graph.append('line')
	    .attr('class','minuteLine')
	    .attr('stroke','#F00')
	    .attr('stroke-width','10')
	    .attr('opacity','0.8');
    
    graph.append('line')
	    .attr('class','secondLine')
	    .attr('stroke','#FC0')
	    .attr('stroke-width','20')
	    .attr('opacity','0.8');
    
    var pathVzKd16HoursLabels = graph.selectAll('text.vzkd16hour')
	    .data(vzkd16hoursPieData)
	    .enter()
	    .append('text')
	    .attr('x',function(d){
		return (donutInnerRadius - 50)*Math.sin(d.endAngle);
	    })
	    .attr('y',function(d){
		return -(donutInnerRadius - 50)*Math.cos(d.endAngle);
	    })
	    .attr('text-anchor', 'middle')
	    .attr('font-size',(radiusMax) / 15)
	    .attr('class', 'vzkd16hour')
	    .attr('fill','#000')
	    .text(function(d){
		var cas = d.data.hodina;
		return cas;
	    });
    
    var pieVzkdData = pieVzKd([vzkdData.vzkdAktual, vzkdData.vzkdPlan - vzkdData.vzkdAktual]);
    console.log('create pieVzkdData');
    console.log(pieVzkdData);
    var pathVzKd = graph.selectAll('path .vzkd')
	    .data(pieVzkdData)
	    .enter()
	    .append('path')
	    .attr('class', 'vzkd')
	    .attr('d', arcVzKd)
	    .attr('fill', 'none');
//	    .attr('fill', function (d, i) {
//		var c = ['#000', '#ccc'];
//		return c[i];
//	    });


    
    graph.append('text')
	    .attr({
		class: 'vzkdHodnota',
		x: 0,
		y: -(radiusMax / 2),
		"font-size":(2*radiusMax) / 6,
		dy:'0.5em',
		"text-anchor": 'middle',
		stroke: '#fff',
		
		'stroke-width': '2px'
		//size:70,
	    })
	    .text(function (d) {
		return vzkdData.vzkdAktual;
	    });
	    
    graph.append('text')
	    .attr({
		class: 'vzkdPlan',
		x: 0,
		y: -(radiusMax / 2),
		"font-size":(2*radiusMax) / 10,
		dy:'2em',
		"text-anchor": 'middle',
		stroke: '#fff',
		'stroke-width': '2px'
	    })
	    .text(function (d) {
		return numeral(vzkdData.vzkdPlan).format('0,0');
	    });

    graph.append('text')
	    .attr({
		class: 'dt',
		//fill: '#00ff00',
		x: 0,
		y: -(radiusMax / 2),
		dy:'3em',
		"text-anchor": 'middle',
		stroke: '#fff',
		'stroke-width': '2px'
	    });


    
    
    // updatovani pri zmenach dat ----------------------------------------------
    // sledovani zmen vzkd
    $scope.$watch("vzkd", function () {

	//console.log('watch vzkd');
	//console.log(vzkdData);
	
	svg.selectAll('text.statVzkdText')
    	    .data(data)
	    .text(function(d){
		var cas = parseFloat(d.aktual);
		var plan = parseFloat(d.plan);
		var procent = plan!=0?numeral(cas/plan*100).format('0'):'';
		return numeral(cas).format('0,0') + ' (' + procent + '%)';
	    });    	    
	
	graph.select('text.vzkdPlan')
	    .text(function (d) {
		return 'Plan: '+numeral(vzkdData.vzkdPlan).format('0,0');
	    });
	    
	graph.select('text.vzkdHodnota')
		.text(function (d) {
		    return numeral(vzkdData.vzkdAktual).format('0,0');
		});

	var rest = vzkdData.vzkdPlan - vzkdData.vzkdAktual;
	if (rest < 0) {
	    // prekrocili jsme plan
	    fill = '#ff0';
	    rest = 0;
	}
	else {
	    fill = '#c00';
	}
	var vzkdArcDataAll = pieVzKd([vzkdData.vzkdAktual, rest]);
	var vzkdArcDataVzkdAktual = [vzkdArcDataAll[0]];
	
	graph.selectAll('path.vzkd')
		.data(vzkdArcDataVzkdAktual)
		.attr('d', arcVzKd)
		.attr('opacity',0.2)
		.attr('fill', function (d, i) {
		    var c = [fill, '#eee'];
		    return c[i];
		});

	dataStatAktual = pie(data).map(function (v) {
	    var scale = d3.scale.linear().domain([0, v.value]).range([v.startAngle, v.endAngle]);
	    var vzkdA = parseFloat(vzkdData.vzkdStat[v.data.statnr]);
	    return {
		startAngle: v.startAngle,
		endAngle: scale(vzkdA),
		value: vzkdA,
		padAngle: 0, //v.padAngle,
		data: v.data
	    };
	});
	// upravit koncovy uhel podle aktualnich hodnot vzkd
	var pathStat = graph.selectAll('path.stataktual')
		.data(dataStatAktual)
		.attr('d', arcStatAktual)
		.attr('fill', function (d, i) {
		    return colors(i);
		});
    }, true);

    // sledovani zmen v rozdeleni planu na statisticke oblasti
    $scope.$watch("data", function () {
	var path = graph.selectAll('path.stat')
		.data(pie(data))
		.attr('d', arc);
    }, true);

    //sleduju zmenu datetime
    $scope.$watch("dt", function () {
	startTimeMinutes = 5.5 * 60;
	endTimeMinutes = 22.5 * 60;
	minutesScale = d3.scale.linear().domain([startTimeMinutes, endTimeMinutes]).range([-Math.PI/2, Math.PI/2]);
	secondScale = d3.scale.linear().domain([0, 60]).range([-Math.PI/2, Math.PI/2]);
	
	timeAktualMinutes = $scope.dt.getHours()*60+$scope.dt.getMinutes()+$scope.dt.getSeconds()/60;
	timeAktualSeconds = $scope.dt.getSeconds();
	
	uhelAktual = minutesScale(timeAktualMinutes);
	uhelAktualSecond = secondScale(timeAktualSeconds);
	
	x1 = (50)*Math.sin(uhelAktual);
	y1 = -(50)*Math.cos(uhelAktual);
	x2 = (donutInnerRadius - donutVzkdPadding)*Math.sin(uhelAktual);
	y2 = -(donutInnerRadius - donutVzkdPadding)*Math.cos(uhelAktual);
	
	sx1 = (donutInnerRadius - donutVzkdPadding)*Math.sin(uhelAktualSecond);
	sy1 = -(donutInnerRadius - donutVzkdPadding)*Math.cos(uhelAktualSecond);
	sx2 = (donutInnerRadius)*Math.sin(uhelAktualSecond);
	sy2 = -(donutInnerRadius)*Math.cos(uhelAktualSecond);
	
	
	minuteLine = graph.select('line.minuteLine')
	    .attr('x1',x1)
	    .attr('y1',y1)
	    .attr('x2',x2)
	    .attr('y2',y2);
    
	secondLine = graph.select('line.secondLine')
//		.transition()
//		.delay(200)
		.attr('x1',sx1)
		.attr('y1',sy1)
		.attr('x2',sx2)
		.attr('y2',sy2);
	    
//	var timeFormat = d3.time.format('%d.%m.%Y');
//	graph.select('text.dt')
//		.text(function (d) {
//		    return timeFormat($scope.dt);
//		});
    }, true);
}




// -----------------------------------------------------------------------------
/**
 * 
 * @param {type} $scope
 * @param {type} $elements
 * @param {type} $attrs
 * @returns {undefined}
 */
function renderBarsView($scope,$elements,$attrs){
//    console.log('renderBarsView');
//    console.log($elements);
    var width = $attrs.width;
    var height = $attrs.height;
    var svg = d3.select($elements[0]).append("svg");
    var data = $scope.data;
    //console.log(data);
    svg.attr(
	    {
		width:width,
		height:height
	    });

    var svgWidth = parseInt(svg.style('width'));
    var svgHeight = parseInt(svg.style('height'));
	
    //console.log(data);
    var maxPlan = d3.max(data,function(d){return d.plan});
    //console.log(maxPlan);
    var colors = d3.scale.category10();
    var barHeight = svgHeight/data.length;
    var leftMargin = 15;
    var barTextOffsetY = barHeight/2;
    
    //vytvoreni 
    svg.selectAll('rect')
	    .data(data)
	    .enter()
	    .append('rect')
	    .attr({
		height:barHeight,
		width:0,
		x:0,
		y:function(d,i){
		    return i*barHeight;
		},
		stroke:'white'
	    })
	    .style('fill',function(d,i){
		return colors(i);
	    })
//	    .transition()
//	    .duration(1000)
	    .attr('width',function(d,i){
		return d.plan / (maxPlan/svgWidth);
	    });
	    
    //update
    svg.selectAll('rect')
	    .data(data)
//	    .transition()
//	    .duration(1000)
	    .attr('width',function(d,i){
		return d.plan / (maxPlan/svgWidth);
	    });
	    

//vytvoreni
    svg.selectAll('text')
	.data(data)
	.enter()
	.append('text')
	.attr({
	    fill: '#fff',
	    x: leftMargin,
	    y: function(d, i) {
		    return i * barHeight + barTextOffsetY;
		}
	    })
	    .text(function(d) {
		return d.statnr + "\nPlan = (" + d.plan + ')';
	    });
	    
	    //update
    svg.selectAll('text')
	.data(data)
	.attr({
	    fill: '#fff',
	    x: leftMargin,
	    y: function(d, i) {
		    return i * barHeight + barTextOffsetY;
		}
	    })
	    .text(function(d) {
		return d.statnr + "\nPlan = (" + d.plan + ')';
	    });

}
