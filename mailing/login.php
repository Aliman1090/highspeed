<?php
date_default_timezone_set('Europe/Madrid');

define("RECAPTCHA_V3_SECRET_KEY", '6LejHlMgAAAAAAtdmwMwKG6d2tYEWkm58Vv6nL-t');

$myObj = new stdClass();



switch ($_POST['api']) { //strip_tags es como un filtro
    case "loginUser":
        checkCaptcha(sanitize($_POST['captcha']), $myObj);

        if (isset($myObj->success)) {
            // echo "BB";
            loginUser(sanitize($_POST['email']), sanitize($_POST['password']), $myObj);
        }

        break;
    default:
        $myObj->error = "error en switchcase";
        break;
}

echo json_encode($myObj);
function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, 'UTF-8');
}

function  checkCaptcha($captcha, $myObj)
{
    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
        //Do something with error 
        $myObj->error = "no recaptcha.";
    } else {
        if ($response->success == true && $response->score > 0.5) {
            $myObj->success = "eres humano.";
        } else if ($response->success == true && $response->score <= 0.5) {
            $myObj->error = "eres humano?";
        } else {
            $myObj->error = "NO eres humano?";
        }
    }
}


function loginUser($email, $password, $myObj)
{
    $usuario = new stdClass();
    $conn = new mysqli("sql4.freemysqlhosting.net", "sql4499635", "GvJjtAH8pC", "sql4499635");
    $sql = "SELECT nombre FROM usuarios WHERE email='" . $email . "' && password='" . md5($password) . "' ;";
    // echo $sql;
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $usuario->email = $email;
            $usuario->nombre = $row['nombre'];

            $usuario->token = md5(time() . "-" . $usuario->email);
            $sql_a = "UPDATE usuarios SET token='" . $usuario->token . "' WHERE email='" . $email . "';";
            $conn->query($sql_a); //$resultado_a=$conn->query($sql_a); 

            $myObj->success = json_encode($usuario);
            // $myObj->error = null;
        }
    } else {
        echo "Error: user not found.";
    }
    $conn->close();
}
