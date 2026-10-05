<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idCliente = (int) ($_POST["id_cliente"] ?? 0);
    $fechaReserva = trim($_POST["fecha_reserva"] ?? "");
    $idVuelo = (int) ($_POST["id_vuelo"] ?? 0);
    $idHotel = (int) ($_POST["id_hotel"] ?? 0);


    /* Validación de los datos recibidos */

    if (
        $idCliente <= 0 ||
        empty($fechaReserva) ||
        $idVuelo <= 0 ||
        $idHotel <= 0
    ) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode(
                "Debes completar todos los datos de la reserva."
            )
        );

        exit;
    }


    try {

        /* Comprobar que el vuelo seleccionado existe */

        $consultaVuelo = $conexion->prepare(
            "SELECT id_vuelo
             FROM VUELO
             WHERE id_vuelo = :id_vuelo"
        );

        $consultaVuelo->execute([
            ":id_vuelo" => $idVuelo
        ]);


        /* Comprobar que el hotel seleccionado existe */

        $consultaHotel = $conexion->prepare(
            "SELECT id_hotel
             FROM HOTEL
             WHERE id_hotel = :id_hotel"
        );

        $consultaHotel->execute([
            ":id_hotel" => $idHotel
        ]);


        if (
            !$consultaVuelo->fetch() ||
            !$consultaHotel->fetch()
        ) {

            header(
                "Location: index.php?tipo=error&mensaje="
                . urlencode(
                    "El vuelo o el hotel seleccionado no existe."
                )
            );

            exit;
        }


        /* Insertar la reserva completa */

        $sql = "
            INSERT INTO RESERVA
            (
                id_cliente,
                fecha_reserva,
                id_vuelo,
                id_hotel
            )
            VALUES
            (
                :id_cliente,
                :fecha_reserva,
                :id_vuelo,
                :id_hotel
            )
        ";


        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            ":id_cliente" => $idCliente,
            ":fecha_reserva" => $fechaReserva,
            ":id_vuelo" => $idVuelo,
            ":id_hotel" => $idHotel
        ]);


        header(
            "Location: index.php?tipo=exito&mensaje="
            . urlencode(
                "Reserva registrada correctamente."
            )
        );

        exit;


    } catch (PDOException $error) {

        header(
            "Location: index.php?tipo=error&mensaje="
            . urlencode(
                "No fue posible registrar la reserva."
            )
        );

        exit;
    }
}


header("Location: index.php");
exit;

?>