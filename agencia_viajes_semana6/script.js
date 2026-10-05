document.addEventListener("DOMContentLoaded", function () {

    const formVuelo =
        document.getElementById("formVuelo");

    const formHotel =
        document.getElementById("formHotel");


    /* VALIDACIÓN DEL FORMULARIO DE VUELOS */

    formVuelo.addEventListener("submit", function (evento) {

        const origen =
            document.getElementById("origen").value.trim();

        const destino =
            document.getElementById("destino").value.trim();

        const fecha =
            document.getElementById("fecha").value;

        const plazas =
            parseInt(
                document.getElementById("plazas").value
            );

        const precio =
            parseFloat(
                document.getElementById("precio").value
            );

        if (
            origen === "" ||
            destino === "" ||
            fecha === ""
        ) {

            evento.preventDefault();

            alert(
                "Debes completar todos los datos del vuelo."
            );

            return;
        }

        if (origen.toLowerCase() === destino.toLowerCase()) {

            evento.preventDefault();

            alert(
                "La ciudad de origen y destino deben ser diferentes."
            );

            return;
        }

        if (plazas <= 0 || isNaN(plazas)) {

            evento.preventDefault();

            alert(
                "Las plazas disponibles deben ser mayores a 0."
            );

            return;
        }

        if (precio <= 0 || isNaN(precio)) {

            evento.preventDefault();

            alert(
                "El precio del vuelo debe ser mayor a 0."
            );

            return;
        }
    });


    /* VALIDACIÓN DEL FORMULARIO DE HOTELES */

    formHotel.addEventListener("submit", function (evento) {

        const nombre =
            document.getElementById("nombre").value.trim();

        const ubicacion =
            document.getElementById("ubicacion").value.trim();

        const habitaciones =
            parseInt(
                document.getElementById("habitaciones").value
            );

        const tarifa =
            parseFloat(
                document.getElementById("tarifa").value
            );

        if (
            nombre === "" ||
            ubicacion === ""
        ) {

            evento.preventDefault();

            alert(
                "Debes completar todos los datos del hotel."
            );

            return;
        }

        if (
            habitaciones <= 0 ||
            isNaN(habitaciones)
        ) {

            evento.preventDefault();

            alert(
                "Las habitaciones disponibles deben ser mayores a 0."
            );

            return;
        }

        if (
            tarifa <= 0 ||
            isNaN(tarifa)
        ) {

            evento.preventDefault();

            alert(
                "La tarifa por noche debe ser mayor a 0."
            );

            return;
        }
    });
});