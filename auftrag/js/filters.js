/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('aplFilters', []).filter('numeral_procenta', function() {
  return function(input) {
    return numeral(input).format('0,0.0000');
  };
});
