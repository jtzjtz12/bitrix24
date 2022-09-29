
	
  <?php 
include ("/home/bitrix/www/bitrix/php_interface/dbconn.php"); 


$acc  = 'Username';
$pass = 'your password';
$endPoint = 'https://api.wayforpay.com/api';
$req = [
        "apiVersion" => 1,
        "transactionType" => "CURRENCY_RATES",
        "merchantAccount" => $acc,
        "orderDate" => time(),
        "merchantSignature" => ""
    ];
$sig = hash_hmac('md5', "{$req['merchantAccount']};{$req['orderDate']}", $pass);
$req['merchantSignature'] = $sig;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endPoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));
$res = curl_exec($ch);
curl_close($ch);

 $toppings = json_decode($res, true);

 // print_r( $toppings);
 
 
$usr=$toppings['rates']['USD'];
$rub=$toppings['rates']['RUB'];
$kzt=$toppings['rates']['KZT'];
$eur=$toppings['rates']['EUR'];
 $d=date("Y-m-d H:i:s");

  echo "USD=$usr  RUB=$rub  KZT=$kzt  EUR=$eur  $d  ";
  
$servername = "localhost";
$username = "bitrix0";
$password = "f9vy!oRXCT}pL)50mORO";
$dbname = "sitemanager";

 

// Create connection
$conn = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE b_catalog_currency SET AMOUNT='$usr',CURRENT_BASE_RATE='$usr',DATE_UPDATE='$d' WHERE CURRENCY='USD'";


if ($conn->query($sql) === TRUE) {
    echo " Updated USD";
} else {
    echo "Error USD: " . $conn->error;
}


$sql = "UPDATE b_catalog_currency SET AMOUNT='$rub',CURRENT_BASE_RATE='$rub',DATE_UPDATE='$d' WHERE CURRENCY='RUB'";


if ($conn->query($sql) === TRUE) {
    echo " Updated RUB";
} else {
    echo "Error RUB" . $conn->error;
}

$sql = "UPDATE b_catalog_currency SET AMOUNT='$kzt',CURRENT_BASE_RATE='$kzt',DATE_UPDATE='$d' WHERE CURRENCY='KZT'";


if ($conn->query($sql) === TRUE) {
    echo " Updated KZT";
} else {
    echo "Error KZT" . $conn->error;
}

$sql = "UPDATE b_catalog_currency SET AMOUNT='$eur',CURRENT_BASE_RATE='$eur',DATE_UPDATE='$d' WHERE CURRENCY='EUR'";

if ($conn->query($sql) === TRUE) {
    echo " Updated EUR";
} else {
    echo "Error EUR" . $conn->error;
}
$conn->close();
 
?> 











