<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<a class="navbar-brand" href="#">POBB</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav">
			{$nav = get_navigation()}
			{foreach $nav as $r}
				<li class='{$r.class}'><a id='{$r.id}' href='{$r.url}'>{$r.title}</a></li>
			{/foreach}
		  </ul>
		  
		  <ul class="nav navbar-nav navbar-right">
			<li><a href="<?php echo base_url().'user/logout';?>">Logout</a></li>
		  </ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>