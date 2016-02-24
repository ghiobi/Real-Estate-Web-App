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
	$POSTDELETED = false;
	//DELETE POST
	if(isset($_POST['DELETEPOST'])){
		$DELETEPOST = $_POST['DELETEPOST'];
		$SQL = "DELETE FROM rentalspace WHERE PostID = '$DELETEPOST'";
		mysqli_query($connection, $SQL);
		$POSTDELETED = true;
	}

	$SQL = "SELECT * FROM rentalspace WHERE MemberID = '$ID'";
	$result = mysqli_query($connection, $SQL);

	$num_rows = mysqli_num_rows($result);


?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Owner Postings - rentalmtl</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="../../css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="ownerpost.css">
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
									<li><a href="">Your Postings</a></li>
									<li><a href="newpost/">Post an Ad</a></li>
									<li><a href="../ownersearch">Search Tenant</a></li>
									<li><a href="../owneraccount">Account Settings</a></li>
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
					<div class="col-xs-8 col-sm-9">
						<h3 style="margin-top: 5px"><?php print $num_rows; ?> Postings</h3>
					</div>
					<div class="col-xs-4 col-sm-3">
						<a class="btn btn-info" style="float:right" href="newpost/">Add a Post</a>
					</div>
				</div>
				<hr>
				<?php
				if($POSTDELETED){
					print '
					<div class="row" id="ToggleButton">
						<div class="col-md-12">
							<button type="btn btn-success col-md-12" style="width:100%" class="btn btn-success" onClick="ToggleSuccess()">
								A post was deleted successfully! Click to dismiss!
							</button>
							<hr>
						</div>
					</div>
					';
				}
				$BOROUGHNAME = array("Ahuntsic-Cartierville","Anjou","Côte-des-Neiges–Notre-Dame-de-Grâce","Lachine","LaSalle","Le Plateau-Mont-Royal","Le Sud-Ouest","L'Île-Bizard–Sainte-Geneviève","Mercier–Hochelaga-Maisonneuve",
										"Montréal-Nord","Outremont","Pierrefonds-Roxboro","Rivière-des-Prairies–Pointe-aux-Trembles","Rosemont–La Petite-Patrie","Saint-Laurent","Saint-Léonard","Verdun","Ville-Marie","Villeray–Saint-Michel–Parc-Extension");
				if($num_rows > 0){
					$row = mysqli_fetch_assoc($result);
					for ($rownum = 0; $rownum < $num_rows ; $rownum++) { 
						print'
							<div class="row">
								<div class="col-sm-3 col-lg-2">
									<img style="width: 100%" src="../img/'.$row['image'].'" alt="Post Image" class="img-rounded">
								</div>
								<div class="col-sm-9 col-lg-10">
									<h3 class="post-title">'.stripslashes($row['title']).'</h3>
									<p>'.stripslashes($row['description']).'<br>
										<strong>Address: </strong> '.stripslashes($row['address']).'<br>
										<strong>Borough: </strong> '.$BOROUGHNAME[$row['borough']].' | <strong>Price: </strong>'.$row['price'].'$
									</p>
								
									<form method="post" action=""><button type="submit" class="btn btn-danger btn-sm" name="DELETEPOST" value="'.$row['PostID'].'">Delete Post</button></form>
								</div>
							</div>
							<hr>
						';
						$row = mysqli_fetch_assoc($result);
					}
				}
				?>
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
	<script type="text/javascript">
	function ToggleSuccess(){
		document.getElementById("ToggleButton").style.display = "none";
	}
	</script>
	<script src="../../script/validateform.js"></script>
	<script src="../../script/jquery-1.11.2.min.js"></script>
	<script src="../../script/bootstrap.min.js"></script>
</body>
</html>
