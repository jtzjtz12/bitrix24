<?

function json_fix_cyr($json_str) {
    $cyr_chars = array (
        '\u0430' => 'а', '\u0410' => 'А',
        '\u0431' => 'б', '\u0411' => 'Б',
        '\u0432' => 'в', '\u0412' => 'В',
        '\u0433' => 'г', '\u0413' => 'Г',
        '\u0434' => 'д', '\u0414' => 'Д',
        '\u0435' => 'е', '\u0415' => 'Е',
        '\u0451' => 'ё', '\u0401' => 'Ё',
        '\u0436' => 'ж', '\u0416' => 'Ж',
        '\u0437' => 'з', '\u0417' => 'З',
        '\u0438' => 'и', '\u0418' => 'И',
        '\u0439' => 'й', '\u0419' => 'Й',
        '\u043a' => 'к', '\u041a' => 'К',
        '\u043b' => 'л', '\u041b' => 'Л',
        '\u043c' => 'м', '\u041c' => 'М',
        '\u043d' => 'н', '\u041d' => 'Н',
        '\u043e' => 'о', '\u041e' => 'О',
        '\u043f' => 'п', '\u041f' => 'П',
        '\u0440' => 'р', '\u0420' => 'Р',
        '\u0441' => 'с', '\u0421' => 'С',
        '\u0442' => 'т', '\u0422' => 'Т',
        '\u0443' => 'у', '\u0423' => 'У',
        '\u0444' => 'ф', '\u0424' => 'Ф',
        '\u0445' => 'х', '\u0425' => 'Х',
        '\u0446' => 'ц', '\u0426' => 'Ц',
        '\u0447' => 'ч', '\u0427' => 'Ч',
        '\u0448' => 'ш', '\u0428' => 'Ш',
        '\u0449' => 'щ', '\u0429' => 'Щ',
        '\u044a' => 'ъ', '\u042a' => 'Ъ',
        '\u044b' => 'ы', '\u042b' => 'Ы',
        '\u044c' => 'ь', '\u042c' => 'Ь',
        '\u044d' => 'э', '\u042d' => 'Э',
        '\u044e' => 'ю', '\u042e' => 'Ю',
        '\u044f' => 'я', '\u042f' => 'Я',
 
        '\r' => '',
        '\n' => '<br />',
        '\t' => ''
		
    );
 
    foreach ($cyr_chars as $cyr_char_key => $cyr_char) {
        $json_str = str_replace($cyr_char_key, $cyr_char, $json_str);
    }
    return $json_str;
}
$data = explode(",", str_replace('{','',str_replace('}','',str_replace('[','',str_replace(']','',str_replace('"','',file_get_contents('php://input') ))))));
  
$data1 = explode(";", json_fix_cyr($data[27]));
$data1[0]=stristr($data1[0], '#');
  
 
 
$orderReference=   str_replace(':','',stristr($data[1], ':'));
$merchantSignature=str_replace(':','',stristr($data[2], ':'));
$amount=           str_replace(':','',stristr($data[3], ':'));
$createdDate=      str_replace(':','',stristr($data[8], ':'));
$processingDate=   str_replace(':','',stristr($data[9], ':'));
$clientName=       json_fix_cyr(str_replace(':','',stristr($data[22],':')));
$email=            str_replace(':','',stristr($data[6], ':'));
$phone=        '+'.str_replace(':','',stristr($data[7], ':'));
$reason=           str_replace(':','',stristr($data[16],':'));
$transactionStatus=str_replace(':','',stristr($data[15],':'));
$issuerBankName=   str_replace(':','',stristr($data[13],':'));
$issuerBankCountry=str_replace(':','',stristr($data[12],':'));
 
$nameKurs=$data1[0].$data1[1].$data1[2].$data1[3];


include ("/home/bitrix/www/bitrix/php_interface/dbconn.php"); 
$link = mysqli_connect($DBHost, $DBLogin, $DBPassword, $DBName);
mysqli_query("SET NAMES utf8");
 
 //ИЗВЛЕЧЕНИЕ id КОНТАКТА ИЗ WayForPay
$result = mysqli_query($link,"SELECT ID FROM `WayForPay` WHERE 
orderReference=   '$orderReference' AND
merchantSignature='$merchantSignature' AND
amount=           '$amount' AND
createdDate=      '$createdDate' AND
processingDate=   '$processingDate' AND
clientName=       '$clientName' AND
name=             '$nameKurs' AND
email=            '$email' AND
phone=            '$phone' AND
reason=           '$reason'AND
transactionStatus='$transactionStatus'
  ORDER by ID DESC");
 
 if(!mysqli_fetch_array($result)[0])
{
//ЗАПИСЬ В WayForPay
mysqli_query($link,"INSERT INTO WayForPay (
orderReference,
merchantSignature,
amount, 
createdDate,
processingDate,
clientName,
name,
email,
phone,
reason,
transactionStatus,
issuerBankName,
issuerBankCountry)
VALUES (
'$orderReference',
'$merchantSignature',
'$amount',
'$createdDate',
'$processingDate',
'$clientName',
'$nameKurs',
'$email',
'$phone',
'$reason',
'$transactionStatus',
'$issuerBankName',
'$issuerBankCountry'
)");

file_put_contents(__DIR__."/log.txt", date("Y-m-d H:i:s")."\r\n".print_r($data, true)."\r\n", FILE_APPEND | LOCK_EX);
file_put_contents(__DIR__."/log.txt",print_r($data1, true)."\r\r\n\n", FILE_APPEND | LOCK_EX);
 
 if($reason==Ok AND $transactionStatus==Approved AND $amount!=1){
	 
$accountName = ' ';
$secretKey = ' ';
 
$deals = []; 
$deals['user']['email'] = $email;

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

$deals['deal']['deal_number'] = "";
$deals['deal']['offer_code'] = $data1[3];
$deals['deal']['product_title'] = $data1[0].$data1[1].$data1[2];
$deals['deal']['product_description'] = '';
$deals['deal']['quantity'] = "1";
$deals['deal']['deal_cost'] = $amount;
$deals['deal']['deal_status'] = "payed";
$deals['deal']['deal_is_paid'] = "1";
$deals['deal']['manager_email'] = "";
$deals['deal']['deal_created_at'] = "";
$deals['deal']['deal_finished_at'] = "";
$deals['deal']['deal_comment'] = "";
$deals['deal']['payment_type'] = "";
$deals['deal']['payment_status'] = "";
$deals['deal']['partner_email'] = "";
$deals['deal']['addfields']['utm_source_t']= "merch_wayforpay";
$deals['deal']['deal_currency'] = "USD";
 
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

 }
 
}



 
  
 
 
 
 
 
  


 