
(function() {
	'use strict';
	var defaultPage = '';
	$(function() {
		var loadPage = (window.location.hash != '') ? window.location.hash
				.substr(1) : defaultPage;
		if (!empty(loadPage)) {
			$('#main_content').load(loadPage);
		}
		/* jika navbar diklik maka load halaman utama sesuai dengan href */

		$('#navbar').on('click','a.ajax',function(e){
			Home.load_main_content(e,e.target,e.target.href,'#main_content');
		});
		$('#popup_gantipassword').click(function(e){
			var url = $(this).attr('href');
			var nama_user = $(this).data('nama_user');
			/* simpan ke database */
			$.ajax({
				type : 'post',
				url : url,
				data : {nama_user : nama_user},
				success : function(data){

				},
				dataType : 'html'
			}).done(function(data){
				var _options = {
						title : 'Ganti Password',
						message : data,
						buttons : {

							}
						};
					bootbox.dialog(_options);
			});

			e.preventDefault();
		});

	});

}());

(function(API){
	 API.myText = function(txt, options, x, y) {
			 options = options ||{};
			 /* Use the options align property to specify desired text alignment
				* Param x will be ignored if desired text alignment is 'center'.
				* Usage of options can easily extend the function to apply different text
				* styles and sizes
			 */
			 if( options.align == "center" ){
					 // Get current font size
					 var fontSize = this.internal.getFontSize();

					 // Get page width
					 var pageWidth = this.internal.pageSize.width;

					 // Get the actual text's width
					 /* You multiply the unit width of your string by your font size and divide
						* by the internal scale factor. The division is necessary
						* for the case where you use units other than 'pt' in the constructor
						* of jsPDF.
					 */
					 txtWidth = this.getStringUnitWidth(txt)*fontSize/this.internal.scaleFactor;

					 // Calculate text's x coordinate
					 x = ( pageWidth - txtWidth ) / 2;
			 }

			 // Draw text at x,y
			 this.text(txt,x,y);
	 }
})(jsPDF.API);
