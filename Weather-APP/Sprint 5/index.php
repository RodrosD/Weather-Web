<?php
session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <!-- PHP CONEX -->
    <?php
    $link= mysqli_connect("localhost", "root", "", "sprint5");
    mysqli_set_charset($link, 'utf8');
    ?>
    
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap // CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://miniature.earth/demo/hologram/style.css">

    <!-- Estilo para el canvas -->
    <style>
      .earth-container{position:relative;z-index:1}.earth-container::before{content:"";display:block;padding-top:100%}.earth-container>canvas{position:absolute;top:0;left:0;z-index:1000;user-select:none}
      .earth-draggable{cursor:all-scroll;cursor:-webkit-grab;cursor:grab}.earth-dragging *{cursor:all-scroll;cursor:-webkit-grabbing!important;cursor:grabbing!important}.earth-clickable{cursor:pointer}
      .earth-overlay{position:absolute;top:0;left:0;user-select:none;pointer-events:none;transform-origin:0 0}.earth-overlay a,.earth-overlay input,.earth-overlay button{pointer-events:all}
      #earth-hittest{position:fixed;max-width:100%;top:0;left:0;z-index:100000}#earth-hittest svg{max-width:100%;height:auto;display:block;margin:0;opacity:0}
    </style>

    <title>El Tiempo</title>

  </head>
  <body>
    <!-- Comprobamos si la sesión está iniciada -->
    <?php
      if (isset($_SESSION['type'])){
        if ($_SESSION['type'] == 'u') {?>
          <!-- HEADER -->
          <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="display: flex;">
              <span class="navbar-text col col-3 col-xs-3 col-md-3 col-lg-3">
                Best weather web ever? Well... Nope
              </span>
              <div class="dropdown col col-2 col-xs-2 col-md-2 col-lg-2">
                <p class="col col-5 col-xs-5 col-md-5 col-lg-5" style="color: #0096F6; text-align: center;">Favorites</p>
                <select id="cityFav" class="custom-select col col-5 col-xs-5 col-md-5 col-lg-5" name="cityFav">
                      <?php
                        $x = $_SESSION['id'];
                        $con = "SELECT city.id_city, city.name, cityuser.fk_city, cityuser.fk_user, users.email FROM cityuser
                        INNER JOIN city ON city.id_city = cityuser.fk_city INNER JOIN users ON users.email = cityuser.fk_user 
                        WHERE users.email= '$x'";
                        $r = mysqli_query($link, $con);
                        while ($arr = mysqli_fetch_array($r)) { ?>
                        <option value="<?php echo $arr[0]; ?>">
                          <?php echo $arr[1]; ?>
                        </option>
                      <?php      }  ?>
                    </select>
                </div>
              </div>
              <div class="input-group-sm seleccion col col-2 col-xs-2 col-md-2 col-lg-2">
                <div class="row">
                  <div class="col col-9 col-xs-9 col-md-9 col-lg-9">
                    <select id="citySelected" class="custom-select" name="city">
                      <?php
                        $con = "SELECT id_city, name FROM city";
                        $r = mysqli_query($link, $con);
                        while ($arr = mysqli_fetch_array($r)) { ?>
                        <option value="<?php echo $arr[0]; ?>" <?php if($arr[1] === "Madrid" or $arr[0] === "6359304"){echo "selected";}?>>
                          <?php echo $arr[1]; ?>
                        </option>
                      <?php      }  ?>
                    </select>
                  </div>
                  <div class="col col-1 col-xs-1 col-md-1 col-lg-1">
                  </div>
                  <div class="col col-2 col-xs-2 col-md-2 col-lg-2" class="refresh">
                    <button id="refresh" class="btn btn-outline-info btn-md refrescar" style="color: #0096F6; border-color: #0096F6">Refresh</button>
                  </div>       
                </div>
              </div>
              <div class="col col-2 col-xs-2 col-md-2 col-lg-2">
              </div>  
              <div id="user" class="input-group col col-3 col-xs-3 col-md-3 col-lg-3">
                <span class="col col-5 col-xs-5 col-md-5 col-lg-5" style="color: #0096F6; text-align: center; margin-top:9px">
                  Bienvenida/o!!!
                </span>
                <div class="col col-2 col-xs-2 col-md-2 col-lg-2">
                </div>
                <form action="php/controlador.php" method="post" class="col col-5 col-xs-5 col-md-5 col-lg-5">
                  <button class="btn btn-outline-info btn-mb" type="submit" id="logOut" style="color: #0096F6; border-color: #0096F6">Log Out</button>
                  <input type="hidden" name="oculto" value="3">
                </form>
              </div>
            </nav>
          </header>

          <!-- CUERPO -->
          <section class="container-fluid">
            <div class="row tal">
              <!--3 columnas para el tiempo actual -->
              <div class="col col-3 col-xs-3 col-md-3 col-lg-3 justify-content-center align-items-center cprincipal" style="display: flex">
                <br>
                <div class="card row infoprin" style="display: flex;">   
                  <div class="row card-body">
                    <div class="col col-7 col-xs-7 col-md-7 col-lg-7 justify-content-end" style="margin: 0; display: flex;">
                      <img src="" class="imgprin card-img" alt="tiempo" id="imgCurrent">
                    </div>
                    <div class="col col-1 col-xs-1 col-md-1 col-lg-1">
                    </div>
                    <div class="tempactual col col-4 col-xs-4 col-md-4 col-lg-4 justify-content-start" style="margin: 0; display: flex; align-items:center">
                      <h1 style="color: #0096F6" id="mainTemp"></h1> 
                    </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row" style="margin: 15px;">
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12" style="display: flex; justify-content: center">
                      <div class="col col-3 col-xs-3 col-md-3 col-lg-3" style="display: flex; justify-content: center">
                        <form action="php/controlador.php" method="post">
                          <input class="btn btn-outline-info btn-mb" type="submit" id="addFav" style="color: #0096F6; border-color: #0096F6" value="Fav +">
                          <input type="hidden" name="oculto" value="4">
                          <input type="hidden" id="oc" name="name" value="">
                        </form>  
                      </div>
                      <div class="col col-6 col-xs-6 col-md-6 col-lg-6" style="display: flex; justify-content: center">
                        <h2 style="color: #0096F6; justify-content: center" id="name" name="name"></h2>
                      </div>
                      <div class="col col-3 col-xs-3 col-md-3 col-lg-3" style="display: flex; justify-content: center">
                      </div>
                    </div>
                    <br>
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="row">
                        <h6 class="col col-12 col-xs-12 col-md-12 col-lg-12 justify-content-center" style="display: flex;" id="day"></h6>
                      </div>
                    </div>
                    <br>
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12 justify-content-center" style="display: flex;">
                      <h4 id="maxMin"></h4>
                    </div>
                    <br>
                  </div>

                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <br>
                  <div class="row">        
                    <table class="col col-12 col-xs-12 col-md-12 col-lg-12">
                      <tbody class="col col-12 col-xs-12 col-md-12 col-lg-12">
                        <tr class="row justify-content-center">
                          <td class="justify-content-center" style="text-align: center"><h6 style="color: #0096F6">Velocidad del viento</h6>
                              <p id="wind"></p>
                            
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Nubosidad</h6>
                            <p id="cloud"></p>
                            
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Volumen de lluvia</h6>
                            <p id="rain"></p>
                          
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Volumen de nieve</h6>
                            <p id="snow"></p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <!--6 columnas para el canvas -->
              <div class="col-xs-6 col-md-6 col-lg-6">
                <div id="myearth" class="earth-container earth-ready">
                  <div id="glow">
                  </div>
                  <canvas width="666" height="657" style="width: 666px; height: 657px;"></canvas>
                </div>
              </div>
              <!--3 columnas para el pronostico 5 dias -->          
              <div class="col-xs-3 col-md-3 col-lg-3 justify-content-center align-items-center cprincipal" style="margin-top: 20px; padding: 0; width: 100%; display: flex">

                <div class="row" style="display: flex;">
                  <div class="row">        
                    <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                      <img class="imgdias" src="" alt="Card image cap" id="imgDay1">
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                      <br>
                      <h5 class="card-text" id="day1"></h5>
                      <h6 style="color: #0096F6" id="maxMinDay1"></h6>
                    </div>
                  </div>
                  <br>
                  <br> 
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row">        
                    <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                      <img class="imgdias" src="" alt="Card image cap" id="imgDay2">
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                      <br>
                      <h5 class="card-text" id="day2"></h5>
                      <h6 style="color: #0096F6" id="maxMinDay2"></h6>
                    </div>
                  </div>
                  <br>
                  <br> 
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row">        
                    <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                      <img class="imgdias" src="" alt="Card image cap" id="imgDay3">
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                      <br>
                      <h5 class="card-text" id="day3"></h5>
                      <h6 style="color: #0096F6" id="maxMinDay3"></h6>
                    </div>
                  </div>
                  <br>
                  <br> 
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row">        
                    <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                      <img class="imgdias" src="" alt="Card image cap" id="imgDay4">
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                      <br>
                      <h5 class="card-text" id="day4"></h5>
                      <h6 style="color: #0096F6" id="maxMinDay4"></h6>
                    </div>
                  </div>
                  <br>
                  <br> 
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row">        
                    <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                      <img class="imgdias" src="" alt="Card image cap" id="imgDay5">
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                      <br>
                      <h5 class="card-text" id="day5"></h5>
                      <h6 style="color: #0096F6" id="maxMinDay5"></h6>
                    </div>
                  </div>  
                </div>
              </div>
            </div>
          </section>

        <?php } }

      else{ ?>
        <!-- HEADER -->
        <header>
          <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <span class="navbar-text col col-3 col-xs-3 col-md-3 col-lg-3">
              Best weather web ever? Well... Nope
            </span>
            <div class="col col-2 col-xs-2 col-md-2 col-lg-2">
            </div>
            <div class="input-group-sm seleccion col col-2 col-xs-2 col-md-2 col-lg-2">
              <div class="row">
                <div class="col col-9 col-xs-9 col-md-9 col-lg-9">
                  <select id="citySelected" class="custom-select" name="city">
                    <?php
                      $con = "SELECT id_city, name FROM city";
                      $r = mysqli_query($link, $con);
                      while ($arr = mysqli_fetch_array($r)) { ?>
                      <option value="<?php echo $arr[0]; ?>" <?php if($arr[1] === "Madrid" or $arr[0] === "6359304"){echo "selected";}?>>
                        <?php echo $arr[1]; ?>
                      </option>
                    <?php      }  ?>
                  </select>
                </div>
                <div class="col col-1 col-xs-1 col-md-1 col-lg-1">
                </div>
                <div class="col col-2 col-xs-2 col-md-2 col-lg-2" class="refresh">
                  <button id="refresh" class="btn btn-outline-info btn-md refrescar" style="color: #0096F6; border-color: #0096F6">Refresh</button>
                </div>       
              </div>
            </div>
            <div class="col col-2 col-xs-2 col-md-2 col-lg-2">
            </div>  
            <div id="user" class="input-group col col-3 col-xs-3 col-md-3 col-lg-3">
              <button class="btn btn-info btn-md col col-mb-5 col-md-5 col-lg-5" type="button" id="logIn" style="color: #353A40; background-color: #0096F6">Log In</button>
              <div class="col col-2 col-xs-2 col-md-2 col-lg-2">
              </div>
              <button class="btn btn-outline-info btn-mb col col-5 col-xs-5 col-md-5 col-lg-5" type="button" id="register" style="color: #0096F6; border-color: #0096F6">Register</button>
            </div>
          </nav>
        </header>
        <!-- CUERPO -->
        <section class="container-fluid">
          <div class="row tal">
            <!--3 columnas para el tiempo actual -->
            <div class="col col-3 col-xs-3 col-md-3 col-lg-3 justify-content-center align-items-center cprincipal" style="display: flex">
                <br>
                <div class="card row infoprin" style="display: flex;">   
                  <div class="row card-body">
                    <div class="col col-7 col-xs-7 col-md-7 col-lg-7 justify-content-end" style="margin: 0; display: flex;">
                      <img src="" class="imgprin card-img" alt="tiempo" id="imgCurrent">
                    </div>
                    <div class="col col-1 col-xs-1 col-md-1 col-lg-1">
                    </div>
                    <div class="tempactual col col-4 col-xs-4 col-md-4 col-lg-4 justify-content-start" style="margin: 0; display: flex; align-items:center">
                      <h1 style="color: #0096F6" id="mainTemp"></h1> 
                    </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <div class="row" style="margin: 15px;">
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12" style="display: flex; justify-content: center">
                      <div class="col col-12 col-xs-12 col-md-12 col-lg-12" style="display: flex; justify-content: center">
                        <h2 style="color: #0096F6; justify-content: center" id="name"></h2>
                      </div>
                    </div>
                    <br>
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="row">
                        <h6 class="col col-12 col-xs-12 col-md-12 col-lg-12 justify-content-center" style="display: flex;" id="day"></h6>
                      </div>
                    </div>
                    <br>
                    <div class="col col-12 col-xs-12 col-md-12 col-lg-12 justify-content-center" style="display: flex;">
                      <h4 id="maxMin"></h4>
                    </div>
                    <br>
                  </div>

                  <hr style="border: 1px solid #0096F6; width:100%;">
                  <br>
                  <div class="row">        
                    <table class="col col-12 col-xs-12 col-md-12 col-lg-12">
                      <tbody class="col col-12 col-xs-12 col-md-12 col-lg-12">
                        <tr class="row justify-content-center">
                          <td class="justify-content-center" style="text-align: center"><h6 style="color: #0096F6">Velocidad del viento</h6>
                              <p id="wind"></p>
                            
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Nubosidad</h6>
                            <p id="cloud"></p>
                            
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Volumen de lluvia</h6>
                            <p id="rain"></p>
                          
                          </td>
                        </tr>
                        
                        <tr class="row justify-content-center" style="text-align: center">
                          <td class="justify-content-center"><h6 style="color: #0096F6">Volumen de nieve</h6>
                            <p id="snow"></p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <!--6 columnas para el canvas -->
            <div class="col-xs-6 col-md-6 col-lg-6">
              <div id="myearth" class="earth-container earth-ready">
                <div id="glow">
                </div>
                <canvas width="666" height="657" style="width: 666px; height: 657px;"></canvas>
              </div>
            </div>
            <!--3 columnas para el pronostico 5 dias -->          
            <div class="col-xs-3 col-md-3 col-lg-3 justify-content-center align-items-center cprincipal" style="margin-top: 20px; padding: 0; width: 100%; display: flex">

              <div class="row" style="display: flex;">
                <div class="row">        
                  <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                    <img class="imgdias" src="" alt="Card image cap" id="imgDay1">
                  </div>
                  <div class="col-xs-6 col-md-6 col-lg-6">
                    <br>
                    <h5 class="card-text" id="day1"></h5>
                    <h6 style="color: #0096F6" id="maxMinDay1"></h6>
                  </div>
                </div>
                <br>
                <br> 
                <hr style="border: 1px solid #0096F6; width:100%;">
                <div class="row">        
                  <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                    <img class="imgdias" src="" alt="Card image cap" id="imgDay2">
                  </div>
                  <div class="col-xs-6 col-md-6 col-lg-6">
                    <br>
                    <h5 class="card-text" id="day2"></h5>
                    <h6 style="color: #0096F6" id="maxMinDay2"></h6>
                  </div>
                </div>
                <br>
                <br> 
                <hr style="border: 1px solid #0096F6; width:100%;">
                <div class="row">        
                  <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                    <img class="imgdias" src="" alt="Card image cap" id="imgDay3">
                  </div>
                  <div class="col-xs-6 col-md-6 col-lg-6">
                    <br>
                    <h5 class="card-text" id="day3"></h5>
                    <h6 style="color: #0096F6" id="maxMinDay3"></h6>
                  </div>
                </div>
                <br>
                <br> 
                <hr style="border: 1px solid #0096F6; width:100%;">
                <div class="row">        
                  <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                    <img class="imgdias" src="" alt="Card image cap" id="imgDay4">
                  </div>
                  <div class="col-xs-6 col-md-6 col-lg-6">
                    <br>
                    <h5 class="card-text" id="day4"></h5>
                    <h6 style="color: #0096F6" id="maxMinDay4"></h6>
                  </div>
                </div>
                <br>
                <br> 
                <hr style="border: 1px solid #0096F6; width:100%;">
                <div class="row">        
                  <div class="col-xs-6 col-md-6 col-lg-6" style="margin: 0; align-items:center">
                    <img class="imgdias" src="" alt="Card image cap" id="imgDay5">
                  </div>
                  <div class="col-xs-6 col-md-6 col-lg-6">
                    <br>
                    <h5 class="card-text" id="day5"></h5>
                    <h6 style="color: #0096F6" id="maxMinDay5"></h6>
                  </div>
                </div>  
              </div>
            </div>
          </div>
        </section>

      <?php }; ?>
    
     <!-- Script para animación de la bola(svg del halo y mapamundi descargados por ahi y algunas animaciones de los destellos retocadas) *Lo he modificado un poquete*-->
    <script>
      if ( location.protocol == 'file:' ) {
        alert( 'This demo does not work with the file protocol due to browser security restrictions.' );
      }

      var myearth;
      var sprites = [];

      window.addEventListener( 'load', function() {

        myearth = new Earth( document.getElementById('myearth'), {
        
          light: 'none',
          
          texture: 'img/hologram-map.svg',
          transparent: true,
          
          location: { lat: 0, lng : 0 },
          
          autoRotate : true,
          autoRotateSpeed: 1.2,
          autoRotateDelay: 100,
          autoRotateStart: 2000,			
          
        } );	
        
        myearth.addEventListener( "ready", function() {

          this.startAutoRotate();
          
          //Conexiones
          
          var line = {
            color : 'white',
            opacity: 0.2,
            hairline: true,
            offset: -0.5
          };
          
          for ( var i in connections ) {			
            line.locations = [ { lat: connections[i][0], lng: connections[i][1] }, { lat: connections[i][2], lng: connections[i][3] } ];
            this.addLine( line );
          }
              
          //5 puntos brillantes
          
          for ( var i=0; i < 5; i++ ) {
            sprites[i] = this.addSprite( {
              image: 'img/hologram-shine.svg',
              scale: 0.01,
              offset: -0.5
            } );
            pulse( i );
            //setTimeout( function() { pulse( 1 ); }, 300 );
          }
        } );		
      } );

      function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min)) + min;
      }

      function pulse( index ) {
        var random_location = connections[ getRandomInt(0, connections.length-1) ];
        sprites[index].location = { lat: random_location[0] , lng: random_location[1] };
        
        sprites[index].animate( 'scale', 0.6, { easing: 'out-quad', duration: 300, complete : function(){
          this.animate( 'scale', 0.01, { easing: 'in-quad', duration: 300, complete : function(){
            setTimeout( function(){ pulse( index ); }, getRandomInt(100, 400) );
          } });
        } });
      }

      var connections = [
        [59.651901245117,17.918600082397,	41.8002778,12.2388889],
        [59.651901245117,17.918600082397,	51.4706,-0.461941],
        
        [13.681099891662598,100.74700164794922,	-6.1255698204,106.65599823],
        [13.681099891662598,100.74700164794922,	28.566499710083008,77.10310363769531],
        
        [30.12190055847168,31.40559959411621, -1.31923997402,36.9277992249],
        [30.12190055847168,31.40559959411621, 25.2527999878,55.3643989563],
        [30.12190055847168,31.40559959411621, 41.8002778,12.2388889],

        [28.566499710083008,77.10310363769531,	7.180759906768799,79.88410186767578],
        [28.566499710083008,77.10310363769531,	40.080101013183594,116.58499908447266],
        [28.566499710083008,77.10310363769531,	25.2527999878,55.3643989563],

        [-33.9648017883,18.6016998291, -1.31923997402,36.9277992249],
        
        [-1.31923997402,36.9277992249, 25.2527999878,55.3643989563],
        
        [41.8002778,12.2388889, 51.4706,-0.461941],
        [41.8002778,12.2388889, 40.471926,-3.56264],

        [19.4363,-99.072098,	25.79319953918457,-80.29060363769531],
        [19.4363,-99.072098,	33.94250107,-118.4079971],
        [19.4363,-99.072098,	-12.0219,-77.114304],
        
        [-12.0219,-77.114304,	-33.393001556396484,-70.78579711914062],
        [-12.0219,-77.114304, -34.8222,-58.5358],
        [-12.0219,-77.114304, -22.910499572799996,-43.1631011963],
        
        [-34.8222,-58.5358, -33.393001556396484,-70.78579711914062],
        [-34.8222,-58.5358, -22.910499572799996,-43.1631011963],
        
        [22.3089008331,113.915000916, 13.681099891662598,100.74700164794922],
        [22.3089008331,113.915000916, 40.080101013183594,116.58499908447266],
        [22.3089008331,113.915000916, 31.143400192260742,121.80500030517578],
        
        [35.552299,139.779999, 40.080101013183594,116.58499908447266],
        [35.552299,139.779999, 31.143400192260742,121.80500030517578],
        
        [33.94250107,-118.4079971,	40.63980103,-73.77890015],
        [33.94250107,-118.4079971,	25.79319953918457,-80.29060363769531],
        [33.94250107,-118.4079971,	49.193901062,-123.183998108],
        
        [40.63980103,-73.77890015, 25.79319953918457,-80.29060363769531],
        [40.63980103,-73.77890015, 51.4706,-0.461941],
        
        [51.4706,-0.461941, 40.471926,-3.56264],
        
        [40.080101013183594,116.58499908447266,	31.143400192260742,121.80500030517578],
        
        [-33.94609832763672,151.177001953125,	-41.3272018433,174.804992676],
        [-33.94609832763672,151.177001953125,	-6.1255698204,106.65599823],
        
        [55.5914993286,37.2615013123, 59.651901245117,17.918600082397],
        [55.5914993286,37.2615013123, 41.8002778,12.2388889],
        [55.5914993286,37.2615013123, 40.080101013183594,116.58499908447266],
        [55.5914993286,37.2615013123, 25.2527999878,55.3643989563],
      ];
    </script>

    <!-- Jquery -->
    <script
      src="https://code.jquery.com/jquery-3.4.1.js"
      integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
      crossorigin="anonymous">
    </script>

    <!-- JS "robado" de la tierra -->
    <script src="https://miniature.earth/miniature.earth.core.js"></script>

    <!-- Hoja de JS -->
    <script src="js/sprint5.js"></script>

  </body>
</html>