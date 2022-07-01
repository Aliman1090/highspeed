<?php

date_default_timezone_set('Europe/Madrid');

require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/admin.php";

$myObject = new stdClass();

function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, "UTF-8");
}
switch ($_POST['api']) {
    case "checkEmail":
        $email = sanitize($_POST['email']);
        checkEmail($email, $myObject);
        if (isset($myObject->success)) {
            insertUser($email, sanitize($_POST['nombre']), sanitize($_POST['apellido']), sanitize($_POST['phone']), sanitize($_POST['dni']), sanitize($_POST['password']), sanitize($_POST['captcha']), $myObject);
        }
        break;
    default:
        $myObject->error = "error en el switchcase";
        break;
}
echo json_encode($myObject);
function checkEmail($email, $myObject)
{

    $email = strtolower(str_replace(" ", "", trim($email)));
    if ($email == "" || is_numeric($email)) {
        $myObject->error =  "PHP lado servidor: el mail no puede estar vacio o es valor numerico";
    }

    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);

    $sql = "SELECT email FROM usuarios WHERE email= '" . $email . "';";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $myObject->error = "YA EXISTE: vacio - " . $row['email'];
        }
    } else {
        $myObject->success = "PHP: lado servidor : mail is OK";
    }
    $conn->close();
}
function insertUser($email, $nombre, $apellido, $phone, $dni, $password, $captcha, $myObject)
{

    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
    } else {
        if ($response->success == true && $response->score > 0.5) {
            guardarDB($email, $nombre, $apellido,  $phone, $dni, $password, $myObject);
        } else if ($response->success == true && $response->score <= 0.5) {
            $myObject->error = "Human?<br>";
        } else {
            $myObject->error = "NO<br>";
        }
    }
}
function guardarDB($email, $nombre, $apellido, $phone, $dni, $password, $myObject)
{
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "INSERT INTO usuarios_temp(email,nombre,apellido,password,phone,dni) VALUES('" . $email . "', '" . $nombre . "','" . $apellido . "', '" . md5($password) . "','" . $phone . "','" . $dni . "' )";
    if ($conn->multi_query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        enviarmail($email, $myObject);
    } else {
        $myObject->success = " ERROR Usuario NO guardado DB";
    }
    $conn->close();
}
function enviarmail($email, $myObject)
{
    $usuario = new stdClass();
    $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
    $sql = "SELECT * FROM usuarios_temp WHERE email = '" . $email . "' ORDER BY id DESC LIMIT 1 ;";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {

        while ($row = $result->fetch_assoc()) {
            $usuario->id = $row['id'];
            $usuario->email = $row['email'];
            $usuario->nombre = $row['nombre'];
            $usuario->apellido = $row['apellido'];
            $usuario->phone = $row['phone'];
            $usuario->dni = $row['dni'];
            $usuario->password = $row['password'];
            $usuario->reg_date = $row['reg_date'];
        }
        $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellido . "-" . $usuario->phone . "-" . $usuario->dni . "-" . $usuario->password . "-" . $usuario->reg_date;
        $sha1 = sha1($xstring);

        sendMail($usuario, $sha1, $myObject);
    } else {
        // echo "ERROR:<br>";
    }
    $conn->close();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($usuario, $sha1, $myObject)
{
    //MAIL
    $HostSMTP = 'smtp.gmail.com';
    $ContrasenaDelCorreo = 'fwwgxcjmunebldby';
    $SendFromEMAIL = 'javicesi77@gmail.com';
    $QuienLoEnviaNAME = 'moderator';
    $SendFromEMAILreply = 'javicesi77@gmail.com';
    $QuienResponderNAME = 'moderator';
    //$PortSMTP = 465; // con consulting.localhost este en cao de q falle
    $PortSMTP = 587; // freeemyhosting: este puerto y tmb xa consultoria.localhost

    $SentToEmail = $usuario->email;
    $Asunto = "ninguno";

    $css = file_get_contents('../css/emailContacta.css');

    $BodyHTML = '
    <html>
        <head>
        <style>
          ' . $css . '
           </style>
        </head>
        <body bgcolor="#FFFFFF">
        <!-- HEADER -->
        <table class="head-wrap" bgcolor="#999999">
            <tr>
                <td></td>
                <td class="header container" >
                        
                        <div class="content">
                        <table bgcolor="#999999">
                            <tr>
                                <td align="center"><h6 class="collapse">Bienvenido a Consulting S.A</h6></td>
                            </tr>
                        </table>
                        </div>		
                </td>
                <td></td>
            </tr>
        </table>
        <!-- /HEADER -->
<!-- BODY -->
<table class="body-wrap">
<tr>
    <td></td>
    <td class="container" bgcolor="#FFFFFF">
        <div class="content">
        <table>
            <tr>
                <td>
                
                    <h3>Sr/a: <i><b>' . "  " . $usuario->nombre . " " . $usuario->apellido . '<code>	
                     &#128076;</code></b></i></h3>
                    <p class="lead">Está a solo un paso de confirmar su email<br/></p>
                    </h1><p style="font-size:15px;"> Valida tu correo :<code>&#10004;</code></p><br/><p style="font-size:15px;">Click en el link para acceder:        <a href="http://' . $_SERVER['HTTP_HOST'] . '/registerFinal/nuevo_usuario.php?id=' . $usuario->id . '&clave=' . $sha1 . '"><b>' . $sha1 . '</b></a> </p>
                  
                    <!-- Callout Panel -->
                    <p class="callout" >
                       Regresa a la página principal <a href="' .  $_SERVER['HTTP_HOST'] . '/index.html">Click aquí! &raquo;</a>
                    </p><!-- /Callout Panel -->					
                                            
                    <!-- social & contact -->
                    <table class="social" width="100%">
                        <tr>
                            <td>
                                
                                <!-- column 1 -->
                                <table align="left" class="column">
                                    <tr>
                                        <td>				
                                            
                                            <h5 class="">Conecta con nosotros:</h5>
                                            <p class=""><a href="https://www.facebook.com/" class="soc-btn fb">Facebook</a> <a href="https://twitter.com/" class="soc-btn tw">Twitter</a> <a href="https://www.google.es/" class="soc-btn gp">Google+</a></p>
                    
                                            
                                        </td>
                                    </tr>
                                </table><!-- /column 1 -->	
                                
                                <!-- column 2 -->
                                <table align="left" class="column">
                                    <tr>
                                        <td>				
                                                                        
                                            <h5 class="">Contacta sin compromiso:</h5>												
                                            <p>Phone: <strong>+34-655874123</strong><br/>
            Email: <strong><a href="emailto:consultingAsesores@gmail.com">consultingAsesores@gmail.com</a></strong></p>
            
                                        </td>
                                    </tr>
                                </table><!-- /column 2 -->
                                
                                <span class="clear"></span>	
                                
                            </td>
                        </tr>
                    </table><!-- /social & contact -->
                    
                </td>
            </tr>
        </table>
        </div><!-- /content -->
                                
    </td>
    <td></td>
</tr>
</table>
<!-- /BODY -->

    <!-- FOOTER -->
    <table class="footer-wrap">
        <tr>
            <td></td>
            <td class="container">
                
                    <!-- content -->
                    <div class="content">
                    <table>
                    <tr>
                        <td align="center">
                            <p>
                                <a href="#">Terms</a> |
                                <a href="#">Privacy</a> |
                                <a href="#"><unsubscribe>Unsubscribe</unsubscribe></a>
                            </p>
                        </td>
                    </tr>
                </table>
                    </div><!-- /content -->
                    
            </td>
            <td></td>
        </tr>
    </table><!-- /FOOTER -->

    </body>
</html>';
    $BodyNOHTML = "hola que tal?";

    require realpath($_SERVER["DOCUMENT_ROOT"]) . '/vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = $HostSMTP;
        $mail->SMTPAuth   = true;
        $mail->Username   = $SendFromEMAIL;
        $mail->Password   = $ContrasenaDelCorreo;
        $mail->Port       = $PortSMTP;
        $mail->setFrom($SendFromEMAIL, $QuienLoEnviaNAME);
        $mail->addAddress($SentToEmail);
        $mail->addReplyTo($SendFromEMAIL, $QuienLoEnviaNAME);
        $mail->isHTML(true);
        $mail->Subject = $Asunto;
        $mail->Body    = $BodyHTML;
        $mail->AltBody = $BodyNOHTML;
        //esto es lo nuevo xa el freemyhosting.net
        $mail->SMTPSecure = 'tls';


        $mail->send();
        $myObject->success = "Message has been sent";
    } catch (Exception $e) {
        $myObject->error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
