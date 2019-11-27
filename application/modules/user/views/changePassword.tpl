{extends file='base.tpl'}
{block name=body}
<div class="container">
{$nav}
	<div id="divChangePassword" class="container">
	    <div class="row">
	        <div class="col-sm-6 col-md-4 col-md-offset-4">
	            <h1 class="text-center login-title">Ganti Password</h1>
	            <div class="account-wall">
	            	<img class="profile-img" src="assets/css/user/photo.png" />
	                <form class="form-signin" onsubmit="return changePassword()">
	                <div id="divinfo">{if isset($message)}<div class="alert alert-danger">{$message}</div>{/if}</div>
	                <input type="password" class="form-control" placeholder="Password lama" name="oldPassword" required autofocus />
	                <input type="password" class="form-control" placeholder="Password baru" name="newPassword" required />
	                <input type="password" class="form-control" placeholder="Konfirmasi password baru" name="confirmPassword" required />
	                <button class="btn btn-lg btn-primary btn-block" type="submit">
	                	<span class="glyphicon glyphicon-save"></span>
	                     Simpan 
	               </button>    
	                </form>
	            </div>
	            
	        </div>
	    </div>
	</div>	
</div>
{/block}
{block name=cssAdditional}
	<link rel="stylesheet" media="all" type="text/css" href="assets/css/user/login.css">
{/block}
{block name=jsAdditional}
	<script type="text/javascript" src="assets/js/user/changePassword.js"></script>
{/block}