<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerificarMail</title>
</head>

<body>
    <?php
    require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/admin.php";

    if (isset($_GET['id']) && isset($_GET['clave'])) {
        $id = $_GET['id'];
        $clave = $_GET['clave'];

        // echo "id=" . $id . "<br />";

        // echo "clave=" . $clave;

        $usuario = new stdClass();
        $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
        $sql = "SELECT * FROM contacta_temp WHERE id= '" . $id . "';";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {

            while ($row = $result->fetch_assoc()) {
                $usuario->id = $row['id'];
                $usuario->email = $row['email'];
                $usuario->nombre = $row['nombre'];
                $usuario->apellido = $row['apellido'];
                $usuario->phone = $row['phone'];
                $usuario->asunto = $row['asunto'];
                $usuario->textarea = $row['textarea'];
                $usuario->estado = $row['estado'];
                $usuario->reg_date = $row['reg_date'];
            }


            $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellido . "-" . $usuario->phone . "-" . $usuario->asunto . "-" . $usuario->textarea . "-" . $usuario->reg_date . '-0';
            $sha1 = sha1($xstring);
            // echo '<br/>';
            // echo 'AA+' . $usuario->email . '+AA';
            // echo '<br/>';
            // echo $sha1;
            // echo '<br/>';

            echo '<br/>';
            if ($clave == $sha1) {

                // un avez hecho descomento insertuser
                insertUser($usuario);
            } else {
                echo 'mal';
            }
        } else {
            echo 'Error:id not found';
        }
        $conn->close();
    }


    function insertUser($usuario)
    {
        $conn = new mysqli(data_base_hosting_consultoria, data_base_username_consultoria, data_base_password_consultoria, nameTabla_data_base_consultoria);
        $sql = 'UPDATE contacta_temp  SET estado=1 WHERE id="' . $usuario->id . '"';
        if ($conn->query($sql) === TRUE) {
            // copiar aqui tmb if
            echo '
            <html>
                <head>
                    <link rel="stylesheet" type="text/css" href="/css/emailContacta.css" />
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
                            <h3>Sr/a: <i><b>' . "  " . $usuario->nombre . " " . $usuario->apellido . '</b></i></h3>
                            <p class="lead">Le confirmamos que su consulta a sido recibida
                            correctamente.<br/> Nuestros especialista darán una solución lo más
                            rápidamente posible a su consulta poniendose en contacto con usted mediante: <br/><ul><li>correo:' . "  " . '<b><i>' . $usuario->email . '</b></i></li><li>Telefono:' . "  " . '<b><i>' . $usuario->phone . '</b></i></li></ul></p>
                            <p>Le adjuntamos los datos de su consulta:</p>
                            <div style="border:2px solid black;background-color:#ccccff;margin:auto;padding:10px; margin-bottom:20px;"> 
                                <ul>
                                    <li>id=' . $usuario->id . '</li>
                                    <li>Nombre=' . $usuario->nombre . '</li>
                                    <li>Apellidos=' . $usuario->apellido . '</li>
                                    <li>telefono=' . $usuario->phone . '</li>
                                    <li>Asunto: ' . $usuario->asunto . '</li>
                                    <li>Fecha:' . $usuario->reg_date . '</li>
                                    <li>Texto: ' . $usuario->textarea . '</li>
                                </ul>
                            </div>
                            <!-- Callout Panel -->				
                                                    
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

            //     echo '<div style="background-color: grey; border: 2px solid black">
            //     <h1>Te damos la bienvenida a Consulting S.A</h1>
            //     <p>
            //         Sr/a' . "  " . '<i><b>' . $usuario->nombre . "  " .  $usuario->apellido . "  " . '</i></b>le confirmamos que su consulta a sido recibida
            //         correctamente.<br /> Nuestros especialista darán una solución lo más
            //         rápidamente posible poniendose en contacto con usted mediante su correo:' . "  " . '<i><b>' .
            //         $usuario->email . '</i></b>  o su número de telefono:' . "  " . '<i><b>' . $usuario->phone . '
            //     <i><b> </p>
            //     <div style="background-color: white; width:50%;margin:auto; padding-left:5px; border: 2px solid black">
            //     id=' . $usuario->id . '<br/> Asunto: ' . $usuario->asunto . '<br/> Fecha:' . $usuario->reg_date . '<br/>
            //     Texto: ' . $usuario->textarea . '

            //     </div>
            //     <p>En caso de que los datos sean erroneos pongasé en contacto mediante el correo: <a href="#">consultingAsesores@gmail.com</a></p>
            //     <h3>Atentamente la dirección de Consuting S.A</h3>
            // </div>';

            // header('Location: login.html');
        } else {
            echo "<br>ERROR";
            //echo "Error: insert table \"usuarios\" " . $conn->error . " <br>" . $sql . "<br>";
        }
        $conn->close();
    }
    ?>
</body>

</html>