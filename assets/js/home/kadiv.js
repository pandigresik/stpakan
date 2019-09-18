$(window).load(function(){
	'use strict';

	/* cek apakah ada notif atau tidak */
	var _notif_json = $('#data-notif').text();
	var _notif;
	var _msg = '';
	var _title = 'Alasan Reject';
	if(_notif_json.length){
		_notif = $.parseJSON(_notif_json);
		toastr.options = {
				  "closeButton": true,	
				  "timeOut": "0",
				  "extendedTimeOut": "0",
				  "tapToDismiss" : false
				};
		for(var i in _notif){
			var _title = _notif[i]['title'];
			//console.log(_title);
			var _content = _notif[i]['message'];
			for(var y in _content){
				//console.log(_content[y]);
				_msg = _content[y];
				toastr.warning(_msg,_title);
			}
		}
		/*
		for(var i in _notif){
			_msg = _notif[i].ket_reject;
			toastr.warning(_msg,_title+' '+_notif[i].no_lpb);
			
		}
		*/
		//toastr.clear();
		toastr.options = {
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				};
	}	
});