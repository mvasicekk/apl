/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('d607iApp');

aplApp.controller('d607iController', function ($scope, $http) {
    $scope.terminMatch = '';
    $scope.importMatch = '';
    $scope.teilMatch = '';

    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (($scope.terminMatch.length >= 3)&&(e.which==13)) {
	    $http.get('./getD607i.php?termin=' + $scope.terminMatch
		    +'&import='+$scope.importMatch
		    +'&teil='+$scope.teilMatch
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			$scope.zeilenD = data.zeilenD;
			$scope.dZeilen = [].concat($scope.zeilen);
			$scope.abgnrKeysArray = data.abgnrKeysArray;
		    });
	}
    };
});

//aplApp.controller('detailController', ['$scope', '$routeParams', '$http',
//    function ($scope, $routeParams, $http) {
//	
//	var initDetail = function () {
//	    $scope.rekl = undefined;
//	    $scope.disabled = undefined;
//
//	    $scope.abmahnungVorschlagUser = undefined;
//	    $scope.abmahnungBemerkung = "";
//	    $scope.abmahnungVorschlagBetrag = 0;
//	    $scope.abmahnungDatum = new Date();
//
//	    $scope.dateOptions = {
//		dateFormat: 'dd.mm.yy',
//		firstDay: 1
//	    };
//
//	    $scope.abmahnungPersnr = {};
//	    $scope.schulungPersnr = {};
//	    $scope.kunde = {};
//	    $scope.kunde.selected = {};
//	    $scope.teil = {};
//	    $scope.teil.selected = {};
//
//	    $http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
//		$scope.rekl = data.rekl;
//		$scope.user = data.rekl.user;
//		$scope.abmahnungVorschlagUser = data.rekl.user;
//		$scope.refreshKunde(data.rekl.kunde);
//		$scope.kunde.selected.kunde = data.rekl.kunde;
//		$scope.refreshTeil(data.rekl.teil, data.rekl.kunde);
//		$scope.teil.selected.teil = data.rekl.teil;
//
//		var uploader = new plupload.Uploader({
//		    runtimes: 'html5,flash,browserplus',
//		    flash_swf_url: '../plupload/js/plupload.flash.swf',
//		    browse_button: 'pickfiles',
//		    container: 'uploader',
//		    url: '../upload.php?savepath=' + data.rekl.savePath
//		});
//
//		uploader.init();
//		uploader.bind('FilesAdded', function (up, files) {
//		    $.each(files, function (i, file) {
//			$('#filelist').append(
//				'<div id="' + file.id + '">' +
//				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
//		    });
//		    up.start();
//		});
//
//		uploader.bind('UploadProgress', function (up, file) {
//		    $('#' + file.id + " b").html(file.percent + "%");
//		});
//
//		uploader.bind('Error', function (up, err) {
//		    $('#filelist').append("<div>Error: " + err.code +
//			    ", popis chyby: " + err.message +
//			    (err.file ? ", soubor: " + err.file.name : "") +
//			    "</div>"
//			    );
//		    up.refresh(); // Reposition Flash/Silverlight
//		});
//		uploader.bind('FileUploaded', function (up, file) {
//		    //$('#' + file.id + " b").html("uloženo");
//		    $('#' + file.id).remove();
//		    console.log('file uploaded,' + file.id);
//		});
//
//		uploader.bind('UploadComplete', function (up, files) {
//		    console.log('upload complete');
//		    $http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
//			$scope.rekl = data.rekl;
//		    });
//		});
//
//	    });
//	}
//	
//	$scope.reklid = $routeParams.reklid;
//
//	//nova reklamace ?
//	var semaphore = false;
//	if ($scope.reklid == 0) {
//	    //zalozit novou s nejakyma default hodnotama
//	    console.log('tvorim novou reklamaci !')
//	    $http.post('./createNewRekl.php', {rekl_datum: new Date()}).success(function (data) {
//		$scope.reklid = data.reklid;
//		initDetail();
//	    });
//	}
//	else {
//	    initDetail();
//	}
//
//	/**
//	 * 
//	 * @returns {undefined}
//	 */
//	$scope.report8Dgenerieren = function () {
//	    console.log('8D generieren');
//	    $scope.reklSave();
//	    var params = {
//		rekl: $scope.rekl
//	    };
//	    $http.post('../Reports/report8D_pdf.php', params).success(function (data) {
//		console.log('8D generiert ' + data);
//		$http.get('./getReklDetail.php?reklid=' + $scope.reklid).success(function (data) {
//		    $scope.rekl = data.rekl;
//		});
//	    });
//	}
//
//	/**
//	 * 
//	 * @param {type} id
//	 * @returns {undefined}
//	 */
//	$scope.delAbmahnung = function (id) {
//	    console.log('delAbmahnung ' + id);
//	    $http.post('./delAbmahnungVorschlag.php', {id: id, rekl_id: $scope.rekl.id}).success(function (data) {
//		if (data.affectedRows > 0) {
//		    $scope.rekl.abmahnungen = data.abmahnungen;
//		}
//	    });
//	}
//
//	$scope.addAbmahnung = function () {
//	    var persnr = 0;
//	    if ($scope.abmahnungPersnr.selected !== undefined) {
//		persnr = $scope.abmahnungPersnr.selected.persnr;
//	    }
//	    console.log("addAbmahnung, persnr:" + persnr
//		    + ' bemerkung: ' + $scope.abmahnungBemerkung
//		    + ' abmahnungVorschlagUser: ' + $scope.abmahnungVorschlagUser
//		    + ' abmahnungVorschlagBetrag: ' + $scope.abmahnungVorschlagBetrag
//		    + ' abmahnungDatum: ' + $scope.abmahnungDatum
//
//		    );
//
//	    var params = {
//		rekl_id: $scope.rekl.id,
//		persnr: persnr,
//		datum: $scope.abmahnungDatum,
//		vorschlagBemerkung: $scope.abmahnungBemerkung,
//		vorschlagUser: $scope.abmahnungVorschlagUser,
//		vorschlagBetrag: $scope.abmahnungVorschlagBetrag
//	    };
//	    $http.post('./addAbmahnungVorschlag.php', params).success(function (data) {
//		//abmahnung vorschlag added
//		if (data.insertId > 0) {
//		    $scope.rekl.abmahnungen = data.abmahnungen;
//		}
//	    });
//	}
//
//
//	$scope.addSchulung = function () {
//	    var persnr = 0;
//	    if ($scope.schulungPersnr.selected !== undefined) {
//		persnr = $scope.schulungPersnr.selected.persnr;
//	    }
//	    console.log("addSchulung, persnr:" + persnr);
//
//	    var params = {
//		rekl_id: $scope.rekl.id,
//		persnr: persnr,
//		datum: $scope.rekl.rekl_datum
//	    };
//	    $http.post('./addSchulungVorschlag.php', params).success(function (data) {
//		//abmahnung vorschlag added
//		if (data.insertId > 0) {
//		    $scope.rekl.schulungen = data.schulungen;
//		}
//	    });
//	}
//
//	$scope.teilOnSelect = function($item, $model){
//	    console.log($item);
//	    $http.get('./getTeilGew.php?teil=' + $item.teil).success(function (data) {
//		    $scope.rekl.teilgew = parseFloat(data.tia.Gew);
//		});
//	}
//	
//	$scope.reklSave = function () {
//	    console.log("reklSave");
//
//	    $scope.rekl.kunde = $scope.kunde.selected.kunde;
//	    $scope.rekl.teil = $scope.teil.selected.teil;
//
//	    var params = {
//		rekl: $scope.rekl
//	    };
//	    $http.post('./reklSave.php', params).success(function (data) {
//		console.log('rekl saved ' + data);
//		initDetail();
//	    });
//	}
//
//	/**
//	 * 
//	 * @returns {undefined}
//	 */
//	$scope.reklBeenden = function () {
//	    console.log('reklBeenden');
//	    // ulozit aktualni datum do rekl_erledigt_am
//	    $scope.rekl.rekl_erledigt_am1 = new Date();
//	    $scope.reklSave();
//	    // schovat tlacitka save = vyresit pomoc ngShow s podminkou na vyplneny datum rekl_erledigt_am
//	}
//
//	$scope.delSchulung = function (id) {
//	    console.log('delSchulung ' + id);
//	    $http.post('./delSchulungVorschlag.php', {id: id, rekl_id: $scope.rekl.id}).success(function (data) {
//		if (data.affectedRows > 0) {
//		    $scope.rekl.schulungen = data.schulungen;
//		}
//	    });
//	}
//
//	$scope.enable = function () {
//	    $scope.disabled = false;
//	};
//
//	$scope.disable = function () {
//	    $scope.disabled = true;
//	};
//
//	$scope.clear = function () {
//	    $scope.abmahnungPersnr.selected = undefined;
//	    $scope.schulungPersnr.selected = undefined;
//	    //$scope.kunde.selected = undefined;
//	};
//
//
//
//
//	$scope.refreshAbmahnungPersnr = function (e) {
//	    var params = {e: e};
//	    return $http.get(
//		    './getPersnr.php',
//		    {params: params}
//	    ).then(function (response) {
//		$scope.abmahnungPersnrArray = response.data.persnrArray;
//	    });
//	};
//
//	$scope.refreshKunde = function (e) {
//	    var params = {e: e};
//	    return $http.get(
//		    './getKunde.php',
//		    {params: params}
//	    ).then(function (response) {
//		$scope.kundeArray = response.data.kundeArray;
//	    });
//	};
//
//	$scope.refreshTeil = function (e, k) {
//	    var params = {e: e, k: k};
//	    return $http.get(
//		    './getTeil.php',
//		    {params: params}
//	    ).then(function (response) {
//		$scope.teilArray = response.data.teilArray;
//	    });
//	};
//
//	$scope.refreshSchulungPersnr = function (e) {
//	    var params = {e: e};
//	    return $http.get(
//		    './getPersnr.php',
//		    {params: params}
//	    ).then(function (response) {
//		$scope.schulungPersnrArray = response.data.persnrArray;
//	    });
//	};
//    }]);
