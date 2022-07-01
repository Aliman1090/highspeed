"use strict";
const boton = document.getElementById("buttonContacta");
boton.addEventListener("click", contactarUsuario);

const inputName = document.getElementById("nameContacta");
const inputFirstName = document.getElementById("apellidoContacta");
const inputPhone = document.getElementById("phoneContacta");
const inputEmail = document.getElementById("emailContacta");
const inputAsunto = document.getElementById("asuntoContact");
const inputTextarea = document.getElementById("textAreaContacta");

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

function contactarUsuario() {
	let inputName_valor = inputName.value;
	let inputFirstName_valor = inputFirstName.value;
	let inputPhone_valor = inputPhone.value;
	let inputEmail_valor = inputEmail.value;
	let inputAsunto_valor = inputAsunto.value;
	let inputTextarea_valor = inputTextarea.value;

	let nameBoolean = true;
	let firstNameBoolean = true;
	let phoneBoolean = true;
	let emailBoolean = true;
	let asuntoBoolean = true;
	let textareaBoolean = true;

	if (inputName_valor == "" && !isNaN(inputName_valor)) {
		nameBoolean = false;
	}
	/* hacer regex xa todos en JS y luego poner el mismo eh PHP */
	if (inputName_valor.length < 2) {
		nameBoolean = false;
	}
	if (inputFirstName_valor == "" && !isNaN(inputFirstName_valor)) {
		firstNameBoolean = false;
	}
	if (inputFirstName_valor.length < 2) {
		firstNameBoolean = false;
	}
	if (inputPhone_valor == "" && isNaN(inputPhone_valor)) {
		phoneBoolean = false;
	}
	if (inputEmail_valor == "" && !isNaN(inputEmail_valor)) {
		emailBoolean = false;
	}
	if (inputAsunto_valor == "" && !isNaN(inputAsunto_valor)) {
		asuntoBoolean = false;
	}
	if (inputTextarea_valor == "" && !isNaN(inputTextarea_valor)) {
		textareaBoolean = false;
	}

	$.ajax({
		url: "./contacta/contacta.php",
		type: "POST",
		data: {
			api: "checkEmail",
			email: inputEmail_valor,
			nombre: inputName_valor,
			apellido: inputFirstName_valor,
			phone: inputPhone_valor,
			asunto: inputAsunto_valor,
			textarea: inputTextarea_valor,
			captcha: document.getElementById("g-recaptcha-response").value,
		},
		dataType: "json", //esta quitado xq hola NO ES UN JSON es texto plano
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
					phoneBoolean,
					asuntoBoolean,
					textareaBoolean
				);
			}
		},
		error: function (error) {
			console.log("ERROR" + error);
			emailBoolean = false;
			coloresCampo(
				nameBoolean,
				firstNameBoolean,
				emailBoolean,
				phoneBoolean,
				asuntoBoolean,
				textareaBoolean
			);
		},
	});
}

function coloresCampo(
	nameBoolean,
	firstNameBoolean,
	emailBoolean,
	phoneBoolean,
	asuntoBoolean,
	textareaBoolean
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
	if (phoneBoolean) {
		inputPhone.classList.remove("inputError");
		inputPhone.classList.add("inputSucces");
	} else {
		inputPhone.classList.remove("inputSucces");
		inputPhone.classList.add("inputError");
	}
	if (asuntoBoolean) {
		inputAsunto.classList.remove("inputError");
		inputAsunto.classList.add("inputSucces");
	} else {
		inputAsunto.classList.remove("inputSucces");
		inputAsunto.classList.add("inputError");
	}
	if (textareaBoolean) {
		inputTextarea.classList.remove("inputError");
		inputTextarea.classList.add("inputSucces");
	} else {
		inputTextarea.classList.remove("inputSucces");
		inputTextarea.classList.add("inputError");
	}
}
document.getElementById("limpiar").addEventListener("click", LimpiarForm);
function LimpiarForm() {
	document.getElementById("nameContacta").value = "";
	document.getElementById("apellidoContacta").value = "";
	document.getElementById("phoneContacta").value = "";
	document.getElementById("emailContacta").value = "";
	document.getElementById("asuntoContact").value = "";
	document.getElementById("textAreaContacta").value = "";
}
