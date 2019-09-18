{extends file='base.tpl'}
{block name=body}

<div id="divlogin" class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">Login POBB</h1>
            <div class="account-wall">
                <img class="profile-img" src="assets/css/user/photo.png" />
                <form class="form-signin" onsubmit="return login()">
                <div id="divinfo">{if isset($message)}<div class="alert alert-danger">{$message}</div>{/if}</div>
                <input type="text" class="form-control" placeholder="Username" name="username" required autofocus />
                <input type="password" class="form-control" placeholder="Password" name="password" required />
                <button class="btn btn-lg btn-primary btn-block" type="submit">
                	<span class="glyphicon glyphicon-lock"></span>
                     Login 
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

{/block}
{block name=cssAdditional}
	<link rel="stylesheet" type="text/css" href="assets/css/user/login.css" />
{/block}
{block name=jsAdditional}
	<script type="text/javascript" src="assets/js/user/login.js"></script>
{/block}