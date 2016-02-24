<?php

	session_start();
	if (!isset($_SESSION['login_MEMBERID']) || $_SESSION['login_USERTYPE'] == 'tenant') {
		header("location: ../../../");
	}
	$ID = $_SESSION['login_MEMBERID'];

	include("../../../db_configlogin.php");

	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}
	
	//SUBMITTING A POST INTO DATABASE
	if(isset($_POST['submit1'])){

		$TITLE = dress_query_string($_POST['TITLE']);
		$PRICE = dress_query_string($_POST['PRICE']);
		$ADDRESS = dress_query_string($_POST['ADDRESS']);
		$BOROUGH = htmlspecialchars($_POST['BOROUGH']);
		$DESCRIPTION = dress_query_string($_POST['DESCRIPTION']);

		$target_dir = "../../img/";
		$target_file = $target_dir . basename($_FILES["IMAGEFILE"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$newFileName = uniqid().'.'.$imageFileType;
		move_uploaded_file($_FILES["IMAGEFILE"]["tmp_name"], $target_dir.$newFileName);

		$SQL = "INSERT INTO rentalspace (MemberID, title, price, borough, address, image, description) VALUES ('$ID','$TITLE','$PRICE','$BOROUGH','$ADDRESS', '$newFileName','$DESCRIPTION')";
		mysqli_query($connection, $SQL);

		header("location: ../");

	}

	function dress_query_string($str){
		return addslashes(trim(htmlspecialchars($str)));
	}
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Add a new Post - rentalmtl</title>
	<link href="../../../css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="../../../css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="newpost.css">
	<link rel="shortcut icon" href="../../../logo_icon.ico">
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
					<img src="../../../logo.png" width="140" style="margin-top:-10px">
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
									<li><a href="../">Your Postings</a></li>
									<li><a href="">Post an Ad</a></li>
									<li><a href="../../ownersearch">Search Tenant</a></li>
									<li><a href="../../owneraccount">Account Settings</a></li>
									<li class="divider"></li>
						';
					if ($_SESSION['login_USERTYPE'] == 'tenant')
						print'
									<li><a href="">Tenant Profile</a></li>
									<li><a href="../tenantsearch">Search Listings</a></li>
									<li class="divider"></li> 
						';

					print '
									<li><a href="../../../logout.php">Sign Out</a></li>
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
						<h3>Post a new ad.</h3>
					</div>
				</div>
				<hr>
				<form class="form-horizontal" method="post" action="" onsubmit="return validatePost()" enctype="multipart/form-data">
					<div name="form-validate" class="form-group">
						<label for="TITLE" class="col-sm-2 control-label">Title:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="TITLE" name="TITLE" placeholder="Enter ad title here." maxlength="50">
						</div>
					</div>
					<div name="form-validate" class="form-group">
						<label for="PRICE" class="col-sm-2 control-label">Asking price:</label>
						<div class="col-sm-10">
							<div class="input-group">
								<div class="input-group-addon">$</div>
								<input type="number" class="form-control" id="PRICE" name="PRICE" min="0" max="100000" max="10" placeholder="Enter your price per month.">
							</div>
						</div>
					</div>
					<div name="form-validate" class="form-group">
						<label for="ADDRESS" class="col-sm-2 control-label">Address</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="ADDRESS" name="ADDRESS" placeholder="Ex: 1455 De Maisonneuve Blvd. W., H3G 1M8" maxlength="40">
							<span class="help-block">Include only the civil number, street name and postal code.</span>
						</div>
					</div>


					<div name="form-validate" class="form-group">
						<label for="BOROUGH" class="col-sm-2 control-label">Borough</label>
						<div class="col-sm-10">
							<select class="form-control" id="BOROUGH" name="BOROUGH"> 
								<option></option>
								<option value="0">Ahuntsic-Cartierville</option>
								<option value="1">Anjou</option>
								<option value="2">Côte-des-Neiges–Notre-Dame-de-Grâce</option>
								<option value="3">Lachine</option>
								<option value="4">LaSalle</option>
								<option value="5">Le Plateau-Mont-Royal</option>
								<option value="6">Le Sud-Ouest</option>
								<option value="7">L'Île-Bizard–Sainte-Geneviève</option>
								<option value="8">Mercier–Hochelaga-Maisonneuve</option>
								<option value="9">Montréal-Nord</option>
								<option value="10">Outremont</option>
								<option value="11">Pierrefonds-Roxboro</option>
								<option value="12">Rivière-des-Prairies–Pointe-aux-Trembles</option>
								<option value="13">Rosemont–La Petite-Patrie</option>
								<option value="14">Saint-Laurent</option>
								<option value="15">Saint-Léonard</option>
								<option value="16">Verdun</option>
								<option value="17">Ville-Marie</option>
								<option value="18">Villeray–Saint-Michel–Parc-Extension</option>
							</select>
						</div>
					</div>

					<div name="form-validate" class="form-group">
						<label for="IMAGEFILE" class="col-sm-2 control-label">Select Image:</label>
						<div class="col-sm-10">
							<input type="file" id="IMAGEFILE" name="IMAGEFILE" accept="image/*">
							<p class="help-block">Please select an image of the house.</p>
						</div>
					</div>
					<div name="form-validate" class="form-group">
						<label for="DESCRIPTION" class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="3" placeholder="Describe the renting space" maxlength="300" onkeydown="numChar()" id="DESCRIPTION" name="DESCRIPTION"></textarea>
							<span id="CHARACTERS-LEFT" class="help-block">300 Characters Left</span>
						</div>
					</div>
					<script type="text/javascript">
						function numChar(){
							var x = document.getElementById("DESCRIPTION").value;
							document.getElementById("CHARACTERS-LEFT").innerHTML = (300 - x.length)  + ' Characters Left';
						}	
					</script>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" name="submit1" class="btn btn-primary">Post Ad</button>
						</div>
					</div>
				</form>
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
	<script src="../../../script/jquery-1.11.2.min.js"></script>
	<script src="../../../script/bootstrap.min.js"></script>
	<script type="text/javascript">
		function validatePost(){
			var formgroup = document.getElementsByName("form-validate");
			for (var i = 0; i < formgroup.length; i++){
				formgroup[i].className = "form-group";
			}

			var onSubmit = true;

			var input = document.getElementsByTagName("input");
			for (var i = 0; i < input.length - 1; i++) {
				if (input[i].value == '') {
					formgroup[i].className = "form-group has-error";
					onSubmit = false;
				}
			}
			if (input[3].value == '') {
				formgroup[4].className = "form-group has-error";
				onSubmit = false;
			}

			if(document.getElementById("BOROUGH").selectedIndex == 0){
				formgroup[3].className = "form-group has-error";
				onSubmit = false;
			}

			if(document.getElementById("DESCRIPTION").value == ''){
				formgroup[5].className = "form-group has-error";
				onSubmit = false;
			}
			return onSubmit;
		}

	</script>
</body>
</html>
