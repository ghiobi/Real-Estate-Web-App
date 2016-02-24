<?php
	session_start();

	if ($_SESSION['login_USERTYPE'] == 'tenant' || !isset($_SESSION['login_MEMBERID']))
		header("location: ../../");
	
	include("../../db_configlogin.php");

	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}

	$ID = $_SESSION['login_MEMBERID'];

	$MIN_AGE = 18;
	$MAX_AGE = 99;
	$OCCUPATION = -1;
	$INCOME = -1;
	$PET = 'any';
	$SMOKES = 'any';
	
	//CHECK IF OWNER PREFERENCE EXISTS
	$OWNERPREF_EXISTS = false;
	$SQL = "SELECT * FROM ownersearch WHERE MemberID = '$ID'";
	$result = mysqli_query($connection, $SQL);
	$num_rows = mysqli_num_rows($result);

	if ($num_rows > 0) {
		$OWNERPREF_EXISTS = true;
		$row = mysqli_fetch_assoc($result);
		$MIN_AGE = $row['agemin'];
		$MAX_AGE = $row['agemax'];
		$OCCUPATION = $row['occupation'];
		$INCOME = $row['income'];
		$PET = $row['pet'];
		$SMOKES = $row['smoke'];
	}

	//SAVE PREFERENCE
	if(isset($_POST['savepref'])){
		savePreferences();
	 }
	
	//DELETLE PREFERENCES
	if(isset($_POST['delpref'])){
		if($OWNERPREF_EXISTS){
			mysqli_query($connection, "DELETE FROM ownersearch WHERE '$ID'");
			$OWNERPREF_EXISTS = false;
		}
	}
	
	$TenantArr = null;
	if(isset($_POST['matchbtn'])){
		savePreferences();
		$result = mysqli_query($connection, "SELECT * FROM tenantprofiles");
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		$TenantArr = array();
		for ($i=0; $i < $num_rows ; $i++) { 
			array_push($TenantArr, new tenant ($row['MemberID'], $row['age'], $row['occupation'], $row['income'], $row['smoker'], $row['pet']));
			$row = mysqli_fetch_assoc($result);
		}

		for ($i=0; $i < count($TenantArr); $i++) { //sorting magnitude
			$closest = $TenantArr[$i]->magnitude;
			for ($j=$i + 1; $j < count($TenantArr); $j++) { 
				if ($TenantArr[$j]->magnitude < $closest) {
					$closest = $TenantArr[$j]->magnitude;
					$temp = $TenantArr[$j];
					$TenantArr[$j] = $TenantArr[$i];
					$TenantArr[$i] = $temp;
				}
			}
		}
	}

	class tenant { //the tenant is use to sort the most relevance tenant by magnitude.

		public $MemberID;
		public $age;
		public $occupation;
		public $income;
		public $smoke;
		public $pet;
		public $magnitude;

		public function __construct($ID, $age, $occp, $inc, $smok, $pet){
			$this->MemberID = $ID;
			$this->age = $age;
			$this->occupation = $occp;
			$this->income = $inc;
			$this->smoke = $smok;
			$this->pet = $pet;
			$this->calMagnitude();
		}

		function calMagnitude(){ //Calculates the magnitude accoring to the the use preference, the lowest magnitude is the most relevant.
			global $MIN_AGE, $MAX_AGE, $OCCUPATION, $INCOME, $PET, $SMOKES;

			$magMINAGE = 0;
			if($this->age < $MIN_AGE)
				$magMINAGE = $MIN_AGE - $this->age;

			$magMAXAGE = 0;
			if ($this->age > $MAX_AGE) 
				$magMAXAGE = $this->age - $MAX_AGE;
			
			$magOCCP = 0;
			if ($OCCUPATION != -1) {
				if($this->occupation != $OCCUPATION)
				$magOCCP = 10;
			}
			
			$magINC = 0;
			if ($INCOME != -1) {
				if($this->income != $INCOME)
				$magINC = abs($INCOME - $this->income);
			}

			$magPET = 0;
			if($PET != 'any'){
				if($this->pet != $PET)
					$magPET = 100;
			}

			$magSMOKE = 0;
			if($SMOKES != 'any'){
				if($this->smoke != $SMOKES)
					$magSMOKE = 100;
			}

			$this->magnitude = sqrt(pow($magMINAGE, 2) + pow($magMAXAGE, 2) + pow($magOCCP, 2) + pow($magINC, 2) + pow($magPET, 2) + pow($magSMOKE, 2));
		}
	}
	function savePreferences(){
		global $MIN_AGE, $MAX_AGE, $OCCUPATION, $INCOME, $PET, $SMOKES, $connection, $OWNERPREF_EXISTS, $ID;
		$MIN_AGE = htmlspecialchars($_POST['MIN_AGE']);
	 	$MAX_AGE = htmlspecialchars($_POST['MAX_AGE']);
	 	$OCCUPATION = $_POST['OCCUPATION'];
	 	$INCOME = $_POST['INCOME'];
	 	$PET = $_POST['PET'];
	 	$SMOKES = $_POST['SMOKER_TYPE'];

	 	if ($OWNERPREF_EXISTS){
	 		$SQL = "UPDATE ownersearch SET agemin = '$MIN_AGE', agemax = '$MAX_AGE', occupation = '$OCCUPATION', income = '$INCOME', pet = '$PET', smoke = '$SMOKES' WHERE MemberID = '$ID'";
			mysqli_query($connection, $SQL);
		}
	 	else{
	 		$SQL = "INSERT INTO ownersearch (MemberID, agemin, agemax, occupation, income, pet, smoke) VALUES ('$ID','$MIN_AGE','$MAX_AGE','$OCCUPATION','$INCOME','$PET','$SMOKES')";
	 		mysqli_query($connection, $SQL);
	 		$OWNERPREF_EXISTS = true;
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
	<link rel="stylesheet" type="text/css" href="ownersearch.css">
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
									<li><a href="">Search Tenant</a></li>
									<li><a href="../owneraccount">Account Settings</a></li>
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
	</div>
	<div class="container">
		<div class="well">
			<div class="row">
				<div class="col-md-12">
					<h3 style="display: block">Search Listings
						<button style="margin: 5px 0" type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#SEARCH_SETTINGS" aria-expanded="true" aria-controls="SEARCH_SETTINGS" onClick="toggleHTMLSEARCH()" id="btnSEARCHCOLLAPSE">Collapse Search Settings</button>
					</h3>
					<?php if(!$OWNERPREF_EXISTS) print '<p style="color: red">You mush set your preferences before you can match with a tenant.</p>';
						else print '<p style="color: green">Your preferences are set.</p>';
					?>
					<div id="SEARCH_SETTINGS" class="collapse in" style="margin-top:10px">
						<form  onSubmit="return validateSearch()" method="post" action="">
							<label>Age:</label>
							<div class="row">
								<div class="col-xs-6 col-md-3">
									<div name="form-validate" class="form-group">
										<div class="input-group">
											<div class="input-group-addon">Min</div>
											<input type="number" class="form-control" id="MIN_AGE" name="MIN_AGE" placeholder="Minimum" min="18" value="<?php print $MIN_AGE;?>" required>
										</div>
									</div>
								</div>
								<div class="col-xs-6 col-md-3">
									<div name="form-validate" class="form-group">
										<div class="input-group">
											<div class="input-group-addon">Max</div>
											<input type="number" class="form-control" id="MAX_AGE" name="MAX_AGE" placeholder="Maximum" min="18" max="99"value="<?php print $MAX_AGE;?>" required>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div name="form-validate" class="form-group">
		                        	<label for="OCCUPATION" class="col-md-2 control-label">Occupation:</label>
		                            <div class="col-md-4">
		                            	<select class="form-control" name="OCCUPATION" id="OCCUPATION">
		                            		<option value="-1" <?php if($OCCUPATION == -1) print 'selected'?>>Any</option>
											<option value="0" <?php if($OCCUPATION == 0) print 'selected'?>>Student</option>
											<option value="1" <?php if($OCCUPATION == 1) print 'selected'?>>Health</option>
											<option value="2" <?php if($OCCUPATION == 2) print 'selected'?>>Law</option>
											<option value="3" <?php if($OCCUPATION == 3) print 'selected'?>>Engineering</option>
											<option value="4" <?php if($OCCUPATION == 4) print 'selected'?>>Research and Sciences</option>
											<option value="5" <?php if($OCCUPATION == 5) print 'selected'?>>Sales</option>
											<option value="6" <?php if($OCCUPATION == 6) print 'selected'?>>Entertainment</option>
											<option value="7" <?php if($OCCUPATION == 7) print 'selected'?>>Arts</option>
											<option value="8" <?php if($OCCUPATION == 8) print 'selected'?>>Other</option>
											<option value="9" <?php if($OCCUPATION == 9) print 'selected'?>>None</option>
		                            	</select>
		                            </div>
	                        	</div>
							</div>
							
							<div class="row">
		                        <div name="form-validate" class="form-group">
		                        	<label for="INCOME" class="col-md-2 control-label">Income:</label>
		                        	<div class="col-md-4">
		                        		<select class="form-control" name="INCOME" id="INCOME">
		                        			<option value="-1" <?php if($INCOME == -1) print 'selected'?>>Any</option>
											<option value="0" <?php if($INCOME == 0) print 'selected'?>>Less than $15,000</option>
											<option value="1" <?php if($INCOME == 1) print 'selected'?>>$15,000 - $30,000</option>
											<option value="2" <?php if($INCOME == 2) print 'selected'?>>$30,001 - $45,000</option>
											<option value="3" <?php if($INCOME == 3) print 'selected'?>>$45,001 - $60,000</option>
											<option value="4" <?php if($INCOME == 4) print 'selected'?>>$60,001 - $75,000</option>
											<option value="5" <?php if($INCOME == 5) print 'selected'?>>$75,001 - $90,000</option>
											<option value="6" <?php if($INCOME == 6) print 'selected'?>>$90,001 - $200,000</option>
											<option value="7" <?php if($INCOME == 7) print 'selected'?>>$200,001 and beyond</option>
		                        		</select>
		                        	</div>
		                        </div>
	                        </div>
	                        <div class="row">
		                     	<div name="form-validate" class="form-group">
		                        	<label for="PET_OWNAGE" class="col-md-2 control-label">Own a pet?:</label>
	                                <div id="PET_OWNAGE" class="col-md-4">
										<label class="radio-inline">
											<input type="radio" name="PET" id="PET_YES" value="yes" <?php if($PET == 'yes') print 'checked'?>>Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="PET" id="PET_NO" value="no" <?php if($PET == 'no') print 'checked'?>>No
										</label>
										<label class="radio-inline">
											<input type="radio" name="PET" id="PET_ANY" value="any" <?php if($PET == 'any') print 'checked'?>>Any
										</label>
	                                </div>
		                        </div>
	                        </div>
	                        <div class="row">
		                        <div name="form-validate" class="form-group">
		                        	<label for="SMOKER" class="col-md-2 control-label">Is a smoker?:</label>
		                            <div id="SMOKER" class="col-md-4">
		                            	<label class="radio-inline">
											<input type="radio" name="SMOKER_TYPE" id="SMOKER_YES" value="yes"  <?php if($SMOKES == 'yes') print 'checked'?>>Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="SMOKER_TYPE" id="SMOKER_NO" value="no" <?php if($SMOKES == 'no') print 'checked'?>>No
										</label>
										<label class="radio-inline">
											<input type="radio" name="SMOKER_TYPE" id="SMOKER_ANY" value="any" <?php if($SMOKES == 'any') print 'checked'?>>Any
										</label>
		                            </div>
		                        </div>
		                    </div>
		                    <button type="submit" class="btn btn-primary" name="savepref">Save Preferences</button>

		                    <?php 
		                    //show these buttons if the owner preferences exist
		                    	if($OWNERPREF_EXISTS) print '
		                    		<button type="submit" class="btn btn-info" name="delpref">Delete Preferences</button><br>
		                    		<div style="margin-top: 10px;"></div>
		                    		<button type="submit" class="btn btn-success btn-lg" name="matchbtn">MATCH</button>
		                    	'; 
		                    ?>
							
						</form>
					</div>
				</div>
			</div>
			<hr>
					<?php
						if (isset($_POST['matchbtn'])) {
							$result = mysqli_query($connection, "SELECT * FROM tenantprofiles");
							$num_rows = mysqli_num_rows($result);
							print '
							<div class="row">
								<div class="col-md-12">
									<h3>'.$num_rows.' Results Found</h3>
								</div>
							</div>
							<hr>
							';
							$OCCUPATION_LIST = array('Student', 'Health', 'Law', 'Engineering', 'Research and Sciences', 'Sales', 'Entertainment', 'Arts', 'Other', 'None');
							$INCOME_LIST = array('Less than $15,000', '$15,000 - $30,000', '$30,001 - $45,000', '$45,001 - $60,000', '$60,001 - $75,000', '$75,001 - $90,000', '$90,001 - $200,000', '$200,001 and beyond');
							//for each tenant in the TanantArray it will print out the tenant's information accord to the order
							if ($TenantArr != null && count($TenantArr) != 0) {
								foreach ($TenantArr as $TA) { 
									$TenantID = $TA->MemberID;
									$row = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM tenantprofiles WHERE MemberID = '$TenantID'"));
									$PRIMEKEY = $row['MemberID'];
									$row2 = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM user WHERE MemberID = '$PRIMEKEY'"));
									print'
										<div class="row">
											<div class="col-sm-9 col-lg-10">
												<h3 class="post-title">'.$row2['fname'].' '.$row2['lname'].'</h3>
												<p>
													Age: '.$row['age'].' | PET?: '.$row['pet'].' | Smoker?: '.$row['smoker'].'<br>
													Occupation: '.$OCCUPATION_LIST[$row['occupation']].'<br>
													Income: '.$INCOME_LIST[$row['income']].'<br>
													<strong>#</strong>: '.$row2['phonenum'].' <strong>@</strong>: '.$row2['email'].'
												</p>
												<hr>
											</div>
										</div>
									';
								}
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
							Tel: 514-848-2424</small> </p>
					</div>
					<p class="text-center col-md-12"><small>© Copyright Laurendy Lam, 2014. All rights reserved.</small></p>
				</div>
			</footer>
		</div>
	</div>
	<script type="text/javascript">
	function checkAll(checktoggle){
		var checkboxes = document.getElementsByName('BOROUGHNAME[]');

		for (var i=0; i<checkboxes.length; i++)  {
			checkboxes[i].checked = checktoggle;
		}
	}
	
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
