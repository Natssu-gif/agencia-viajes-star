<?php
require_once "conexion.php";

$mensaje = $_GET["mensaje"] ?? "";
$tipo = $_GET["tipo"] ?? "";


/* =========================
   BUSCAR Y RECUPERAR VUELOS
   ========================= */

$buscarOrigen = trim($_GET["buscar_origen"] ?? "");
$buscarDestino = trim($_GET["buscar_destino"] ?? "");

$sqlVuelos = "SELECT * FROM VUELO WHERE 1=1";
$parametrosVuelos = [];

if ($buscarOrigen !== "") {
    $sqlVuelos .= " AND origen LIKE :origen";
    $parametrosVuelos["origen"] = "%" . $buscarOrigen . "%";
}

if ($buscarDestino !== "") {
    $sqlVuelos .= " AND destino LIKE :destino";
    $parametrosVuelos["destino"] = "%" . $buscarDestino . "%";
}

$sqlVuelos .= " ORDER BY id_vuelo ASC";

$consultaVuelos = $conexion->prepare($sqlVuelos);
$consultaVuelos->execute($parametrosVuelos);

$vuelos = $consultaVuelos->fetchAll();


/* =========================
   RECUPERAR HOTELES
   ========================= */

$consultaHoteles = $conexion->query(
    "SELECT * FROM HOTEL ORDER BY id_hotel ASC"
);

$hoteles = $consultaHoteles->fetchAll();


/* =========================
   RECUPERAR RESERVAS
   ========================= */

$consultaReservas = $conexion->query(
    "SELECT
        r.id_reserva,
        r.id_cliente,
        r.fecha_reserva,
        v.origen,
        v.destino,
        h.nombre AS hotel
     FROM RESERVA r
     INNER JOIN VUELO v
        ON r.id_vuelo = v.id_vuelo
     INNER JOIN HOTEL h
        ON r.id_hotel = h.id_hotel
     ORDER BY r.id_reserva ASC"
);

$reservas = $consultaReservas->fetchAll();


/* =========================================
   HOTELES CON MÁS DE 2 RESERVAS
   ========================================= */

$consultaHotelesReservados = $conexion->query(
    "SELECT
        h.id_hotel,
        h.nombre,
        h.ubicacion,
        COUNT(r.id_reserva) AS total_reservas
     FROM HOTEL h
     INNER JOIN RESERVA r
        ON h.id_hotel = r.id_hotel
     GROUP BY
        h.id_hotel,
        h.nombre,
        h.ubicacion
     HAVING COUNT(r.id_reserva) > 2
     ORDER BY total_reservas DESC"
);

$hotelesReservados =
    $consultaHotelesReservados->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Agencia de Viajes - Semana 6</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <div>

        <h1>Agencia de Viajes</h1>

        <p>
            Administración de vuelos, hoteles y reservas
        </p>

    </div>

</header>


<main>

    <?php if (!empty($mensaje)): ?>

        <div
            class="mensaje <?php echo htmlspecialchars($tipo); ?>"
        >

            <?php echo htmlspecialchars($mensaje); ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         VUELO Y HOTEL
         ========================= -->

    <section class="formularios">


        <!-- REGISTRAR VUELO -->

        <article class="tarjeta">

            <h2>✈️ Registrar vuelo</h2>

            <p class="descripcion">
                Ingresa los datos del nuevo vuelo.
            </p>

            <form
                id="formVuelo"
                action="guardar_vuelo.php"
                method="POST"
            >

                <label for="origen">
                    Ciudad de origen
                </label>

                <input
                    type="text"
                    id="origen"
                    name="origen"
                    placeholder="Ej: Santiago"
                    required
                >

                <label for="destino">
                    Ciudad de destino
                </label>

                <input
                    type="text"
                    id="destino"
                    name="destino"
                    placeholder="Ej: Cancún"
                    required
                >

                <label for="fecha">
                    Fecha del vuelo
                </label>

                <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    required
                >

                <label for="plazas">
                    Plazas disponibles
                </label>

                <input
                    type="number"
                    id="plazas"
                    name="plazas"
                    min="1"
                    placeholder="Ej: 120"
                    required
                >

                <label for="precio">
                    Precio
                </label>

                <input
                    type="number"
                    id="precio"
                    name="precio"
                    min="1"
                    step="1"
                    placeholder="Ej: 450000"
                    required
                >

                <button type="submit">
                    Guardar vuelo
                </button>

            </form>

        </article>


        <!-- REGISTRAR HOTEL -->

        <article class="tarjeta">

            <h2>🏨 Registrar hotel</h2>

            <p class="descripcion">
                Ingresa los datos del nuevo hotel.
            </p>

            <form
                id="formHotel"
                action="guardar_hotel.php"
                method="POST"
            >

                <label for="nombre">
                    Nombre del hotel
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ej: Hotel Caribe"
                    required
                >

                <label for="ubicacion">
                    Ubicación
                </label>

                <input
                    type="text"
                    id="ubicacion"
                    name="ubicacion"
                    placeholder="Ej: Cancún, México"
                    required
                >

                <label for="habitaciones">
                    Habitaciones disponibles
                </label>

                <input
                    type="number"
                    id="habitaciones"
                    name="habitaciones"
                    min="1"
                    placeholder="Ej: 40"
                    required
                >

                <label for="tarifa">
                    Tarifa por noche
                </label>

                <input
                    type="number"
                    id="tarifa"
                    name="tarifa"
                    min="1"
                    step="1"
                    placeholder="Ej: 85000"
                    required
                >

                <button type="submit">
                    Guardar hotel
                </button>

            </form>

        </article>

    </section>


    <!-- =========================
         REGISTRAR RESERVA
         ========================= -->

    <section class="reserva">

        <div class="titulo-seccion">

            <div>

                <h2>🧳 Registrar reserva</h2>

                <p>
                    Registra los datos del cliente y selecciona
                    los servicios turísticos.
                </p>

            </div>

        </div>


        <form
            class="form-reserva"
            action="guardar_reserva.php"
            method="POST"
        >

            <div class="campo-reserva">

                <label for="id_cliente">
                    ID del cliente
                </label>

                <input
                    type="number"
                    id="id_cliente"
                    name="id_cliente"
                    min="1"
                    placeholder="Ej: 1"
                    required
                >

            </div>


            <div class="campo-reserva">

                <label for="fecha_reserva">
                    Fecha de reserva
                </label>

                <input
                    type="date"
                    id="fecha_reserva"
                    name="fecha_reserva"
                    required
                >

            </div>


            <div class="campo-reserva">

                <label for="id_vuelo">
                    Vuelo
                </label>

                <select
                    id="id_vuelo"
                    name="id_vuelo"
                    required
                >

                    <option value="">
                        Selecciona un vuelo
                    </option>

                    <?php foreach ($vuelos as $vuelo): ?>

                        <option
                            value="<?php echo $vuelo["id_vuelo"]; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $vuelo["origen"]
                                . " → "
                                . $vuelo["destino"]
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="campo-reserva">

                <label for="id_hotel">
                    Hotel
                </label>

                <select
                    id="id_hotel"
                    name="id_hotel"
                    required
                >

                    <option value="">
                        Selecciona un hotel
                    </option>

                    <?php foreach ($hoteles as $hotel): ?>

                        <option
                            value="<?php echo $hotel["id_hotel"]; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $hotel["nombre"]
                                . " - "
                                . $hotel["ubicacion"]
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="campo-boton">

                <button type="submit">
                    Registrar reserva
                </button>

            </div>

        </form>

    </section>


    <!-- =========================
         VUELOS REGISTRADOS
         ========================= -->
   <section class="reserva">

    <div class="titulo-seccion">
        <div>
            <h2>🔎 Buscar vuelos</h2>
            <p>
                Filtra los vuelos disponibles por ciudad de origen o destino.
            </p>
        </div>
    </div>

    <form class="form-reserva" method="GET" action="index.php">

        <div class="campo-reserva">
            <label for="buscar_origen">
                Ciudad de origen
            </label>

            <input
                type="text"
                id="buscar_origen"
                name="buscar_origen"
                placeholder="Ej: Santiago"
                value="<?php echo htmlspecialchars($buscarOrigen); ?>"
            >
        </div>

        <div class="campo-reserva">
            <label for="buscar_destino">
                Ciudad de destino
            </label>

            <input
                type="text"
                id="buscar_destino"
                name="buscar_destino"
                placeholder="Ej: Cancún"
                value="<?php echo htmlspecialchars($buscarDestino); ?>"
            >
        </div>

        <div class="campo-boton">
            <button type="submit">
                Buscar vuelos
            </button>
        </div>

    </form>

    <?php if ($buscarOrigen !== "" || $buscarDestino !== ""): ?>

        <p class="descripcion">
            Se encontraron
            <strong><?php echo count($vuelos); ?></strong>
            vuelo(s) que coinciden con la búsqueda.
            <a href="index.php">Mostrar todos</a>
        </p>

    <?php endif; ?>

</section>

   
    <section class="datos">

        <div class="titulo-seccion">

            <div>

                <h2>✈️ Vuelos registrados</h2>

                <p>
                    Información recuperada desde MySQL.
                </p>

            </div>

            <span class="contador">

                <?php echo count($vuelos); ?>
                registros

            </span>

        </div>


        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Plazas</th>
                        <th>Precio</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($vuelos as $vuelo): ?>

                    <tr>

                        <td>
                            <?php echo $vuelo["id_vuelo"]; ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $vuelo["origen"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $vuelo["destino"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo date(
                                "d-m-Y",
                                strtotime($vuelo["fecha"])
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $vuelo["plazas_disponibles"];
                            ?>
                        </td>

                        <td class="precio-tabla">

                            $

                            <?php
                            echo number_format(
                                $vuelo["precio"],
                                0,
                                ",",
                                "."
                            );
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =========================
         HOTELES REGISTRADOS
         ========================= -->

    <section class="datos">

        <div class="titulo-seccion">

            <div>

                <h2>🏨 Hoteles registrados</h2>

                <p>
                    Información recuperada desde MySQL.
                </p>

            </div>

            <span class="contador">

                <?php echo count($hoteles); ?>
                registros

            </span>

        </div>


        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Habitaciones</th>
                        <th>Tarifa por noche</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($hoteles as $hotel): ?>

                    <tr>

                        <td>
                            <?php echo $hotel["id_hotel"]; ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $hotel["nombre"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $hotel["ubicacion"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $hotel[
                                "habitaciones_disponibles"
                            ];
                            ?>
                        </td>

                        <td class="precio-tabla">

                            $

                            <?php
                            echo number_format(
                                $hotel["tarifa_noche"],
                                0,
                                ",",
                                "."
                            );
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =========================
         RESERVAS REGISTRADAS
         ========================= -->

    <section class="datos">

        <div class="titulo-seccion">

            <div>

                <h2>🧳 Reservas registradas</h2>

                <p>
                    Información recuperada desde las tablas
                    RESERVA, VUELO y HOTEL.
                </p>

            </div>

            <span class="contador">

                <?php echo count($reservas); ?>
                registros

            </span>

        </div>


        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Hotel</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($reservas as $reserva): ?>

                    <tr>

                        <td>
                            <?php echo $reserva["id_reserva"]; ?>
                        </td>

                        <td>
                            <?php echo $reserva["id_cliente"]; ?>
                        </td>

                        <td>

                            <?php

                            if (
                                $reserva["fecha_reserva"]
                                !== "0000-00-00"
                            ) {

                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $reserva["fecha_reserva"]
                                    )
                                );

                            } else {

                                echo "Sin fecha";
                            }

                            ?>

                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $reserva["origen"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $reserva["destino"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $reserva["hotel"]
                            );
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =========================
         CONSULTA AVANZADA
         ========================= -->

    <section class="datos">

        <div class="titulo-seccion">

            <div>

                <h2>
                    📊 Hoteles con más de 2 reservas
                </h2>

                <p>
                    Consulta avanzada utilizando
                    GROUP BY, COUNT y HAVING.
                </p>

            </div>

            <span class="contador">

                <?php
                echo count($hotelesReservados);
                ?>

                resultados

            </span>

        </div>


        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>ID Hotel</th>
                        <th>Hotel</th>
                        <th>Ubicación</th>
                        <th>Total de reservas</th>
                    </tr>

                </thead>

                <tbody>

                <?php
                foreach (
                    $hotelesReservados
                    as $hotelReservado
                ):
                ?>

                    <tr>

                        <td>
                            <?php
                            echo $hotelReservado["id_hotel"];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $hotelReservado["nombre"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $hotelReservado["ubicacion"]
                            );
                            ?>
                        </td>

                        <td>
                            <strong>
                                <?php
                                echo $hotelReservado[
                                    "total_reservas"
                                ];
                                ?>
                            </strong>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>


<script src="script.js"></script>

</body>
</html>
