<?
//api рабочая машина
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule('crm'); //crm

if (isset ($_GET['id'])){$id = $_GET['id'];}
if (isset ($_GET['user_name'])){$user_name = $_GET['user_name'];}
if (isset ($_GET['last_name'])){$last_name = $_GET['last_name'];}
if (isset ($_GET['email'])){$email = $_GET['email'];}
if (isset ($_GET['phone'])){$phone = preg_replace("/^/", "+", str_replace(' ','', str_replace('+','',$_GET['phone']) ) );}
if (isset ($_GET['city'])){$city = $_GET['city'];}
if (isset ($_GET['studygroup'])){$studygroup = $_GET['studygroup'];} 
 
 
 
 
 include ("/home/bitrix/www/bitrix/php_interface/dbconn.php"); 
 $link = mysqli_connect($DBHost, $DBLogin, $DBPassword, $DBName);
 mysqli_query("SET NAMES utf8");


$today = date("Y-m-d H:i:s");
 $str = '';
 foreach($_GET as $key => $val){$str .= $key.'='.$val."\r\n";}
 file_put_contents('primer.txt', "\r\n".$str.$today.$action."\r\n", FILE_APPEND);   

  echo "<pre>";
  var_dump($_GET."\r\n".$str.$today.$action."\r\n");
 
 

$studygroup1 = str_replace('Группа ГК обучения:', '', explode(';', $studygroup)[0]);
$studygroup2 = str_replace('Номер потока:', '', explode(';', $studygroup)[1]);
$studygroup3 = str_replace('Дата старта:', '', explode(';', $studygroup)[2]);
$studygroup4 = str_replace('Дата завершения:', '', explode(';', $studygroup)[3]);
$studygroup5 = str_replace('Дата последнего присутствия в группе:', '', explode(';', $studygroup)[4]);
$studygroup6 = str_replace('Статус в группе обучения:', '', explode(';', $studygroup)[5]);
 

$dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT',
        'VALUE'     => $email
    )
);
$el_id = $dbResult->Fetch()['ELEMENT_ID'];
 //echo "Обновляем контакт с ID - ".$el_id;
 
 /*


$dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT',
		'COMPLEX_ID'=> 'PHONE_WORK',
        'ELEMENT_ID'=>  $el_id 
    )
);





$phone_ = $dbResult->Fetch()['VALUE'];
//if($phone!=$phone_)echo " нет - ".$phone_;



$dbResult = CCrmFieldMulti::GetList(
    array('ID' => 'asc'),
    array(
        'ENTITY_ID' => 'CONTACT', 
		'COMPLEX_ID'=> 'EMAIL_WORK',
        'ELEMENT_ID'=>  $el_id 
    )
);
$email_ = $dbResult->Fetch()['VALUE'];
//if($email!=$email_)echo " нет - ".$email_;
*/

 //$result = mysqli_query($link,"SELECT `VALUE_ID` FROM `b_uts_crm_contact` WHERE `UF_CRM_1631694055` LIKE '$id'");  
 //$el_id = mysqli_fetch_array($result)[0];
 
 if(!$el_id)
{
$arFields = array(
       "NAME"      => $user_name,
       "LAST_NAME" => $last_name,
       "OPENED"    => "N", //открыто для других пользователей                
       "EXPORT"    => "Y",//участвует в экспорте 
       "SOURCE_ID" => "154",
	   "HAS_PHONE" => "Y",
       "HAS_EMAIL" => "Y",
	   "CREATED_BY_ID" => "564",
	   "MODIFY_BY_ID"  => "564",
	   "UF_CRM_1631694055" => $id,
	   "UF_CRM_1376771107" => $city,
	   "UF_CRM_1631694249" => $studygroup,
	   "UF_CRM_1632146419" => $studygroup1,
	   "UF_CRM_1632147187" => $studygroup2,
	   "UF_CRM_1632147250" => $studygroup3,
	   "UF_CRM_1632147396" => $studygroup4,
	   "UF_CRM_1633601890" => $studygroup5,
	   "UF_CRM_1632147542" => $studygroup6,	    
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
       "NAME"      => $user_name,
       "LAST_NAME" => $last_name,
       "OPENED"    => "N", //открыто для других пользователей                
       "EXPORT"    => "Y",//участвует в экспорте 
       "SOURCE_ID" => "154",
	   "HAS_PHONE" => "Y",
       "HAS_EMAIL" => "Y",
	   "CREATED_BY_ID" => "564",
	   "MODIFY_BY_ID"  => "564",
	   "UF_CRM_1631694055" => $id,
	   "UF_CRM_1376771107" => $city,
	   "UF_CRM_1631694249" => $studygroup,
	   "UF_CRM_1632146419" => $studygroup1,
	   "UF_CRM_1632147187" => $studygroup2,
	   "UF_CRM_1632147250" => $studygroup3,
	   "UF_CRM_1632147396" => $studygroup4,
	   "UF_CRM_1633601890" => $studygroup5,
	   "UF_CRM_1632147542" => $studygroup6,           
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

 
 