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
require_once('vendor/autoload.php');
require_once('config.php');
error_reporting(0);

$site_id 					= '';
$controlleruser				= '';
$controllerpassword		    = '';
$controllerurl				= '';
$site_id					= '';
$controllerversion		    = '';


$unifi_connection 			= new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   			= $unifi_connection->set_debug($debug);
$loginresults     			= $unifi_connection->login();

$data_guests 		        = $unifi_connection->list_guests(); 			 // Se llama a la funcion "list_guests" para obtener las estadisticas 
$mac_find 			        = json_encode($data_guests, JSON_PRETTY_PRINT); // las estadisticas obtenidas se codifican en un json




function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

$newip = get_client_ip();

//echo "ESTA ES LA IP: $newip ";

$comando1="ping $newip";
//echo  "COMANDO1: $comando1 ";
exec($comando1);

$comando2="arp -a | find \"$newip\"";
//echo  "COMANDO2: $comando2 ";

//echo exec($comando2);
$salidaARP = exec($comando2);
//echo "EL RESULTADO DEL COMANDO ARP: $salidaARP";

$prueba = preg_match("/[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f]/i", $salidaARP, $matches);

$mac_add = $matches[0];
$mac_add = str_replace('-', ':', $mac_add); 
//echo "$mac----------";


foreach ($data_guests as $record) {
	$status_expired = $record->expired;
	$address_mac    = $record->mac;
	//echo "--DIRECCION MAC: $address_mac -- --ESTADO ESTATUS: $status_expired-- ";
	if ($mac_add === $address_mac) {
		//echo "-- $address_mac";
		if ($status_expired == false){
			$unifi_connection->unauthorize_guest($mac_add);
			include 'create.php';
			break;
		}
	} 
}

if ($status_expired === true) { echo "No se ha encontrado el dispositivo";}


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


