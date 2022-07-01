"use strict";
const boton = document.getElementById("buttonRegistro");
boton.addEventListener("click", registrarUsuario);

const inputEmail = document.getElementById("emailSignUp");
const inputPassword = document.getElementById("passwordSignUp");

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
	let inputEmail_valor = inputEmail.value;
	let inputPassword_valor = inputPassword.value;

	let emailBoolean = true;
	let passwordBoolean = true;

	if (inputEmail_valor == "" && !isNaN(inputEmail_valor)) {
		emailBoolean = false;
	}
	if (inputPassword_valor == "" && !isNaN(inputPassword_valor)) {
		passwordBoolean = false;
	}

	$.ajax({
		url: "./login.php",
		type: "POST",
		data: {
			api: "loginUser",
			email: inputEmail_valor,
			password: inputPassword_valor,
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
					crearCookie(response.success);
				}
				coloresCampo(emailBoolean, passwordBoolean);
			}
		},
		error: function (error) {
			console.log("ERROR" + error);
			emailBoolean = false;
			coloresCampo(emailBoolean, passwordBoolean);
		},
	});
}

function coloresCampo(emailBoolean, passwordBoolean) {
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
}
function crearCookie(user) {
	user = JSON.parse(user);
	var exdays = 30;
	var d = new Date();
	d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
	var expires = "expires" + d.toUTCString();
	document.cookie = "email" + "=" + user.email + ";" + expires + ";path=/";
	document.cookie = "nombre" + "=" + user.nombre + ";" + expires + ";path=/";
	document.cookie = "token" + "=" + user.token + ";" + expires + ";path=/";
	window.location.replace("../index.html");
}

function getCookie(cname) {
	let name = cname.trim() + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(";");
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == " ") {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
