var User = {
		login : function(){
			var username = $('#divlogin input[name=username]').val();
			var password = $('#divlogin input[name=password]').val();
			var defaultPage = 'home/home';
			$.ajax({
				url:'user/user/checkLogin',
				data:{username : username, password : password},
				type:'POST',
				dataType:'json',
				beforeSend: function(){},
				success: function(data){
					if(data.status){
						$('#divlogin #divinfo').html('<div class="alert alert-success">'+data.message+' success'+'</div>');
						var redirectPage = (window.location.hash != '')? window.location.hash.substr(1) : defaultPage;
						window.location.href = redirectPage;
					}
					else{
						$('#divlogin #divinfo').html('<div class="alert alert-danger">'+data.message +' gagal'+'</div>');
					}
				},
				error: function(){}
			});
			return false;
		},


};
