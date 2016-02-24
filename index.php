<?php
	session_start();
	$error = ''; //IF LOGIN.PHP RETRUNS A LOGIN ERROR, THIS IT WILL DISPLAY AN MESSAGE
	if (isset($_POST['submit1'])) {
		include("login.php");
	}

?>


<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Home - rentalmtl</title>
	<link href="css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="shortcut icon" href="logo_icon.ico">
</head>

<body>
	<div class="navbar-default">
		<div class="container">
			<div class="navbar-header nabar-left">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-bar-links"> <span class="sr-only">Toggle navigation</span> 
					<span class="icon-bar"></span> 
					<span class="icon-bar"></span> 
				</button>
				<a class="navbar-brand" href="">
					<img src="logo.png" width="140" style="margin-top:-10px">
				</a> 
			</div>

			<div class="collapse navbar-collapse" id="nav-bar-links">
				<?php
					if(isset($_SESSION['login_MEMBERID'])){
						print '
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">Welcome! Signed in as '.$_SESSION['login_FIRSTNAME'].' '.$_SESSION['login_LASTNAME'].'<span class="caret"></span> </a>
									<ul class="dropdown-menu" role="menu">
						';

						if ($_SESSION['login_USERTYPE'] == 'owner')
							print'
										<li><a href="users/ownerpost">Your Postings</a></li>
										<li><a href="users/ownerpost/newpost">Post an Ad</a></li>
										<li><a href="users/ownersearch">Search Tenant</a></li>
										<li><a href="users/owneraccount">Account Settings</a></li>
										<li class="divider"></li>
							';
						if ($_SESSION['login_USERTYPE'] == 'tenant')
							print'
										<li><a href="users/tenantprofile">Tenant Profile</a></li>
										<li><a href="users/tenantsearch">Search Listings</a></li>
										<li class="divider"></li>
							';

						print '
										<li><a href="logout.php">Sign Out</a></li>
									</ul>
								</li>
							</ul>
						';
					}
					else {

						print '
							<form class="navbar-form navbar-right" style="border: none" onSubmit="return Validate()" method="post" action="">
								<div class="form-group">
									<input type="text" class="form-control" placeholder="User Name" name="username" maxlength="20">
									<input type="password" class="form-control" placeholder="Password" name="password" maxlength="20">
									<input type="submit" class="btn btn-default" value="Sign In" name="submit1">
								</div>
							</form>
						';
						if ($error != '') print '<div class="navbar-text navbar-right" style="color: #FF5858"><small>'.$error.'</small></div>';
						print '
							<ul class="nav navbar-nav navbar-right">
								<li><a href="registration/">Register</a></li>
							</ul>
						';
					}
				?>
			</div>
		</div>
	</div>
	<div class="jumbotron">
		<div class="container">
			<div class="transbox">
				<h1><?php if(isset($_SESSION['login_MEMBERID'])) print 'Welcome '.$_SESSION['login_FIRSTNAME'].". ";?>Find a place to stay in Montreal.</h1>
				<p >With over 100 million users subscribed to our service!</p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<h3>Welcome!</h3>
				<p style="opacity:">Find the perfect home for you and your family. Meet home owners who are searching for you, or you search for them. With millions of people subscribed to our service, we at rentalmtl are trying our best to put smiles on everyone's face.</p>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php
				if(isset($_SESSION['login_MEMBERID'])){
					print '
						<h3>Get Started!</h3>
						<ul style="list-style-type:square">
					';

					if ($_SESSION['login_USERTYPE'] == 'owner')
						print'
							<li><a href="users/ownerpost">Your Postings</a></li>
							<li><a href="users/ownerpost/newpost">Post an Ad</a></li>
							<li><a href="users/ownersearch">Search Tenant</a></li>
							<li><a href="users/owneraccount">Account Settings</a></li>
						';
					if ($_SESSION['login_USERTYPE'] == 'tenant')
						print'
							<li><a href="users/tenantprofile">Click here to update your profile!</a></li>
							<li><a href="users/tenantsearch">Or start searching listings.</a></li>
						';

					print '
						</ul>
					';
				}
				else{
					print '
					<h3>What is rentalmtl?</h3>
					<p>rentalmtl is a place where tenants and home owners create a trusting relationship to sleep soundly in their own beds.</p>
					';
				}
				?>
			</div>
			<div class="col-md-4 col-sm-12" >
				<h3>How does it work?</h3>
				<dl>
					<dt>For Tenants:</dt>
					<dd>Tenants have to simply have to set up a quick profile, and wait until a home owner finds you! Or search for homes that comfort your perferences.</dd>
					<dt>For Owners:</dt>
					<dd>Owners post houses or apartments for rental, and wait for the tenants to contact you. Or search for a tenant that is searching for a home.</dd>
				</dl>
			</div>
		</div>
		<hr>
	</div>
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-10  col-md-offset-1 text-center">
					<h3>Contact Us</h3>
					<p><small> <strong>Concordia University</strong><br>
						1455 De Maisonneuve Blvd. W. <br>
						Montreal, Quebec, Canada <br>
						H3G 1M8 <br>
						Tel: 514-848-2424</small> </p>
					</div>
				</div>
				<p class="text-center col-md-12"r style="padding-top: 30px">
					<small>© Copyright Laurendy Lam, 2014. All rights reserved.</small>
				</p>
			</div>
		</footer>
		<script src="script/validateform.js"></script>
		<script src="script/jquery-1.11.2.min.js"></script>
		<script src="script/bootstrap.min.js"></script>
	</body>
	</html>
