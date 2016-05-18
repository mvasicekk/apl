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

function renderDonutView($scope,$elements,$attrs){
    
    console.log('renderDonutView');
//    console.log($elements);
    var width = $attrs.width;
    var height = $attrs.height;
    var svg = d3.select($elements[0]).append("svg");
    var data = $scope.data;
    var vzkdData = $scope.vzkd;
    var dt = $scope.dt;
    //console.log(data);
    svg.attr(
	    {
		width:width,
		height:height
	    });

    var svgWidth = parseInt(svg.style('width'));
    var svgHeight = parseInt(svg.style('height'));

    var graph = svg.append('g')
	    .attr('transform','translate('+svgWidth/2+','+svgHeight+')');

    // sledovani zmen vzkd
    $scope.$watch("vzkd",function(){
	console.log('watch vzkd');
	graph.select('text.vzkdHodnota')
	    .text(function(d) {
		return numeral(vzkdData.vzkdAktual).format('0,0');
	    });	    

    var rest = vzkdData.vzkdPlan-vzkdData.vzkdAktual;
    if(rest<0){
	// prekrocili jsme plan
	fill = '#ff0';
	rest = 0;
    }
    else{
	fill = '#c00';
    }
    var pathVzKd = graph.selectAll('path.vzkd')
	    .data(pieVzKd([vzkdData.vzkdAktual,rest]))
//	    .transition()
//	    .delay(200)
	    .attr('fill',function(d,i){
		var c = [fill,'#eee'];
		return c[i];
	    })
	    .attr('d',arcVzKd);
    },true);
    
    // sledovani zmen v rozdeleni planu na statisticke oblasti
    $scope.$watch("data",function(){
	console.log('watch data');
	var path = graph.selectAll('path.stat')
	    .data(pie(data))
//	    .transition()
//	    .delay(1000)
	    .attr('d',arc);
    },true);
    
    //sleduju zmenu datetime
    $scope.$watch("dt",function(){
	console.log('watch dt');
	var timeFormat = d3.time.format('%d.%m.%Y %H:%M:%S');
	graph.select('text.dt')
	    .text(function(d) {
		return timeFormat($scope.dt);
	    });	    
    },true);
    
    
    //console.log(data);
    var maxPlan = d3.max(data,function(d){return d.plan});
    //console.log(maxPlan);
   var colors = d3.scale.ordinal()
    .domain([0,1,2,3,4,5])
    .range(['#FFC8FA', '#D2BEFF' , '#F5B95F','#FFFFB4','#FFAAAA','#00FF00']);
    
    console.log(colors.domain());
   //var colors = d3.scale.category10();
    var colorsVzKd = [d3.rgb('#fcc'),d3.rgb('#fff')];
    //var radius = Math.min(svgWidth, svgHeight) / 2;
    var radius = svgWidth / 2;
    var arc = d3.svg.arc()
	    .outerRadius(radius)
	    .innerRadius(3*radius/4);
    
    var arcVzKd = d3.svg.arc()
	    .outerRadius(3*radius/4-5)
	    .innerRadius(50);
    
    var pie = d3.layout.pie()
	    .startAngle(-Math.PI/2)
	    .endAngle(Math.PI/2)
	    .padAngle(0.00)
	    .value(function(d){return d.plan})
	    .sort(null);
    
    var pieVzKd = d3.layout.pie()
	    .startAngle(-Math.PI/2)
	    .endAngle(Math.PI/2)
	    .value(function(d){return d})
	    .sort(null);
    
    var path = graph.selectAll('path .stat')
	    .data(pie(data))
	    .enter()
	    .append('path')
	    .attr('class','stat')
	    .attr('d',arc)
	    .attr('fill',function(d,i){
		return d3.rgb(colors(i)).brighter(0.6);
		//return colors(i);
	    });
	    
    var pathVzKd = graph.selectAll('path .vzkd')
	    .data(pieVzKd([vzkdData.vzkdAktual,vzkdData.vzkdPlan-vzkdData.vzkdAktual]))
	    .enter()
	    .append('path')
	    .attr('class','vzkd')
	    .attr('d',arcVzKd)
	    .attr('fill',function(d,i){
		var c = ['#000','#ccc'];
		return c[i];
	    });

    graph.append('text')
	.attr({
	    class:'vzkdHodnota',
	    //fill: '#00ff00',
	    x: 0,
	    y: -(3*radius/4-5)/2,
	    "text-anchor":'middle',
	    stroke:'#fff',
	    'stroke-width':'2px'
	    })
	    .text(function(d) {
		return vzkdData.vzkdAktual;
	    });	    
	    
    graph.append('text')
	.attr({
	    class:'dt',
	    //fill: '#00ff00',
	    x: 0,
	    y: -80,
	    "text-anchor":'middle',
	    stroke:'#fff',
	    'stroke-width':'2px'
	    });
}

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
