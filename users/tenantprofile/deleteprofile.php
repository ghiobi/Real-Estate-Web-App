<?php
	session_start();
	
	//DELETNG PROFILE
	$ID = $_SESSION['login_MEMBERID'];
	if ($_SESSION['login_USERTYPE'] == 'owner' || !isset($_SESSION['login_MEMBERID']) )
	header("location: ../../");
	
	include("../../db_configlogin.php");
	//deleting profile
	mysqli_query($connection, "DELETE FROM tenantprofiles WHERE MemberID = '$ID'");
	
	mysqli_close($connection);
	header("location: index.php");
?>