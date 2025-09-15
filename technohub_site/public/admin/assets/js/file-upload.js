/*
Author       : Dreamstechnologies
Template Name: Smarthr - Bootstrap Admin Template
*/

(function () {
    "use strict";

	if($('.custom-file-container').length > 0) {
		//First upload
		var firstUpload = new FileUploadWithPreview('myFirstImage')
		//Second upload
		var secondUpload = new FileUploadWithPreview('mySecondImage')

        var firstdUploadEdit = new FileUploadWithPreview('myFirstImageEdit')

        var secondUploadEdit = new FileUploadWithPreview('mySecondImageEdit')
	}
})();
