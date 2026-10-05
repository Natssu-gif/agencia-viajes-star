<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $origen = trim($_POST["origen"] ?? "");
    $destino = trim($_POST["destino"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $plazas = (int) ($_POST["plazas"] ?? 0);
    $precio = (float) ($_POST["precio"] ?? 0);

    if (
        $origen === "" ||
        $destino === "" ||
        $fecha === "" ||
        $plazas <= 0 ||
        $precio <= 0
    ) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode("Los datos del vuelo no son válidos.")
        );

        exit;
    }

    try {

        $sql = "
            INSERT INTO VUELO
            (
                origen,
                destino,
                fecha,
                plazas_disponibles,
                precio
            )
            VALUES
            (
                :origen,
                :destino,
                :fecha,
                :plazas,
                :precio
            )
        ";

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            ":origen" => $origen,
            ":destino" => $destino,
            ":fecha" => $fecha,
            ":plazas" => $plazas,
            ":precio" => $precio
        ]);

        header(
            "Location: index.php?tipo=exito&mensaje="
            . urlencode("Vuelo registrado correctamente.")
        );

        exit;

    } catch (PDOException $error) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode("No fue posible registrar el vuelo.")
        );

        exit;
    }
}

header("Location: index.php");
exit;
?>