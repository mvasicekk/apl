/**
 * Created by Miroslav on 05.04.2017.
 */
var aplApp = angular.module('statApp');
aplApp.filter('unique', function() {
   return function(collection, keyname,isEnable) {
      var output = [],
          keys = [];
if (isEnable) {
      angular.forEach(collection, function(item) {
          var key = item[keyname];
          //console.log(keys.indexOf(key));
          if(keys.indexOf(key) === -1) {
              keys.push(key);
              output.push(item);
          }
      });
      return output;
    }
    return collection;
   };
});
aplApp.directive('datepicker', function() {
  return {
    link: function(scope, el, attr) {
      $(el).datepicker({
        onSelect: function(dateText) {
          console.log(dateText);
          var expression = attr.ngModel + " = " + "'" + dateText + "'";
          scope.$apply(expression);
          console.log(scope.startDate);
          // how do i set this elements model property ?
        }
      });
    }
  };
});


aplApp.controller('st', function ($scope,$http,$filter, $window, $q,$route) {
  $scope.isEnable = true;   // filter
  $scope.vyrazene = false;  // zobrazit i vyrazene
  $scope.select = 1;        // vyber palet
  $scope.pop = false;       // zobrazit input pro pridani statoru
// rozdeleni podle operaci + seznam + vyrazene
$scope.operationen = [
  {
    id: 1,
    name: "Seznam"
  },
  {
    id: 2,
    name: "8710 - Bourání"
  },
  {
    id: 3,
    name: "8720 - Navijení"
  },
  {
    id: 4,
    name: "8730 - Namáčení"
  },
  {
    id: 5,
    name: "8740 - Vypalování"
  },
  {
    id: 6,
    name: "Vyřazené"
  }
];
// takova super vec na rozdeleni podle typu statoru :OOO
$scope.data = {
  model: null,
  availableOptions: [
    {id: '1', name: 'PM 56x37'},
    {id: '2', name: 'UM 57x45'},
    {id: '3', name: 'R 72x70'},
    {id: '4', name: 'US 81x80'},
    {id: '5', name: 'PS 66x65'},
    {id: '6', name: 'UV 3kW'}
  ]
 };
// neco to ukaze
$scope.show = function(a){
  $scope.showData = a;
}
// filter Data, kdo hleda najde :))
$scope.filterData = function(invnummer){
    return function(item) {
        return (invnummer == undefined || invnummer.length == 0 || item.et_invnummer.indexOf(invnummer) !== -1 )
    }
}
// seznam vsech statoru (vsech co zname)
function showSeznam(pal){
  return $http.post('./seznam.php',{pal: pal}).then(function(response){
    $scope.statSeznam = response.data.res;
  });
}
// daj sem data o statorech  jaja
$scope.getData = function(typ,odd,ddo,vyrazen){
  //console.log(vyrazen);
return $http.post('./statory.php',{typ: typ,odd:odd,ddo:ddo,vyrazen:vyrazen}).then(function(response){
    $scope.statData = response.data.res;
    showSeznam(typ);
});
}
// vyradit data of course prazdne kdy stator neni vyrazen :)
  $scope.datVyradit = "";
// informace o statoru, a dalsi kraviny
$scope.getInfo = function(s){
  $scope.showDiv = false;
$http.post('./stat_info.php',{stator:s}).then(function(response){
    $scope.drueckIn = response.data.drueckInfo;    // informace z drueck, drueckplus, dstator, dstator_pal
    $scope.reparInf = response.data.reparaturInfo; // informace z dreparaturpos, dreparaturkopf
    $scope.infoStat = response.data.stator;        // cislo statoru ktere se posila pro sql dotaz (where)
    $scope.prevStat = response.data.prevInfo;
// totok se ukaze kdyz super data nejsou prazdne
if( $scope.drueckIn !== null ){
    $scope.drDatum = $scope.drueckIn[0].datum;
    $scope.lastTat = $scope.drueckIn[0].TaetNr;
    $scope.drVyr = $scope.drueckIn[0].vyrazen;
    $scope.bemer = $scope.drueckIn[0].bemer;
    $scope.warN = $scope.drueckIn[0].warN;
    $scope.dang = $scope.drueckIn[0].dang;
    $scope.datVyradit = $scope.drueckIn[0].vyrazen;
}else{
    $scope.drDatum = '1970-01-01';
    $scope.drVyr = "0000-00-00";
    $scope.bemer = "";
}
// pokud je stator previnut vice jak 6 krat tak ma priznak VAROVANI :OOO
if($scope.warN == 1){
  $scope.warning = true;
}else{
  $scope.warning = false;
}
// pokud stator byl varezn ci je v tom jina vada oznacim ho jako DANGER
if($scope.dang == 1){
  $scope.danger = true;
}else{
  $scope.danger = false;
}
// vypocitani dnu opreav, zda je stator v masine ci jakou ma aktualni operaci
if($scope.drVyr == "0000-00-00" && $scope.reparInf !== null && $scope.drDatum <= $scope.reparInf[0].repStamp ){
      $scope.actRep = $scope.reparInf[0].repStamp;
      $scope.in = "days";
      $scope.actDate = new Date(), 'yyyy-MM-dd';
      $scope.repDate = new Date($scope.reparInf[0].repStamp), 'yyyy-MM-dd';
      $scope.act =(($scope.actDate-$scope.repDate) / 1000 / 60 / 60 / 24).toFixed();
      $scope.invnum = $scope.reparInf[0].inv;
      $scope.vyrazen = "0000-00-00";
  }else if( $scope.reparInf !== null && $scope.drueckIn[0].vyrazen !== "0000-00-00"){
        $scope.in = "oper";
        $scope.vyrazen = $scope.drueckIn[0].vyrazen;
        //console.log($scope.vyrazen);
    }else if($scope.drueckIn !== null && $scope.drueckIn[0].vyrazen == "0000-00-00"){
          $scope.in = "oper";
          $scope.act = $scope.drueckIn[0].TaetNr;
          $scope.vyrazen = $scope.drVyr;
      }else{
            $scope.in = "";
            $scope.act = "";
            $scope.vyrazen = $scope.drVyr;
            $scope.actRep = "";
}

  $scope.reparData = [];
if($scope.reparInf !== null && $scope.drueckIn !== null){
  if($scope.in == "oper"){
 var i = 0;
 }else{
   var i = 1;
 }
  for (var o = 0; o < $scope.drueckIn.length; o++) {
    if($scope.drueckIn[o].TaetNr == "8710" && $scope.drueckIn[o].datum >= $scope.reparInf[i].repStamp ){
      $scope.actD = new Date($scope.drueckIn[o].datum), 'yyyy-MM-dd';
      $scope.repD = new Date($scope.reparInf[i].repStamp), 'yyyy-MM-dd';
      $scope.dat =$scope.reparData.push({"od":$scope.reparInf[i].repStamp,"do": $scope.drueckIn[o].datum,"dny": (($scope.actD-$scope.repD)/ 1000 / 60 / 60 / 24).toFixed(),"inv":$scope.reparInf[i].inv});
      //console.log($scope.reparInf[i].repStamp+"  " + $scope.drueckIn[o].datum);
      i += 1;
    }
}
}
console.log($scope.reparInf);
    });
//    console.log($scope.in);
}
// update stator
// update informaci o statoru
$scope.updateBem = function(bb,stat,pal,warning,danger,vyradit){
  //console.log(warning);
  $http.post('./updateStat',{bb:bb,stat:stat,warning:warning,danger:danger,vyradit:vyradit}).then(function(response){
    console.log("Update v DB");
    showSeznam(pal)
  });
}
// pridani noveho statoru do už tak dlouheho seznamu, ze se z toho clovek jednou zblazni
$scope.addStat = function(event,newStat,pall){
  if(event.keyCode == 13){
    $http.post('./insertStat.php',{newStat:newStat,pall:pall}).then(function(response){
     console.log(event +" " + newStat + " "+pall);
      showSeznam(pall)
      $scope.newStat = "";
      $scope.pop = false;
    });
  }
}





  // ========================================================================
  // end
});
