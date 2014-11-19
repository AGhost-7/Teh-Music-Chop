<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="/">Teh Music Chop</a>
	</div>
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav navbar-right">
			<li>
				<a href="about.php">About</a>
			</li>
			<?php 
				if($user)
					echo '<li><a href="product-browser.php">Browse</a></li>';
				if(isset($user) && $user && $user->get_is_admin())
					echo '<li><a href="product-admin.php">Administrate</a></li>';
			?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					Account<?php if($user) echo ' - ' . $user->get_user_name(); ?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<?php if($user): ?>
						<li><a href="cart.php">My Cart</a></li>
						<li class="divider"></li>
						<li><a href="logout.php">Logout</a></li>
					<?php else: ?>
						<li><a href="registration.php">Register</a></li>
						<li><a href="login.php">Login</a></li>
					<?php endIf; ?>
				</ul>
			</li>
		</ul>
	</div>
</nav>