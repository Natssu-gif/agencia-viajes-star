<?php

$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "AGENCIA";

try {

    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $error) {

    die(
        "Error de conexión con la base de datos: "
        . $error->getMessage()
    );
}
?>