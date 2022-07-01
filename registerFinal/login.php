<?php
date_default_timezone_set('Europe/Madrid');

require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/admin.php";
$myObject = new stdClass();

function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, "UTF-8");
}

switch ($_POST['api']) {
    case "loginUser":
        checkCaptcha(sanitize($_POST['captcha']), $myObject);

        if (isset($myObject->success)) {
            loginUser(sanitize($_POST['email']), sanitize($_POST['password']), $myObject);
        }
        break;
    default:
        $myObject->error = "error en el switchcase";
        break;
}
echo json_encode($myObject);

function checkCaptcha($captcha, $myObject)
{

    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
        $myObject->error = "NO recaptcha<br>";
    } else {
        if ($response->success == true && $response->score > 0.5) {
            $myObject->success = "Human";
        } else if ($response->success == true && $response->score <= 0.5) {
            $myObject->error = "Human?<br>";
        } else {
            $myObject->error = "NO<br>";
        }
    }
}
function loginUser($email, $password, $myObject)
{
    $usuario = new stdClass();
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "SELECT nombre FROM usuarios WHERE email= '" . $email . "'&& password='" . md5($password) . "';";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {

            $usuario->email = $email;
            $usuario->nombre = $row['nombre'];
            $usuario->token = md5(time() . "-" . $usuario->email);
            $sql = "UPDATE usuarios SET token='" . $usuario->token . "' WHERE email= '" . $email . "';";
            $result_a = $conn->query($sql);
            $myObject->success = json_encode($usuario);
            break;
        }
    } else {
        echo 'Error:usuario not found';
    }
    $conn->close();
}
