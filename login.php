<?php
	//GETTING USERNAME AND PASSWORD
	$username = $_POST['username'];
	$password = $_POST['password'];

	include("db_configlogin.php");

	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
	}

	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);

	$result = mysqli_query($connection, "SELECT * FROM user WHERE BINARY uname = '$username'AND pword = md5('$password')");

	if (!$result) {
		print "Error - the query could not be executed";
		exit;
	}
	//INSERT LOGING SESSION INFORMATION
	$num_rows = mysqli_num_rows($result);
	if ($result && $num_rows > 0) {
		$row = mysqli_fetch_assoc($result);
		$_SESSION['login_MEMBERID'] = $row['MemberID'];
		$_SESSION['login_FIRSTNAME'] = $row['fname'];
		$_SESSION['login_LASTNAME'] = $row['lname'];
		$_SESSION['login_USERTYPE'] = $row['usertype'];
	}
	else{
		$error ="Invalid Username or Password";
	}
	mysqli_close($connection);

?>