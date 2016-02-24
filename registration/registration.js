function validateRegistration(){

	var formgroup = document.getElementsByName("form-validate");
	for (var i = 0; i < formgroup.length; i++){
		formgroup[i].className = "form-group";
	}
	
	var onSubmit = true;
	
	if(-1 == document.getElementById("FIRST_NAME").value.search(/^[A-z]+[\-[A-z]+?]*$/g)){
		formgroup[0].className = "form-group has-error";
		onSubmit = false;
	}

	if(-1 == document.getElementById("LAST_NAME").value.search(/^[A-z]+[\-[A-z]+?]*$/g)){
		formgroup[1].className = "form-group has-error";
		onSubmit = false;
	}

	var USERTYPE = document.getElementsByName("USERTYPE");
	if(!USERTYPE[0].checked && !USERTYPE[1].checked){
		formgroup[2].className = "form-group has-error";
		onSumit = false;	
	}

	if(-1 == document.getElementById("PHONE_NUMBER").value.search(/^\(\d{3}\)\d{3}[-]\d{4}$/)){
		formgroup[3].className = "form-group has-error";
		onSubmit = false;
	}

	if(-1 == document.getElementById("EMAIL").value.search(/^\w+[@][A-z]+\.[A-z]+$/)){
		formgroup[4].className = "form-group has-error";
		onSubmit = false;
	}

	if(-1 == document.getElementById("USERNAME").value.search(/^[A-z0-9]{6,}$/)){
		formgroup[5].className = "form-group has-error";
		onSubmit = false;
	}

	var PASS1 = document.getElementById("PASSWORD").value;
	var PASS2 = document.getElementById("CONFIRM_PASS").value;
	if (!checkPassword(PASS1) && !checkPassword(PASS2) || (PASS1 != PASS2)){
		formgroup[6].className = "form-group has-error";
		formgroup[7].className = "form-group has-error";
		onSubmit = false;
	}

	return onSubmit;
}

function checkPassword(str){
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
    return re.test(str);
}