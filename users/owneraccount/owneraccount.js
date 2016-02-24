function validateprofile(){
	var formgroup = document.getElementsByName("form-validate");
	for (var i = 0; i < formgroup.length; i++){
		formgroup[i].className = "form-group";
	}

	var onSubmit = true;

	if(document.getElementById("AGE").value === ''){
		formgroup[0].className = "form-group has-error";
		onSubmit = false;
	}

	var aselect = [1, 2];
	var select = document.getElementsByTagName('select');
	for (var i = 0; i < select.length; i++) {
		if(select[i].selectedIndex == 0){
			formgroup[aselect[i]].className = "form-group has-error";
			onSubmit = false;
		}
	}

	var pet = document.getElementsByName('PET');
	if(!pet[0].checked  && !pet[1].checked){
			formgroup[3].className = "form-group has-error";
			onSubmit = false;
	}

	var smoke = document.getElementsByName('SMOKER_TYPE');
	if(!smoke[0].checked  && !smoke[1].checked){
			formgroup[4].className = "form-group has-error";
			onSubmit = false;
	}

	return onSubmit;
}

function validateChange(){

	var formgroup = document.getElementsByName("form-validate1");
	for (var i = 0; i < formgroup.length; i++){
		formgroup[i].className = "form-group";
	}
	
	if(-1 == document.getElementById("FIRST_NAME").value.search(/^[A-z]+[\-[A-z]+?]*$/g)){
		formgroup[0].className = "form-group has-error";
		onSubmit = false;
	}

	if(-1 == document.getElementById("LAST_NAME").value.search(/^[A-z]+[\-[A-z]+?]*$/g)){
		formgroup[1].className = "form-group has-error";
		onSubmit = false;
	}
	var onSubmit = true;
	if(-1 == document.getElementById("PHONE_NUMBER").value.search(/^\(\d{3}\)\d{3}[-]\d{4}$/)){
		formgroup[2].className = "form-group has-error";
		onSubmit = false;
	}

	if(-1 == document.getElementById("EMAIL").value.search(/^\w+[@][A-z]+\.[A-z]+$/)){
		formgroup[3].className = "form-group has-error";
		onSubmit = false;
	}
	return onSubmit;
}

function ToggleSuccess(){
	document.getElementById("ToggleButton").style.display = "none";
}