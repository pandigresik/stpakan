(function() {
	'use strict';

	$(function() {
		$.ajaxSetup({
			statusCode : {
				401 : function() {
					bootbox.alert('Session telah habis login lagi', function() {
						window.location.href = 'user/user/login';
					})
				},
				403 : function(xhr, status, text) {
					bootbox.alert(text, function() {
						window.location.href = 'user/user/login';
					})
				},
			},
			//cache: false,
			cache: true,
			beforeSend:function(){

			},
			success:function(){

			},
			error : function(xhr, status, text) {
				var pesan = xhr.responseText;
				bootbox.alert('Terjadi error di server \n' + pesan, function() {
				});
			}
		});
	})
}());
