/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('reklApp');

aplApp.controller('reklController',function($scope,$http){
    $http.get('./getReklamationen.php').success(function(data){
	$scope.reklamationen = data.reklamationen;
	$scope.dReklamationen = [].concat($scope.reklamationen);
    });
});

aplApp.controller('detailController', ['$scope', '$routeParams','$http',
  function($scope, $routeParams,$http) {
    $scope.reklid = $routeParams.reklid;
    $scope.rekl = undefined;
    
    $http.get('./getReklDetail.php?reklid='+$scope.reklid).success(function(data){
	$scope.rekl = data.rekl;
	
	var uploader = new plupload.Uploader({
	    runtimes: 'html5,flash,browserplus',
	    flash_swf_url: '../plupload/js/plupload.flash.swf',
	    browse_button: 'pickfiles',
	    container: 'uploader',
	    url: '../upload.php?savepath='+data.rekl.savePath
	});
    
//	console.log(uploader);
	uploader.init();
	uploader.bind('FilesAdded', function(up, files) {
	    $.each(files, function(i, file) {
		$('#filelist').append(
		    '<div id="' + file.id + '">' +
		    file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
	    });
	    up.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
	    $('#' + file.id + " b").html(file.percent + "%");
	});

	uploader.bind('Error', function(up, err) {
	    $('#filelist').append("<div>Error: " + err.code +
            ", popis chyby: " + err.message +
            (err.file ? ", soubor: " + err.file.name : "") +
            "</div>"
	    );
	    up.refresh(); // Reposition Flash/Silverlight
	});
	uploader.bind('FileUploaded', function(up, file) {
	    //$('#' + file.id + " b").html("uloženo");
	    $('#' + file.id).remove();
	    console.log('file uploaded,'+file.id);
	});
	
	uploader.bind('UploadComplete', function(up, files) {
	    console.log('upload complete');
	    $http.get('./getReklDetail.php?reklid='+$scope.reklid).success(function(data){
		$scope.rekl = data.rekl;
	    });
	});

    });
    
  }]);

