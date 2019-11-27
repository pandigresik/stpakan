
	<div id="divChangePassword" class="container">
	    <div class="row">
	        <div class="col-md-8">
	            <div class="account-wall">
	            <form class="form-horizontal" onsubmit="return Home.changePassword()">
					<div class="form-group">
						<label class="col-md-3 control-label" for="username">Username</label>
						<div class="col-md-5 ">
						<input type="text"  name="username" class="form-control" readonly value="<?php echo $nama ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label" for="oldPassword">Password</label>
						<div class="col-md-5 ">
						<input type="password" required maxlength="50" name="oldPassword" class="form-control" autofocus>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label" for="newPassword">New Password</label>
						<div class="col-md-5 ">
						<input type="password" required maxlength="50" name="newPassword" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label" for="confirmPassword">Confirm Password</label>
						<div class="col-md-5 ">
						<input type="password" required maxlength="50" name="confirmPassword" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4 col-md-offset-3 ">
							<button type="submit" class="btn btn-primary">Save</button>&nbsp;&nbsp;<span class="btn btn-danger"  data-dismiss='modal' aria-hidden='true'>Close</span>
						</div>
					</div>
			</form>
	             
	            </div>
	            
	        </div>
	    </div>
	</div>	

