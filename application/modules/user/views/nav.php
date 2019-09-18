<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<a class="navbar-brand" href="#">POBB</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav">
			<?php 
				$nav = $menu;
				foreach ($nav as $r){
			?>
				<li class='<?php echo $r['class']; ?>'><a id='<?php echo $r['id']; ?>' href='<?php echo $r['url']; ?>'><?php echo $r['title']; ?></a></li>
			<?php 
				}
			?>
		  </ul>
		  
		  <ul class="nav navbar-nav navbar-right" style="margin-right : 1%;">
		  	  <?php if(empty($username)){ ?>
			  <li><a href="<?php echo base_url().'user/logout';?>">Login</a></li>
			  <?php }else{ ?>
			  <li class="dropdown" role="presentation">
		        <a href="#" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">
		          <?php echo $username; ?> <span class="caret"></span>
		        </a>
		        <ul role="menu" class="dropdown-menu">
		          <li><a href="<?php echo base_url().'user/changePassword';?>">Ganti Password</a></li>
		          <li><a href="<?php echo base_url().'user/logout';?>">Logout</a></li>
		        </ul>
		      </li>
			  <?php } ?>
		  </ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>

