<!DOCTYPE html>
<html>
<head>
<title>ST Pakan</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="<?php echo $base_url?>" />
</head>
<body>
	<div class="container" id="divlogin">
		<div class="row">
			<div class="col-sm-6 col-md-4 col-md-offset-4">
				<h1 class="text-center login-title">Login ST Pakan</h1>
				<div class="account-wall">
					<img src="assets/css/user/photo.png" class="profile-img">
					<form onsubmit="return User.login()" class="form-signin">
						<div id="divinfo"></div>
						<input type="text" autofocus="" required="" name="username"
							placeholder="Username" class="form-control"> <input
							type="password" required="" name="password"
							placeholder="Password" class="form-control">
						<button type="submit" class="btn btn-lg btn-primary btn-block">
							<span class="glyphicon glyphicon-lock"></span> Login
						</button>
						<!---       
		                <label class="checkbox pull-left">
		                    <input type="checkbox" value="remember-me">
		                    Remember me
		                </label>
		                <a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span>
		            -->
					</form>
				</div>

			</div>
		</div>
	</div>
</body>

<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" media="all" type="text/css" href="assets/css/user/login.css">

<script type="text/javascript" src="assets/libs/jquery/jquery-2.0.0.min.js"></script>
<script type="text/javascript" src="assets/js/user/user.js"></script>