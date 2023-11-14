<?php
class Turnos{

    public function listarTurnoActualPorSalaYestadoDoce($nombreSala, $nombreVentanilla){

        $con = new Connection();
        $con->setDataConexion(dnsDbTotemHall, dataBaseTotemHall, userDbTotemHall, passDbTotemHall, servidorDbTotemHall);
        $con->db_connect();

        $sql = "SELECT
                    turnos.id,
                    turnos.abreviacionModulo,
                    turnos.numeroTicket,
                    turnos.fechaRegistro,
                    turnos.sala_id,
                    turnos.usuario_id,
                    turnos.paciente_id,
                    turnos.preferencia_id,
                    turnos.id_estado,
                    turnos.nombreVentanilla,
                    modulos.id AS modulo_id,
                    modulos.nombre,
                    modulos.nombreAbreviacion,
                    modulos.fechaRegistro AS modulo_fechaRegistro,
                    salas.id AS salas_id,
                    salas.nombreSala,
                    salas.fechaRegistro AS salas_fechaRegistros,
                    salas.usuario_id AS salas_usuarios_id,
                    salas.modulo_id AS salas_modulo_id,
                    salas.estado_id AS salas_estado_id
                FROM
                    totem_hall.turnos
                INNER JOIN salas ON turnos.sala_id = salas.id
                INNER JOIN modulos ON salas.modulo_id = modulos.id
                WHERE
                    CAST(turnos.fechaRegistro AS DATE) = CURDATE()
                AND id_estado = 12
                AND nombreSala = :nombreSala
                AND nombreVentanilla = :nombreVentanilla";

        $bind["nombreSala"] = $nombreSala;
        $bind["nombreVentanilla"] = $nombreVentanilla;
        //print_r("<pre>");print_r($sql);print_r("</pre>");
        $turnos = $con->select($sql, "turnos", "listarTurnoActualPorSalaYestadoDoce", $bind);

        unset($con);
        return $turnos;
    }

    public function mostrarUltimoLlamadoEnCasoDeEstarSinLlamadoEnView($nombreSala, $nombreVentanilla){

        $con = new Connection();
        $con->setDataConexion(dnsDbTotemHall, dataBaseTotemHall, userDbTotemHall, passDbTotemHall, servidorDbTotemHall);
        $con->db_connect();

        $sql = "SELECT
                    turnos.id,
                    turnos.abreviacionModulo,
                    turnos.numeroTicket,
                    turnos.fechaRegistro,
                    turnos.sala_id,
                    turnos.usuario_id,
                    turnos.paciente_id,
                    turnos.preferencia_id,
                    turnos.id_estado,
                    turnos.nombreVentanilla,
                    modulos.id AS modulo_id,
                    modulos.nombre,
                    modulos.nombreAbreviacion,
                    modulos.fechaRegistro AS modulo_fechaRegistro,
                    salas.id AS salas_id,
                    salas.nombreSala,
                    salas.fechaRegistro AS salas_fechaRegistros,
                    salas.usuario_id AS salas_usuarios_id,
                    salas.modulo_id AS salas_modulo_id,
                    salas.estado_id AS salas_estado_id
                FROM
                    totem_hall.turnos
                INNER JOIN salas ON turnos.sala_id = salas.id
                INNER JOIN modulos ON salas.modulo_id = modulos.id
                WHERE
                    CAST(turnos.fechaRegistro AS DATE) = CURDATE()
                AND nombreSala = :nombreSala
                AND nombreVentanilla = :nombreVentanilla
                ORDER BY
                    id DESC
                LIMIT 1";
        
        $bind["nombreSala"] = $nombreSala;
        $bind["nombreVentanilla"] = $nombreVentanilla;
        
        $turnos = $con->select($sql, "turnos", "mostrarUltimoLlamadoEnCasoDeEstarSinLlamadoEnView", $bind);

        unset($con);
        return $turnos;
    }

    public function seleccionarUltimoTurnoPorAbrevmodulo($abreviacionModulo){
        $con = new Connection();
        $con->setDataConexion(dnsDbTotemHall, dataBaseTotemHall, userDbTotemHall, passDbTotemHall, servidorDbTotemHall);
        $con->db_connect();

        $sql = "SELECT
                    *
                FROM
                    turnos
                WHERE
                    DATE(fechaRegistro) = curdate()
                AND abreviacionModulo = :abreviacionModulo
                ORDER BY
                    id DESC
                LIMIT 1";

        $bind["abreviacionModulo"] = $abreviacionModulo;

        $turnos = $con->select($sql, "turnos", "seleccionarUltimoTurnoPorAbrevmodulo", $bind);

        unset($con);
        return $turnos;
    }
}
?>