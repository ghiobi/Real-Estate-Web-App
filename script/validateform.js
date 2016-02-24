function Validate(){
	var val = true;
	var input = document.getElementsByTagName("input");
	for ( var i = 0; i < 2; i++){
		input[i].style.borderColor = "grey";
		input[i].style.boxShadow = "none";
	}	
	for ( var i = 0; i < 2; i++){
		if(input[i].value.length == 0){
			val =false;
			input[i].style.borderColor = "red";
			input[i].style.boxShadow = "0 0 2px red";
		}
	}	
	return val;
}