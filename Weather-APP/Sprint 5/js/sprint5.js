var o;
o = $(document);
o.ready(controlador);

//Realiza la siguiente función al cargar la página
function controlador(){
    
    //Wannabe de hacer un toast chulo
    //$("#snackbar").html("Datos intoducidos inválidos");
    
    //Llamo a la función más abajo definida para que me actualice los datos según el ID definido en el SELECT
    change();

    /*Aquí he intentado que el Favoritos cambie la consulta del Ajax pero ha sido un fracaso
    
    cityfav();

    function cityfav(){
        var fav = $("#cityFav").val();
        $("#citySelected").val(fav);
        var hola = $("#citySelected").val();
        console.log(hola)
        console.log(fav)
    }*/ 

    //Creo un evento que llama a la función más abajo definida para que se ejecute cada vez que se cambie el SELECT
    $("#citySelected").change(change);

    //Función que modifica la url de la API en función de la ciudad que se quiera consultar a través de la elección del SELECT
    function change(){
        
    city = $("#citySelected").val();

    var urlgetCurrent = "http://api.openweathermap.org/data/2.5/weather?id="+city+"&units=metric&APPID=72b335f0f538911bb5af1277cf36cb19";
    var urlgetForecast = "http://api.openweathermap.org/data/2.5/forecast?id="+city+"&units=metric&APPID=72b335f0f538911bb5af1277cf36cb19";
       
    //Solicitud de conexión con el servidor(API) a través de AJAX, la url de la API dependerá de la información que queramos recibir
    $.ajax({
        type: "GET",
        dataType: "json",
        url: urlgetCurrent,
        //Si el enlace es exitoso-> La API nos devuelve un JSON
        success: function(currentData){

            //Consulta el icon metereológico recibido del request y lo pasamos como parámetro del src de la imagen de HTML
            var img = currentData.weather[0].icon;
            $("#imgCurrent").attr("src", "img/"+img+"@2x.png");

            //Consulta el nombre recibido del request
            $("#name").html(currentData.name);
            $("#oc").attr("value", currentData.id);

            //Consulta la temperatura actual recibida del request
            $("#mainTemp").html(Math.round(currentData.main.temp) + "ºC");

            //Consulta las temperaturas máximas y mínimas recibidas del request
            $("#maxMin").html("Máx. " + Math.round(currentData.main.temp_max) + "ºC / Min. " + Math.round(currentData.main.temp_min) + "ºC");

            //Función para extraer la fecha del dispositivo en el momento del request
            var dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
            var d = new Date(currentData.dt*1000);
            var output = d.getDate() + "/" + (d.getMonth()+1) + "/" + d.getFullYear();  
            var dia = d.getDay();
            var finalDate = dias[dia] + ",  " + output

            //Consulta el día del request(Devuelto por la API en UNIX)
            //Consulta la fecha del request dd/mm/aaaa(Devuelta por la API en UNIX)
            $("#day").html(finalDate);

            //Consulta la velocidad del viento recibida del request
            $("#wind").html(currentData.wind.speed + " m/s");

            //Consulta el porcentaje de nubosidad recibido del request
            $("#cloud").html(currentData.clouds.all + " %");
            
            //El request nos retorna lluvia y nieve solo si hay datos, así que hacemos una función para que si no nos devuelve datos
            //nos comunique un mensaje, y si recibe datos, los muestre.
            //Lo de las 3h es porque solo queremos que nos de datos de la hora anterior.

            if(currentData.rain === undefined || currentData.rain["3h"] != null){
                $("#rain").html("0 mm³ en la última hora");
            }
            else{
                $("#rain").html(currentData.rain["1h"] + " mm³ en la última hora");
            };
            if(currentData.snow === undefined || currentData.snow["3h"] != null){
                $("#snow").html("0 mm³ en la última hora");
            }
            else{
                $("#snow").html(currentData.snow["1h"] + " mm³ en la última hora");
            };
        },

        //Si el enlace no es exitoso-> Nos muestra el siguiente mensaje
        error: function(){
            console.log("No se recibió respuesta");
        }
    });

    //Solicitud de conexión con el servidor(API) a través de AJAX
    $.ajax({

        type: "GET",
        dataType: "json",
        url: urlgetForecast,
        //Si el enlace es exitoso-> La API nos devuelve un JSON
        success: function(forecastData){

            var imgDay1 = forecastData.list[7].weather[0].icon;
            var imgDay2 = forecastData.list[15].weather[0].icon;
            var imgDay3 = forecastData.list[23].weather[0].icon;
            var imgDay4 = forecastData.list[31].weather[0].icon;
            var imgDay5 = forecastData.list[39].weather[0].icon;
            
            $("#imgDay1").attr("src", "img/"+imgDay1+"@2x.png");
            $("#imgDay2").attr("src", "img/"+imgDay2+"@2x.png");
            $("#imgDay3").attr("src", "img/"+imgDay3+"@2x.png");
            $("#imgDay4").attr("src", "img/"+imgDay4+"@2x.png");
            $("#imgDay5").attr("src", "img/"+imgDay5+"@2x.png");

            //Función para extraer la fecha del dispositivo en el momento del request
            var dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
            function dia(){
                var dia = new Date(forecastData.list[0].dt*1000);
                var f = (dia.getDay());
                return f;
            }

            //Para mostrar los 5 días siguientes al actual, realizamos la siguiente operación:
            //Módulo de ((la posición del array "días" en el día actual(x), más 1 si es al siguiente día, más dos si es dentro de dos días, etc...) dividido entre 7)->
            // (x+1,2,3,4 ó 5)%7
            //El resultado será la posición en el array del día que queramos mostrar 
            $("#day1").html(dias[(dia()+1)%7]);
            $("#day2").html(dias[(dia()+2)%7]);
            $("#day3").html(dias[(dia()+3)%7]);
            $("#day4").html(dias[(dia()+4)%7]);
            $("#day5").html(dias[(dia()+5)%7]);
            
            //Mostramos la temperatura media de las siguientes 24, 48, 72... horas desde la consulta(sumando los 8 datos y dividiéndolos entre 8),
            //asumiendo así que esa será la temperatura media de ese día, 
            //ya que la API solo nos devuelve 40 datos, separados en intervalos de 3 horas
            //Las almaceno en variables por si quisiera mostrarlas en el html (he decidido que no quiero por ahora)
            var tempDay1 = (Math.round((forecastData.list[0].main.temp + forecastData.list[1].main.temp + 
                forecastData.list[2].main.temp + forecastData.list[3].main.temp + forecastData.list[4].main.temp +
                forecastData.list[5].main.temp + forecastData.list[6].main.temp + forecastData.list[7].main.temp)/8) + " ºC");
            var tempDay2 = (Math.round((forecastData.list[8].main.temp + forecastData.list[9].main.temp + 
                forecastData.list[10].main.temp + forecastData.list[11].main.temp + forecastData.list[12].main.temp +
                forecastData.list[13].main.temp + forecastData.list[14].main.temp + forecastData.list[15].main.temp)/8) + " ºC");
            var tempDay3 = (Math.round((forecastData.list[16].main.temp + forecastData.list[17].main.temp + 
                forecastData.list[18].main.temp + forecastData.list[19].main.temp + forecastData.list[20].main.temp +
                forecastData.list[21].main.temp + forecastData.list[22].main.temp + forecastData.list[23].main.temp)/8) + " ºC");
            var tempDay4 = (Math.round((forecastData.list[24].main.temp + forecastData.list[25].main.temp + 
                forecastData.list[26].main.temp + forecastData.list[27].main.temp + forecastData.list[28].main.temp +
                forecastData.list[29].main.temp + forecastData.list[30].main.temp + forecastData.list[31].main.temp)/8) + " ºC");
            var tempDay5 = (Math.round((forecastData.list[32].main.temp + forecastData.list[33].main.temp + 
                forecastData.list[34].main.temp + forecastData.list[35].main.temp + forecastData.list[36].main.temp +
                forecastData.list[37].main.temp + forecastData.list[38].main.temp + forecastData.list[39].main.temp)/8) + " ºC");

            //Mostramos la temperatura máxima y mínima de los siguientes días
            //para ello extraemos de la respuesta de la API el día al que corresponde cada posición del array de respuesta
            //ponemos un cap de temperatura máx y min "imposible" (100ºC) y al recorrer el bucle, sustituimos esa cifra para cada día
            //quedándonos con la mayor o menor que haya salido en el día correspondiente.
            currentDate = new Date();
            currentDate.setHours(0,0,0,0);

            var minAux1 = 100;
            var maxAux1= -100;

            var minAux2 = 100;
            var maxAux2= -100;

            var minAux3 = 100;
            var maxAux3= -100;

            var minAux4 = 100;
            var maxAux4= -100;

            var minAux5 = 100;
            var maxAux5= -100;

            $.each(forecastData.list, function(index, value){
               
                apiDay = new Date(value.dt_txt);
                apiDay.setHours(0,0,0,0);
                var diff = (apiDay - currentDate)
                diff = diff / (1000*3600*24);
         
                if (diff == 1){
                    arr1 = value.main.temp_max;
                    if(arr1 > maxAux1){
                        maxAux1 = arr1;
                    }
                    if(arr1 < minAux1){
                        minAux1 = arr1;
                    }

                    $("#maxMinDay1").html("Máx. " + Math.round(maxAux1) + "ºC / Min. " + Math.round(minAux1) + "ºC");    
                };
                if (diff == 2){
                    arr2 = value.main.temp_max;
                    if(arr2 > maxAux2){
                        maxAux2 = arr2;
                    }
                    if(arr2 < minAux2){
                        minAux2 = arr2;
                    }

                    $("#maxMinDay2").html("Máx. " + Math.round(maxAux2) + "ºC / Min. " + Math.round(minAux2) + "ºC");    
                };
                if (diff == 3){
                    arr3 = value.main.temp_max;
                    if(arr3 > maxAux3){
                        maxAux3 = arr3;
                    }
                    if(arr3 < minAux3){
                        minAux3 = arr3;
                    }

                    $("#maxMinDay3").html("Máx. " + Math.round(maxAux3) + "ºC / Min. " + Math.round(minAux3) + "ºC");    
                };
                if (diff == 4){
                    arr4 = value.main.temp_max;
                    if(arr4 > maxAux4){
                        maxAux4 = arr4;
                    }
                    if(arr4 < minAux4){
                        minAux4 = arr4;
                    }

                    $("#maxMinDay4").html("Máx. " + Math.round(maxAux4) + "ºC / Min. " + Math.round(minAux4) + "ºC");    
                };
                if (diff == 5){
                    arr5 = value.main.temp_max;
                    if(arr5 > maxAux5){
                        maxAux5 = arr5;
                    }
                    if(arr5 < minAux5){
                        minAux5 = arr5;
                    }

                    $("#maxMinDay5").html("Máx. " + Math.round(maxAux5) + "ºC / Min. " + Math.round(minAux5) + "ºC");    
                };

            });
            
        },
        //Si el enlace no es exitoso-> Nos muestra el siguiente mensaje
        error: function(){
            console.log("No se recibió respuesta");
        }
    });
    
    //Actualiza la búsqueda sin refrescar la página
    $("#refresh").click(function(){
        controlador();
    });
    };
};


//Cambia el HTML cuando clicamos el botón Log In
$("#logIn").click(function(){
    $("#user button").hide();
    var first = $("#user").prepend("<form class='form-inline lol' action='php/controlador.php' method='post'>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%;'><label for='email' class='sr-only'>Email</label><input type='email' class='form-control col-md-11 col' id='email' name='email' placeholder='Email' required></div>" + 
    "<div class='form-group mb-2 col-md-6 col'><label for='pass' class='sr-only'>Password</label><input type='password' class='form-control col-md-11 col' id='pass' name='pass' placeholder='Password' required></div>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%; display: flex; justify-content: center;'><button type='submit' class='btn btn-info mb-2' name='submit' id='submit' style='color: #353A40; background-color: #0096F6'>Log In</button></div>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%; display: flex; justify-content: center;'><button type='button' class='btn btn-outline-info mb-2' name='cancel' id='cancel' style='color: #0096F6; border-color: #0096F6'>Cancel</button></div>" + 
    "<input type='hidden' name='oculto' value='1'></form>");
    
    //Devuelve el HTML original cuando clicamos el botón Cancel
    $("#cancel").click(function(){
        $(".lol").remove();
        $("#user button").show();
    });
});


//Cambia el HTML cuando clicamos el botón Register
$("#register").click(function(){
    $("#user button").hide();
    var first = $("#user").prepend("<form class='form-inline lolRegister' action='php/controlador.php' method='post'>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%;'><label for='emailRegister' class='sr-only'>Email</label><input type='email' class='form-control col-md-11 col' id='emailRegister' name='emailRegister' placeholder='Email' required></div>" + 
    "<div class='form-group mb-2 col-md-6 col'><label for='passRegister' class='sr-only'>Password</label><input type='password' class='form-control col-md-11 col' id='passRegister' name='passRegister' placeholder='Password' required></div>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%; display: flex; justify-content: center;'><button type='submit' class='btn btn-info mb-2' name='submitRegister' id='submitRegister' style='color: #353A40; background-color: #0096F6'>Register</button></div>" +
    "<div class='form-group mb-2 col-md-6 col' style='width: 100%; display: flex; justify-content: center;'><button type='button' class='btn btn-outline-info mb-2' name='cancelRegister' id='cancelRegister' style='color: #0096F6; border-color: #0096F6'>Cancel</button></div>" + 
    "<input type='hidden' name='oculto' value='2'></form>");
    
    //Devuelve el HTML original cuando clicamos el botón Cancel
    $("#cancelRegister").click(function(){
        $(".lolRegister").remove();
        $("#user button").show();
    });
});