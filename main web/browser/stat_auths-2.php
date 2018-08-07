
<head>
  <link rel="stylesheet" href="/browser/static/css/bootstrap.css">
  <link rel="stylesheet" href="/browser/static/css/bootstrap-theme.css">
  <title> Formulario para cambio de vales </title>
</head>

<body style="background-color:#F0F3F4;">

<br>
<br>
<br>
<p></p>
<center>
     <img src="campus.png" alt="Solvetic">
<br>
<p></p>

<legend>Formulario para cambio de vales</legend>
</center>


<p></p>

<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
  
  
</div>
<div class="col-md-4"></div>
</div>
<br>

<p></p>

<center>
<H4>
<?php
$origen = $_SERVER['REQUEST_URI'];


date_default_timezone_set('Europe/Madrid');
$fecha_actual=date("Y/m/d h:i");
$datetime1 = new DateTime($fecha_actual);


/**
 * PHP API usage example
 *
 * contributed by: Art of WiFi
 * description: example basic PHP script to create a set of vouchers, returns an array containing the newly created vouchers
 */

/**
 * using the composer autoloader
 */
require_once('vendor/autoload.php');

/**
 * include the config file (place your credentials etc. there if not already present)
 * see the config.template.php file for an example
 */
require_once('config.php');
error_reporting(0);

/**
 * The site where you want to create the voucher(s)
 */
$site_id 					= '';
$controlleruser				= '';
$controllerpassword			= '';
$controllerurl				= '';
$site_id					= '';
$controllerversion			= '';
$findThisVoucher   			= $_POST['voucher_input'];
$room   					= $_POST['room'];

/**
 * initialize the UniFi API connection class and log in to the controller ---------------------------------------------------------
 */
$unifi_connection 			= new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   			= $unifi_connection->set_debug($debug);
$loginresults     			= $unifi_connection->login();

/*
 * Comprobamos si la longitud del vale es de 10
 */
$findThisVoucher  = str_replace('-', '', $findThisVoucher); 
 
$longitud_voucher = strlen($findThisVoucher); // sacamos longitud del vale

if ($longitud_voucher < 10) { // si es menor de 10 devuelve el siguiente mensaje
    echo "El vale $findThisVoucher no se ha introducido correctamente, tiene menos de 10 carácteres. ";
    $findThisVoucher = false;
}
if ($longitud_voucher > 10) { // si es mayor de 10 devuelve el siguiente mensaje
    echo "El vale $findThisVoucher no se ha introducido correctamente, tiene mas de 10 carácteres. ";
    $findThisVoucher = false;
	//echo "----$findThisVoucher---";
}


// lo necesario para obtener la mac a partir de un voucher
$data_auths			 = $unifi_connection->stat_auths(); 			 // Se llama a la funcion "stat_auths" para obtener las estadisticas
$status_find 		 = json_encode($data_auths, JSON_PRETTY_PRINT);  // las estadisticas obtenidas se codifican en un json
$json_mac 			 = json_decode($status_find); 					 // se decodifica para recorrerlo con un foreach
$mac_device		 	 = null;										 // establecemos la variable a null para "llevar un control de error"

// lo necesario para obtener el estado de "expired"
$data_guests 		 = $unifi_connection->list_guests(); 			 // Se llama a la funcion "list_guests" para obtener las estadisticas 
$mac_find 			 = json_encode($data_guests, JSON_PRETTY_PRINT); // las estadisticas obtenidas se codifican en un json
$json_expired 		 = json_decode($mac_find);						 // se decodifica para recorrerlo con un foreach


/**
*Recorremos el array para buscar la mac, si se encuentra se almacena en $mac_device. Si no la encuentra su estado sigue en nul
*/
foreach ($json_mac as $record) {
    $num_voucher = $record->voucher_code; // por cada posicion recorrida almacenamos en $num_voucher el voucher_code del array
    
    if ($num_voucher === $findThisVoucher) { // se hace la comprobacion, si coincide se almacena y sale del foreach
        $mac_device = $record->mac;
		//echo "-- $mac_device --";
		break;
		
    }
}

if ($mac_device === null) { // si es null se mostrará el mensaje
    echo "no se ha encontrado el vale o no está activo";
	$note   = '[Cambio de vale ERROR] ' . $room . ' ' . $fecha_actual . ' >> C:\xampp\logs\log-API.txt [not found] ' ;
	exec("echo $note");
} else {
    foreach ($json_expired as $record) { // si no es null se recorre el otro array para buscar el estado de expired
        $mac = $record->mac;
        $status_expired = $record->expired;
        
        if ($mac_device === $mac) { // Cuando coincida la mac comprobamos el campo expired
            //echo "--[$mac]--";
			//echo "--[$mac_device]--";
            if ($status_expired != null) {				
                echo "El vale no está activo";
				$note   = '[Cambio de vale ERROR] ' . $room . ' ' . $fecha_actual . ' >> C:\xampp\logs\log-API.txt [not active] ' . $findThisVoucher . ' ' .$mac_device  ;
				exec("echo $note");
            } else {
                echo "El vale está activo"; // si está activo se anula y se crea un vale nuevo
				//$unifi_connection->unauthorize_guest($mac_device); // desautorizamos el dispositivo
				include 'create.php';
                break;
            }
        }
    }
}
?>
</H4>

<form action="http://172.26.0.250:8081/index.html">
    <input type="submit" value="volver" />
</form>

</center>
<br>
<br>
<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4"><center><img src="algoritmo.png" alt="Solvetic">
  <p> Para incidencias:</p>
  <p>incidencias@algoritmoingenieria.com</p>
  </center></div>
  <div class="col-md-4"></div>
</div>


<script src="/browser/static/js/jquery-3.3.1.js"></script>		
<script src="/browser/static/js/bootstrap.js"></script>
</body>





