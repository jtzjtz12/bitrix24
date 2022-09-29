<? 
//https://b24.ovitta.team/activity/leeloo-gk/log.txt

include ("/home/bitrix/www/bitrix/php_interface/dbconn.php"); 
$link = mysqli_connect($DBHost, $DBLogin, $DBPassword, $DBName);
mysqli_query("SET NAMES utf8");

 $json = file_get_contents('php://input');
 $data = json_decode($json,1);
 
  file_put_contents(__DIR__."/log.txt", date("Y-m-d H:i:s")."\r\n".print_r($data, true)."\r\n", FILE_APPEND | LOCK_EX);
  file_put_contents(__DIR__."/log.txt", count($data['events'])."\r\n", FILE_APPEND | LOCK_EX);
  
  
 //$data['events'][0]['data']['offer_name']=str_replace('[','',str_replace(']','',$data['events'][0]['data']['offer_name']));
$i=0;
while(count($data['events'])>$i)
{
$offer_name=   $data['events'][$i]['data']['offer_name'];
$price=        $data['events'][$i]['data']['price'];
$price_usd=    $data['events'][$i]['data']['price_usd'];
$currency=     $data['events'][$i]['data']['currency'];
$email=        $data['events'][$i]['data']['email'];
$phone=        $data['events'][$i]['data']['phone'];
$order_id=     $data['events'][$i]['data']['order_id'];
$created_at=   $data['events'][$i]['data']['created_at'];
$account_name= $data['events'][$i]['data']['account_name'];



/*
if($data['events'][$i]['type']==ORDER and $data['events'][1]['type']==SALE){
//ИЗВЛЕЧЕНИЕ sale КОНТАКТА ИЗ leeloo
$result = mysqli_query($link,"SELECT sale FROM `leeloo` ORDER by ID DESC LIMIT 1");
$sale=mysqli_fetch_array($result)[0]+1;
$deal_status="payed";
$deal_status1=1;
	}
*/
if($data['events'][$i]['type']==ORDER){
		//ИЗВЛЕЧЕНИЕ sale КОНТАКТА ИЗ leeloo
$result = mysqli_query($link,"SELECT sale FROM `leeloo` ORDER by ID DESC LIMIT 1");
$sale=mysqli_fetch_array($result)[0]+1;
$deal_status="new";
$deal_status1=0;
	}
	
if($data['events'][$i]['type']==SALE){
//ИЗВЛЕЧЕНИЕ sale КОНТАКТА ИЗ leeloo
$result = mysqli_query($link,"SELECT sale FROM `leeloo` WHERE 
offer_name=         '$offer_name' AND
price=              '$price' AND
currency=           '$currency' AND
email=              '$email' AND
phone=              '$phone' AND
id_order=           '$order_id' AND
account_name=       '$account_name'
  ORDER by ID DESC");
$sale=mysqli_fetch_array($result)[0];	
$deal_status="payed";
$deal_status1=1;
}
 

// $sale=70853;

$data1 = explode("[", $offer_name);

 file_put_contents(__DIR__."/log.txt", $deal_status."\r\n", FILE_APPEND | LOCK_EX);
 file_put_contents(__DIR__."/log.txt", $sale."\r\n", FILE_APPEND | LOCK_EX);
file_put_contents(__DIR__."/log.txt", str_replace(']','',$data1[2])."\r\r\n\n", FILE_APPEND | LOCK_EX);
 
 //ИЗВЛЕЧЕНИЕ id КОНТАКТА ИЗ leeloo
$result = mysqli_query($link,"SELECT ID FROM `leeloo` WHERE 
offer_name=         '$offer_name' AND
price=              '$price' AND
currency=           '$currency' AND
email=              '$email' AND
phone=              '$phone' AND
id_order=           '$order_id' AND
account_name=       '$account_name'
  ORDER by ID DESC");
 
 if(!mysqli_fetch_array($result)[0] AND $sale)
{
//ЗАПИСЬ В leeloo
mysqli_query($link,"INSERT INTO leeloo (
offer_name,
price,
price_usd, 
currency,
email,
phone,
id_order,
created_at,
account_name,
deal_status,
sale)
VALUES (
'$offer_name',
'$price',
'$price_usd',
'$currency',
'$email',
'$phone',
'$order_id',
'$created_at',
'$account_name',
'$deal_status',
'$sale'
)");
}
else mysqli_query($link,"UPDATE leeloo SET deal_status='$deal_status' WHERE email='$email'");


$accountName = ' ';
$secretKey = ' ';

$deals = [];
  $deals['user']['email'] = $email;
//$deals['user']['phone'] = $data['events'][0]['data']['phone'];
//$deals['user']['first_name'] = $data['clientName'];
//$deals['user']['last_name'] = $data['clientName'];
// $deals['user']['email'] = 'test@tesst.com';


$deals['system']['refresh_if_exists'] = 0;
$deals['system']['partner_email'] = '';
$deals['system']['multiple_offers'] = 0;
$deals['system']['return_payment_link'] = 0;
$deals['system']['return_deal_number'] = 0;
 
$deals['session']['utm_source'] = "";
$deals['session']['utm_medium'] = "";
$deals['session']['utm_content'] = "";
$deals['session']['utm_campaign'] = "";
$deals['session']['utm_group'] = "";
$deals['session']['gcpc'] = "";
$deals['session']['gcao'] = "";
$deals['session']['referert'] = "";

$deals['deal']['deal_number'] = $sale;
$deals['deal']['offer_code'] = str_replace(']','',$data1[2]);
$deals['deal']['product_title'] = $offer_name;
$deals['deal']['product_description'] = '';
$deals['deal']['quantity'] = "1";
$deals['deal']['deal_cost'] = $price;
$deals['deal']['deal_status'] = $deal_status;
$deals['deal']['deal_is_paid'] = $deal_status1;
$deals['deal']['manager_email'] = "";
$deals['deal']['deal_created_at'] = "";
$deals['deal']['deal_finished_at'] = "";
$deals['deal']['deal_comment'] = "";
$deals['deal']['payment_type'] = "";
$deals['deal']['payment_status'] = "";
$deals['deal']['partner_email'] = "";
$deals['deal']['addfields']['utm_source_t']= "leeloo";
$deals['deal']['deal_currency'] = $currency;
 
$json = json_encode($deals);
$base64 = base64_encode($json);
 
if( $curl = curl_init() ) {
	curl_setopt($curl, CURLOPT_URL, 'https://' . $accountName . '.getcourse.ru/pl/api/deals');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'action=add&key=' . $secretKey . '&params=' . $base64);
	$out = curl_exec($curl);
	echo $out;
	curl_close($curl);
} else {
	echo 'Failed initialization';
}
 $i++;
 // sleep(10);

}