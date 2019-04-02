
var aplApp = angular.module('fApp');
aplApp.directive("enterfocus", function () {
        return {
            restrict: "A",
            link: function ($scope, elem, attrs) {
                var focusables = $(":focusable");
                elem.bind("keydown", function (e) {
                    var code = e.keyCode || e.which;
                    if (code === 13 ) {
                        var current = focusables.index(this);
                        var next = focusables.eq(current + 1).length ? focusables.eq(current + 1) : focusables.eq(0);
			//console.log('current='+current+' next=');
			//console.log(next);
                        next.focus();
			            next.select();
                        e.preventDefault();
                    }
                });
            }
        }
});

aplApp.filter('unique', function() {
    return function(collection, keyname,isEnable) {
        var output = [],
            keys = [];
        isEnable = true;
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

aplApp.filter('custom', function() {
    return function(input, search) {
        if (!input) return input;
        if (!search) return input;
        var expected = ('' + search).toLowerCase();
        var result = {};
        angular.forEach(input, function(value, key) {
            var actual = ('' + value).toLowerCase();
            if (actual.indexOf(expected) !== -1) {
                result[key] = value;
            }
        });
        return result;
    }
});


aplApp.controller('fasApp', function ($scope,$http,$sanitize,$window) {
// funguje ?
//$scope.pokus = "Hello world";
    $scope.isEditor = false;	//urcuje zda muze uzivatel editovat helptext,
    $scope.securityInfo = undefined;
    $scope.tinyMceOptions = {
        inline: true,
        menubar: false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.showHelp = false;






// nastaveni dnesniho data
    $scope.date = new Date();
// datepicker
// datepicker
    $scope.open1 = function() {
        $scope.popup1.opened = true;
    };
    $scope.popup1 = {
        opened: false
    };
    $scope.today = function() {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.formats = ['yyyy-MM-dd', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];
    console.log($scope.format);
// security info
    $scope.initSecurity = function () {
        var p = {
            form_id: 'fasovani'
        };
        return $http.post('../getSecurityInfo.php', p).then(
            function (response) {
                $scope.securityInfo = response.data.securityInfo;
                //zkusim najit roli helptexteditor

                $scope.securityInfo.roles.forEach(function (v) {

                        if( v.role_id === '1' || v.role_id === '15'){
                            $scope.role = true;
                            console.log( v.role_id);
                        }
                    console.log(v.rolename);
                    if (v.rolename === 'helptexteditor') {
                        $scope.isEditor = true;
                        console.log('is helptexteditor');
                    }
                    if (v.rolename === 'admin') {
                        $scope.isAdmin = true;
                        console.log('is admin');
                    }
                console.log($scope.isEditor);
                });
            }
        );
    };
    $scope.initHelp = function () {
        var p = {
            form_id: 'fasovani'
        };
        return $http.post('../getHelpInfo.php', p).then(
            function (response) {
                $scope.helpText = response.data.help.helpText;
                $scope.hIArray = response.data.help.hiArray;
                console.log($scope.hIArray);
            }
        );
    };
    $scope.initSecurity();
    $scope.initHelp();

    $scope.sklady = [];$scope.sklady2 =[];
    // sklady
    $http.get('./sklady.php').then(
        function (response) {
            $scope.sklady = response.data.from;
            $scope.sklady2 = response.data.to;
        }
    );

 // ziskani itemu po vyberu skladu
    $scope.getItem = function (sklad) {
        console.log(sklad);
        $http.post('./getItems.php',{sklad:sklad}).then(
            function (response) {
                $scope.availableItems = response.data;
                console.log($scope.availableItems);
            }
        );
    
    };

    $scope.AlertMessage = false;
    $scope.addToTable = function(z,d,co,pcs,pozn,datum){
        /*
        * z = z ktereho skladu
        * d = do jakeho skladu
        * co = jaka polozka
        * pcs = pocet kusu
        */
        if(co !== '' && pcs !== ''){
        $http.post('./insertItem.php',{
            z:z,
            d:d,
            co:co,
            pcs:pcs,
            pozn:pozn,
            datum:datum
        }).then(
            function (response) {
                $scope.getActitems();
                $scope.selected = "";
                $scope.ks = "";
                $scope.poznamka = "";
                document.getElementById("pol").focus();
            }
        );
        }else{
            $scope.AlertMessage = true;
            document.getElementById("pol").focus();
            console.log($scope.AlertMessage);

        }
    };
    // schovat button pro odeslani
    $scope.showOd = false;
    $scope.getActitems = function () {
    var date = $scope.dt;
        $http.post('getActItem.php',{date:date}).then(function (response) {
                $scope.actItems = response.data;
                if($scope.actItems){
                    $scope.showOd = true;
                }
                console.log($scope.actItems);
        });
    };
    $scope.getActitems();

    // dam nedam 
    $scope.updateStatus = function (id,typ,ks) {
        console.log(id + " " + pozn);
        $http.post('updateStat.php',{id:id,typ:typ,ks:ks}).then(function (response) {
            $scope.getActitems();
         });
    };

    $scope.sendMail = function (sklad) {
        var dat = $scope.dt;

        $http.post('sendMail.php',{dat:dat,sklad:sklad}).then(function (response) {
            $scope.getActitems();
        });

    };

    // tisk

    $scope.printPage = function (datum) {
	var d="";
	d = datum.getFullYear()+'-'+ (datum.getMonth()+1) + '-' +datum.getDate();
	
	console.log("d="+d+" datum" + datum + " " + $scope.cSkladu + " ");
        $window.open("../Reports/F460_pdf.php?datum="+d+"&sklad="+ $scope.cSkladu+"", "_blank");
        
    };
    $scope.alertMe = function(index) {
        console.log(index);
        $scope.cSkladu = index;
    };


// end
});
