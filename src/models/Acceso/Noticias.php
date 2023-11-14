<?php
class Noticias{
    
    public function cargaUltimas4NoticiasActuales(){

        $con = new Connection();
        $con->setDataConexion(dnsDbAcceso, dataBaseAcceso, userDbAcceso, passDbAcceso, servidorDbAcceso);
        $con->db_connect();

        $sql = "SELECT
                    noticias.not_id,
                    noticias.not_titulo,
                    noticias.not_resumen,
                    noticias.not_fechaActual,
                    noticias.not_autor,
                    noticias.not_cr,
                    noticias.not_foto,
                    noticias.not_cuerpoNot
                FROM
                    acceso.noticias
                ORDER BY
                    noticias.not_id DESC
                LIMIT 4";

        $noticias = $con->select($sql, "Noticias", "cargaUltimas4NoticiasActuales", null);
        unset($con);
        return $noticias;
    }

}
?>