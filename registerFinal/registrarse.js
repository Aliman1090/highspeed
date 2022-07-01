"use strict";
const boton = document.getElementById("buttonRegistro");
boton.addEventListener("click", registrarUsuario);

const inputName = document.getElementById("nameSignUp");
const inputFirstName = document.getElementById("firstNameSignUp");
const inputEmail = document.getElementById("emailSignUp");
const inputPassword = document.getElementById("passwordSignUp");
const inputPhone = document.getElementById("phoneSignUp");
const inputDni = document.getElementById("dniSignUp");

grecaptcha.ready(function () {
	grecaptcha
		.execute("6LepHlMgAAAAAPTY7N2X6M7AkmJL7v3Dv5S86Ywx", {
			action: "validate_captcha",
		})
		.then(function (token) {
			document.getElementById("g-recaptcha-response").value = token;
			checkRecaptcha();
		});
});

function checkRecaptcha() {
	let inputCaptchat_valor = document.getElementById(
		"g-recaptcha-response"
	).value;
	console.log(inputCaptchat_valor);
	if (inputCaptchat_valor != "") {
		boton.disabled = false;
		return true;
	}
	return false;
}

function registrarUsuario() {
	let inputName_valor = inputName.value;
	let inputFirstName_valor = inputFirstName.value;
	let inputEmail_valor = inputEmail.value;
	let inputPassword_valor = inputPassword.value;
	let inputPhone_valor = inputPhone.value;
	let inputDni_valor = inputDni.value;

	let nameBoolean = true;
	let firstNameBoolean = true;
	let emailBoolean = true;
	let passwordBoolean = true;
	let phoneBoolean = true;
	let dniBoolean = true;

	if (inputName_valor == "" && !isNaN(inputName_valor)) {
		nameBoolean = false;
	}
	if (inputName_valor.length < 2) {
		nameBoolean = false;
	}
	if (inputFirstName_valor == "" && !isNaN(inputFirstName_valor)) {
		firstNameBoolean = false;
	}
	if (inputFirstName_valor.length < 2) {
		firstNameBoolean = false;
	}
	if (inputEmail_valor == "" && !isNaN(inputEmail_valor)) {
		emailBoolean = false;
	}
	if (inputPassword_valor == "" && !isNaN(inputPassword_valor)) {
		passwordBoolean = false;
	}
	if (inputPhone_valor == "" && isNaN(inputPhone_valor)) {
		phoneBoolean = false;
	}
	if (
		(inputDni_valor == "" && !isNaN(inputDni_valor)) ||
		!funcionDni(inputDni_valor)
	) {
		dniBoolean = false;
	}

	$.ajax({
		url: "./registrarse.php",
		type: "POST",
		data: {
			api: "checkEmail",
			email: inputEmail_valor,
			nombre: inputName_valor,
			apellido: inputFirstName_valor,
			phone: inputPhone_valor,
			dni: inputDni_valor,
			password: inputPassword_valor,
			captcha: document.getElementById("g-recaptcha-response").value,
		},
		dataType: "json",
		success: function (response) {
			if (response == 0) {
				console.warn(response);
			} else {
				console.log(response);
				if ("error" in response) {
					console.warn("ERROR");
					emailBoolean = false;
				} else {
					console.warn("TODO OK");
					emailBoolean = true;
				}
				coloresCampo(
					nameBoolean,
					firstNameBoolean,
					emailBoolean,
					passwordBoolean,
					phoneBoolean,
					dniBoolean
				);
			}
		},
		error: function (error) {
			console.log("ERROR");
			console.log(error);
			emailBoolean = false;
			coloresCampo(
				nameBoolean,
				firstNameBoolean,
				emailBoolean,
				passwordBoolean,
				phoneBoolean,
				dniBoolean
			);
		},
	});
}

function coloresCampo(
	nameBoolean,
	firstNameBoolean,
	emailBoolean,
	passwordBoolean,
	phoneBoolean,
	dniBoolean
) {
	if (nameBoolean) {
		inputName.classList.remove("inputError");
		inputName.classList.add("inputSucces");
	} else {
		inputName.classList.remove("inputSucces");
		inputName.classList.add("inputError");
	}
	if (firstNameBoolean) {
		inputFirstName.classList.remove("inputError");
		inputFirstName.classList.add("inputSucces");
	} else {
		inputFirstName.classList.remove("inputSucces");
		inputFirstName.classList.add("inputError");
	}
	if (emailBoolean) {
		inputEmail.classList.remove("inputError");
		inputEmail.classList.add("inputSucces");
	} else {
		inputEmail.classList.remove("inputSucces");
		inputEmail.classList.add("inputError");
	}

	if (passwordBoolean) {
		inputPassword.classList.remove("inputError");
		inputPassword.classList.add("inputSucces");
	} else {
		inputPassword.classList.remove("inputSucces");
		inputPassword.classList.add("inputError");
	}

	if (phoneBoolean) {
		inputPhone.classList.remove("inputError");
		inputPhone.classList.add("inputSucces");
	} else {
		inputPhone.classList.remove("inputSucces");
		inputPhone.classList.add("inputError");
	}
	if (dniBoolean) {
		inputDni.classList.remove("inputError");
		inputDni.classList.add("inputSucces");
	} else {
		inputDni.classList.remove("inputSucces");
		inputDni.classList.add("inputError");
	}
}
function funcionDni($dni) {
	let $numero;
	let $letr;
	let $letra;
	let $expresion_regular_dni;

	$expresion_regular_dni = /^\d{8}[a-zA-Z]$/;

	if ($expresion_regular_dni.test($dni) == true) {
		$numero = $dni.substr(0, $dni.length - 1);
		$letr = $dni.substr($dni.length - 1, 1);
		$numero = $numero % 23;
		$letra = "TRWAGMYFPDXBNJZSQVHLCKET";
		$letra = $letra.substring($numero, $numero + 1);

		if ($letra != $letr.toUpperCase()) {
			console.log("Dni erroneo");
		} else {
			console.log("Dni correcto");
			return true;
		}
	}
	return false;
}
//assert
console.log("holaaaaa");
console.assert(funcionDni("43555458j") == true);
console.assert(funcionDni("43555499j") == false);
console.assert(funcionDni("43555499") == false);
console.assert(funcionDni("4355599j") == false);
console.assert(funcionDni("43499j") == false);
