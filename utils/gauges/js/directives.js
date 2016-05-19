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

gApp.directive('halfdonutView', function () {
	    
	    return {
		restrict: 'E',
		scope:{data:'=',vzkd:'=',dt:'='},
		link: renderDonutView
	    }
	});

function renderDonutView($scope, $elements, $attrs) {

    var width = $attrs.width;
    var height = $attrs.height;
    var svg = d3.select($elements[0]).append("svg");
    var data = $scope.data;
    var vzkdData = $scope.vzkd;
    var dt = $scope.dt;

    // nastavit sirku svg elementu
    svg.attr(
	    {
		width: width,
		height: height
	    });

    // zjistim sirku vysku svg elementu v pixelech
    var svgWidth = parseInt(svg.style('width'));
    var svgHeight = parseInt(svg.style('height'));


    var graph = svg.append('g')
	    .attr('transform', 'translate(' + svgWidth / 2 + ',' + svgHeight + ')');

    var colors = d3.scale.ordinal()
	    .domain([0, 1, 2, 3, 4, 5])
	    .range(['#FFC8FA', '#D2BEFF', '#F5B95F', '#FFFFB4', '#FFAAAA', '#00FF00']);

    var colorsVzKd = [d3.rgb('#fcc'), d3.rgb('#fff')];

    //var radius = Math.min(svgWidth, svgHeight) / 2;

    var radius = svgWidth / 2;

    // oblouk pro donut pro plan jednotlivych statnr
    var arc = d3.svg.arc()
	    .outerRadius(radius)
	    .innerRadius(3 * radius / 4);

    // oblouk pro donut pro skutecne vzkd jednotlivych statnr
    var inPadding = 20;
    var arcStatAktual = d3.svg.arc()
	    .outerRadius(radius - inPadding)
	    .innerRadius(3 * radius / 4 + inPadding);

    // oblouk pro skutecne vzkd v sume
    var arcVzKd = d3.svg.arc()
	    .outerRadius(3 * radius / 4 - 10)
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

    var pathVzKd = graph.selectAll('path .vzkd')
	    .data(pieVzKd([vzkdData.vzkdAktual, vzkdData.vzkdPlan - vzkdData.vzkdAktual]))
	    .enter()
	    .append('path')
	    .attr('class', 'vzkd')
	    .attr('d', arcVzKd)
	    .attr('fill', function (d, i) {
		var c = ['#000', '#ccc'];
		return c[i];
	    });

    graph.append('text')
	    .attr({
		class: 'vzkdHodnota',
		x: 0,
		y: -(3 * radius / 4 - 5) / 2,
		"text-anchor": 'middle',
		stroke: '#fff',
		'stroke-width': '2px'
	    })
	    .text(function (d) {
		return vzkdData.vzkdAktual;
	    });
	    
    graph.append('text')
	    .attr({
		class: 'vzkdPlan',
		x: 0,
		y: -(3 * radius / 4 - 5) / 3,
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
		y: -80,
		"text-anchor": 'middle',
		stroke: '#fff',
		'stroke-width': '2px'
	    });


    // updatovani pri zmenach dat ----------------------------------------------
    // sledovani zmen vzkd
    $scope.$watch("vzkd", function () {

	graph.select('text.vzkdPlan')
	    .text(function (d) {
		//var procenta = parseFloat(vzkdData.vzkdPlan)!==0:numeral(parseFloat(vzkdData.vzkdAktual)/parseFloat(vzkdData.vzkdPlan)*100).format('0.00'):'';
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
	graph.selectAll('path.vzkd')
		.data(pieVzKd([vzkdData.vzkdAktual, rest]))
		.attr('fill', function (d, i) {
		    var c = [fill, '#eee'];
		    return c[i];
		})
		.attr('d', arcVzKd);

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
	var timeFormat = d3.time.format('%d.%m.%Y %H:%M:%S');
	graph.select('text.dt')
		.text(function (d) {
		    return timeFormat($scope.dt);
		});
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
