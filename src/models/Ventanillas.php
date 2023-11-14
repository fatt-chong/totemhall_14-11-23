<?php
class Ventanillas{

    public function listarVentanillaAbiertasHoy(){

        $con = new Connection();
        $con->setDataConexion(dnsDbTotemHall, dataBaseTotemHall, userDbTotemHall, passDbTotemHall, servidorDbTotemHall);
        $con->db_connect();

        $sql = "SELECT
                    ventanillas_salas.id AS ventanillas_salas_id,
                    ventanillas_salas.ventanilla_id AS ventanillas_salas_ventanilla_id,
                    ventanillas_salas.sala_id AS ventanillas_salas_sala_id,
                    ventanillas_salas.fechaRegistro AS ventanillas_salas_fechaRegistro,
                    ventanillas_salas.idUsuario AS ventanillas_salas_idUsuario,
                    ventanillas_salas.estado_id AS ventanillas_salas_estado_id,
                    ventanillas.id AS ventanillas_id,
                    ventanillas.nombreVentanilla AS ventanillas_nombreVentanilla,
                    ventanillas.fechaRegistro AS ventanillas_fechaRegistro,
                    salas.id AS salas_id,
                    salas.nombreSala AS salas_nombreSala,
                    salas.fechaRegistro AS salas_fechaRegistro,
                    salas.usuario_id AS salas_usuario_id,
                    salas.modulo_id AS salas_modulo_id,
                    salas.estado_id AS salas_estado_id,
                    modulos.id AS modulos_id,
                    modulos.nombre AS modulos_nombre,
                    modulos.nombreAbreviacion AS modulos_nombreAbreviacion,
                    modulos.fechaRegistro AS modulos_fechaRegistro
                FROM
                    totem_hall.ventanillas_salas
                INNER JOIN ventanillas ON ventanillas_salas.ventanilla_id = ventanillas.id
                INNER JOIN salas ON ventanillas_salas.sala_id = salas.id
                INNER JOIN modulos ON salas.modulo_id = modulos.id
                WHERE
                    CAST(
                        ventanillas_salas.fechaRegistro AS DATE
                    ) = CURDATE()
                AND ventanillas_salas.estado_id = 1
                ORDER BY
                    ventanillas.id DESC";

        $ventanillas = $con->select($sql, "ventanillas", "listarVentanillaAbiertasHoy", null);

        unset($con);
        return $ventanillas;

    }
}
?>