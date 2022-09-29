<? 
 require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
 CModule::IncludeModule('crm'); //crm

$json1 = file_get_contents('php://input');
$data1 = json_decode($json1,1);

file_put_contents(__DIR__."/log.txt", date("Y-m-d H:i:s")."\r\n".print_r($data1, true)."\r\r\n\n", FILE_APPEND | LOCK_EX);


//$webId="3918:kxpwdu6c2r*2022-02-11T14:24:38";

 $webId=$data1['webinarId'];

$url="https://online.bizon365.ru/api/v1/webinars/reports/get?webinarId=$webId";
 $headers = [
	"X-Token: HbW2NDzkcS-e-hEDfJcH-Wb3EDGyqBZGWnNPz1cSW7ZhEvf1q"
];
  
if( $curl = curl_init() ) {
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, 0);
	//curl_setopt($curl, CURLOPT_POSTFIELDS, $data );


$out = curl_exec($curl);
curl_close($curl);

$json = json_decode($out, true);
echo'<pre>';
print_r($json);
echo'</pre>';


} 


$room_title=$json['room_title'];
$webinarId=$json['report']['webinarId'];
  
 
 
 $m=json_decode($json['report']['messages'], true) ;
 
 
if( $curl = curl_init() ) {
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);	
	curl_setopt($curl, CURLOPT_URL, "https://online.bizon365.ru/api/v1/webinars/reports/getviewers?webinarId=$webId&skip=0&limit=1");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, 0);
$out = curl_exec($curl);
curl_close($curl);
$json = json_decode($out, true);
$total = $json['total'];
echo $total.'<br>';
}
 
 // &limit=2
 $skip=0;
 	while($total>$skip)
	{		
echo $skip.'<br>'; 
$url="https://online.bizon365.ru/api/v1/webinars/reports/getviewers?webinarId=$webId&skip=$skip"; 
 
  
if( $curl = curl_init() ) {
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, 0);
	//curl_setopt($curl, CURLOPT_POSTFIELDS, $data );


$out = curl_exec($curl);
curl_close($curl);

$json = json_decode($out, true);
echo'<pre>';
print_r($json);
echo'</pre>';


}

echo count($json['viewers']).'<br>';

 for($i=0; count($json['viewers'])>$i; $i++)
 {
 sleep(0.5);
 
    $email=$json['viewers'][$i]['email'];
    $user_name=$json['viewers'][$i]['username'];
    $phone=$json['viewers'][$i]['phone'];
	
	
 	//  $email='vikdawe@fr123.com';
   

 
 include ("/home/bitrix/www/bitrix/php_interface/dbconn.php"); 
 $link = mysqli_connect($DBHost, $DBLogin, $DBPassword, $DBName);
 mysqli_query("SET NAMES utf8");



$dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT',
        'VALUE'     => $email
    )
);
$el_id = $dbResult->Fetch()['ELEMENT_ID'];
 //echo "Обновляем контакт с ID - ".$el_id;
 
 // Приверка поля UF_CRM_1642410597
 $res22 = mysqli_query($link,"SELECT UF_CRM_1642410597 FROM b_uts_crm_contact WHERE VALUE_ID='$el_id' ORDER by VALUE_ID DESC");
  
	  
 if(!$el_id)
{
$arFields = array(
       "NAME"      => $user_name,
      // "LAST_NAME" => $last_name,
       "OPENED"    => "N", //открыто для других пользователей                
       "EXPORT"    => "Y",//участвует в экспорте 
	   "ASSIGNED_BY_ID" => 754,//id ответственного менеджера
       "SOURCE_ID" => "154",
	   "HAS_PHONE" => "Y",
       "HAS_EMAIL" => "Y",
	   "CREATED_BY_ID" => "564",
	   "MODIFY_BY_ID"  => "564",	    
       'FM' => array(//почта, телефон
                 'EMAIL' => array(
                    'n0' => array('VALUE' => $email, 'VALUE_TYPE' => 'WORK')
                 ),
                 'PHONE' => array(
                    'n0' => array('VALUE' => $phone, 'VALUE_TYPE' => 'WORK')
                 ) 
             )
             
   ); 
   
    $arOptions = array(
       //'DISABLE_USER_FIELD_CHECK' => true,
   "CURRENT_USER"=> 1,
   "ENABLE_SYSTEM_EVENTS" =>true
);
   
   //создаем контакт   
   $oContact = new \CCrmContact(false);
   $id=$oContact->add($arFields,true,$arOptions);
   
   
$startParameters = []; //BP parameters
\CCrmBizProcHelper::AutoStartWorkflows(
    \CCrmOwnerType::Contact, // \CCrmOwnerType::Lead, ...
    $el_id,
    1 ? \CCrmBizProcEventType::Create : \CCrmBizProcEventType::Edit,
    $errors,
    $startParameters
);
   
   
   if($oContact->LAST_ERROR != "") echo "не создался контакт";
   
   echo 'Нет тако контакта! СОЗДАЁМ';
}

else
 {
 
 $arFields = array(
      //  "NAME"      => $user_name,
     //  "LAST_NAME" => $last_name,
       "OPENED"    => "N", //открыто для других пользователей                
       "EXPORT"    => "Y",//участвует в экспорте 
   //  "ASSIGNED_BY_ID" => 754,//id ответственного менеджера 
   //  "SOURCE_ID" => "154",
	   "HAS_PHONE" => "Y",
       "HAS_EMAIL" => "Y",
	   "CREATED_BY_ID" => "564",
	   "MODIFY_BY_ID"  => "564",        
   );
   
   
   $arOptions = array(
       //'DISABLE_USER_FIELD_CHECK' => true,
   "CURRENT_USER"=> 1,
   "ENABLE_SYSTEM_EVENTS" =>true
);
 
 
 $dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT',
        'VALUE'     => $email
    )
);
$el_id = $dbResult->Fetch()['ELEMENT_ID'];

 
   $oContact = new CCrmContact(false);
   $oContact->Update($el_id,$arFields,true,true,$arOptions);
   echo " Обновляем контакт с ID - ".$el_id;
   

$startParameters = []; //BP parameters
\CCrmBizProcHelper::AutoStartWorkflows(
    \CCrmOwnerType::Contact, // \CCrmOwnerType::Lead, ...
    $el_id,
    0 ? \CCrmBizProcEventType::Create : \CCrmBizProcEventType::Edit,
    $errors,
    $startParameters
);

   
 $result = mysqli_query($link,"SELECT `ID` FROM `b_crm_field_multi` WHERE `VALUE` LIKE '$phone' AND ENTITY_ID LIKE 'CONTACT' AND ELEMENT_ID LIKE '$el_id'");  
 if(!mysqli_fetch_array($result)[0])
   {
   $arFields = array(
              'FM' => array(//телефон
                 'PHONE' => array(
                    'n0' => array('VALUE' => $phone, 'VALUE_TYPE' => 'WORK')
                 ) 
             )
             
   );
   $oContact = new CCrmContact(false);
   $oContact->Update($el_id,$arFields);
   echo " нет - ".$phone;
   }
 
} 
	 
	 
	 
	 
	 
	 
	 
	 
$created1=explode(".", str_replace('-','.',str_replace('T',' ',stristr($json['viewers'][$i]['created'],'.', true))));	
	
//echo stristr(explode(".", str_replace('-','.',str_replace('T',' ',stristr($json['viewers'][$i]['created'],'.', true)))),' ');	
	
$comments = implode(", ", $m[$json[viewers][$i][chatUserId]]);

echo $comments;
echo  $json['viewers'][$i]['email'].'<br>';	 
$created=str_replace('-','.',str_replace('T',' ',stristr($json['viewers'][$i]['created'],'.', true)));


$created2=explode("-",str_replace('*','',stristr(str_replace('T','',stristr($webinarId,'T', true)),'*')));
 

$wr=$created2[2].".".$created2[1].".".$created2[0].str_replace('T',' ',stristr($webinarId,'T'));

echo $wr.'<br>';
echo  $json['viewers'][$i]['ip'].'<br>';

//$secondsW=($json['viewers'][$i]['viewTill']-$json['viewers'][$i]['view']) / 1000;
//if ($secondsW>60)echo round($secondsW /60) .'<br>';

$secondsN = $json['viewers'][$i]['view'] / 1000;
$secondsC = $json['viewers'][$i]['viewTill'] / 1000;
echo date("m-d-Y H:i:s", $secondsN).'<br>';
echo date("m-d-Y H:i:s", $secondsC).'<br>';



echo  $room_title.'<br>'; 
echo  $webinarId.'<br>';
echo  $json['viewers'][$i]['city'].'<br>'; 
echo  $json['viewers'][$i]['url'].'<br><br>'; 
 
 // $email=$json['viewers'][$i]['email'];
 





  //$email='vikdawe@fr.com';
 
 

 $dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT',
        'VALUE'     => $email
    )
);
$el_id = $dbResult->Fetch()['ELEMENT_ID'];

 $arFields = array(
"UF_CRM_1617966477" => $wr,
"UF_CRM_1617632984" => $json['viewers'][$i]['ip'],
"UF_CRM_1617967842" => date("d.m.Y H:i:s", $secondsN),
"UF_CRM_1617967897" => date("d.m.Y H:i:s", $secondsC),
"TITLE"             => "BIZON: ".$room_title,
"UF_CRM_1617966213" => $webinarId,
"UF_CRM_1617633153" => $json['viewers'][$i]['city'],
"SOURCE_DESCRIPTION"=> $json['viewers'][$i]['url'],
"UF_CRM_1617966386"=> $data1['len'],
"COMMENTS" => $comments,
	  "CATEGORY_ID" => 8,
        "TYPE_ID" => "SALE", 
        "STAGE_ID" => "C8:NEW",                
        //"COMPANY_ID" => $companyId,                        
        "OPENED" => "Y", 
        "ASSIGNED_BY_ID" => 754, 
		"CONTACT_ID" => $el_id,
        //"CREATED_BY_ID" =>$managerId,                         
        //UF_CRM_CONF_NAME => $_POST["configs_deal"],
        //UF_CRM_CONF_NAME1 => $_POST["configsnew_deal"], 
);
//UF_CRM_CONF_NAME1 - пользовательское свойство сделки, без кавычек, потому что это константа, в которой хранится системное название поля
$bpId=753;

$options = array('CURRENT_USER'=>1); //из под админа
$deal = new CCrmDeal(false);
$dealId = $deal->Add($arFields,true,$options);
if($dealId > 0){                    
   CModule::IncludeModule('bizproc'); //запускаем робота для текущей стадии сделки
   $arErrors = Array();                  
   CBPDocument::StartWorkflow(
         $bpId,  //ID робота, смотреть через таблицы 753
         array("crm","CCrmDocumentDeal","DEAL_".$dealId), 
         array("TargetUser" => "user_1"),
         $arErrorsTmp
   );                  
}
 
}
 
$skip=$skip+1000;
	}