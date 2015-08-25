/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//angular.module('aplFilters', [])
//    .filter('numeral_procenta', function() {
//	return function(input) {
//	    return numeral(input).format('0,0.0000');
//	};
//});

angular.module('aplFilters', [])
    .filter('celecislo', function() {
	return function(input) {
	    numeral.language('cz', {
	    delimiters: {
	        thousands: ' ',
	        decimal: ','
	    },
	    abbreviations: {
	        thousand: 'tis',
	        million: 'mil',
	        billion: 'b',
	        trillion: 't'
	    },
	    ordinal : function (number) {
	        return number === 1 ? 'ni' : 'ti';
	    },
	    currency: {
	        symbol: 'czk'
	    }
	});
	    numeral.language('cz');
	    return numeral(input).format('0,0');
	};
});
