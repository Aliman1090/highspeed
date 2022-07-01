<?php
if (isset($_GET['id']) && isset($_GET['clave'])) {;
    $id = $_GET['id'];
    $clave = $_GET['clave'];

    echo "id: " . $_GET['id'] . "<br />";
    echo "clave: " . $_GET['clave'];

    $usuario = new stdClass();
    $conn = new mysqli("sql4.freemysqlhosting.net", "sql4499635", "GvJjtAH8pC", "sql4499635");
    $sql = "SELECT * FROM usuarios_temp WHERE id='" . $id . "' ;";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $usuario->id = $row['id'];
            $usuario->email = $row['email'];
            $usuario->nombre = $row['nombre'];
            $usuario->phone = $row['phone'];
            $usuario->password = $row['password'];
            $usuario->reg_date = $row['reg_date'];
        }
        $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->phone . "-" . $usuario->password . "-" . $usuario->reg_date;
        $sha1 = sha1($xstring);

        if ($clave == $sha1) {
            insertUser($usuario);
        }
    } else {
        echo "Error:  id not found.";
    }
    $conn->close();
}

function insertUser($usuario)
{
    $conn = new mysqli("sql4.freemysqlhosting.net", "sql4499635", "GvJjtAH8pC", "sql4499635");
    $sql = "INSERT INTO usuarios (email,nombre,phone,password,reg_date) VALUES ('" . $usuario->email . "','" . $usuario->nombre . "'," . $usuario->phone . ",'" . $usuario->password . "','" . date("Y-m-d H:i:s") . "');";
    if ($conn->query($sql) === TRUE) {
        echo "<br>OK";
        $sql_a = "DELETE FROM usuarios_temp WHERE email='" . $usuario->email . "' || reg_date <= NOW() - INTERVAL 1 DAY;";
        $conn->query($sql_a);
        header('Location: login.html');
    } else {
        echo "<br>ERROR";
        //echo "Error: insert table \"usuarios\" " . $conn->error . " <br>" . $sql . "<br>";
    }
    $conn->close();
}
