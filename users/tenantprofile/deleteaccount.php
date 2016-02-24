<?php
	session_start();
	
	$ID = $_SESSION['login_MEMBERID'];
	
	if ($_SESSION['login_USERTYPE'] == 'owner' || !isset($_SESSION['login_MEMBERID']))
	header("location: ../../");

	include("../../db_configlogin.php");
	
	if (mysqli_connect_errno()) {
		print "Connect failed: ".mysqli_connect_error();
		exit();
	}
	
	mysqli_query($connection, "DELETE FROM user WHERE MemberID = $ID");
	mysqli_query($connection, "DELETE FROM tenantprofiles WHERE MemberID = $ID");
	
	include("../../logout.php");
?>