var socket = io("10.6.21.29:3001");
            
            socket.on("abrir-cerrar-ventanilla-cliente", (data) => {
                console.log(data);
                location.reload();
            });

            var listadoVentanillasDisponibles = [];

            async function listarVentanillasActuales(){
                listadoVentanillasDisponibles = [];
                
                let data = await fetch("http://10.6.21.29:3001/totem-hall/monitor");

                let response = await data.json();

                response.forEach(function(dato){
                    //console.log(dato.ventanillas_salas)
                    listadoVentanillasDisponibles.push(dato.ventanillas_salas);
                });
            }

            document.addEventListener("DOMContentLoaded", async function(){
                listarVentanillasActuales();


            });

            function reproduciorSonido(){
                const sonidoSrc = "http://10.6.21.19/Totem_Hall_Monitor_Web_exe/sounds/SD_ALERT_3.mp3";
                new Audio(sonidoSrc).play();
            }

            const animacionesEnCola = new Map();

            socket.on("llamando-cliente", (data) => {
                console.log(data)
                
                let ventanillaAux = data.nombreVentanilla.replace(/\s/g, '');
                let contenedorTurnoAux = document.getElementById('contenedorTurno-' + ventanillaAux);

                let ventanillaTurnoAux = data.nombreVentanilla.replace(/\s/g, '');
                let contenedorventanillaTurnoAux = document.getElementById('contenedorventanilla-' + ventanillaAux);
                let logoPrincipal = document.getElementById('logoPrincipal');
                let logoPrincipalCargando = document.getElementById('logoPrincipalCargando');
                let contenedorNumeroTurnoAux = document.getElementById('contenedorNumeroLLamado-' + ventanillaAux);
                
                listadoVentanillasDisponibles.forEach(function (ventanilla) {
                    if (ventanilla.ventanillas_nombreVentanilla === data.nombreVentanilla) {
                        //console.log(ventanilla)

                        contenedorNumeroTurnoAux.textContent = data.numero;
                        reproduciorSonido();

                        // Agrega la clase "parpadeo" al elemento
                        contenedorTurnoAux.classList.add("parpadeo");
                        contenedorventanillaTurnoAux.classList.add("parpadeo");
                        logoPrincipal.style.display = "none";
                        logoPrincipalCargando.style.display = "block";
                        // Establece una duración en segundos para el parpadeo (por ejemplo, 5 segundos)
                        const duracionSegundos = 5;
                        const duracionMilisegundos = duracionSegundos * 1000;

                        // Verifica si ya hay una animación en cola para este elemento
                        if (animacionesEnCola.has(contenedorTurnoAux) || animacionesEnCola.has(contenedorventanillaTurnoAux)) {
                            // Detiene la animación actual en cola
                            cancelAnimationFrame(animacionesEnCola.get(contenedorTurnoAux));
                            cancelAnimationFrame(animacionesEnCola.get(contenedorventanillaTurnoAux));
                        }

                        // Inicia un bucle de animación con requestAnimationFrame
                        let startTime = null;
                        function parpadear(timestamp) {
                            if (!startTime) startTime = timestamp;
                            const tiempoTranscurrido = timestamp - startTime;

                            if (tiempoTranscurrido < duracionMilisegundos) {
                                // Sigue parpadeando
                                animacionesEnCola.set(contenedorTurnoAux, requestAnimationFrame(parpadear));
                                // animacionesEnCola.set(contenedorventanillaTurnoAux, requestAnimationFrame(parpadear));
                                logoPrincipal.style.display = "none";
                                logoPrincipalCargando.style.display = "block";
                            } else {
                                // Detén el parpadeo después de la duración especificada
                                contenedorTurnoAux.classList.remove("parpadeo");
                                animacionesEnCola.delete(contenedorTurnoAux);
                                contenedorventanillaTurnoAux.classList.remove("parpadeo");
                                logoPrincipal.style.display = "block";
                                logoPrincipalCargando.style.display = "none";
                                // animacionesEnCola.delete(contenedorventanillaTurnoAux);
                            }
                        }

                        animacionesEnCola.set(contenedorTurnoAux, requestAnimationFrame(parpadear));
                    }
                });
            });

            //14-11-23
            socket.on("numero-total-por-sala-cliente", (data) => {
                console.log("en: numero-total-por-sala-cliente");
                console.log(data);

                var cadena = data?.numeroTurno;
                var GINLAB = "GINLAB";

                if(cadena.includes(GINLAB)) {
                    console.log("La cadena contiene la palabra GINLAB");
                    let ultimoTurnoGinLab = document.getElementById("ultimoTurnoGinLab");
                    ultimoTurnoGinLab.textContent = data?.numeroTurno;
                }else{
                    console.log("La cadena NO contiene la palabra GINLAB");
                }

                var OIRS = "OIRS";

                if(cadena.includes(OIRS)) {
                    console.log("La cadena contiene la palabra OIRS");
                    let ultimoTurnoOirs = document.getElementById("ultimoTurnoOirs");
                    ultimoTurnoOirs.textContent = data?.numeroTurno;
                }else{
                    console.log("La cadena NO contiene la palabra OIRS");
                }
            });
            //14-11-23


            