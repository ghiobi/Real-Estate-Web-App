<?php

	session_start();

	$ID = $_SESSION['login_MEMBERID'];

	if ($_SESSION['login_USERTYPE'] == 'owner' || !isset($_SESSION['login_MEMBERID']) )
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
	
	//RETRIEVING PROFILE SETTINGS
	$AGE = "";
	$OCCUPATION = 10;
	$INCOME = 10;
	$PET = "";
	$SMOKER = "";
	
	//SEEING IF PROFILE EXISTS TO RETRIEVE INFORMATION
	$doesPROFILEEXIST = false;
	$SQL = "SELECT * FROM tenantprofiles WHERE MemberID = '$ID'";
	$result = mysqli_query($connection, $SQL);
	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0){
		$row = mysqli_fetch_assoc($result);
		$AGE = $row['age'];
		$OCCUPATION = $row['occupation'];
		$INCOME = $row['income'];
		$PET = $row['pet'];
		$SMOKER = $row['smoker'];
		$doesPROFILEEXIST = true;
	} 
	
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

	
	//UPDATE PROFILE SETTINGS
	$ProfileUPDATED = false;
	if(isset($_POST['submit1'])){

		//RETRIVING DATA INPUT
		$FULLNAME = $_SESSION['login_FIRSTNAME'].' '.$_SESSION['login_LASTNAME'];
		$AGE = htmlspecialchars($_POST['AGE']);
		$OCCUPATION = $_POST['OCCUPATION'];
		$INCOME = $_POST['INCOME'];
		$PET = $_POST['PET'];
		$SMOKER = $_POST['SMOKER_TYPE'];

		//SEEING IF PROFILE EXISTS
		$SQL = "SELECT * FROM tenantprofiles WHERE MemberID = '$ID'";
		$result = mysqli_query($connection, $SQL);

		$num_rows = mysqli_num_rows($result);
		//INSERT OR UPDATE PROFILE
		if($num_rows > 0){
			$SQL = "UPDATE tenantprofiles SET age = '$AGE', occupation = '$OCCUPATION', income ='$INCOME', pet ='$PET', smoker = '$SMOKER' WHERE MemberID = '$ID'";
			mysqli_query($connection, $SQL);
		}
		else {
			$SQL = "INSERT INTO tenantprofiles (MemberID, age, occupation, income, pet, smoker) VALUES ('$ID','$AGE','$OCCUPATION','$INCOME','$PET','$SMOKER')";
			mysqli_query($connection, $SQL);
		}
		$doesPROFILEEXIST = true;
		$ProfileUPDATED = true;
	}
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>User Profile - rentalmtl</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" >
	<link href="../../css/stylesheet.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="tenantprofile.css">
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
									<li><a href="#">Owner Posting</a></li>
									<li><a href-"#">Owner Preferences</a></li>
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
					<div class="col-md-7">
						<?php
							if($ProfileUPDATED){
								print '
								<div class="row" id="ToggleButton">
									<div class="col-md-12">
										<button type="btn btn-success col-md-12" style="width:100%" class="btn btn-success" onClick="ToggleSuccess()">
											Your profile has been updated! Click to dismiss!
										</button>
									</div>
								</div>
								';
							}
						?>
						<h3>Current Profile Settings</h3>
						<p style="margin-top:3px">
							<?php 
								if (!$doesPROFILEEXIST) print'<span style="color:#D43F3A">Your profile does not exist, please create one.</span><br>';
								else print '<span style="color:#04B45F">Your profile has been created or exists.</span><br>';
							?>
							Once you set or update your profile, owners will be able to contact you. Delete your profile to prevent home owners from searching you.
						</p>
						<form class="form-horizontal" method="post" onSubmit="return validateprofile()" action="">
	                        <div name="form-validate" class="form-group">
	                        	<label for="AGE" class="col-md-3 control-label">Age:</label>
	                        	<div class="col-md-6">
	                        		<input type="number" class="form-control" id="AGE" name="AGE" placeholder="Please enter your age." min="18" max="99" value="<?php print $AGE;?>">
	                        	</div>
	                        </div>
							<div name="form-validate" class="form-group">
	                        	<label for="OCCUPATION" class="col-md-3 control-label">Occupation:</label>
	                            <div class="col-md-6">
	                            	<select class="form-control" name="OCCUPATION" id="OCCUPATION">
	                            		<option></option>
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
	                        <div name="form-validate" class="form-group">
	                        	<label for="INCOME" class="col-md-3 control-label">Income:</label>
	                        	<div class="col-md-6">
	                        		<select class="form-control" name="INCOME" id="INCOME">
	                        			<option></option>
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
	                     	<div name="form-validate" class="form-group">
	                        	<label for="PET_OWNAGE" class="col-md-3 control-label">Own a pet?:</label>
	                                  <div id="PET_OWNAGE" class="col-md-9">
	                                      <label class="radio-inline">
	                                        <input type="radio" name="PET" id="PET_YES" value="yes" <?php if($PET == 'yes') print 'checked'?>>Yes
	                                      </label>
	                                      <label class="radio-inline">
	                                        <input type="radio" name="PET" id="P" value="no" <?php if($PET == 'no') print 'checked'?>>No
	                                      </label>
	                                </div>
	                        </div>
	                        <div name="form-validate" class="form-group">
	                        	<label for="SMOKER" class="col-md-3 control-label">Do you smoke?:</label>
	                            <div id="SMOKER" class="col-md-9">
	                            	<label class="radio-inline">
										<input type="radio" name="SMOKER_TYPE" id="SMOKER_YES" value="yes"  <?php if($SMOKER == 'yes') print 'checked'?>>Yes
									</label>
									<label class="radio-inline">
										<input type="radio" name="SMOKER_TYPE" id="SMOKER_NO" value="no" <?php if($SMOKER == 'no') print 'checked'?>>No
									</label>
	                            </div>
	                        </div>
	                        <div class="form-group">
		                        <div class="col-md-9 col-md-offset-3">
			                        <button type="submit" class="btn btn-default" name="submit1">Set or Update Profile</button>
			                        <?php if ($doesPROFILEEXIST) print '<a class="btn btn-info" id="DELETEPROFILEBTN" href="deleteprofile.php">Delete Profile</a>';?>
		                        </div>
	                        </div>
	                    </form>
					</div>
					<div class="col-md-5">
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
						    <button type="submit" class="btn btn-default" name="submit2">Update Contact Information</button>
						</form>
						<h3>Delete Account</h3>
							<a class="btn btn-danger" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Click Here To Delete Account
							</a>
							<div class="collapse" id="collapseExample">
								<div class="well" style="padding:10px; width:70%">
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
	<script src="tenantprofile.js"></script>
	<script src="../../script/validateform.js"></script>
	<script src="../../script/jquery-1.11.2.min.js"></script>
	<script src="../../script/bootstrap.min.js"></script>
</body>
</html>
