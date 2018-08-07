<?php
if($origen!= "/browser/stat_auths-2.php"){
        header("location:http://172.26.0.250:8081/index.html");
    }
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

// sacamos los días que va a durar un vale 
date_default_timezone_set('Europe/Madrid');
$fecha_actual    = date("Y/m/d h:i"); //Sacamos fecha y hora actuales
$datetime1       = new DateTime($fecha_actual); //Creamos un objeto DateTime con la fecha actual
$datetime2       = new DateTime('2019-07-15'); //Creamos un objeto DateTime con la fecha de final de curso

$interval        = $datetime1->diff($datetime2); //Calculamos la diferencia entre una fecha y otra
$days            = $interval->format('%a'); //Le damos formato para almacenarlo en la variale y poder pasarlo
$date_limit      = 60*24*$days; // hacemos el calculo para saber cuantos minutos son

/**
 * minutes the voucher is valid after activation (expiration time)
 */

$voucher_expiration = $date_limit;

/**
 * the number of vouchers to create
 */
$voucher_count = 1;


$site_id 					= '';
$controlleruser				= '';
$controllerpassword			= '';
$controllerurl				= '';
$site_id					= '';
$controllerversion			= '';

/*
* Parametros que le pasamos a la funcion create_voucher
*/
$quota  = 1;
$note   = 'Vale-API, ' . $room . ' ' . $fecha_actual  ;
$up     = null;
$down   = null; 
$MBytes = null;

/**
 * initialize the UniFi API connection class and log in to the controller
 */
$unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   = $unifi_connection->set_debug($debug);
$loginresults     = $unifi_connection->login();

/**
 * then we create the required number of vouchers with the requested expiration value
 */
$voucher_result = $unifi_connection->create_voucher($voucher_expiration, $voucher_count, $quota, $note, $up, $down, $MBytes);

/**
 * we then fetch the newly created vouchers by the create_time returned
 */
$vouchers = $unifi_connection->stat_voucher($voucher_result[0]->create_time);

/**
 * provide feedback (the newly created vouchers) in json format
 */
$data_vouchers 	= json_encode($vouchers, JSON_PRETTY_PRINT);
$json_voucher  	= json_decode($data_vouchers); 	


foreach ($json_voucher as $record) {
    $num_voucher = $record->code; // recorremos el json para sacar el vale
	
}
/*
* Creamos un log de pacotilla
*/
$note   = '[Cambio de vale OK] ' . $room . ' ' . $fecha_actual . ' ' .  $findThisVoucher . ' ' .$mac_device .' ' . $num_voucher .' >> C:\xampp\logs\log-API.txt';
exec("echo $note");
?>

<head>
  <link rel="stylesheet" href="/browser/static/css/bootstrap.css">
  <link rel="stylesheet" href="/browser/static/css/bootstrap-theme.css">
  <title> Formulario para cambio de vales </title>
</head>
<center>


<H4>
<?php	
echo  "Se ha anulado y creado el siguiente: $num_voucher ";
?>
</H4>



</center>
<body>
<script src="/browser/static/js/jquery-3.3.1.js"></script>		
<script src="/browser/static/js/bootstrap.js"></script>
</body>