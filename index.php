<!DOCTYPE html>
<html lang="en">
<head>
    <?php header('Content-Type: text/html; charset=UTF-8'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Totem Hall</title>
    <link href="http://10.6.21.19/standard/bootstrap-5.3.0-dist/css/bootstrap.min.css?v=17.01.51" rel="stylesheet">
    <!-- <script src="http://10.6.21.19/acceso/jquery/jquery-1.9.1.min.js"></script> -->
    <script src="http://10.6.21.19/standard/bootstrap-5.3.0-dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="http://10.6.21.19/acceso/jquery/jquery-ui-1.10.1.custom.min.js"></script> -->
    <!-- <script src="http://10.6.21.19/acceso/js/bjqs-1.3.js"></script> -->
    <link rel="stylesheet" href="./src/css/monitor.css">
    <?php
        require("./src/config/config.php");
        require("./src/models/Connection.php");
        require("./src/models/Ventanillas.php");
        require("./src/models/Turnos.php");
        require("./src/models/Acceso/Noticias.php");

        $noticiasAcceso = new Noticias();

        $responseNoticias = $noticiasAcceso->cargaUltimas4NoticiasActuales();

        $noticias = array();

        if(count($responseNoticias)>0 && $responseNoticias["status"] == 200){
            $noticias = $responseNoticias["datos"];
        }

        //ultimas 4 noticias
        //print_r("<pre>");print_r($noticias);print_r("</pre>");
        ?>
        
        <?php
        //die();
        $ventanillaModel = new Ventanillas();
        $turnoModel = new Turnos();
        $responseVentanillasDisponibles = $ventanillaModel->listarVentanillaAbiertasHoy();

       // print_r("<pre>");print_r($responseVentanillasDisponibles["datos"]);print_r("</pre>");
        $tuArray = $responseVentanillasDisponibles["datos"];
        // La abreviatura que estás buscando
        $abreviaturaBuscada = "GINLAB";

        // Función para filtrar los registros
        function filtroPorAbreviatura($registro) {
            global $abreviaturaBuscada;
            return $registro['modulos_nombreAbreviacion'] == $abreviaturaBuscada;
        }

        // Utiliza array_filter para filtrar el array
        $registrosFiltrados = array_filter($tuArray, 'filtroPorAbreviatura');

        // Toma el primer elemento del array filtrado
        $primerRegistroGINLAB = reset($registrosFiltrados);

        $ventanillaGinLabAbierta = "No";
        // Verifica si se encontró un registro
        if($primerRegistroGINLAB !== false){
            // Imprime el primer registro encontrado
            //print_r($primerRegistroGINLAB);
            $ventanillaGinLabAbierta = "Si";
        }else{
            //echo "No se encontraron registros con la abreviatura '$abreviaturaBuscada'.";
            $ventanillaGinLabAbierta = "No";
        }

        //OIRS
        $abreviaturaBuscada = "OIRS";
        // Utiliza array_filter para filtrar el array
        $registrosFiltrados = array_filter($tuArray, 'filtroPorAbreviatura');

        // Toma el primer elemento del array filtrado
        $primerRegistroOIRS = reset($registrosFiltrados);

        $ventanillaOirAbierta = "No";
        // Verifica si se encontró un registro
        if($primerRegistroOIRS !== false){
            // Imprime el primer registro encontrado
            //print_r($primerRegistroOIRS);
            $ventanillaOirAbierta = "Si";
        }else{
            //echo "No se encontraron registros con la abreviatura '$abreviaturaBuscada'.";
            $ventanillaOirAbierta = "No";
        }
        require("./src/controllers/MonitorControllers.php");
    ?>

</head>

<style>

.media-gallery {
    font-family: "Arial";
    font-size: 14px;
}

.carousel-caption {
    position: relative !important;
    /* border: 1px solid black; */
    top: 0 !important;
    left: 0 !important;
    color: black !important;
    text-align: left!important;
    padding: 20px !important;
    height: 120px !important;
}

.carousel .carousel-control-next,
.carousel .carousel-control-prev {
    z-index: 999;
    width: 45px;
    height: 45px;
    font-size: 30px;
    background: rgba(255, 255, 255, 0.25) none repeat scroll 0 0;
    border-radius: 100%;
    padding: 0 0 10px 0px;
    line-height: 20px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
}

.carousel .carousel-control-prev,
.carousel .carousel-control-next {
    top: 50%;
    bottom: 50%;
    width: 5%;
    opacity: 1;
    position: absolute;
}

.carousel .carousel-control-prev,
.carousel .carousel-control-next {
    font-size: 30px;
    background: none;
}

.carousel .carousel-control-next {
    right: 20px;
}
</style>
<style>
    #contenedorPropagandaHJ {
    /* top: 0;
    bottom: 0;
    right: 0; */
     /* Ajusta el valor según tus necesidades */
    /* background-color: red; */
    /* position: absolute;
    display: flex;
    justify-content: center;
    align-items: center; */
    /* position: absolute;
    display: flex; */
    justify-content: center;
    align-items: center;
        margin-right: 10px; /* Agrega un margen derecho de 10 píxeles */
        width: 625px !important;
  }

  .contenedorDerecha {
    /* top: 0;
    bottom: 0;
    right: 0; */
     /* Ajusta el valor según tus necesidades */
    /* background-color: red; */
    /* position: absolute;
    display: flex;
    justify-content: center;
    align-items: center; */
    /* position: absolute;
    display: flex; */
    justify-content: center;
    align-items: center;
        margin-right: 10px; /* Agrega un margen derecho de 10 píxeles */
        width: 625px !important;
  }

  #propagandaHJNC {
    display: block;
    margin: 0 auto;
  }

  #hora {
    font-size: 80px;
  }

  .centered-element {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%; /* Asegura que ocupe todo el espacio vertical disponible en el contenedor */
  }

  .carousel-item img {
      width: 100% !important; /* Hace que la imagen ocupe el 100% del ancho del contenedor */
      height: 450px !important; /* Mantiene la proporción original de la imagen ajustando la altura automáticamente */
    }
  
</style>

<body>
    <?php
    if(count($responseVentanillasDisponibles["datos"])>0){
        ?>
        <!--x-->
        <div class="row mx-4">
            <div class="col-8" id="contenedorModulos">
              <!---->
                    <div class="row">
                        <!-- <div class="col-6 text-center" id="contenedorCargandoSpinnerLogo" hidden>
                            <img src="http://10.6.21.19/estandar/assets/img/loading-5.gif" alt="Logo HJNC" height="200" id="logoPrincipalCargando">
                            <img src="./src/img/logo_transparente_bl.png">
                        </div> -->
                        <div class="col-3 text-center" id="contenedorSinCargandoSpinnerLogo" style="height:220px">
                        </div>
                        <div class="col-6 text-center d-flex justify-content-center align-items-center" id="hora"></div>
                    </div>
                    <div class="row">
                            <div class="col-1" hidden>
                                <img src="http://10.6.21.19/estandar(V2)/img/logo_hospital.png" alt="Logo HJNC" height="100" id="logoPrincipal">
                                <img src="http://10.6.21.19/estandar/assets/img/loading-5.gif" alt="Logo HJNC" height="150" id="logoPrincipalCargando" style="display:none;"> 
                            </div>
                            <div class="col-12">
                        
                        <div class="row" id="contenedorPrincipalTurnos">
                            <div class="col-12 bg-info text-light">
                                <div class="row">
                                    <div class="col-6 text-center p-3 bg-info">
                                        <h1 style="font-size: 60px;">MODULO</h1>
                                    </div>
                                    <div class="col-6 text-center p-3 bg-info">
                                        <h1 style="font-size: 60px;">TURNO</h1>
                                    </div>
                                </div>
                            </div>
                            
                                <?php
                                    foreach($responseVentanillasDisponibles["datos"] as $index=>$ventanillas){
                                        $turno = "SIN LLAMADO";

                                        //cambiar al color aqui si se quiere intercalar
                                        if($index % 2 == 0){
                                            $color = "#CFE2FF";
                                        }else{
                                            $color = "#CFE2FF";
                                        }
                                        $res = $turnoModel->listarTurnoActualPorSalaYestadoDoce($ventanillas["salas_nombreSala"], $ventanillas["ventanillas_nombreVentanilla"]);                            

                                        if(count($res["datos"])>0){
                                            $turno = 1;

                                            $validarTerceraEdad = "No";

                                            if($res["datos"][0]["preferencia_id"] != "" || $res["datos"][0]["preferencia_id"] != null){
                                                $turno = $res["datos"][0]["abreviacionModulo"] . "-T-" . $res["datos"][0]["numeroTicket"];
                                            }else{
                                                $turno = $res["datos"][0]["abreviacionModulo"] ."-". $res["datos"][0]["numeroTicket"];
                                            }

                                        }else{

                                            $resUltimoLLamado = $turnoModel->mostrarUltimoLlamadoEnCasoDeEstarSinLlamadoEnView($ventanillas["salas_nombreSala"], $ventanillas["ventanillas_nombreVentanilla"]);
                                            //print_r($resUltimoLLamado);
                                            if($resUltimoLLamado["datos"][0]["id"] != "" || $resUltimoLLamado["datos"][0]["id"] != null){

                                                if($resUltimoLLamado["datos"][0]["preferencia_id"] != "" || $resUltimoLLamado["datos"][0]["preferencia_id"] !=null){
                                                    $turno = $resUltimoLLamado["datos"][0]["abreviacionModulo"] . "-T-" . $resUltimoLLamado["datos"][0]["numeroTicket"];
                                                }else{
                                                    $turno = $resUltimoLLamado["datos"][0]["abreviacionModulo"] . "-" . $resUltimoLLamado["datos"][0]["numeroTicket"];
                                                }    
                                                                                            
                                            }else{
                                                $turno = "SIN LLAMADO";
                                            }

                                            
                                        }
                                        // echo "###";
                                        ?>

                                            <div class="col-6 text-center p-3 mt-1" 
                                                style="background-color:<?=$color?>;"
                                                id="contenedorventanilla-<?=str_replace(" ", "", $ventanillas["ventanillas_nombreVentanilla"])?>" 
                                            >
                                                    <h1 style="font-size: 80px;">
                                                        <?php
                                                            //$ventanillas["ventanillas_nombreVentanilla"]
                                                            $moduloVentanilla = str_replace("Ventanilla", "", $ventanillas["ventanillas_nombreVentanilla"]);
                                                            echo $moduloVentanilla;
                                                        ?>
                                                    </h1>
                                            </div>

                                            <div class="col-6 p-3 mt-1" 
                                                style="background-color:<?=$color?>;" 
                                                id="contenedorTurno-<?=str_replace(" ", "", $ventanillas["ventanillas_nombreVentanilla"])?>"
                                            >
                                                    
                                                    <h1 style="font-size: 80px;" 
                                                        class="d-flex justify-content-center"
                                                        id="contenedorNumeroLLamado-<?=str_replace(" ", "", $ventanillas["ventanillas_nombreVentanilla"])?>" 
                                                    >
                                                            <?=$turno?>
                                                    </h1>


                                            </div>
                                        <?php
                                    }

                                ?>
                        </div>    
                    </div>
              <!---->  
            </div>
            
        </div>
        <div class="col-4">
                        
                            <div class="col-12" style="width:625px;">
                                <img src="./src/img/logo_transparente_bl_1.png">
                            </div>
                            <div class="col-4" id="contenedorPropagandaHJ">
                                
                                <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        <?php for ($i = 0; $i < count($noticias); $i++) { ?>
                                            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="<?= $i ?>" <?= $i == 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $i + 1 ?>"></button>
                                        <?php } ?>
                                    </div>

                                    <div class="carousel-inner">
                                        <?php for ($i = 0; $i < count($noticias); $i++) { ?>
                                            <?php
                                                
                                            ?>
                                            <div class="carousel-item<?= $i == 0 ? ' active' : '' ?>" data-bs-interval="10000">
                                                <img src="<?= 'http://10.6.21.31/pizarra_hjnc/' . $noticias[$i]["not_foto"] ?>" class="d-block w-100" alt="...">
                                                <div class="carousel-caption d-none d-md-block" style="background:#CFE2FF;">
                                                                                        <h5 class="text-center">
                                                                                            <?php echo htmlentities(str_replace("&nbsp;", " ", $noticias[$i]["not_titulo"]));?>
                                                                                        </h5>
                                                    <br>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
        <div>
            <?php
                $ultimoGinLab = $turnoModel->seleccionarUltimoTurnoPorAbrevmodulo("GINLAB");
                $ultimoOirs = $turnoModel->seleccionarUltimoTurnoPorAbrevmodulo("OIRS");
            ?>
        <div class="col-4 mt-0">
                        <?php
                            if($ultimoGinLab["status"] == 200){

                                if(count($ultimoGinLab["datos"]) > 0){
                                    //recordar que antes del 14-11-23 existia el turno con preferencia, sera considera por si se vuelve activar.
                                    if($ultimoGinLab["datos"][0]["preferencia"] == "" || $ultimoGinLab["datos"][0]["preferencia"] == null){
                                        $turnoGinLab = $ultimoGinLab["datos"][0]["abreviacionModulo"] . "-" . $ultimoGinLab["datos"][0]["numeroTicket"];
                                    }else{
                                        $turnoGinLab = $ultimoGinLab["datos"][0]["abreviacionModulo"] . "-T-" . $ultimoGinLab["datos"][0]["numeroTicket"];
                                    }
                                }else{
                                    $turnoGinLab = "Sin turno";
                                }

                            }

                            if($ultimoOirs["status"] == 200){

                                if(count($ultimoOirs["datos"]) > 0){
                                    //recordar que antes del 14-11-23 existia el turno con preferencia, sera considera por si se vuelve activar
                                    if($ultimoOirs["datos"][0]["preferencia"] == "" || $ultimoOirs["datos"][0]["preferencia"] == null){
                                        $turnoOirs = $ultimoOirs["datos"][0]["abreviacionModulo"] . "-" . $ultimoOirs["datos"][0]["numeroTicket"];
                                    }else{
                                        $turnoOirs = $ultimoOirs["datos"][0]["abreviacionModulo"] . "-T-" . $ultimoOirs["datos"][0]["numeroTicket"];
                                    }
                                }else{
                                    $turnoOirs = "Sin turno";
                                }

                            }
                        ?>
                            <div class="col-12 mt-1 p-3 bg-info" style="background-color:yellow; width:625px;">
                                <h1>ULTIMOS TURNOS</h1>
                            </div>
                        <?php if($ventanillaGinLabAbierta == "Si"){?>
                            <div class="col-12 mt-1 p-3 bg-info" style="background-color:#CFE2FF !important; width:625px;" id="contenedorUltimoGinLab">
                                <h1 style="color: #212529 !important;" id="ultimoTurnoGinLab"><?=$turnoGinLab?></h1>
                            </div>
                        <?php } ?>
                        <?php if($ventanillaOirAbierta == "Si"){?>
                            <div class="col-12 mt-1 p-3 bg-info" style="background-color:#CFE2FF !important; width:625px;" id="contenedorUltimoOirs">
                                <h1 style="color: #212529 !important;" id="ultimoTurnoOirs"><?=$turnoOirs?></h1>
                            </div>
                        <? } ?>

        </div>
        
<!--x-->
    <?php
    }else{
        ?>
            <style>
                body{
                    background-color: #1A4075 !important;
                }
            </style>
            <div class="container d-flex justify-content-center align-items-center vh-100" style="background-color:#1A4075;">
                <img src="http://10.6.21.19/estandar(V2)/img/logo_hospital.png" height="200" alt="Hospital Logo" class="mx-auto">
            </div>
        <?php
    }
    ?>
    
    </div>
<script src="http://10.6.21.29:3001/socket.io/socket.io.min.js"></script>
<script src="./src/js/monitor.js?v=1"></script>
<script>
  function mostrarHora() {
    const elementoHora = document.getElementById("hora");
    const horaActual = new Date();
    let hora = horaActual.getHours();
    const minutos = horaActual.getMinutes().toString().padStart(2, '0');
    const segundos = horaActual.getSeconds().toString().padStart(2, '0');
    let periodo = "AM"; // Por defecto, asumimos que es AM
    
    if (hora >= 12) {
      // Si la hora es 12 o más, cambiamos el período a PM
      periodo = "PM";
      if (hora > 12) {
        hora -= 12; // Convertimos la hora en un formato de 12 horas
      }
    }

    const horaFormateada = `${hora}:${minutos}:${segundos} ${periodo}`;
    elementoHora.innerText = horaFormateada;
  }

  // Actualiza la hora cada segundo
  setInterval(mostrarHora, 1000);

  // Llama a la función mostrarHora al cargar la página
  mostrarHora();
  document.body.style.overflow = 'hidden';
</script>
</body>
</html>
