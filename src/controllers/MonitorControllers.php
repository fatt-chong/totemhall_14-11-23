<?php
    
    class MonitorControllers{
        
        public function obtenerVentanillasDisponiblesHoy(){
            // URL a la que deseas hacer la solicitud GET
            $url = 'http://10.6.21.29:3001/totem-hall/monitor';

            // Inicializa cURL
            $ch = curl_init($url);

            // Configura opciones de cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Realiza la solicitud GET
            $response = curl_exec($ch);
            // Verifica si la solicitud fue exitosa
            if ($response === false) {
                die('Error al realizar la solicitud GET: ' . curl_error($ch));
            }

            // Cierra la sesión cURL
            curl_close($ch);

            // Puedes imprimir la respuesta o realizar otras operaciones
            return $response;
        }
    }
    
?>