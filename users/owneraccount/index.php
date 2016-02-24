<?php

	session_start();

	$ID = $_SESSION['login_MEMBERID'];

	if ($_SESSION['login_USERTYPE'] == 'tenant' || !isset($_SESSION['login_MEMBERID']) )
		header("location: ../../");

	include("../../db_configlogin.php");

	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}
	//RETRIEVING USER ACCOUTING SETTINGS
	$SQL = "SELECT * FROM user WHERE MemberID = '$ID'";
	$result = mysqli_query($connection, $SQL); 

	$row = mysqli_fetch_assoc($result);
	$PHONENUMBER = $row['phonenum'];
	$EMAIL = $row['email'];
	

	//UPDATE ACCOUNT SETTINGS
	$accountUPDATED = false;
	if(isset($_POST['submit2'])){
		//RETRIEVING NEW ACCOUNT INFORMATION 
		$FIRST_NAME = trim(htmlspecialchars($_POST['FIRST_NAME']));
		$LAST_NAME = trim(htmlspecialchars($_POST['LAST_NAME']));
		$PHONENUMBER = htmlspecialchars($_POST['PHONE_NUMBER']);
		$EMAIL = htmlspecialchars($_POST['EMAIL']);
		
		//CHANGING SESSION NAME
		$_SESSION['login_FIRSTNAME'] = $FIRST_NAME;
		$_SESSION['login_LASTNAME'] = $LAST_NAME;
		
		$SQL = "UPDATE user SET fname = '$FIRST_NAME', lname = '$LAST_NAME',email = '$EMAIL', phonenum = '$PHONENUMBER' WHERE MemberID = '$ID'";
		mysqli_query($connection, $SQL);
		
		$accountUPDATED = true;
	}

	
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Account Settings - rentalmtl</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="../../css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="owneraccount.css">
	<link rel="shortcut icon" href="../../logo_icon.ico">
</head>

<body>
	<div class="navbar-default">
		<div class="container">
			<div class="navbar-header nabar-left">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-bar-links"> <span class="sr-only">Toggle navigation</span> 
					<span class="icon-bar"></span> 
					<span class="icon-bar"></span> 
				</button>
				<a class="navbar-brand" href="../../">
					<img src="../../logo.png" width="140" style="margin-top:-10px">
				</a> 
			</div>

			<div class="collapse navbar-collapse" id="nav-bar-links">
				<?php
					print '
						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">Welcome! Signed in as '.$_SESSION['login_FIRSTNAME'].' '.$_SESSION['login_LASTNAME'].'<span class="caret"></span> </a>
								<ul class="dropdown-menu" role="menu">
					';

					if ($_SESSION['login_USERTYPE'] == 'owner')
						print'
									<li><a href="../ownerpost">Your Postings</a></li>
									<li><a href="../ownerpost/newpost">Post an Ad</a></li>
									<li><a href="../ownersearch">Search Tenant</a></li>
									<li><a href="">Account Settings</a></li>
									<li class="divider"></li>
						';
					if ($_SESSION['login_USERTYPE'] == 'tenant')
						print'
									<li><a href="">Tenant Profile</a></li>
									<li><a href="../tenantsearch">Search Listings</a></li>
									<li class="divider"></li> 
						';

					print '
									<li><a href="../../logout.php">Sign Out</a></li>
								</ul>
							</li>
						</ul>
					';
				?>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="well">
				<div class="row">
					<div class="col-md-12">
						<?php
							if($accountUPDATED){
								print '
								<div class="row" id="ToggleButton">
									<div class="col-md-12">
										<button type="btn btn-success col-md-12" style="width:100%" class="btn btn-success" onClick="ToggleSuccess()">
											Your contact information has been updated! Click to dismiss!
										</button>
									</div>
								</div>
								';
							}
						?>
						<h3>Change Contact Information</h3>
						<form method="post" onSubmit="return validateChange()">
							<div name="form-validate1" class="form-group">
	                        	<label for="FIRST_NAME">First Name:</label>
								<input type="text" class="form-control" id="FIRST_NAME" name="FIRST_NAME" placeholder="Ex. John-Young" maxlength="20" value="<?php print $_SESSION['login_FIRSTNAME']; ?>">
	                        </div>
	                        <div name="form-validate1" class="form-group">
	                        	<label for="LAST_NAME">Last Name:</label>
								<input type="text" class="form-control" id="LAST_NAME" name="LAST_NAME" placeholder="Ex. Smith-Leblanc" maxlength="20" value="<?php print $_SESSION['login_LASTNAME']; ?>">
	                        </div>
						    <div name="form-validate1" class="form-group">
						    	<label for="PHONE_NUMBER">Phone Number:</label>
						    	<input type="text" class="form-control" id="PHONE_NUMBER" name="PHONE_NUMBER" placeholder="Ex. (514)555-5555" maxlength="40" value="<?php print $PHONENUMBER ;?>">
						    </div>
						    <div name="form-validate1" class="form-group">
						    	<label for="EMAIL">Email:</label>
						    	<input type="email" class="form-control" id="EMAIL" name="EMAIL" placeholder="Ex. john-smith@example.com" value="<?php print $EMAIL; ?>">
						    </div>
						    <button type="submit" class="btn btn-primary" name="submit2">Update Contact Information</button>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 col-md-6">
						<h3>Delete Account</h3>
							<a class="btn btn-danger" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Click Here To Delete Account
							</a>
							<div class="collapse" id="collapseExample">
								<div class="well" style="padding:10px">
									<img src="deleteimg.jpg" alt="Delete Image" style="width: 100%">
									<h4 style="padding-top:0; margin:5px 0">Are you sure?</h4>
									<p>Everything will be lost! Please confirm to delete.</p>
									<a class="btn btn-danger" href="deleteaccount.php">DELETE</a>
								</div>
							</div>
					</div>
				</div>
				<hr>
			<footer>
				<div class="row">
					<div class="col-md-12">
						<h4>Contact Us</h4>
						<p><small> Concordia University<br>
							1455 De Maisonneuve Blvd. W. <br>
							Montreal, Quebec, Canada <br>
							H3G 1M8 <br>
							Tel: 514-848-2424</small>
						</p>
					</div>
					<p class="text-center col-md-12"><small>© Copyright Laurendy Lam, 2014. All rights reserved.</small></p>
				</div>
			</footer>
		</div>
	</div>
	<script src="owneraccount.js"></script>
	<script src="../../script/validateform.js"></script>
	<script src="../../script/jquery-1.11.2.min.js"></script>
	<script src="../../script/bootstrap.min.js"></script>
</body>
</html>
