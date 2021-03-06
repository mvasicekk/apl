/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('reklApp');

aplApp.controller('reklController', function ($scope, $http) {
    $http.get('./getReklamationen.php').success(function (data) {
	$scope.reklamationen = data.reklamationen;
	$scope.dReklamationen = [].concat($scope.reklamationen);
    });
});

aplApp.controller('detailController', ['$scope', '$routeParams', '$http',
    function ($scope, $routeParams, $http) {
	
	$scope.beenden = 0;
	
	var initSecurity = function(){
	var p={
	    form_id:'reklamation'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
	}

/**
 * 
 * @returns {unresolved}
 */
$scope.summeKostenEUR = function(){
    if($scope.rekl!==undefined){
	return parseFloat($scope.rekl.anerkannt_ausschuss_preis_eur)
	    +parseFloat($scope.rekl.anerkannt_ausschuss_selbst_preis_eur)
	    +parseFloat($scope.rekl.anerkannt_nacharbeit_preis_eur)
	    +parseFloat($scope.rekl.dif_falsch_deklariert_preis_eur)
	    +parseFloat($scope.rekl.verpackung_preis_eur)
	    +parseFloat($scope.rekl.kreislauf_preis_eur)
	    +parseFloat($scope.rekl.pauschale_preis_eur);
    }
    else{
	return 0;
    }
    
}

/**
 * 
 * @returns {Number}
 */
$scope.summeForecastEUR = function(){
    if($scope.rekl!==undefined){
	return parseFloat($scope.rekl.forecast_anerkannt_ausschuss_eur)
	    +parseFloat($scope.rekl.forecast_anerkannt_ausschuss_selbst_eur)
	    +parseFloat($scope.rekl.forecast_anerkannt_nacharbeit_eur)
	    +parseFloat($scope.rekl.forecast_dif_falsch_deklariert_eur)
	    +parseFloat($scope.rekl.forecast_verpackung_eur)
	    +parseFloat($scope.rekl.forecast_kreislauf_eur)
	    +parseFloat($scope.rekl.forecast_pauschale_eur);
    }
    else{
	return 0;
    }
    
}

/**
 * 
 * @returns {Number}
 */
$scope.summeForecastCZK = function(){
    if($scope.rekl!==undefined){
	return parseFloat($scope.rekl.forecast_anerkannt_ausschuss_czk)
	    +parseFloat($scope.rekl.forecast_anerkannt_ausschuss_selbst_czk)
	    +parseFloat($scope.rekl.forecast_anerkannt_nacharbeit_czk)
	    +parseFloat($scope.rekl.forecast_dif_falsch_deklariert_czk)
	    +parseFloat($scope.rekl.forecast_verpackung_czk)
	    +parseFloat($scope.rekl.forecast_kreislauf_czk)
	    +parseFloat($scope.rekl.forecast_pauschale_czk);
    }
    else{
	return 0;
    }
    
}

/**
 * 
 * @returns {unresolved}
 */
$scope.summeKostenCZK = function(){
    if($scope.rekl!==undefined){
	return parseFloat($scope.rekl.anerkannt_ausschuss_preis_czk)
	    +parseFloat($scope.rekl.anerkannt_ausschuss_selbst_preis_czk)
	    +parseFloat($scope.rekl.anerkannt_nacharbeit_preis_czk)
	    +parseFloat($scope.rekl.dif_falsch_deklariert_preis_czk)
	    +parseFloat($scope.rekl.verpackung_preis_czk)
	    +parseFloat($scope.rekl.kreislauf_preis_czk)
	    +parseFloat($scope.rekl.pauschale_preis_czk);
    }
    else{
	return 0;
    }
}
/**
 * 
 * @returns {undefined}
 */
	$scope.ausschussKostenVorschlag = function(){
	    var p={};
	    p.teil = $scope.rekl.teil;
	    p.aussStk = $scope.rekl.anerkannt_stk_ausschuss;
	    return $http.post('./getAussKostenVorschlag.php',p).then(
		    function(response){
			console.log(response.data);
			$scope.preisVorschlag_Ausschuss = response.data.preisVorschlag;
			$scope.preisVorschlag_Ausschuss_Vom = response.data.vom;
			//2017-01-30 misto kosten ukladam navrh do forecast
			//$scope.rekl.anerkannt_ausschuss_preis_eur = response.data.preisVorschlag;
			$scope.rekl.forecast_anerkannt_ausschuss_eur = response.data.preisVorschlag;
			$scope.recalcKurs('EURtoCZK','forecast_anerkannt_ausschuss_eur','forecast_anerkannt_ausschuss_czk');
		    }
		);
	}
	
	/**
	 * 
	 * @returns {unresolved}
	 */
	$scope.ausschussSelbstKostenVorschlag = function(){
	    var p={};
	    p.teil = $scope.rekl.teil;
	    p.aussStk = $scope.rekl.anerkannt_stk_ausschuss_selbst;
	    return $http.post('./getAussKostenVorschlag.php',p).then(
		    function(response){
			console.log(response.data);
			//$scope.preisVorschlag_Ausschuss = response.data.preisVorschlag;
			$scope.preisVorschlag_AusschussSelbst_Vom = response.data.vom;
			//2017-01-30 misto kosten ukladam navrh do forecast
			//$scope.rekl.anerkannt_ausschuss_preis_eur = response.data.preisVorschlag;
			$scope.rekl.forecast_anerkannt_ausschuss_selbst_eur = response.data.preisVorschlag;
			$scope.recalcKurs('EURtoCZK','forecast_anerkannt_ausschuss_selbst_eur','forecast_anerkannt_ausschuss_selbst_czk');
		    }
		);
	}
	
	
	/**
	 * 
	 * @param {type} fromto
	 * @param {type} propertyEUR
	 * @param {type} propertyCZK
	 * @returns {undefined}
	 */
	$scope.recalcKurs = function(fromto,propertyEUR,propertyCZK){
	    console.log('recalcKurz:'+fromto+"propEUR="+propertyEUR+"propCZK="+propertyCZK);
	    if(fromto=='EURtoCZK'){
		var v = $scope.rekl[propertyEUR].toString();
		v = numeral().unformat(v.replace(',','.'));
		$scope.rekl[propertyCZK] = $scope.rekl.kurs_EUR_CZK * v;
		$scope.rekl[propertyEUR] = numeral(v).format('0.0000');
	    }
	    else{
		var v = $scope.rekl[propertyCZK].toString();
		v = numeral().unformat(v.replace(',','.'));
		$scope.rekl[propertyEUR] = $scope.rekl.kurs_EUR_CZK!=0?v/$scope.rekl.kurs_EUR_CZK:0;
		$scope.rekl[propertyCZK] = numeral(v).format('0.0000');
	    }
	}
	
	
	var initDetail = function (save) {
	    $scope.rekl = undefined;
	    $scope.disabled = undefined;

	    $scope.abmahnungVorschlagUser = undefined;
	    $scope.abmahnungBemerkung = "";
	    $scope.abmahnungVorschlagBetrag = 0;
	    $scope.abmahnungDatum = new Date();
	    

	    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
	    };

	    $scope.abmahnungPersnr = {};
	    $scope.schulungPersnr = {};
	    $scope.kunde = {};
	    $scope.kunde.selected = {};
	    $scope.teil = {};
	    $scope.teil.selected = {};

	    $http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
		$scope.rekl = data.rekl;
		$scope.user = data.rekl.user;
		$scope.abmahnungVorschlagUser = data.rekl.user;
		$scope.refreshKunde(data.rekl.kunde);
		$scope.kunde.selected.kunde = data.rekl.kunde;
		$scope.refreshTeil(data.rekl.teil, data.rekl.kunde);
		$scope.teil.selected.teil = data.rekl.teil;

		var uploader = new plupload.Uploader({
		    runtimes: 'html5,flash,browserplus',
		    flash_swf_url: '../plupload/js/plupload.flash.swf',
		    browse_button: 'pickfiles',
		    container: 'uploader',
		    url: '../upload.php?savepath=' + data.rekl.savePath
		});

                if(save!==1){
                uploader.init();
		uploader.bind('FilesAdded', function (up, files) {
		    $.each(files, function (i, file) {
			$('#filelist').append(
				'<div id="' + file.id + '">' +
				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
		    });
		    up.start();
		});

		uploader.bind('UploadProgress', function (up, file) {
		    $('#' + file.id + " b").html(file.percent + "%");
		});

		uploader.bind('Error', function (up, err) {
		    $('#filelist').append("<div>Error: " + err.code +
			    ", popis chyby: " + err.message +
			    (err.file ? ", soubor: " + err.file.name : "") +
			    "</div>"
			    );
		    up.refresh(); // Reposition Flash/Silverlight
		});
		uploader.bind('FileUploaded', function (up, file) {
		    //$('#' + file.id + " b").html("uloženo");
		    $('#' + file.id).remove();
		    console.log('file uploaded,' + file.id);
		});

		uploader.bind('UploadComplete', function (up, files) {
		    console.log('upload complete');
		    $http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
			$scope.rekl = data.rekl;
		    });
		});    
                }
		

	    });
	}
	
	$scope.reklid = $routeParams.reklid;

	//nova reklamace ?
	var semaphore = false;
	if ($scope.reklid == 0) {
	    //zalozit novou s nejakyma default hodnotama
	    console.log('tvorim novou reklamaci !')
	    $http.post('./createNewRekl.php', {rekl_datum: new Date()}).success(function (data) {
		$scope.reklid = data.reklid;
		initDetail();
	    });
	}
	else {
	    initDetail();
	}

	initSecurity();
	
	$scope.reklOpen = function(){
	    $http.post('./openRekl.php', {rekl: $scope.rekl}).success(function (data) {
		//$scope.reklid = data.reklid;
		$scope.rekl.rekl_erledigt_am1 = null;
	    });
	}
	/**
	 * 
	 * @returns {undefined}
	 */
	$scope.report8Dgenerieren = function () {
	    console.log('8D generieren');
	    $scope.reklSave();
	    var params = {
		rekl: $scope.rekl
	    };
	    $http.post('../Reports/report8D_pdf.php', params).success(function (data) {
		console.log('8D generiert ' + data);
		$http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
		    $scope.rekl = data.rekl;
		});
	    });
	}

	/**
	 * 
	 * @param {type} id
	 * @returns {undefined}
	 */
	$scope.delAbmahnung = function (id) {
	    console.log('delAbmahnung ' + id);
	    $http.post('./delAbmahnungVorschlag.php', {id: id, rekl_id: $scope.rekl.id}).success(function (data) {
		if (data.affectedRows > 0) {
		    $scope.rekl.abmahnungen = data.abmahnungen;
		}
	    });
	}

	$scope.addAbmahnung = function () {
	    var persnr = 0;
	    if ($scope.abmahnungPersnr.selected !== undefined) {
		persnr = $scope.abmahnungPersnr.selected.persnr;
	    }
	    console.log("addAbmahnung, persnr:" + persnr
		    + ' bemerkung: ' + $scope.abmahnungBemerkung
		    + ' abmahnungVorschlagUser: ' + $scope.abmahnungVorschlagUser
		    + ' abmahnungVorschlagBetrag: ' + $scope.abmahnungVorschlagBetrag
		    + ' abmahnungDatum: ' + $scope.abmahnungDatum

		    );

	    var params = {
		rekl_id: $scope.rekl.id,
		persnr: persnr,
		datum: $scope.abmahnungDatum,
		vorschlagBemerkung: $scope.abmahnungBemerkung,
		vorschlagUser: $scope.abmahnungVorschlagUser,
		vorschlagBetrag: $scope.abmahnungVorschlagBetrag
	    };
	    $http.post('./addAbmahnungVorschlag.php', params).success(function (data) {
		//abmahnung vorschlag added
		if (data.insertId > 0) {
		    $scope.rekl.abmahnungen = data.abmahnungen;
		}
	    });
	}


	$scope.addSchulung = function () {
	    var persnr = 0;
	    if ($scope.schulungPersnr.selected !== undefined) {
		persnr = $scope.schulungPersnr.selected.persnr;
	    }
	    console.log("addSchulung, persnr:" + persnr);

	    var params = {
		rekl_id: $scope.rekl.id,
		persnr: persnr,
		datum: $scope.rekl.rekl_datum
	    };
	    $http.post('./addSchulungVorschlag.php', params).success(function (data) {
		//abmahnung vorschlag added
		if (data.insertId > 0) {
		    $scope.rekl.schulungen = data.schulungen;
		}
	    });
	}

	$scope.teilOnSelect = function($item, $model){
	    console.log($item);
	    $scope.rekl.teil = $scope.teil.selected.teil;
	    $http.get('./getTeilGew.php?teil=' + $item.teil).success(function (data) {
		    $scope.rekl.teilgew = parseFloat(data.tia.Gew);
		});
	}
	
	$scope.kundeOnSelect = function($item, $model){
	    console.log($item);
	    $scope.rekl.kunde = $scope.kunde.selected.kunde;
	}
	
	$scope.reklSave = function () {
	    console.log("reklSave");

	    $scope.rekl.kunde = $scope.kunde.selected.kunde;
	    $scope.rekl.teil = $scope.teil.selected.teil;

	    var params = {
		rekl: $scope.rekl,
		beenden: $scope.beenden
	    };
	    $http.post('./reklSave.php', params).success(function (data) {
		console.log('rekl saved ' + data);
		$scope.beenden = 0;
		initDetail(1);
	    });
	}

	/**
	 * 
	 * @returns {undefined}
	 */
	$scope.reklBeenden = function () {
	    console.log('reklBeenden');
	    // ulozit aktualni datum do rekl_erledigt_am
	    $scope.rekl.rekl_erledigt_am1 = new Date();
	    $scope.beenden = 1;
	    $scope.reklSave();
	    // schovat tlacitka save = vyresit pomoc ngShow s podminkou na vyplneny datum rekl_erledigt_am
	}

	$scope.editSchulung = function(a,f){
	    if(f==='ursacher'){
		console.log('editSchulung');
		console.log(a);
		console.log(a.rekl_verursacher);
		$http.post('./editSchulungVorschlag.php', {id: a.id, f:'rekl_verursacher',value:a.rekl_verursacher}).success(function (data) {
		// nikde nic neaktualizuju
	    });
	    }
	}
	
	$scope.delSchulung = function (id) {
	    console.log('delSchulung ' + id);
	    $http.post('./delSchulungVorschlag.php', {id: id, rekl_id: $scope.rekl.id}).success(function (data) {
		if (data.affectedRows > 0) {
		    $scope.rekl.schulungen = data.schulungen;
		}
	    });
	}

	$scope.enable = function () {
	    $scope.disabled = false;
	};

	$scope.disable = function () {
	    $scope.disabled = true;
	};

	$scope.clear = function () {
	    $scope.abmahnungPersnr.selected = undefined;
	    $scope.schulungPersnr.selected = undefined;
	    //$scope.kunde.selected = undefined;
	};




	$scope.refreshAbmahnungPersnr = function (e) {
	    var params = {e: e};
	    return $http.get(
		    './getPersnr.php',
		    {params: params}
	    ).then(function (response) {
		$scope.abmahnungPersnrArray = response.data.persnrArray;
	    });
	};

	$scope.refreshKunde = function (e) {
	    var params = {e: e};
	    return $http.get(
		    './getKunde.php',
		    {params: params}
	    ).then(function (response) {
		$scope.kundeArray = response.data.kundeArray;
	    });
	};

	$scope.refreshTeil = function (e, k) {
	    var params = {e: e, k: k};
	    return $http.get(
		    './getTeil.php',
		    {params: params}
	    ).then(function (response) {
		$scope.teilArray = response.data.teilArray;
	    });
	};

	$scope.refreshSchulungPersnr = function (e) {
	    var params = {e: e};
	    return $http.get(
		    './getPersnr.php',
		    {params: params}
	    ).then(function (response) {
		$scope.schulungPersnrArray = response.data.persnrArray;
	    });
	};
	
	
    }]);

