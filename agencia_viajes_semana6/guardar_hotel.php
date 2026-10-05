<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $habitaciones =
        (int) ($_POST["habitaciones"] ?? 0);

    $tarifa =
        (float) ($_POST["tarifa"] ?? 0);

    if (
        $nombre === "" ||
        $ubicacion === "" ||
        $habitaciones <= 0 ||
        $tarifa <= 0
    ) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode("Los datos del hotel no son válidos.")
        );

        exit;
    }

    try {

        $sql = "
            INSERT INTO HOTEL
            (
                nombre,
                ubicacion,
                habitaciones_disponibles,
                tarifa_noche
            )
            VALUES
            (
                :nombre,
                :ubicacion,
                :habitaciones,
                :tarifa
            )
        ";

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            ":nombre" => $nombre,
            ":ubicacion" => $ubicacion,
            ":habitaciones" => $habitaciones,
            ":tarifa" => $tarifa
        ]);

        header(
            "Location: index.php?tipo=exito&mensaje="
            . urlencode("Hotel registrado correctamente.")
        );

        exit;

    } catch (PDOException $error) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode("No fue posible registrar el hotel.")
        );

        exit;
    }
}

header("Location: index.php");
exit;
?>