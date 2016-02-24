<?php
	session_start();

	if ($_SESSION['login_USERTYPE'] == 'owner' || !isset($_SESSION['login_MEMBERID']))
		header("location: ../../");

	include("../../db_configlogin.php");

	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}

	$ID = $_SESSION['login_MEMBERID'];

	//DEFAULT SETTINGS
	$PREFPRICE = 999;
	$PREFBOROUGH = -1;
	$BOROUGHNAME = array("Ahuntsic-Cartierville","Anjou","Côte-des-Neiges–Notre-Dame-de-Grâce","Lachine","LaSalle","Le Plateau-Mont-Royal","Le Sud-Ouest","L'Île-Bizard–Sainte-Geneviève","Mercier–Hochelaga-Maisonneuve",
						"Montréal-Nord","Outremont","Pierrefonds-Roxboro","Rivière-des-Prairies–Pointe-aux-Trembles","Rosemont–La Petite-Patrie","Saint-Laurent","Saint-Léonard","Verdun","Ville-Marie","Villeray–Saint-Michel–Parc-Extension");

	//GETTING USER SETTINGS IF TENANT PREFERENCES EXISTS
	$TENANTPREF_EXISTS = false;
	$SQL = "SELECT * FROM tenantsearch WHERE MemberID = '$ID'";
	$result = mysqli_query($connection, $SQL);
	$num_rows = mysqli_num_rows($result);

	if ($num_rows > 0) {
		$TENANTPREF_EXISTS = true;
		$row = mysqli_fetch_assoc($result);
		$PREFPRICE = $row['price'];
		$PREFBOROUGH = $row['borough'];
	}

	//SAVING PREFERENCES
	if(isset($_POST['savepref'])){
	 	savePreference();
	}
	//DELETING PREFERENCES
	if(isset($_POST['delpref'])){
		if($TENANTPREF_EXISTS){
			mysqli_query($connection, "DELETE FROM tenantsearch WHERE '$ID'");
			$TENANTPREF_EXISTS = false;
		}
	}
	//MATCHING ACCORDING TO BOROUGH DISTANCE
	$MATCHBTNPUSHED = false;
	$RentArray = null;
	if (isset($_POST['matchbtn'])) {
		savePreference(); //saving preference
		$MATCHBTNPUSHED = true; //show results if this btn is pressed
		$result = mysqli_query($connection, "SELECT * FROM rentalspace");
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		$RentArray = array();
		for ($i=0; $i < $num_rows ; $i++) { 
			array_push($RentArray, new RentalSpace ($row['PostID'], $row['price'], $row['borough'], $PREFBOROUGH));
			$row = mysqli_fetch_assoc($result);// will push on a new rentalspace object created
		}
		for ($i=0; $i < count($RentArray); $i++) { //sort rentalspace object according to magnitude aka relevance
			$closest = $RentArray[$i]->magnitude;
			for ($j=$i + 1; $j < count($RentArray); $j++) { 
				if ($RentArray[$j]->magnitude < $closest) {
					$closest = $RentArray[$j]->magnitude;
					$temp = $RentArray[$j];
					$RentArray[$j] = $RentArray[$i];
					$RentArray[$i] = $temp;
				}
			}
		}
		


	}
	//SAVING PREFERENCE FUNCTION
	function savePreference(){
		global $PREFPRICE, $PREFBOROUGH, $TENANTPREF_EXISTS, $ID, $connection;
	 	$PREFPRICE = htmlspecialchars($_POST['PREFPRICE']);
	 	$PREFBOROUGH = htmlspecialchars($_POST['BOROUGH']);

	 	if ($TENANTPREF_EXISTS){
	 		$SQL = "UPDATE tenantsearch SET borough = '$PREFBOROUGH', price = '$PREFPRICE' WHERE MemberID = '$ID'";
			mysqli_query($connection, $SQL);
		}
	 	else{
	 		$SQL = "INSERT INTO tenantsearch (MemberID, borough, price) VALUES ('$ID','$PREFBOROUGH', '$PREFPRICE')";
	 		mysqli_query($connection, $SQL);
	 		$TENANTPREF_EXISTS = true;
	 	}
	}
	//RENTALSPACE CLASS
	class RentalSpace {
            // Creating some properties (variables tied to an object)
		public $postId;
		public $price;
		public $borough;
		public $magnitude;

            // Assigning the values
		public function __construct($postId, $price, $borough) {
			$this->postId = $postId;
			$this->price = $price;
			$this->borough = $borough;
			$this->calMagnitude();
		}
            // Each rental space will have it's magnitude calculated, the most relavant perference to the rental space is shown first.
		function calMagnitude(){
			global $PREFBOROUGH, $PREFPRICE;
			include("../../db_configlogin.php");

			if (mysqli_connect_errno()) {
				print "Connect failed: ".mysqli_connect_error();
				exit();
			}

			$distance = 0;
			if($PREFBOROUGH != -1){ //if it's not any borough it will calculate the distance between two boroughs
				$result = mysqli_query($connection, "SELECT * FROM mtlborough WHERE BoroughID = '$this->borough'");
				$row = mysqli_fetch_assoc($result); //fetch longitude of this borough and the other

				$longitude_1 = $row['longitude'];
				
				$latidute_1 = $row['latitude'];

				$result = mysqli_query($connection, "SELECT * FROM mtlborough WHERE BoroughID = '$PREFBOROUGH'");
				$row = mysqli_fetch_assoc($result);

				$longitude_2 = $row['longitude'];
				$latidute_2 = $row['latitude'];

				$distance = sqrt(pow($longitude_1 - $longitude_2, 2) + pow($latidute_1 - $latidute_2, 2)); //calculate the distance from the preferred brough
			}
			$magPrice = 0; //if the price is over the preferred price, it will put a magnitude to the price.
			if($this->price > $PREFPRICE)
				$magPrice = $this->price - $PREFPRICE;
			$this->magnitude = sqrt(pow($magPrice, 2) + pow($distance, 2)); //setting the magnitude to the price. which will then be sorted
		}
	}

?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Search Listings - rentalmtl</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="../../css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="tenantsearch.css">
	<link rel="shortcut icon" href="../../logo_icon.ico">
</head>

<body>
<!--- NAVIGATION -->
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
									<li><a href="#">Owner Posting</a></li>
									<li><a href-"#">Owner Preferences</a></li>
									<li class="divider"></li>
						';
					if ($_SESSION['login_USERTYPE'] == 'tenant')
						print'
									<li><a href="../tenantprofile">Tenant Profile</a></li>
									<li><a href="">Search Listings</a></li>
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
	</div> <!--- END OF NAVIGATION -->

	<div class="container">
		<div class="well"> 
			<div class="row">
				<div class="col-md-12"><!-- SEARCH SETTINGS-->
				<!-- SEARCH SETTINGS -->
					<h3 style="display: block">Search Listings
						<button style="margin: 5px 0" type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#SEARCH_SETTINGS" aria-expanded="true" aria-controls="SEARCH_SETTINGS" onClick="toggleHTMLSEARCH()" id="btnSEARCHCOLLAPSE">Collapse Search Settings</button>
					</h3>
					<?php 
						//if tenant preferences exists in the data, then if will show these messages or the other
						if(!$TENANTPREF_EXISTS) print '<p style="color: red">You mush set your preferences before you can find a rental space.</p>';
						else print '<p style="color: green">Your preferences are set.</p>';
					?>
					<div id="SEARCH_SETTINGS" class="collapse in" style="margin-top:10px">
						<form  onSubmit="return validateSearch()" method="post" action="">
							<label>Preferred Pricing:</label>
							<div class="row">
								<div class="col-xs-6 col-md-3">
									<div name="form-validate" class="form-group">
										<div class="input-group">
											<div class="input-group-addon">$</div>
											<input type="number" class="form-control" id="PREFPRICE" name="PREFPRICE" placeholder="Preferred Price" min="0" value="<?php print $PREFPRICE;?>" required>
										</div>
									</div>
								</div>
							</div>
							<label for="BOROUGH">Preferred Borough</label>
								<div class="row">
									<div name="form-validate" class="form-group col-sm-6">
										<select class="form-control" id="BOROUGH" name="BOROUGH">
											<option value="-1" <?php if($PREFBOROUGH == -1) print 'selected';?>>Any</option>
											<?php 
												for ($i=0; $i < count($BOROUGHNAME); $i++) { 
													print '<option value="'.$i.'"'; if($i == $PREFBOROUGH) print 'selected'; print '>'.$BOROUGHNAME[$i].'</option>';
												}
											?>
										</select>
									</div>
								</div>
		                    <button type="submit" class="btn btn-primary" name="savepref">Save Preferences</button>
		                    <?php 
		                    	//if tenant preference exists in the database then t will show these buttons
		                    	if($TENANTPREF_EXISTS) print '
		                    		<button type="submit" class="btn btn-info" name="delpref">Delete Preferences</button><br>
		                    		<div style="margin-top: 10px;"></div>
		                    		<button type="submit" class="btn btn-success btn-lg" name="matchbtn">MATCH</button>
		                    	'; 
		                    ?>
							
						</form>
					</div>
				</div><!-- END OF SEARCH SETTINGS-->
			</div>
			<hr> <!-- LISTINGS RESULTS -->
					<?php
						if ($MATCHBTNPUSHED) {
							$result = mysqli_query($connection, "SELECT * FROM rentalspace");
							$num_rows = mysqli_num_rows($result);
							print '
							<div class="row">
								<div class="col-md-12">
									<h3>'.$num_rows.' Results Found</h3>
								</div>
							</div>
							<hr>
							';
							if($RentArray != null){
								for ($i=0; $i < count($RentArray) ; $i++) { 
									$PostID_RentalSpace = $RentArray[$i]->postId;
									$row = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM rentalspace WHERE PostID = '$PostID_RentalSpace'"));
									print'
										<div class="row">
											<div class="col-sm-3 col-lg-2">
												<img style="width: 100%;" src="../img/'.$row['image'].'" alt="Post Image" class="img-rounded">
											</div>
											<div class="col-sm-9 col-lg-10">
												<h3 class="post-title">'.stripslashes($row['title']).'</h3>
												<p>'.stripslashes($row['description']).'<br>';
													$PRIMEKEY = $row['MemberID'];
													$SQL2 = "SELECT * FROM user WHERE MemberID = '$PRIMEKEY'";
													$result2 = mysqli_query($connection, $SQL2);
													$num_rows2 = mysqli_num_rows($result2);
													$row2 = mysqli_fetch_assoc($result2);
													print 
													$row2['fname'].' '.$row2['lname'].' <strong>#</strong>: '.$row2['phonenum'].' <strong>@</strong>: '.$row2['email'].'<br>
													<strong>Address: </strong> '.stripslashes($row['address']).'<br>
													<strong>Borough: </strong> '.$BOROUGHNAME[$row['borough']].' | <strong>Price: </strong>'.$row['price'].'$ Per Month <br>
												</p>
											</div>
										</div>
										<hr>
									';
								}
							}
						}
					?>
			<!-- END OF LISTINGS RESULTS -->
			<!-- FOOTER -->
			<footer>
				<div class="row">
					<div class="col-md-12">
						<h4>Contact Us</h4>
						<p><small> Concordia University<br>
							1455 De Maisonneuve Blvd. W. <br>
							Montreal, Quebec, Canada <br>
							H3G 1M8 <br>
							Tel: 514-848-2424</small> </p>
					</div>
					<p class="text-center col-md-12"><small>© Copyright Laurendy Lam, 2014. All rights reserved.</small></p>
				</div>
			</footer>
		</div>
	</div>

	<script type="text/javascript">
	function toggleHTMLSEARCH(){
		var btn = document.getElementById("btnSEARCHCOLLAPSE");
		if(btn.innerHTML == "Collapse Search Settings")
			btn.innerHTML = "Uncollapse Search Settings";
		else
			btn.innerHTML = "Collapse Search Settings";
	}



	</script>
	<script src="../../script/validateform.js"></script>
	<script src="../../script/jquery-1.11.2.min.js"></script>
	<script src="../../script/bootstrap.min.js"></script>
</body>
</html>
