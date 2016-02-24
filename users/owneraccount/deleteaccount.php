<?php
	session_start();
	//DELETING ACCOUNT INFORMATION
	$ID = $_SESSION['login_MEMBERID'];
	
	if ($_SESSION['login_USERTYPE'] == 'tenant' || !isset($_SESSION['login_MEMBERID']))
		header("location: ../../");

	include("../../db_configlogin.php");
	
	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}
	
	mysqli_query($connection, "DELETE FROM ownersearch WHERE MemberID = '$ID'");
	mysqli_query($connection, "DELETE FROM user WHERE MemberID = '$ID'");
	mysqli_query($connection, "DELETE FROM rentalspace WHERE MemberID = '$ID'");
	
	include("../../logout.php");
?>