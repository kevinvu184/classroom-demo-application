var markRegex = /^(?:[1-9]|0[1-9]|10)$/;
function markValidation() {
	var validation = true;
	var inputs = document.getElementsByTagName('input');
	for (let index = 0; index < inputs.length; ++index) {
		if (!markRegex.test(inputs[index].value)) {
			validation = false;
			document.getElementById(inputs[index].name).innerHTML = '<small class="form-text text-danger">Please input a number between 1-10</small>';
		}else{
            document.getElementById(inputs[index].name).innerHTML = '';
        }
	}
	return validation;
}

function closeModal() {
    document.getElementById("modal").style.display = 'none';
}

function openRegister(){
  window.open("./signup.php");
}
