<?
require __DIR__ . '/include/diagnostics.php';
//регистрируем обработчик в /bitrix/php_interface/init.php
// AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
//  // создаем обработчик события "BeforeIndex"
// function BeforeIndexHandler($arFields)
// {
//    if(!CModule::IncludeModule("iblock")) // подключаем модуль
//       return $arFields;
//    if($arFields["MODULE_ID"] == "iblock")
//    {
//       $db_props = CIBlockElement::GetProperty(        // Запросим свойства индексируемого элемента
//                                     $arFields["PARAM2"],         // IBLOCK_ID индексируемого свойства
//                                     $arFields["ITEM_ID"],          // ID индексируемого свойства
//                                     array("sort" => "asc"),         // Сортировка (можно упустить)
//                                     Array("CODE"=>"VN_NOMER")); // CODE свойства, по которому нужно осуществлять поиск
//       if($ar_props = $db_props->Fetch())
//          $arFields["TITLE"] .= " ".$ar_props["VALUE"];   // Добавим свойство в конец заголовка индексируемого элемента
//    }
//    return $arFields; // вернём изменения
// }

AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
 // создаем обработчик события "BeforeIndex"
function BeforeIndexHandler($arFields)
{
   
   if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == 61)
   {
      $db_props = CIBlockElement::GetProperty(                        // Запросим свойства индексируемого элемента
                                    $arFields["PARAM2"],         // BLOCK_ID индексируемого свойства
                                    $arFields["ITEM_ID"],          // ID индексируемого свойства
                                    array("sort" => "asc"),       // Сортировка (можно упустить)
                                    Array("CODE"=>"POSITION")); // CODE свойства (в данном случае артикул)
      if($ar_props = $db_props->Fetch())
         $arFields["TITLE"] .= " ".$ar_props["VALUE"];   // Добавим свойство в конец заголовка индексируемого элемента
   }
   return $arFields; // вернём изменения
}

/* AddEventHandler("search", "onAfterIndexAdd", "onAfterIndexAddHandler");
function onAfterIndexAddHandler($ID, $arFields)
{    
    $additionalWords = [];

    if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == 61)
    {
       
        $db_props = CIBlockElement::GetList(array(),
                                    array("IBLOCK_ID"=>61, "ID"=>$arFields["ITEM_ID"]),
                                    false, 
                                    false,
                                    Array("PROPERTY_POSITION", "PROPERTY_DEP"));                               
        if ($ar_props = $db_props->Fetch()){
            if (!empty($ar_props["PROPERTY_POSITION_VALUE"])){
                $additionalWords[] = $ar_props["PROPERTY_POSITION_VALUE"];  
            }
            if (!empty($ar_props["PROPERTY_DEP_VALUE"])){
                $additionalWords[] = $ar_props["PROPERTY_DEP_VALUE"];  
            }
        }
    }
    if (!empty($additionalWords)){
        CModule::IncludeModule('search');
        CSearch::IndexTitle(
            $arFields["SITE_ID"],
            $ID,
            implode(' ', $additionalWords)
        );
    }    
} */


//todo - equipment logic
//Вывод массива 
function debug($code) {
	echo "<pre>";
    print_r($code);
    echo "</pre>";
}
//добавление группы Сотрудник новым пользователям из АД
AddEventHandler("main", "OnAfterUserAdd", "group_OnAfterUserUpdate");
function group_OnAfterUserUpdate(&$arFields)
{
	$arGroups = CUser::GetUserGroup($arFields["ID"]);
    $arGroups[] = 14;
    CUser::SetUserGroup($arFields["ID"], $arGroups);
}

//Поиск пользователя по фамилии
function userIDSearch($user) {
    //debug($user);
    $userID = 0;
    $fio = explode(' ', trim($user));
    $surname = $fio[0];
    $filter = Array("LAST_NAME"=> $surname, "GROUP_ID" => [3], "ACTIVE" => "Y");
    $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
    while($arItem = $rsUsers->GetNext()) {
        $name = $arItem['NAME'];
        $secondName = $arItem['SECOND_NAME'];
        $nameInitials = explode('.', trim($fio[1]))[0];
        $secondNameInitials = explode('.', trim($fio[1]))[1];
        $rsUser = CUser::GetByID($arItem['ID']);
        $arUser = $rsUser->Fetch();
        $department = $arUser['UF_DEPARTMENT'][0]; 
        $arSelect = Array("ID", "NAME", "CODE");
        $arFilter = Array("ACTIVE"=>"Y", "PROPERTY_USER" => $arItem['ID']);
        $iblockDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        while($element = $iblockDB->GetNextElement()) {
            $arFieldsElement = $element->GetFields();
            if ((mb_substr($name, 0, 1) === $nameInitials || empty($nameInitials)) && (mb_substr($secondName, 0, 1)  === $secondNameInitials || empty($secondName) || empty($secondNameInitials)) && !empty($department) && !empty($arFieldsElement)) {
                $arUser = $arItem;
                $userID = $arItem['ID'];
            }
        }
        
        
    }
    return intval($userID);
}

//Сравнение строк
function stringChecking($strOne, $strTwo) {
    $valueOne = mb_strtolower(trim($strOne));
    $valueTwo = mb_strtolower(trim($strTwo));
    $result = !strcmp($valueOne, $valueTwo) ? true : false;
    return $result;
}

//
function GetListValueById($ID)
{
   $UserField = CIBlockPropertyEnum::GetList(array(), array("ID" => $ID));
   if($UserFieldAr = $UserField->GetNext())
   {
      return $UserFieldAr["VALUE"];
   }
   else return false;
}

//Поиск послоеднего или получение следуещуего внешнего кода в оборудовании
function lastXMLID($type = 'last') {
    $rsElement = CIBlockElement::GetList(
        $arOrder  = array("ID" => "DESC"),
        $arFilter = array(
            "IBLOCK_ID"    => 85,
        ),
        false,
        false,
        $arSelectFields = array("ID", "XML_ID")
    );
    $max = 0;
    $countNumbers = 10;
    while($arElement = $rsElement->GetNextElement()) {
        $el = $arElement->GetFields();
        $arXmlID[] = $el['XML_ID'];
        
        
    } 

    $rsElement2 = CIBlockElement::GetList(
        $arOrder  = array("ID" => "DESC"),
        $arFilter = array(
            "IBLOCK_ID"    => 89,
        ),
        false,
        false,
        $arSelectFields = array("ID", "XML_ID")
    );
    
    while($arElement2 = $rsElement2->GetNextElement()) {
        $el = $arElement2->GetFields();
        $arXmlID[] = $el['XML_ID'];
    } 

    $max = 0;
    $countNumbers = 10;
    foreach ($arXmlID as $xmlID) {
        $id = explode('_', $xmlID);
        $matches = [];
        $regular = preg_match('/0{2,}/', $id[1], $matches);
        if ($regular) {
            $countEquipment = substr($id[1], strlen($matches[0]));
            if ($countEquipment > $max) {
                $max = $countEquipment;
            }
        }
    }
    
    
    if ($type == 'new') {
        $max++;
    }

    $str = '';
    for ($i = 0; $i < $countNumbers - strlen($max); $i++) {
        $str .= '0';
    }
    
    $maxID = 'urb_'.$str.$max;  
    
    return $maxID;
}

//Поиск значени свосвта типа список
function searchValuePropertyTypeList($iblockID, $iblockIDLog, $code, $codeLog, $value) {
    $result = '';
    $propertyEnums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockID, "CODE"=>$code));
    while($enumFields = $propertyEnums->GetNext()) {
        if ($enumFields['ID'] == $value) {
            $enumValue = $enumFields['VALUE'];
        }
    }

    $propertyEnumsLog = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockIDLog, "CODE"=>$codeLog));
    while($enumFields = $propertyEnumsLog->GetNext()) {
        if (stringChecking($enumFields['VALUE'], $enumValue)) {
            $result = $enumFields['ID'];
        }
    }
    return $result;
}

//Получение ссылки на карточку сотрудника по ID
function urlUser($userID) {
	$arSelect = Array("ID", "NAME", "CODE");
	$arFilter = Array("ACTIVE"=>"Y", "PROPERTY_USER" => $userID);
	$iblockDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
	while($element = $iblockDB->GetNextElement()) {
		$arFieldsElement = $element->GetFields();
	}
    //debug($userID);
	$groupElementDB = CIBlockElement::GetElementGroups($arFieldsElement['ID'], false);
	while($arGroup = $groupElementDB->Fetch()) {
		$groupID = $arGroup["ID"];
	}
	$sectionDB = CIBlockSection::GetByID($groupID);
	if($arSection = $sectionDB->GetNext()) {
        
		$sectionCode = $arSection['CODE'];
	}	
	$url = '/structure/'.$sectionCode.'/'.$arFieldsElement['CODE'];
	return ['url' => $url, 'user' => $arFieldsElement['NAME'], 'id' => $userID];
}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("MyClass", "OnBeforeIBlockElementAddHandler"));

class MyClass
{
    // создаем обработчик события "OnBeforeIBlockElementAdd"
    static function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        if($arFields["IBLOCK_ID"] == 61) //ORGSTRUCTURE
        {   
            $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>316));
            if($ar_props = $db_props->Fetch()){
                $arr_prop[$ar_props['CODE']] = $ar_props;
            }
            $db_props2020 = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>325));
            if($ar_props = $db_props2020->Fetch()){
                $arr_prop[$ar_props['CODE']] = $ar_props;
            }
            if (!empty($arr_prop['DATE']['VALUE']) && empty($arr_prop['SORT_DATE']['VALUE'])){
                $arr = ParseDateTime($arr_prop['DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS"); 
                $new_date = $arr["DD"].'.'. $arr['MM'].'.'.'2020';
                $arFields['PROPERTY_VALUES']['SORT_DATE'] = FormatDate("d.m.Y", MakeTimeStamp($new_date));
            }
                //   $log .= date("Y.m.d G:i:s") . "\n";
                //    $log .= print_r($arr_prop , 1);
                //    $log .= "\n------------------------\n";
                //    $log .= print_r($new_date , 1);
                //    $log .= "\n------------------------\n";

                //    file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
                    // global $APPLICATION;
                    // $APPLICATION->throwException("Введите символьный код.");
                    // return false;
        }
        if($arFields["IBLOCK_ID"] == 104) //ORGSTRUCTURE
        {   
            // $name = 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("ID" => "DESC"),
                $arFilter = array(
                    "IBLOCK_ID"    => 61,
                    "ID" => $arFields['PROPERTY_VALUES'][866],
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE")
               );
               while($arElement = $rsElement->GetNextElement()) {
                $el = $arElement->GetFields();
               }
            //    $arFields['NAME'] = $el['NAME'];
                //   $log .= date("Y.m.d G:i:s") . "\n";
                //    $log .= print_r($arFields , 1);
                //    $log .= "\n------------------------\n";
                //    $log .= print_r($el , 1);
                //    $log .= "\n------------------------\n";
                //    $log .= print_r($arFields[866] , 1);
                //    $log .= "\n------------------------\n";
                //    file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
                    // global $APPLICATION;
                    // $APPLICATION->throwException("Введите символьный код.");
                    // return false;
        }
        if($arFields["IBLOCK_ID"] == 105) //ORGSTRUCTURE
        {   
            // $name = 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("ID" => "DESC"),
                $arFilter = array(
                    "IBLOCK_ID"    => 104,
                    "ID" => $arFields['PROPERTY_VALUES'][870],
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE")
               );
               while($arElement = $rsElement->GetNextElement()) {
                $el = $arElement->GetFields();
               }
            //    $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>896));
            // if($ar_props = $db_props->Fetch()){
            //     $arr_prop[$ar_props['CODE']] = $ar_props;
            // }
            // $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"], "ID"=>$arFields['PROPERTY_VALUES'][896]));
            //     while($enum_fields = $property_enums->GetNext())
            //     {
            //         $arr_prop[$enum_fields['CODE']] =  $enum_fields;
            //     }
            $arr_prop = array();
                foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                    $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                    if($ar_props = $db_props->Fetch()){
                        $arr_prop[$ar_props['CODE']] = $ar_props;
                        if(is_array($arFields[$key])){
                            foreach ($field as $key_2 => $value) {
                                $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                            }
                        }else{
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                            $arr_prop[$ar_props['CODE']]['key'] = $key;
                            $arr_prop[$ar_props['CODE']]['val'] = $field;
                        }
                        
                    }
                    
                }
               $arFields['NAME'] = $el['NAME'] . ' ЗП за ' . $arr_prop['MESYATS']['VALUE'];
                  $log .= date("Y.m.d G:i:s") . "\n";
                   $log .= print_r($arFields , 1);
                   $log .= "\n------------------------\n";
                   $log .= print_r($arr_prop , 1);
                   $log .= "\n------------------------\n";
                   $log .= print_r($arFields['PROPERTY_VALUES'][870] , 1);
                   $log .= "\n------------------------\n";
                   file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
                    // global $APPLICATION;
                    // $APPLICATION->throwException("Введите символьный код.");
                    // return false;
        }

        if($arFields["IBLOCK_ID"] == 98) //ORGSTRUCTURE
        {   
            
                  
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("ID" => "DESC"),
                $arFilter = array(
                    "IBLOCK_ID"    => 98,
                ),
                false,
                array("nTopCount" => 1),
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
               );
               while($arElement = $rsElement->GetNextElement()) {
                $el = $arElement->GetFields();
                $el["PROPERTIES"] = $arElement->GetProperties();
                $old_items[$el['ID']] = $el;
               
                  
                    $last_id = $el["PROPERTIES"]['UNIQ_ID']['VALUE'];
                    $arFields['PROPERTY_VALUES'][822]['n0']['VALUE'] = intval($last_id+1);
                
               }

        }
        if($arFields["IBLOCK_ID"] == 80) //news
        {			
			$isValueIsEmpty = true;
			if (is_array($arFields['PROPERTY_VALUES'][398])){
				foreach($arFields['PROPERTY_VALUES'][398] as $key=>$value){
					if (!empty($value["VALUE"])){
						$isValueIsEmpty = false;
					}
				}
			}
			else{
				if (!empty($arFields['PROPERTY_VALUES'][398])){
					$isValueIsEmpty = false;
				}
			}
        	if($isValueIsEmpty){  //Кастомная дата новости
				// $log .= "\n-EMPTY--------\n";	
				// $today = date("j.n.Y");
				// $arFields['PROPERTY_VALUES'][398] = $today;
			} 
			else{
				// $log .= "\n-SKIPPED--------\n";	
			}    
    		// $log .= date("Y.m.d G:i:s") . "\n";
			// $log .= print_r($arFields , 1);
			// $log .= "\n------------------------\n";
			// $log .= print_r($today , 1);
			// $log .= "\n------------------------\n";

           	// file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        }
        /*if($arFields["IBLOCK_ID"] == 85) //news
        {   
           //debug($arFields);
           CModule::IncludeModule('iblock');
            if ($arFields['PROPERTY_VALUES'][579] == '') {
                $arFilter = Array("IBLOCK_ID"=>85);
                $rsElement = CIBlockElement::GetList(Array("ID" => "DESC"), $arFilter, array('PROPERTY_VN_NOMER'), array("nTopCount" => 1));
                while($arElement = $rsElement->GetNext()) {
                    //debug($arElement);
                    $LastElement = $arElement;
                }
                $prefix = substr($LastElement['PROPERTY_VN_NOMER_VALUE'], 0, 4);
                $number = substr($LastElement['PROPERTY_VN_NOMER_VALUE'], 4);
                $number = intval($number) + 1;
                
                if(strlen($number) == 4){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'000000'.$number;
                }
                if(strlen($number) == 5){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'00000'.$number;
                }
                if(strlen($number) == 6){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'0000'.$number;
                }
                if(strlen($number) == 7){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'000'.$number;
                }
                if(strlen($number) == 8){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'00'.$number;
                }
                if(strlen($number) == 9){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.'0'.$number;
                }
                if(strlen($number) == 10){
                    $arFields['PROPERTY_VALUES'][579] = [];
                    $arFields['PROPERTY_VALUES'][579]['n0']['VALUE'] = $prefix.''.$number;
                }
            } 
            //Bitrix\Main\Diag\Debug::writeToFile(array('ID' => $id, 'fields'=>$arFields ),"","prices.txt");
            
        //     $log .= date("Y.m.d G:i:s") . "\n";
        //    $log .= print_r($LastElement , 1);
        //    $log .= "\n------------------------\n";
        //    $log .= print_r($arFields , 1);
        //    $log .= "\n------------------------\n";
        //    $log .= print_r($prefix , 1);
        //    $log .= "\n------------------------\n";
        //    $log .= print_r($number , 1);
        //    $log .= "\n------------------------\n";

        //    file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
            
            if ($i == 1) {
                $arFields['PROPERTY_VALUES'][579]['n0']['value'] = $prefix.'000000'.$number;
            }
            $i++;
        }*/
    }
}

//AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("CustomDateUpdate", "OnAfterIBlockElementUpdateHandler"));

//AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("CustomDateAdd", "OnBeforeIBlockElementAddHandler"));
AddEventHandler("iblock", "OnStartIBlockElementAdd", Array("CustomDateStart", "OnStartIBlockElementAddHandler"));
//AddEventHandler("iblock", "OnStartIBlockElementUpdate", Array("CustomDateStart", "OnStartIBlockElementUpdateHandler"));

class CustomDateStart
{
    // создаем обработчик события "OnStartIBlockElementAdd"
    static function OnStartIBlockElementAddHandler(&$arFields)
    {   
        if($arFields["IBLOCK_ID"] == 85) {
            //debug($arFields);
            //debug($arFields['XML_ID']);
            
            if (!array_key_exists('XML_ID', $arFields) || empty($arFields['XML_ID'])) {
                $xmlID = lastXMLID('new');
                $arFields['XML_ID'] = $xmlID;
                $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>85, "CODE" => "CODE_OBORUDOVANIYA"));
                while ($prop_fields = $properties->GetNext()) {
                    $idPropertyCodeOborudovaniya = $prop_fields['ID'];
                }
                $arFields['PROPERTY_VALUES'][$idPropertyCodeOborudovaniya] = $xmlID;
            }
            /*$arUserFieldID = [555, 557]; //id полей с типом привязка к сотруднику
            $arDateFieldID = [553, 559, 565, 566, 569, 570, 573, 574]; //id полей с типом дата
            
            //Заполняет поля id сотрудников
            foreach ($arUserFieldID as $fieldID) { 
                if (!is_array($arFields['PROPERTY_VALUES'][$fieldID])) {
                    if (!empty($arFields['PROPERTY_VALUES'][$fieldID])) {
                        $userID = userIDSearch($arFields['PROPERTY_VALUES'][$fieldID]);
                        $arFields['PROPERTY_VALUES'][$fieldID] = $userID != 0 ? $userID : $arFields['PROPERTY_VALUES'][$fieldID];
                    }
                } else {
                    $isImportUser = false;
                    $arUserID = [];
                    foreach ($arFields['PROPERTY_VALUES'][$fieldID] as $user) {
                        if (!is_array($user)) {
                            $isImportUser = true;
                            $userID = userIDSearch($user);
                            $arUserID[] = $userID != 0 ? $userID : $arFields['PROPERTY_VALUES'][$fieldID];
                        }
                    }

                    if ($isImportUser) {
                        $arFields['PROPERTY_VALUES'][$fieldID] = $arUserID;
                    }
                }
            }
            $month = [
                'января' => '01',
                'февраля' => '02',
                'марта' => '03',
                'апреля' => '04',
                'мая' => '05',
                'июня' => '06',
                'июля' => '07',
                'августа' => '08',
                'сентября' => '09',
                'октября' => '10',
                'ноября' => '11',
                'декабря' => '12',
            ];
            foreach ($arDateFieldID as $fieldID) {
                if (!empty($arFields['PROPERTY_VALUES'][$fieldID])) {
                    if (!is_array($arFields['PROPERTY_VALUES'][$fieldID])) {
                        $arr = ParseDateTime($arFields['PROPERTY_VALUES'][$fieldID], "DD.MM.YYYY");
                        
                        if (is_string($arr['MM'])) {
                            foreach ($month as $key => $value) {
                                if (stripos($key, $arr['MM']) !== false) {
                                    $arr['MM'] = $value;
                                }
                            }
                        } 

                        if ($arr['MM'] > 12) {
                            $day = $arr['DD'];
                            $arr['DD'] = $arr['MM'];
                            $arr['MM'] = $day;
                        }
                        
                        if ($arr['YYYY'][0] == 0) {
                            $arr['YYYY'][0] = '2';
                        }
                        $new_date = $arr["DD"].'.'. $arr['MM'].'.'.$arr['YYYY'];
                        $dateFormat = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                        $arFields['PROPERTY_VALUES'][$fieldID] = $dateFormat;
                    } else {
                        $isImportDate = false;
                        $arDate = [];
                        foreach ($arFields['PROPERTY_VALUES'][$fieldID] as $dateItem) {
                            if (!is_array($dateItem)) {
                                $isImportDate = true;
                                
                                $arr = ParseDateTime($dateItem, "DD.MM.YYYY"); 
                                if (is_string($arr['MM'])) {
                                    foreach ($month as $key => $value) {
                                        if (stripos($key, $arr['MM']) !== false) {
                                            $arr['MM'] = $value;
                                        }
                                    }
                                }

                                if ($arr['MM'] > 12) {
                                    $day = $arr['DD'];
                                    $arr['DD'] = $arr['MM'];
                                    $arr['MM'] = $day;
                                }
                                
                                if ($arr['YYYY'][0] == 0) {
                                    $arr['YYYY'][0] = '2';
                                }
                                
                                $new_date = $arr["DD"].'.'. $arr['MM'].'.'.$arr['YYYY'];
                                $dateFormat = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                                $arDate[] = $new_date;
                            }
                        }

                        if ($isImportDate) {
                            $arFields['PROPERTY_VALUES'][$fieldID] = $arDate;
                        }
                        
                    }
                }
            }*/
            
        } 
        
        
    }

    // создаем обработчик события "OnStartIBlockElementUpdate"
    /*static function OnStartIBlockElementUpdateHandler(&$arFields)
    {   
        if($arFields["IBLOCK_ID"] == 85) {
            $arUserFieldID = [555, 557]; //id полей с типом привязка к сотруднику
            $arDateFieldID = [553, 559, 565, 566, 569, 570, 573, 574]; //id полей с типом дата
            
            //Заполняет поля id сотрудников
            foreach ($arUserFieldID as $fieldID) { 
                if (!is_array($arFields['PROPERTY_VALUES'][$fieldID])) {
                    if (!empty($arFields['PROPERTY_VALUES'][$fieldID])) {
                        $userID = userIDSearch($arFields['PROPERTY_VALUES'][$fieldID]);
                        $arFields['PROPERTY_VALUES'][$fieldID] = $userID != 0 ? $userID : $arFields['PROPERTY_VALUES'][$fieldID];
                    }
                } else {
                    $isImportUser = false;
                    $arUserID = [];
                    foreach ($arFields['PROPERTY_VALUES'][$fieldID] as $user) {
                        if (!is_array($user)) {
                            $isImportUser = true;
                            $userID = userIDSearch($user);
                            $arUserID[] = $userID != 0 ? $userID : $arFields['PROPERTY_VALUES'][$fieldID];
                        }
                    }

                    if ($isImportUser) {
                        $arFields['PROPERTY_VALUES'][$fieldID] = $arUserID;
                    }
                }
            }
            $month = [
                'января' => '01',
                'февраля' => '02',
                'марта' => '03',
                'апреля' => '04',
                'мая' => '05',
                'июня' => '06',
                'июля' => '07',
                'августа' => '08',
                'сентября' => '09',
                'октября' => '10',
                'ноября' => '11',
                'декабря' => '12',
            ];
            foreach ($arDateFieldID as $fieldID) {
                if (!empty($arFields['PROPERTY_VALUES'][$fieldID])) {
                    if (!is_array($arFields['PROPERTY_VALUES'][$fieldID])) {
                        $arr = ParseDateTime($arFields['PROPERTY_VALUES'][$fieldID], "DD.MM.YYYY");
                        
                        if (is_string($arr['MM'])) {
                            foreach ($month as $key => $value) {
                                if (stripos($key, $arr['MM']) !== false) {
                                    $arr['MM'] = $value;
                                }
                            }
                        } 

                        if ($arr['MM'] > 12) {
                            $day = $arr['DD'];
                            $arr['DD'] = $arr['MM'];
                            $arr['MM'] = $day;
                        }
                        
                        if ($arr['YYYY'][0] == 0) {
                            $arr['YYYY'][0] = '2';
                        }
                        $new_date = $arr["DD"].'.'. $arr['MM'].'.'.$arr['YYYY'];
                        $dateFormat = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                        $arFields['PROPERTY_VALUES'][$fieldID] = $dateFormat;
                    } else {
                        $isImportDate = false;
                        $arDate = [];
                        foreach ($arFields['PROPERTY_VALUES'][$fieldID] as $dateItem) {
                            if (!is_array($dateItem)) {
                                $isImportDate = true;
                                
                                $arr = ParseDateTime($dateItem, "DD.MM.YYYY"); 
                                if (is_string($arr['MM'])) {
                                    foreach ($month as $key => $value) {
                                        if (stripos($key, $arr['MM']) !== false) {
                                            $arr['MM'] = $value;
                                        }
                                    }
                                }

                                if ($arr['MM'] > 12) {
                                    $day = $arr['DD'];
                                    $arr['DD'] = $arr['MM'];
                                    $arr['MM'] = $day;
                                }
                                
                                if ($arr['YYYY'][0] == 0) {
                                    $arr['YYYY'][0] = '2';
                                }
                                
                                $new_date = $arr["DD"].'.'. $arr['MM'].'.'.$arr['YYYY'];
                                $dateFormat = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                                $arDate[] = $new_date;
                            }
                        }

                        if ($isImportDate) {
                            $arFields['PROPERTY_VALUES'][$fieldID] = $arDate;
                        }
                        
                    }
                }
            }
            
        }  
    }*/
}

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("CustomDateUpdate", "OnBeforeIBlockElementUpdateHandler"));

class CustomDateUpdate
{
    // создаем обработчик события "OnBeforeIBlockElementAdd"
    static function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {   
        CModule::IncludeModule('iblock');
        if($arFields["IBLOCK_ID"] == 61) //ORGSTRUCTURE
        {   
                  
            $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>316));
            if($ar_props = $db_props->Fetch()){
                $arr_prop[$ar_props['CODE']] = $ar_props;
            }
            $db_props2020 = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>325));
            if($ar_props = $db_props2020->Fetch()){
                $arr_prop[$ar_props['CODE']] = $ar_props;
            }
            if (!empty($arr_prop['DATE']['VALUE']) && empty($arr_prop['SORT_DATE']['VALUE'])){

                $arr = ParseDateTime($arr_prop['DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS"); 
                $new_date = $arr["DD"].'.'. $arr['MM'].'.'.'2020';
                $arFields['PROPERTY_VALUES']['SORT_DATE'] = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                /*     $log .= date("Y.m.d G:i:s") . "\n";
                $log .= print_r($arr_prop , 1);
                $log .= "\n------------------------\n";
                $log .= print_r($new_date , 1);
                $log .= "\n------------------------\n"; 
                file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);                
                */

                // global $APPLICATION;
                // $APPLICATION->throwException("Введите символьный код.");
                // return false;
            }
        }
        if($arFields["IBLOCK_ID"] == 98) //ORGSTRUCTURE
        {   
            $arr_prop = array();
                foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                    $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                    if($ar_props = $db_props->Fetch()){
                        $arr_prop[$ar_props['CODE']] = $ar_props;
                        if(is_array($arFields[$key])){
                            foreach ($field as $key_2 => $value) {
                                $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                            }
                        }else{
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                            $arr_prop[$ar_props['CODE']]['key'] = $key;
                            $arr_prop[$ar_props['CODE']]['val'] = $field;
                        }
                        
                    }
                    
                }
                  
            // $rsElement = CIBlockElement::GetList(
            //     $arOrder  = array("ID" => "DESC"),
            //     $arFilter = array(
            //         "IBLOCK_ID"    => 98,
            //     ),
            //     false,
            //     array("nTopCount" => 1),
            //     $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
            //    );
            //    while($arElement = $rsElement->GetNextElement()) {
            //     $el = $arElement->GetFields();
            //     $el["PROPERTIES"] = $arElement->GetProperties();
            //     $old_items[$el['ID']] = $el;
               
                  
            //         $last_id = $el["PROPERTIES"]['UNIQ_ID']['VALUE'];
            //         $arFields['PROPERTY_VALUES'][822]['n0']['VALUE'] = intval($last_id+1);
                
            //    }

        }

        if($arFields["IBLOCK_ID"] == 80)
        {   			
			$isValueIsEmpty = true;
			if (is_array($arFields['PROPERTY_VALUES'][398])){
				foreach($arFields['PROPERTY_VALUES'][398] as $key=>$value){
					if (!empty($value["VALUE"])){
						$isValueIsEmpty = false;
					}
				}
			}
			else{
				if (!empty($arFields['PROPERTY_VALUES'][398])){
					$isValueIsEmpty = false;
				}
			}
			if($isValueIsEmpty){
			//if(true){
				$log .= "\n-EMPTY--------\n";	
				$rsElement = CIBlockElement::GetList(
					$arOrder  = array("SORT" => "ASC"),
					$arFilter = array(
						"ACTIVE"    => "Y",
						"IBLOCK_ID" => $arFields["IBLOCK_ID"],
						"ID" => $arFields["ID"],
					),
					false,
					false,
					$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "DATE_CREATE","CODE", "PROPERTY_*")
				);
				while($arElement = $rsElement->GetNext()) {
					$custom_date = $arElement['DATE_CREATE'];
				}
				$log .= "\n-$custom_date--------\n";	

				$arFields['PROPERTY_VALUES'][398] = FormatDate("d.m.Y", MakeTimeStamp($custom_date));

				$log .= print_r($arFields , 1);

			}  
			else{
				$log .= "\n-SKIPPED--------\n";	
			} 
			file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);  
        }

        if ($arFields["IBLOCK_ID"] == 98) {
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arFields['ID']
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
            );

            while($arElement = $rsElement->GetNextElement()) {
                // $archive_transports =  $arElement;
                $archive_transports = $arElement->GetFields();
                $archive_transports["PROPERTIES"] = $arElement->GetProperties();
            }
            
            $arr_prop = array();
            foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                if($ar_props = $db_props->Fetch()){
                    $arr_prop[$ar_props['CODE']] = $ar_props;
                    if(is_array($arFields[$key])){
                        foreach ($field as $key_2 => $value) {
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                        }
                    }else{
                        $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                        $arr_prop[$ar_props['CODE']]['key'] = $key;
                        $arr_prop[$ar_props['CODE']]['val'] = $field;
                    }
                    
                }
            }

            if ($archive_transports['PROPERTIES']['TIP']['VALUE'] == 'Системник') {
                $type = 342;
            } elseif($archive_transports['PROPERTIES']['TIP']['VALUE'] == 'Моноблок') {
                $type =343;
            } elseif($archive_transports['PROPERTIES']['TIP']['VALUE'] == 'Ноутбук') {
                $type =344;
            } else {
                $type = '';
            } 

            if($arr_prop['TIP']['VALUE'] == 336){
                $type_new = 339;
            }
            if($arr_prop['TIP']['VALUE'] == 337){
                $type_new = 340;
            }
            if($arr_prop['TIP']['VALUE'] == 338){
                $type_new = 341;
            }

      

            $name = 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $rsIB = new CIBlockElement;
            $arFieldsOld = array(
                "ACTIVE"    => "Y",
                "NAME" => $name,
                "IBLOCK_ID" => 99,
                "PROPERTY_VALUES"   => array(
                    'FIO_STARYY' => $archive_transports['PROPERTIES']['FIO']['VALUE'],
                    'DOLZHNOST_STARYY' => $archive_transports['PROPERTIES']['DOLZHNOST']['VALUE'],
                    'UPRAVLENIE_OTDEL_STARYY' => $archive_transports['PROPERTIES']['UPRAVLENIE_OTDEL']['VALUE'],
                    // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
                    'TIP_STARYY' => $type,
                    'NOMER_KOMPYUTERA_STARYY' => $archive_transports['PROPERTIES']['NOMER_KOMPYUTERA']['VALUE'],
                    'PROGRAMMNOE_OBESPECHENIE_STARYY' => $archive_transports['PROPERTIES']['PROGRAMMNOE_OBESPECHENIE']['VALUE'],
                    'MODEL_SISTEMNOGO_BLOKA_NOTBUKA_ESLI_EST_STARYY' => $archive_transports['PROPERTIES']['MODEL_SISTEMNOGO_BLOKA_NOTBUKA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_EST_' => $archive_transports['PROPERTIES']['SERIYNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_EST']['VALUE'],
                    'INVENTARNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_E']['VALUE'],
                    'MODEL_MONITORA_ESLI_EST_STARYY' => $archive_transports['PROPERTIES']['MODEL_MONITORA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_MONITORA_STARYY' => $archive_transports['PROPERTIES']['SERIYNYY_NOMER_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_MONITORA_ESLI_EST_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER_MONITORA_ESLI_EST']['VALUE'],
                    'MODEL_DOP_MONITOR_ESLI_EST_STARYY' => $archive_transports['PROPERTIES']['MODEL_DOP_MONITOR_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_DOP_MONITORA_STARYY' => $archive_transports['PROPERTIES']['SERIYNYY_NOMER_DOP_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_DOP_MONITORA_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER_DOP_MONITORA']['VALUE'],
                    'MODEL_DOP_TWO_MONITORA_ESLI_EST_STARYY' => $archive_transports['PROPERTIES']['MODEL_DOP_TWO_MONITORA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_DOP_TWO_MONITORA_STARYY' => $archive_transports['PROPERTIES']['SERIYNYY_NOMER_DOP_TWO_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_DOP_TWO_MONITORA_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER_DOP_TWO_MONITORA']['VALUE'],
                    'PRIMECHANIE_STARYY' => $archive_transports['PROPERTIES']['PRIMECHANIE']['VALUE']['TEXT'],
                    'MOL_STARYY' => $archive_transports['PROPERTIES']['MOL']['VALUE'],
                    'NOMER_KOMPYUTERA2_STARYY' => $archive_transports['PROPERTIES']['NOMER_KOMPYUTERA2']['VALUE'],
                    'PROGRAMMNOE_OBESPECHENIE2_STARYY' => $archive_transports['PROPERTIES']['PROGRAMMNOE_OBESPECHENIE2']['VALUE'],
                    // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
                    'FIO' => $arr_prop['FIO']['VALUE'],
                    'DOLZHNOST' => $arr_prop['DOLZHNOST']['VALUE'],
                    'UPRAVLENIE_OTDEL' => $arr_prop['UPRAVLENIE_OTDEL']['VALUE'],
                    'TIP' => $type_new,
                    'NOMER_KOMPYUTERA' => $arr_prop['NOMER_KOMPYUTERA']['VALUE'],
                    'PROGRAMMNOE_OBESPECHENIE' => $arr_prop['PROGRAMMNOE_OBESPECHENIE']['VALUE'],
                    'MODEL_SISTEMNOGO_BLOKA_NOTBUKA_ESLI_EST' => $arr_prop['MODEL_SISTEMNOGO_BLOKA_NOTBUKA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_EST' => $arr_prop['SERIYNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_EST']['VALUE'],
                    'INVENTARNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_E' => $arr_prop['INVENTARNYY_NOMER_SISTEMNOGO_BLOKA_NOUTBUKA_ESLI_E']['VALUE'],
                    'MODEL_MONITORA_ESLI_EST' => $arr_prop['MODEL_MONITORA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_MONITORA' => $arr_prop['SERIYNYY_NOMER_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_MONITORA_ESLI_EST' => $arr_prop['INVENTARNYY_NOMER_MONITORA_ESLI_EST']['VALUE'],
                    'MODEL_DOP_MONITOR_ESLI_EST' => $arr_prop['MODEL_DOP_MONITOR_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_DOP_MONITORA' => $arr_prop['SERIYNYY_NOMER_DOP_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_DOP_MONITORA' => $arr_prop['INVENTARNYY_NOMER_DOP_MONITORA']['VALUE'],
                    'MODEL_DOP_TWO_MONITORA_ESLI_EST' => $arr_prop['MODEL_DOP_TWO_MONITORA_ESLI_EST']['VALUE'],
                    'SERIYNYY_NOMER_DOP_TWO_MONITORA' => $arr_prop['SERIYNYY_NOMER_DOP_TWO_MONITORA']['VALUE'],
                    'INVENTARNYY_NOMER_DOP_TWO_MONITORA' => $arr_prop['INVENTARNYY_NOMER_DOP_TWO_MONITORA']['VALUE'],
                    'PRIMECHANIE' => $arr_prop['PRIMECHANIE']['VALUE'],
                    'MOL' => $arr_prop['MOL']['VALUE'],
                    'NOMER_KOMPYUTERA2' => $arr_prop['NOMER_KOMPYUTERA2']['VALUE'],
                    'PROGRAMMNOE_OBESPECHENIE2' => $arr_prop['PROGRAMMNOE_OBESPECHENIE2']['VALUE'],
                    'UNIQ_ID' => $arr_prop['UNIQ_ID']['VALUE'],
                    'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
                    
                )
            );
            $id = $rsIB->Add($arFieldsOld);

            // $log .= date("Y.m.d G:i:s") . "\n";
            // $log .= print_r($archive_transports['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arr_prop['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFields['PROPERTY_VALUES'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY'] , 1);
            // $log .= "\n------------------------\n";
            $log .= print_r($arr_prop , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($archive_transports , 1);
            $log .= "\n------------------------\n";
 
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        }

        if ($arFields["IBLOCK_ID"] == 152) {
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arFields['ID']
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
            );

            while($arElement = $rsElement->GetNextElement()) {
                // $archive_transports =  $arElement;
                $archive_transports = $arElement->GetFields();
                $archive_transports["PROPERTIES"] = $arElement->GetProperties();
            }

            $arr_prop = array();
            foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                if($ar_props = $db_props->Fetch()){
                    $arr_prop[$ar_props['CODE']] = $ar_props;
                    if(is_array($arFields[$key])){
                        foreach ($field as $key_2 => $value) {
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                        }
                    }else{
                        $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                        $arr_prop[$ar_props['CODE']]['key'] = $key;
                        $arr_prop[$ar_props['CODE']]['val'] = $field;
                    }

                }
            }

            $name = 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $date_time = date("d.m.Y G:i:s");
            $rsIB = new CIBlockElement;
            if (empty($archive_transports['PROPERTIES']['NOTE']['VALUE']['TEXT'])) {
                $note_old = '';
            } else {
                $note_old = $archive_transports['PROPERTIES']['NOTE']['VALUE']['TEXT'];
            }
            $arFieldsOld = array(
                "ACTIVE"    => "Y",
                "NAME" => $name,
                "IBLOCK_ID" => 151,
                "PROPERTY_VALUES"   => array(
                    'TYPE_STARYY' => $archive_transports['PROPERTIES']['TYPE']['VALUE'],
                    'MODEL_STARYY' => $archive_transports['PROPERTIES']['MODEL']['VALUE'],
                    'SERIAL_NUMBER_STARYY' => $archive_transports['PROPERTIES']['SERIAL_NUMBER']['VALUE'],
                    'ACTUAL_USER_STARYY' => $archive_transports['PROPERTIES']['ACTUAL_USER']['VALUE'],
                    'QUANTITY_STARYY' => $archive_transports['PROPERTIES']['QUANTITY']['VALUE'],
                    'UID_MOL_STARYY' => $archive_transports['PROPERTIES']['UID_MOL']['VALUE'],
                    'CODE_MOL_STARYY' => $archive_transports['PROPERTIES']['CODE_MOL']['VALUE'],
                    'MOL_STARYY' => $archive_transports['PROPERTIES']['MOL']['VALUE'],
                    'TMC_UID_STARYY' => $archive_transports['PROPERTIES']['TMC_UID']['VALUE'],
                    'TMC_CODE_STARYY' => $archive_transports['PROPERTIES']['TMC_CODE']['VALUE'],
//                    'TMC_NAME_STARYY' => $archive_transports['PROPERTIES']['TMC_NAME']['VALUE'],
                    'TMC_FULL_NAME_STARYY' => $archive_transports['PROPERTIES']['TMC_FULL_NAME']['VALUE'],
//                    'VENDOR_CODE_STARYY' => $archive_transports['PROPERTIES']['VENDOR_CODE']['VALUE'],
                    'SUBDIVISION_LINK_STARYY' => $archive_transports['PROPERTIES']['SUBDIVISION_LINK']['VALUE'],
                    'SUBDIVISION_CODE_STARYY' => $archive_transports['PROPERTIES']['SUBDIVISION_CODE']['VALUE'],
//                    'SUBDIVISION_NAME_FULL_STARYY' => htmlspecialchars_decode($archive_transports['PROPERTIES']['SUBDIVISION_NAME_FULL']['VALUE'], ENT_QUOTES),
                    'STRUCTURAL_SUBDIVISION_STARYY' => $archive_transports['PROPERTIES']['STRUCTURAL_SUBDIVISION']['VALUE'],
                    'NOMENCLATURE_TYPE_LINK_STARYY' => $archive_transports['PROPERTIES']['NOMENCLATURE_TYPE_LINK']['VALUE'],
//                    'NOMENCLATURE_TYPE_NAME_STARYY' => $archive_transports['PROPERTIES']['NOMENCLATURE_TYPE_NAME']['VALUE'],
                    'UNIT_LINK_STARYY' => $archive_transports['PROPERTIES']['UNIT_LINK']['VALUE'],
                    'UNIT_CODE_STARYY' => $archive_transports['PROPERTIES']['UNIT_CODE']['VALUE'],
                    'UNIT_NAME_STARYY' => $archive_transports['PROPERTIES']['UNIT_NAME']['VALUE'],
//                    'INVENTORY_NUMBER_STARYY' => $archive_transports['PROPERTIES']['INVENTORY_NUMBER']['VALUE'],
//                    'NOTE_STARYY' => $archive_transports['PROPERTIES']['NOTE']['VALUE']['TEXT'],
                    'NOTE_STARYY' => $note_old,
                    'EQUIPMENT_NAME_STARYY' => $archive_transports['NAME'],
                    'TYPE' => $arr_prop['TYPE']['VALUE'],
                    'MODEL' => $arr_prop['MODEL']['VALUE'],
                    'SERIAL_NUMBER' => $arr_prop['SERIAL_NUMBER']['VALUE'],
                    'ACTUAL_USER' => $arr_prop['ACTUAL_USER']['VALUE'],
                    'QUANTITY' => $arr_prop['QUANTITY']['VALUE'],
                    'UID_MOL' => $arr_prop['UID_MOL']['VALUE'],
                    'CODE_MOL' => $arr_prop['CODE_MOL']['VALUE'],
                    'MOL' => $arr_prop['MOL']['VALUE'],
                    'TMC_UID' => $arr_prop['TMC_UID']['VALUE'],
                    'TMC_CODE' => $arr_prop['TMC_CODE']['VALUE'],
//                    'TMC_NAME' => $arr_prop['TMC_NAME']['VALUE'],
                    'TMC_FULL_NAME' => $arr_prop['TMC_FULL_NAME']['VALUE'],
//                    'VENDOR_CODE' => $arr_prop['VENDOR_CODE']['VALUE'],
                    'SUBDIVISION_LINK' => $arr_prop['SUBDIVISION_LINK']['VALUE'],
                    'SUBDIVISION_CODE' => $arr_prop['SUBDIVISION_CODE']['VALUE'],
//                    'SUBDIVISION_NAME_FULL' => htmlspecialchars_decode($arr_prop['PROPERTIES']['SUBDIVISION_NAME_FULL']['VALUE'], ENT_QUOTES),
//                    'SUBDIVISION_NAME_FULL' => $arr_prop['SUBDIVISION_NAME_FULL']['VALUE'],
                    'STRUCTURAL_SUBDIVISION' => $arr_prop['STRUCTURAL_SUBDIVISION']['VALUE'],
                    'NOMENCLATURE_TYPE_LINK' => $arr_prop['NOMENCLATURE_TYPE_LINK']['VALUE'],
//                    'NOMENCLATURE_TYPE_NAME' => $arr_prop['NOMENCLATURE_TYPE_NAME']['VALUE'],
                    'UNIT_LINK' => $arr_prop['UNIT_LINK']['VALUE'],
                    'UNIT_CODE' => $arr_prop['UNIT_CODE']['VALUE'],
                    'UNIT_NAME' => $arr_prop['UNIT_NAME']['VALUE'],
//                    'INVENTORY_NUMBER' => $arr_prop['INVENTORY_NUMBER']['VALUE'],
                    'NOTE' => $arr_prop['NOTE']['VALUE'],
                    'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
                    'CHANGED_ELEMENT' => $arFields['ID'],
                    'DATE_TIME_CHANGE' => $date_time,

                )
            );
            $id = $rsIB->Add($arFieldsOld);

            // $log .= date("Y.m.d G:i:s") . "\n";
            // $log .= print_r($archive_transports['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arr_prop['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFields['PROPERTY_VALUES'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY'] , 1);
            // $log .= "\n------------------------\n";
            $log .= print_r($arr_prop , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($archive_transports , 1);
            $log .= "\n------------------------\n";

            file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        }


         if($arFields["IBLOCK_ID"] == 85)
        {
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arFields['ID'],
                    "IBLOCK_ID" => 85
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "XML_ID", "PROPERTY_*")
            );
            while($arElement = $rsElement->GetNextElement()) {
                // $archive_transports =  $arElement;
                $archive_transports = $arElement->GetFields();
                $archive_transports["PROPERTIES"] = $arElement->GetProperties();
                $xmlID = $archive_transports['XML_ID'];
            }
           
            
            $arr_prop = array();
            foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                if($ar_props = $db_props->Fetch()){
                    $arr_prop[$ar_props['CODE']] = $ar_props;
                    if(is_array($arFields[$key])){
                        foreach ($field as $key_2 => $value) {
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                        }
                    } else {
                        $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                        $arr_prop[$ar_props['CODE']]['key'] = $key;
                        $arr_prop[$ar_props['CODE']]['val'] = $field;
                    }
                    
                }
                
            }
    
            if (is_array($arr_prop['UF_FILE_ID']['VALUE'])) {
                foreach ($arr_prop['UF_FILE_ID']['VALUE'] as $key => $fileId) {
                    $files[] = $fileId['VALUE'];
                }
            } else {
                $files[] = $arr_prop['UF_FILE_ID']['VALUE'];
            }

            if(is_array($arr_prop['UF_FOLDER_ID']['VALUE'])){
                foreach ($arr_prop['UF_FOLDER_ID']['VALUE'] as $key => $fileId) {
                    $folders[] = $fileId['VALUE'];
                }
            } else {
                $folders[] = $arr_prop['UF_FOLDER_ID']['VALUE'];
            }

            if (is_array($arr_prop['UF_FILE_PATH']['VALUE'])){
                foreach ($arr_prop['UF_FILE_PATH']['VALUE'] as $key => $fileId) {
                    $filesPath[] = $fileId['VALUE'];
                }
            } else {
                $filesPath[] = $arr_prop['UF_FILE_PATH']['VALUE'];
            }

            if (is_array($arr_prop['UF_FOLDER_PATH']['VALUE'])){
                foreach ($arr_prop['UF_FOLDER_PATH']['VALUE'] as $key => $fileId) {
                    $foldersPath[] = $fileId['VALUE'];
                }
            } else {
                $foldersPath[] = $arr_prop['UF_FOLDER_PATH']['VALUE'];
            }
            //debug($archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']);
            //debug($arr_prop['STATUS_OBORUDOVANIYA']);
            
            $statusOld = searchValuePropertyTypeList(85, 94, "STATUS_OBORUDOVANIYA", "STATUS_OBORUDOVANIYA", $archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']['VALUE']);
            /*if ($archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']['VALUE'] == 331) {
                $statusOld = 607;
            } else if ($archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']['VALUE'] == 332) {
                $statusOld = 608;
            } else if ($archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']['VALUE'] == 333) {
                $statusOld = 609;
            } */
            $status = searchValuePropertyTypeList(85, 94, "STATUS_OBORUDOVANIYA", "STATUS_OBORUDOVANIYA", $arr_prop['STATUS_OBORUDOVANIYA']['VALUE']);
            /*$status = '';
            if ($arr_prop['STATUS_OBORUDOVANIYA']['VALUE'] == 331) {
                $status = 604;
            } else if ($arr_prop['STATUS_OBORUDOVANIYA']['VALUE'] == 332) {
                $status = 605;
            } else if ($arr_prop['STATUS_OBORUDOVANIYA']['VALUE'] == 333) {
                $status = 606;
            } */
            
            if (stringChecking($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'], "СИ")) {
                $type = 308;
            } elseif (stringChecking($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'], "Вспомогательное")) {
                $type = 310;
            } elseif(stringChecking($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'], "Испытательное")) {
                $type = 309; 
            } else {
                $type = '';
            } 
            
            if(stringChecking($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE_ENUM'], 'не требуется')){
                $pov_old = 312;
            } elseif (stringChecking($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE_ENUM'], 'требуется') || !empty($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE_ENUM'])){
                $pov_old = 311;
            }
    
            if (stringChecking($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE_ENUM'], 'не требуется')) {
                $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 314;
            } elseif (stringChecking($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE_ENUM'], 'требуется') || !empty($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE_ENUM'])){
                $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 313;
            }
            
            if(stringChecking($archive_transports['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE_ENUM'], 'НЕ ОБСЛУЖИВАЕТСЯ')) {
                $przk = 323;
            } elseif (stringChecking($archive_transports['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE_ENUM'], 'Обслуживается')) {
                $przk = 324;
            }

            if (stringChecking($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE_ENUM'], 'не требуется')) {
                $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'] = 316;
            } elseif (stringChecking($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE_ENUM'], 'требуется') || !empty($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE_ENUM'])){
                $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'] = 315;
            }
            
            if($arr_prop['NEOBKHODIMOST_POVERKI']['VALUE'] == 273){
                $poverka = 303;
            }
            if($arr_prop['NEOBKHODIMOST_POVERKI']['VALUE'] == 272){
                $poverka = 302;
            }
            if($arr_prop['KALIBROVKA']['VALUE'] == 277){
                $kalibrovka = 307;
            }
            if($arr_prop['KALIBROVKA']['VALUE'] == 276){
                $kalibrovka = 306;
            }
            if($arr_prop['ATTESTATSIYA']['VALUE'] == 275){
                $atestas = 305;
            }
            if($arr_prop['ATTESTATSIYA']['VALUE'] == 274){
                $atestas = 304;
            }

            if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 289){
                $type_new = 299;
            }
            if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 270){
                $type_new = 300;
            }
            if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 271){
                $type_new = 301;
            }


            if($arr_prop['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] == 317){
                $priznak_oblug = 321;
            }
            if($arr_prop['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] == 318){
                $priznak_oblug = 322;
            }
            if ($arr_prop['LIZING']['VALUE'] == 334) {
                $lizing = 'Да';
            } else if ($arr_prop['LIZING']['VALUE'] == 335) {
                $lizing = 'Нет';
            }
            $name = empty($arFields['NAME']) ? '' : 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $rsIB = new CIBlockElement;
            $arFieldsOld = array(
                "ACTIVE"    => "Y",
                "NAME" => $name,
                "IBLOCK_ID" => 94,
                "PROPERTY_VALUES"   => array(
                    "OBORUDOVANIE" => $arFields['ID'],
                    'INVENTARNYY_NOMER_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER']['VALUE'],
                    'ZAVODSKOY_NOMER_STARYY' => $archive_transports['PROPERTIES']['ZAVODSKOY_NOMER']['VALUE'],
                    'DATA_POSTANOVKI_NA_UCHET_STARYY' => $archive_transports['PROPERTIES']['DATA_POSTANOVKI_NA_UCHET']['VALUE'],
                    // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
                    'TERRITORIALNOE_PODRAZDELENIE_STARYY' => $archive_transports['PROPERTIES']['TERRITORIALNOE_PODRAZDELENIE']['VALUE'],
                    'MOL_PODRAZDELENIYA_STARYY' => $archive_transports['PROPERTIES']['MOL_PODRAZDELENIYA']['VALUE'],
                    'TIP_OBORUDOVANIYA_STARYY' => $type,
                    'OTDEL_V_PODRAZDELENII_STARYY' => $archive_transports['PROPERTIES']['OTDEL_V_PODRAZDELENII']['VALUE'],
                    'MOL_V_OTDELE_STARYY' => $archive_transports['PROPERTIES']['MOL_V_OTDELE']['VALUE'],
                    'OTDELA_V_PODRAZDELENII_STARYY' => $archive_transports['PROPERTIES']['OTDELA_V_PODRAZDELENII']['VALUE'],
                    'DATA_PEREMESHCHENIYA_STARYY' => $archive_transports['PROPERTIES']['DATA_PEREMESHCHENIYA']['VALUE'],
                    'STOIMOST_PERVONACHALNAYA_BEZ_NDS_STARYY' => $archive_transports['PROPERTIES']['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE'],
                    'STATUS_OBORUDOVANIYA_STARYY' => $statusOld,
                    'SOSTAVNOE_OBORUDOVANIYA_STARYY' => $archive_transports['PROPERTIES']['SOSTAVNOE_OBORUDOVANIYA']['VALUE'],
                    'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE'],
                    'POVERKA_STARYY' => $pov_old,
                    'PEREODICHNOST_POVERKI_STARYY' => $archive_transports['PROPERTIES']['PEREODICHNOST_POVERKI']['VALUE'],
                    'DATA_POVERKI_STARYY' => $archive_transports['PROPERTIES']['DATA_POVERKI']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_POVERKI_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_POVERKI']['VALUE'],
                    'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE']['TEXT'],
                    'ATTESTATSIYA_STARYY' => $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'],
                    'PERIODICHNOST_ATTESTAT_STARYY' => $archive_transports['PROPERTIES']['PERIODICHNOST_ATTESTAT']['VALUE'],
                    'DATA_ATTESTATSII_STARYY' => $archive_transports['PROPERTIES']['DATA_ATTESTATSII']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_ATTESTATSII_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_ATTESTATSII']['VALUE'],
                    'KALIBROVKA_STARYY' => $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'],
                    'PEREODICHNOST_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['PEREODICHNOST_KALIBROVKI']['VALUE'],
                    'DATA_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['DATA_KALIBROVKI']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_KALIBROVKI']['VALUE'],
                    'OS_TMTS_STARYY' => $archive_transports['PROPERTIES']['OS_TMTS']['VALUE'],
                    'VN_NOMER_STARYY' => $archive_transports['PROPERTIES']['VN_NOMER']['VALUE'],
                    'UF_FILE_ID_STARYY' => $archive_transports['PROPERTIES']['UF_FILE_ID']['VALUE'],
                    'UF_FOLDER_ID_STARYY' => $archive_transports['PROPERTIES']['UF_FOLDER_ID']['VALUE'],
                    'UF_FILE_PATH_STARYY' => $archive_transports['PROPERTIES']['UF_FILE_PATH']['VALUE'],
                    'UF_FOLDER_PATH_STARYY' => $archive_transports['PROPERTIES']['UF_FOLDER_PATH']['VALUE'],
                    'INVENTARNYY_NOMER' => $arr_prop['INVENTARNYY_NOMER']['VALUE'],
                    'ZAVODSKOY_NOMER' => $arr_prop['ZAVODSKOY_NOMER']['VALUE'],
                    'DATA_POSTANOVKI_NA_UCHET' => $arr_prop['DATA_POSTANOVKI_NA_UCHET']['VALUE'],
                    // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
                    'TERRITORIALNOE_PODRAZDELENIE' => $arr_prop['TERRITORIALNOE_PODRAZDELENIE']['VALUE'],
                    'MOL_PODRAZDELENIYA' => $arr_prop['MOL_PODRAZDELENIYA']['VALUE'],
                    'TIP_OBORUDOVANIYA' => $type_new,
                    'MOL_V_OTDELE' => $arr_prop['MOL_V_OTDELE']['VALUE'],
                    'OTDELA_V_PODRAZDELENII' => $arr_prop['OTDELA_V_PODRAZDELENII']['VALUE'],
                    'DATA_PEREMESHCHENIYA' => $arr_prop['DATA_PEREMESHCHENIYA']['VALUE'],
                    'STOIMOST_PERVONACHALNAYA_BEZ_NDS' => $arr_prop['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE'],
                    'STATUS_OBORUDOVANIYA' => $status,
                    'SOSTAVNOE_OBORUDOVANIYA' => $arr_prop['SOSTAVNOE_OBORUDOVANIYA']['VALUE'],
                    'KOMMENTARIY' => $arr_prop['KOMMENTARIY']['VALUE'],
                    'NEOBKHODIMOST_POVERKI' => $poverka,
                    'PEREODICHNOST_POVERKI' => $arr_prop['PEREODICHNOST_POVERKI']['VALUE'],
                    'DATA_POVERKI' => $arr_prop['DATA_POVERKI']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_POVERKI' => $arr_prop['DATA_SLEDUYUSHCHEY_POVERKI']['VALUE'],
                    'ATTESTATSIYA' => $atestas,
                    'PERIODICHNOST_ATTESTAT' => $arr_prop['PERIODICHNOST_ATTESTAT']['VALUE'],
                    'DATA_ATTESTATSII' => $arr_prop['DATA_ATTESTATSII']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_ATTESTATSII' => $arr_prop['DATA_SLEDUYUSHCHEY_ATTESTATSII']['VALUE'],
                    'KALIBROVKA' => $kalibrovka,
                    'OTDEL_V_PODRAZDELENII' => $arr_prop['OTDEL_V_PODRAZDELENII']['VALUE'],
                    'PEREODICHNOST_KALIBROVKI' => $arr_prop['PEREODICHNOST_KALIBROVKI']['VALUE'],
                    'DATA_KALIBROVKI' => $arr_prop['DATA_KALIBROVKI']['VALUE'],
                    'DATA_SLEDUYUSHCHEY_KALIBROVKI' => $arr_prop['DATA_SLEDUYUSHCHEY_KALIBROVKI']['VALUE'],
                    'OS_TMTS' => $arr_prop['OS_TMTS']['VALUE'],
                    'VN_NOMER' => $arr_prop['VN_NOMER']['VALUE'],
                    'UF_FILE_ID' => $files,
                    'UF_FOLDER_ID' => $folders,
                    'UF_FILE_PATH' => $filesPath,
                    'UF_FOLDER_PATH' => $foldersPath,
                    'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
                    'NOMER_POVERKI' => $arr_prop['NOMER_POVERKI']['VALUE'],
                    'NOMER_ATESTATSII' => $arr_prop['NOMER_ATESTATSII']['VALUE'],
                    'NOMER_KALIBROVKI' => $arr_prop['NOMER_KALIBROVKI']['VALUE'],
                    'SOSTAVNOE' => $arr_prop['SOSTAVNOE']['VALUE'],
                    'V_SOSTAV_VKHODIT' => $arr_prop['V_SOSTAV_VKHODIT']['VALUE'],
                    'LIZING' => $lizing,
                    'KOMENTY' => $arr_prop['KOMENTY']['VALUE'],
                    'DOGOVOR_OBSLUZHIVANIYA' => $arr_prop['DOGOVOR_OBSLUZHIVANIYA']['VALUE'],
                    'PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA' => $priznak_oblug,
                    'DOKUMENT_POSTAVKI' => $arr_prop['DOKUMENT_POSTAVKI']['VALUE'],
                    'NOMER_POVERKI_STARYY' => $archive_transports['PROPERTIES']['NOMER_POVERKI']['VALUE'],
                    'NOMER_ATESTATSII_STARYY' => $archive_transports['PROPERTIES']['NOMER_ATESTATSII']['VALUE'],
                    'NOMER_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['NOMER_KALIBROVKI']['VALUE'],
                    'SOSTAVNOE_STARYY' => $archive_transports['PROPERTIES']['SOSTAVNOE']['VALUE']['TEXT'],
                    'V_SOSTAV_VKHODIT_STARYY' => $archive_transports['PROPERTIES']['V_SOSTAV_VKHODIT']['VALUE']['TEXT'],
                    'LIZING_STARYY' => $archive_transports['PROPERTIES']['LIZING']['VALUE'],
                    'KOMENTY_STARYY' => $archive_transports['PROPERTIES']['KOMENTY']['VALUE'],
                    'DOGOVOR_OBSLUZHIVANIYA_STARYY' => $archive_transports['PROPERTIES']['DOGOVOR_OBSLUZHIVANIYA']['VALUE']['TEXT'],
                    'PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY' => $przk,
                    'DOKUMENT_POSTAVKI_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_POSTAVKI']['VALUE']['TEXT'],
                    'CODE_OBORUDOVANIYA' => $xmlID,
                    'SOSTAVNOE_OBORUDOVANIE' => $arr_prop['SOSTAVNOE_OBORUDOVANIE']['VALUE'],
                    'SOSTAVNOE_OBORUDOVANIE_STARYY' => $archive_transports['PROPERTIES']['SOSTAVNOE_OBORUDOVANIE']['VALUE']
                )
            );
            $id = $rsIB->Add($arFieldsOld);

            // $log .= date("Y.m.d G:i:s") . "\n";
            $log .= print_r($archive_transports['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($arr_prop['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            $log .= "\n------------------------\n";
            // $log .= print_r($arFields['PROPERTY_VALUES'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($arFieldsOld['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY'] , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($priznak_oblug , 1);
            // $log .= "\n------------------------\n";
            // $log .= print_r($przk , 1);
            // $log .= "\n------------------------\n";
 
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        } 

        if($arFields["IBLOCK_ID"] == 86)
        {
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arFields['ID']
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
            );
            while($arElement = $rsElement->GetNextElement()) {
                // $archive_transports =  $arElement;
                $archive_transports = $arElement->GetFields();
                $archive_transports["PROPERTIES"] = $arElement->GetProperties();
            }
            $arr_prop = array();
            foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                if($ar_props = $db_props->Fetch()){
                    $arr_prop[$ar_props['CODE']] = $ar_props;
                    
                    if (is_array($arFields[$key])) {
                        foreach ($field as $key_2 => $value) {
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                        }
                    } else {
                        $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                    }
                    
                }
                
            }
            if(is_array($arr_prop['FILE_ID']['VALUE'])){
                foreach ($arr_prop['FILE_ID']['VALUE'] as $key => $fileId) {
                    $files[] = $fileId['VALUE'];
                }
            } else {
                $files[] = $arr_prop['FILE_ID']['VALUE'];
            }

            if(is_array($arr_prop['FOLDER_ID']['VALUE'])){
                foreach ($arr_prop['FOLDER_ID']['VALUE'] as $key => $fileId) {
                    $folders[] = $fileId['VALUE'];
                }
            } else {
                $folders[] = $arr_prop['FOLDER_ID']['VALUE'];
            }

            if (is_array($arr_prop['FILE_PATH']['VALUE'])) {
                foreach ($arr_prop['FILE_PATH']['VALUE'] as $key => $fileId) {
                    $filesPath[] = $fileId['VALUE'];
                }
            } else {
                $filesPath[] = $arr_prop['FILE_PATH']['VALUE'];
            }

            if (is_array($arr_prop['FOLDER_PATH']['VALUE'])) {
                foreach ($arr_prop['FOLDER_PATH']['VALUE'] as $key => $fileId) {
                    $foldersPath[] = $fileId['VALUE'];
                }
            } else {
                $foldersPath[] = $arr_prop['FOLDER_PATH']['VALUE'];
            }

            foreach ($arr_prop['FOTO']['VALUE'] as $key => $docs) {
                $doc[] = $docs['VALUE'];
            }
            foreach ($arr_prop['DATA_POVERKI']['VALUE'] as $key => $date) {
                $dates = $date['VALUE'];
            }

            if(stringChecking($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE_ENUM'], 'Поверка')){
                $type = 328;
            }elseif(stringChecking($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE_ENUM'], 'Атестация')){
                $type = 329;
            }elseif(stringChecking($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE_ENUM'], 'Калибровка')){
                $type = 330;
            }else{
                $type = '';
            } 
    
    
            if (stringChecking($archive_transports['PROPERTIES']['SROK_DEYSTVIYA']['VALUE_ENUM'], 'Три месяца')){
                $srok = 'Три месяца';
            }

            if(stringChecking($archive_transports['PROPERTIES']['SROK_DEYSTVIYA']['VALUE_ENUM'], 'Пол года')){
                $srok = 'Пол года';
            }

            if(stringChecking($archive_transports['PROPERTIES']['SROK_DEYSTVIYA']['VALUE_ENUM'], 'Год')){
                $srok = 'Год';
            }

            if(stringChecking($archive_transports['PROPERTIES']['SROK_DEYSTVIYA']['VALUE_ENUM'], 'Два года')){
                $srok = 'Два года';
            }
    
            if($arFields['PROPERTY_VALUES'][582] == 281){
                $srok_new = 'Три месяца';
            }
            if($arFields['PROPERTY_VALUES'][582] == 282){
                $srok_new = 'Пол года';
            }
            if($arFields['PROPERTY_VALUES'][582] == 283){
                $srok_new = 'Год';
            }
            if($arFields['PROPERTY_VALUES'][582] == 284){
                $srok_new = 'Два года';
            }

            if($arr_prop['TIP_OBSLUZHIVANIYA']['VALUE'] == 278){
                $type_new = 325;
            }
            if($arr_prop['TIP_OBSLUZHIVANIYA']['VALUE'] == 279){
                $type_new = 326;
            }
            if($arr_prop['TIP_OBSLUZHIVANIYA']['VALUE']== 280){
                $type_new = 327;
            }

            $name = empty($arFields['NAME']) ? '' : 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $rsIB = new CIBlockElement;
            $arFieldsOld = array(
                "ACTIVE" => "Y",
                "NAME" => $name,
                "IBLOCK_ID" => 95,
                "PROPERTY_VALUES"   => array(
                    'OBORUDOVANIE_STARYY' => $archive_transports['PROPERTIES']['OBORUDOVANIE']['VALUE'],
                    'DATA_POVERKI_STARYY' => $archive_transports['PROPERTIES']['DATA_POVERKI']['VALUE'],
                    'SROK_DEYSTVIYA_STARYY' => $srok,
                    'OPISANIE_STARYY' => $archive_transports['PROPERTIES']['OPISANIE']['VALUE']['TEXT'],
                    'FOTO_STARYY' => $archive_transports['PROPERTIES']['FOTO']['VALUE'],
                    'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE']['TEXT'],
                    'TIP_OBSLUZHIVANIYA_STARYY' => $type,
                    
                    'UF_FILE_ID_STARYY' => $archive_transports['PROPERTIES']['FILE_ID']['VALUE'],
                    'UF_FOLDER_ID_STARYY' => $archive_transports['PROPERTIES']['FOLDER_ID']['VALUE'],
                    'UF_FILE_PATH_STARYY' => $archive_transports['PROPERTIES']['FILE_PATH']['VALUE'],
                    'UF_FOLDER_PATH_STARYY' => $archive_transports['PROPERTIES']['FOLDER_PATH']['VALUE'],
                    'TIP_OBSLUZHIVANIYA' => $type_new,
                    'OBORUDOVANIE' => $arr_prop['OBORUDOVANIE']['VALUE'],
                    'DATA_POVERKI' => $dates,
                    'SROK_DEYSTVIYA' => $srok_new,
                    'OPISANIE' => $arr_prop['OPISANIE']['VALUE'],
                    'FOTO' => $doc,
                    'KOMMENTARIY' => $arr_prop['KOMMENTARIY']['VALUE'],
                    'UF_FILE_ID' => $files,
                    'UF_FOLDER_ID' => $folders,
                    'UF_FILE_PATH' => $filesPath,
                    'UF_FOLDER_PATH' => $foldersPath,
                    'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
                )
            );
            $id = $rsIB->Add($arFieldsOld);

            $log .= date("Y.m.d G:i:s") . "\n";
            $log .= print_r($archive_transports , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($arr_prop , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($arFieldsOld , 1);
            $log .= "\n------------------------\n";
            // $log .= print_r($rsIB->LAST_ERROR , 1);
            // $log .= "\n------------------------\n";
 
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        } 

        if($arFields["IBLOCK_ID"] == 87)
        {
            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arFields['ID']
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
            );
            while($arElement = $rsElement->GetNextElement()) {
                // $archive_transports =  $arElement;
                $archive_transports = $arElement->GetFields();
                $archive_transports["PROPERTIES"] = $arElement->GetProperties();
            }

            $arr_prop = array();
            foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                if($ar_props = $db_props->Fetch()){
                    $arr_prop[$ar_props['CODE']] = $ar_props;
                    if(is_array($arFields[$key])){
                        foreach ($field as $key_2 => $value) {
                            $arr_prop[$ar_props['CODE']]['VALUE'] = $value;
                        }
                    } else {
                        $arr_prop[$ar_props['CODE']]['VALUE'] = $field;
                    }
                    
                }
                
            }

        //    foreach ($arFields['PROPERTY_VALUES'][325] as $key => $fileId) {
        //         $files[] = $fileId['VALUE'];
        //    }
        //    foreach ($arFields['PROPERTY_VALUES'][326] as $key => $folderId) {
        //     $folders[] = $folderId['VALUE'];
        //     }
        //     foreach ($arFields['PROPERTY_VALUES'][327] as $key => $filePath) {
        //         $filesPath[] = $filePath['VALUE'];
        //      }
        //     foreach ($arFields['PROPERTY_VALUES'][328] as $key => $folderPath) {
        //         $foldersPath[] = $folderPath['VALUE'];
        //     }

            // foreach ($arFields['PROPERTY_VALUES'][225] as $key => $docs) {
            //     $doc[] = $docs['VALUE'];
            // }
            // foreach ($arFields['PROPERTY_VALUES'][222] as $key => $date) {
            //     $dates = $date['VALUE'];
            // }

            // if($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE'] == 'Поверка'){
            //     $type = 188;
            // }elseif($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE'] == 'Атестация'){
            //     $type =189;
            // }elseif($archive_transports['PROPERTIES']['TIP_OBSLUZHIVANIYA']['VALUE'] == 'Калибровка'){
            //     $type =190;
            // }else{
            //     $type = '';
            // } 
    
            $propertyEnums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>87, "CODE"=>'STATUS'));
            while($enumFields = $propertyEnums->GetNext()) {
                if ($enumFields['ID'] == $arr_prop['STATUS']['VALUE']) {
                    $status = $enumFields['VALUE'];
                }
            }
      
            if($archive_transports['PROPERTIES']['STATUS']['VALUE'] == 'Черновик'){
                $srok = 199;
            }

            if($archive_transports['PROPERTIES']['STATUS']['VALUE'] == 'Ожидается подтверждение физически владеющего'){
                $srok = 200;
            }

            if($archive_transports['PROPERTIES']['STATUS']['VALUE'] == 'Ожидается подтверждение принятия обородувания'){
                $srok = 201;
            }

            if($archive_transports['PROPERTIES']['STATUS']['VALUE'] == 'Перемещение завершено'){
                $srok = 202;
            }
    
           
            if($arFields['PROPERTY_VALUES'][313] == 143){
                $srok_new = 195;
            }
            if($arFields['PROPERTY_VALUES'][313] == 144){
                $srok_new = 196;
            }
            if($arFields['PROPERTY_VALUES'][313] == 145){
                $srok_new = 197;
            }
            if($arFields['PROPERTY_VALUES'][313] == 146){
                $srok_new = 198;
            }

            // if($arFields['PROPERTY_VALUES'][299] == 136){
            //     $type_new = 181;
            // }
            // if($arFields['PROPERTY_VALUES'][299] == 137){
            //     $type_new = 182;
            // }
            // if($arFields['PROPERTY_VALUES'][299] == 138){
            //     $type_new = 183;
            // }
            $arFile = CFile::GetFileArray($archive_transports['PROPERTIES']['DOKUMENT_PODPISANNYY_MOL_OM']['VALUE']);
            //debug($arFile);
            /*$fileMOLStaryy = [
                'n0' => [
                    'VALUE' => [
                        'name' => $arFile['FILE_NAME'],
                        'size' => $arFile['FILE_SIZE'],
                        'type' => $arFile['CONTENT_TYPE'],
                        'full_path' => $arFile['SRC'],
                        'error' => '' 
                    ],
                ]
            ];
            debug($fileMOLStaryy);*/
            //debug($arr_prop['DOKUMENT_PODPISANNYY_MOL_OM']['VALUE']);
            $name = empty($arFields['NAME']) ? "" : 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            $date = ' в ' . date("G:i:s d.m.Y");
            $rsIB = new CIBlockElement;
            $arFieldsOld = array(
                "ACTIVE"    => "Y",
                "NAME" => $name,
                "IBLOCK_ID" => 96,
                "PROPERTY_VALUES"   => array(
                    'DATA_PEREMESHCHENIYA_STARYY' => $archive_transports['PROPERTIES']['DATA_PEREMESHCHENIYA']['VALUE'],
                    'MOL_STARYY' => $archive_transports['PROPERTIES']['MOL']['VALUE'],
                    'FIO_KTO_PEREDAET_STARYY' => $archive_transports['PROPERTIES']['FIO_KTO_PEREDAET']['VALUE'],
                    'FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY' => $archive_transports['PROPERTIES']['FIO']['VALUE'],
                    'OBORUDOVANIE_STARYY' => $archive_transports['PROPERTIES']['OBORUDOVANIE']['VALUE'],
                    'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE']['TEXT'],
                    'DOKUMENT_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT']['VALUE'],
                    
                    'DOKUMENT_PODPISANNYY_MOL_OM_STARYY' => $fileMOLStaryy,
                    'DOKUMENT_PODPISANNYY_MOL_I_PEREDAYUSHCHIM_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD']['VALUE'],
                    'DOKUMENT_PODPISANNYY_VSEMI_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_PODPISANNYY_VSEMI']['VALUE'],
                    'STATUS_STARYY' => $archive_transports['PROPERTIES']['STATUS']['VALUE'],

                    'DATA_PEREMESHCHENIYA' => $arr_prop['DATA_PEREMESHCHENIYA']['VALUE'],
                    'MOL' => $arr_prop['MOL']['VALUE'],
                    'FIO_KTO_PEREDAET' =>  $arr_prop['FIO_KTO_PEREDAET']['VALUE'],
                    'FIO' =>  $arr_prop['FIO']['VALUE'],
                    'OBORUDOVANIE' => $arr_prop['OBORUDOVANIE']['VALUE'],
                    'KOMMENTARIY' => $arr_prop['KOMMENTARIY']['VALUE'],
                    'DOKUMENT' => $arr_prop['DOKUMENT']['VALUE'],
                    'DOKUMENT_PODPISANNYY_MOL_OM' => $arr_prop['DOKUMENT_PODPISANNYY_MOL_OM']['VALUE'],
                    'DOKUMENT_PODPISANNYY_MOL_I_PEREDAYUSHCHIM' => $arr_prop['DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD']['VALUE'],
                    'DOKUMENT_PODPISANNYY_VSEMI_VKLYUCHAYA_POLUCHATELYA' => $arr_prop['DOKUMENT_PODPISANNYY_VSEMI']['VALUE'],
                    'STATUS' => $status,
                    'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
                    // 'DATA_IZMENENIYA' => $date,
                )
            );
            $id = $rsIB->Add($arFieldsOld);

            $log .= date("Y.m.d G:i:s") . "\n";
            $log .= print_r($archive_transports , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($arFields , 1);
            $log .= "\n------------------------\n";
            $log .= print_r($name , 1);
            $log .= "\n------------------------\n";
            // $log .= print_r($rsIB->LAST_ERROR , 1);
            // $log .= "\n------------------------\n";
 
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        } 
		
    }
}




function LMSReminderAgent()
{
	$arrFilter = [];
	$arrFilter["IBLOCK_ID"] = 70;
	$arrFilter["PROPERTY_ASSIGN_REMIND_START"] = 1;
	$date1 = new DateTime();        
	$strDate = $date1->format("Y-m-d");
	$arrFilter["=PROPERTY_ASSIGN_START_DATE"] = $strDate;
	$assingDB = CIBlockElement::GetList(array(), $arrFilter, false,false, array('ID', 'PROPERTY_ASSIGN_STUDENT'));
	
	$SITE_ID = 's1';
	$EVENT_TYPE = 'LMS_ASSIGN_REMIND';
        
	while ($assign = $assingDB->Fetch()) {
		$studID = $assign["PROPERTY_ASSIGN_STUDENT_VALUE"];
		if ($studID){                        
			$userDB = CUSER::getByID($studID);
			if ($user = $userDB->Fetch()) {
				$studEmail = $user["EMAIL"];
				if ($studEmail != '') {
					$arFeedForm = array(
						"ASSIGNTO" => $studEmail
					);
					$result = CEvent::Send($EVENT_TYPE, $SITE_ID, $arFeedForm );
					writeToLogLMS($user["EMAIL"], $result);
				}
			}
		}
	}        
	return "LMSReminderAgent();";
}

function writeToLogLMS($data, $title = '') 
{
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= $title. "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/lms.log', $log, FILE_APPEND);
    return true;
} 

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("equipment", "OnAfterIBlockElementAddHandler"));

class equipment
{
    // создаем обработчик события "OnBeforeIBlockElementAdd"
    static function OnAfterIBlockElementAddHandler(&$arFields)
    {

		// if($arFields["IBLOCK_ID"] == 85)
        // {

			

        //     $rsElement = CIBlockElement::GetList(
        //         $arOrder  = array("SORT" => "ASC"),
        //         $arFilter = array(
        //             "ACTIVE"    => "Y",
        //             "ID" => $arFields['ID']
        //         ),
        //         false,
        //         false,
        //         $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
        //     );
        //     while($arElement = $rsElement->GetNextElement()) {
        //         // $archive_transports =  $arElement;
        //         $archive_transports = $arElement->GetFields();
        //         $archive_transports["PROPERTIES"] = $arElement->GetProperties();
        //     }
            
        //     $arr_prop = array();
        //         foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
        //             $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
        //             if($ar_props = $db_props->Fetch()){
        //                 $arr_prop[$ar_props['CODE']] = $ar_props;
        //             }
                    
        //        }
		// 	   if(is_array($arr_prop['UF_FILE_ID']['VALUE'])){
		// 			foreach ($arr_prop['UF_FILE_ID']['VALUE'] as $key => $fileId) {
		// 				$files[] = $fileId['VALUE'];
		// 		}
		// 	   }else{
		// 			$files[] = $arr_prop['UF_FILE_ID']['VALUE'];
		// 	   }

		// 	   if(is_array($arr_prop['UF_FOLDER_ID']['VALUE'])){
		// 		foreach ($arr_prop['UF_FOLDER_ID']['VALUE'] as $key => $fileId) {
		// 			$folders[] = $fileId['VALUE'];
		// 			}
		// 		}else{
		// 				$folders[] = $arr_prop['UF_FOLDER_ID']['VALUE'];
		// 		}

		// 		if(is_array($arr_prop['UF_FILE_PATH']['VALUE'])){
		// 			foreach ($arr_prop['UF_FILE_PATH']['VALUE'] as $key => $fileId) {
		// 				$filesPath[] = $fileId['VALUE'];
		// 				}
		// 			}else{
		// 					$filesPath[] = $arr_prop['UF_FILE_PATH']['VALUE'];
		// 			}

		// 		if(is_array($arr_prop['UF_FOLDER_PATH']['VALUE'])){
		// 			foreach ($arr_prop['UF_FOLDER_PATH']['VALUE'] as $key => $fileId) {
		// 				$foldersPath[] = $fileId['VALUE'];
		// 				}
		// 			}else{
		// 					$foldersPath[] = $arr_prop['UF_FOLDER_PATH']['VALUE'];
		// 			}
           
          

        //     if($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'СИ'){
        //         $type = 308;
        //     }elseif($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'испытат.' || $archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'Испытат.' ){
        //         $type =309;
        //     }elseif($archive_transports['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'Вспом.'){
        //         $type =310;
        //     }else{
        //         $type = '';
        //     } 
    
    
        //     if(strtolower($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) == 'не требуется'){
        //         $archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] = 312;
        //     }elseif(strtolower($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) == 'требуется' || !empty($archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) && $archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] != 'не требуется'){
        //         $archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] = 311;
        //     }
    
        //     if(strtolower($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE']) == 'не требуется'){
        //         $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 314;
        //     }elseif(strtolower($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE']) == 'требуется' || !empty($archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE']) && $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'] != 'не требуется'){
        //         $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 313;
        //     }
    
        //     if(strtolower($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE']) == 'не требуется'){
        //         $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'] = 316;
        //     }elseif(strtolower($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE']) == 'требуется' || !empty($archive_transports['PROPERTIES']['KALIBROVKA']['VALUE']) && $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'] != 'не требуется'){
        //         $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'] = 315;
        //     }

        //     if($arr_prop['NEOBKHODIMOST_POVERKI']['VALUE'] == 'не требуется'){
        //         $poverka = 303;
        //     }
        //     if($arr_prop['NEOBKHODIMOST_POVERKI']['VALUE'] == 'требуется'){
        //         $poverka = 302;
        //     }
        //     if($arr_prop['KALIBROVKA']['VALUE'] == 'не требуется'){
        //         $atestas = 307;
        //     }
        //     if($arr_prop['KALIBROVKA']['VALUE'] == 'требуется'){
        //         $atestas = 306;
        //     }
        //     if($arr_prop['ATTESTATSIYA']['VALUE'] == 'не требуется'){
        //         $kalibrovka = 305;
        //     }
        //     if($arr_prop['ATTESTATSIYA']['VALUE'] == 'требуется'){
        //         $kalibrovka = 304;
        //     }

        //     if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 'СИ'){
        //         $type_new = 299;
        //     }
        //     if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 'испытат.' || $arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 'Испытат.' ){
        //         $type_new = 300;
        //     }
        //     if($arr_prop['TIP_OBORUDOVANIYA']['VALUE'] == 'Вспом.'){
        //         $type_new = 301;
        //     }

        //     $name = 'Изменен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
        //     $rsIB = new CIBlockElement;
        //     $arFieldsOld = array(
        //         "ACTIVE"    => "Y",
        //         "NAME" => $name,
        //         "IBLOCK_ID" => 94,
        //         "PROPERTY_VALUES"   => array(
        //             'INVENTARNYY_NOMER_STARYY' => $archive_transports['PROPERTIES']['INVENTARNYY_NOMER']['VALUE'],
        //             'ZAVODSKOY_NOMER_STARYY' => $archive_transports['PROPERTIES']['ZAVODSKOY_NOMER']['VALUE'],
        //             'DATA_POSTANOVKI_NA_UCHET_STARYY' => $archive_transports['PROPERTIES']['DATA_POSTANOVKI_NA_UCHET']['VALUE'],
        //             // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
        //             'TERRITORIALNOE_PODRAZDELENIE_STARYY' => $archive_transports['PROPERTIES']['TERRITORIALNOE_PODRAZDELENIE']['VALUE'],
        //             'MOL_PODRAZDELENIYA_STARYY' => $archive_transports['PROPERTIES']['MOL_PODRAZDELENIYA']['VALUE'],
        //             'TIP_OBORUDOVANIYA_STARYY' => $type,
        //             'MOL_V_OTDELE_STARYY' => $archive_transports['PROPERTIES']['MOL_V_OTDELE']['VALUE'],
        //             'OTDELA_V_PODRAZDELENII_STARYY' => $archive_transports['PROPERTIES']['OTDELA_V_PODRAZDELENII']['VALUE'],
        //             'DATA_PEREMESHCHENIYA_STARYY' => $archive_transports['PROPERTIES']['DATA_PEREMESHCHENIYA']['VALUE'],
        //             'STOIMOST_BEZ_NDS_STARYY' => $archive_transports['PROPERTIES']['STOIMOST_BEZ_NDS']['VALUE'],
        //             'STATUS_OBORUDOVANIYA_STARYY' => $archive_transports['PROPERTIES']['STATUS_OBORUDOVANIYA']['VALUE'],
        //             'SOSTAVNOE_OBORUDOVANIYA_STARYY' => $archive_transports['PROPERTIES']['SOSTAVNOE_OBORUDOVANIYA']['VALUE'],
        //             'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE'],
        //             'NEOBKHODIMOST_POVERKI_STARYY' => $archive_transports['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'],
        //             'PEREODICHNOST_POVERKI_STARYY' => $archive_transports['PROPERTIES']['PEREODICHNOST_POVERKI']['VALUE'],
        //             'DATA_POVERKI_STARYY' => $archive_transports['PROPERTIES']['DATA_POVERKI']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_POVERKI_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_POVERKI']['VALUE'],
        //             'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE']['TEXT'],
        //             'ATTESTATSIYA_STARYY' => $archive_transports['PROPERTIES']['ATTESTATSIYA']['VALUE'],
        //             'PERIODICHNOST_ATTESTAT_STARYY' => $archive_transports['PROPERTIES']['PERIODICHNOST_ATTESTAT']['VALUE'],
        //             'DATA_ATTESTATSII_STARYY' => $archive_transports['PROPERTIES']['DATA_ATTESTATSII']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_ATTESTATSII_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_ATTESTATSII']['VALUE'],
        //             'KALIBROVKA_STARYY' => $archive_transports['PROPERTIES']['KALIBROVKA']['VALUE'],
        //             'PEREODICHNOST_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['PEREODICHNOST_KALIBROVKI']['VALUE'],
        //             'DATA_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['DATA_KALIBROVKI']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_KALIBROVKI_STARYY' => $archive_transports['PROPERTIES']['DATA_SLEDUYUSHCHEY_KALIBROVKI']['VALUE'],
        //             'OS_TMTS_STARYY' => $archive_transports['PROPERTIES']['OS_TMTS']['VALUE'],
        //             'VN_NOMER_STARYY' => $archive_transports['PROPERTIES']['VN_NOMER']['VALUE'],
        //             'UF_FILE_ID_STARYY' => $archive_transports['PROPERTIES']['UF_FILE_ID']['VALUE'],
        //             'UF_FOLDER_ID_STARYY' => $archive_transports['PROPERTIES']['UF_FOLDER_ID']['VALUE'],
        //             'UF_FILE_PATH_STARYY' => $archive_transports['PROPERTIES']['UF_FILE_PATH']['VALUE'],
        //             'UF_FOLDER_PATH_STARYY' => $archive_transports['PROPERTIES']['UF_FOLDER_PATH']['VALUE'],
        //             'INVENTARNYY_NOMER' => $arr_prop['INVENTARNYY_NOMER']['VALUE'],
        //             'ZAVODSKOY_NOMER' => $arr_prop['ZAVODSKOY_NOMER']['VALUE'],
        //             'DATA_POSTANOVKI_NA_UCHET' => $arr_prop['DATA_POSTANOVKI_NA_UCHET']['VALUE'],
        //             // 'DATA_POSTANOVKI_NA_UCHET_STARYY' => Array("VALUE" => $ENUM_ID ),
        //             'TERRITORIALNOE_PODRAZDELENIE' => $arr_prop['TERRITORIALNOE_PODRAZDELENIE']['VALUE'],
        //             'MOL_PODRAZDELENIYA' => $arr_prop['MOL_PODRAZDELENIYA']['VALUE'],
        //             'TIP_OBORUDOVANIYA' => $type_new,
        //             'MOL_V_OTDELE' => $arr_prop['MOL_V_OTDELE']['VALUE'],
        //             'OTDELA_V_PODRAZDELENII' => $arr_prop['OTDELA_V_PODRAZDELENII']['VALUE'],
        //             'DATA_PEREMESHCHENIYA' => $arr_prop['DATA_PEREMESHCHENIYA']['VALUE'],
        //             'STOIMOST_BEZ_NDS' => $arr_prop['STOIMOST_BEZ_NDS']['VALUE'],
        //             'STATUS_OBORUDOVANIYA' => $arr_prop['STATUS_OBORUDOVANIYA']['VALUE'],
        //             'SOSTAVNOE_OBORUDOVANIYA' => $arr_prop['SOSTAVNOE_OBORUDOVANIYA']['VALUE'],
        //             'KOMMENTARIY' => $arr_prop['KOMMENTARIY']['VALUE'],
        //             'NEOBKHODIMOST_POVERKI' => $poverka,
        //             'PEREODICHNOST_POVERKI' => $arr_prop['PEREODICHNOST_POVERKI']['VALUE'],
        //             'DATA_POVERKI' => $arr_prop['DATA_POVERKI']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_POVERKI' => $arr_prop['DATA_SLEDUYUSHCHEY_POVERKI']['VALUE'],
        //             'ATTESTATSIYA' => $atestas,
        //             'PERIODICHNOST_ATTESTAT' => $arr_prop['PERIODICHNOST_ATTESTAT']['VALUE'],
        //             'DATA_ATTESTATSII' => $arr_prop['DATA_ATTESTATSII']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_ATTESTATSII' => $arr_prop['DATA_SLEDUYUSHCHEY_ATTESTATSII']['VALUE'],
        //             'KALIBROVKA' => $kalibrovka,
        //             'PEREODICHNOST_KALIBROVKI' => $arr_prop['PEREODICHNOST_KALIBROVKI']['VALUE'],
        //             'DATA_KALIBROVKI' => $arr_prop['DATA_KALIBROVKI']['VALUE'],
        //             'DATA_SLEDUYUSHCHEY_KALIBROVKI' => $arr_prop['DATA_SLEDUYUSHCHEY_KALIBROVKI']['VALUE'],
        //             'OS_TMTS' => $arr_prop['OS_TMTS']['VALUE'],
        //             'VN_NOMER' => $arr_prop['VN_NOMER']['VALUE'],
        //             'UF_FILE_ID' => $files,
        //             'UF_FOLDER_ID' => $folders,
        //             'UF_FILE_PATH' => $filesPath,
        //             'UF_FOLDER_PATH' => $foldersPath,
        //             'KTO_IZMENIL' => $arFields['MODIFIED_BY'],
        //         )
        //     );
        //     $id = $rsIB->Add($arFieldsOld);

        //     $log .= date("Y.m.d G:i:s") . "\n";
        //     $log .= print_r($archive_transports , 1);
        //     $log .= "\n------------------------\n";
        //     $log .= print_r($arFields , 1);
        //     $log .= "\n------------------------\n";
        //     $log .= print_r($name , 1);
        //     $log .= "\n------------------------\n";
        //     $log .= print_r($arr_prop , 1);
        //     $log .= "\n------------------------\n";
 
        //     file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result.log', $log, FILE_APPEND);
        // } 

        if($arFields["IBLOCK_ID"] == 51)
        {
            // $rsElement = CIBlockElement::GetList(
            //     $arOrder  = array("SORT" => "ASC"),
            //     $arFilter = array(
            //         "ACTIVE"    => "Y",
            //         "ID" => $arFields['ID']
            //     ),
            //     false,
            //     false,
            //     $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL", "CODE", "PROPERTY_*")
            // );
            // while($arElement = $rsElement->GetNextElement()) {
            //     // $archive_transports =  $arElement;
            //     $archive_transports = $arElement->GetFields();
            //     $archive_transports["PROPERTIES"] = $arElement->GetProperties();
            // }

            // $name = 'Добавлен элемент: ' .$arFields['NAME']. ' в '. date("G:i:s d.m.Y");
            // $date = ' в ' . date("G:i:s d.m.Y");
            // $rsIB = new CIBlockElement;
            // $arFieldsOld = array(
            //     "ACTIVE"    => "Y",
            //     "NAME" => $name,
            //     "IBLOCK_ID" => 56,
            //     "PROPERTY_VALUES"   => array(
            //         'DATA_PEREMESHCHENIYA_STARYY' => $archive_transports['PROPERTIES']['DATA_PEREMESHCHENIYA']['VALUE'],
            //         'MOL_STARYY' => $archive_transports['PROPERTIES']['MOL']['VALUE'],
            //         'FIO_KTO_PEREDAET_STARYY' => $archive_transports['PROPERTIES']['FIO_KTO_PEREDAET']['VALUE'],
            //         'FIO_STARYY' => $archive_transports['PROPERTIES']['FIO']['VALUE'],
            //         'OBORUDOVANIE_STARYY' => $archive_transports['PROPERTIES']['OBORUDOVANIE']['VALUE'],
            //         'KOMMENTARIY_STARYY' => $archive_transports['PROPERTIES']['KOMMENTARIY']['VALUE']['TEXT'],
            //         'DOKUMENT_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT']['VALUE'],
                    
            //         'DOKUMENT_PODPISANNYY_MOL_OM_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_PODPISANNYY_MOL_OM']['VALUE'],
            //         'DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD']['VALUE'],
            //         'DOKUMENT_PODPISANNYY_VSEMI_STARYY' => $archive_transports['PROPERTIES']['DOKUMENT_PODPISANNYY_VSEMI']['VALUE'],
            //         'STATUS_STARYY' => $srok,

            //         'DATA_PEREMESHCHENIYA' => $arFields['PROPERTY_VALUES'][218][365975]['VALUE'],
            //         'MOL' => $arFields['PROPERTY_VALUES'][312][0],
            //         'FIO_KTO_PEREDAET' =>  $arFields['PROPERTY_VALUES'][241][0],
            //         'FIO' =>  $arFields['PROPERTY_VALUES'][219][0],
            //         'OBORUDOVANIE' => $arFields['PROPERTY_VALUES'][220][0],
            //         'KOMMENTARIY' => $arFields['PROPERTY_VALUES'][275][366599]['VALUE']['TEXT'],
            //         'DOKUMENT' => $arFields['PROPERTY_VALUES'][311][365984]['VALUE'],
            //         'DOKUMENT_PODPISANNYY_MOL_OM' => $arFields['PROPERTY_VALUES'][314]['n0']['VALUE'],
            //         'DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' => $arFields['PROPERTY_VALUES'][315]['n0']['VALUE'],
            //         'DOKUMENT_PODPISANNYY_VSEMI' => $arFields['PROPERTY_VALUES'][316]['n0']['VALUE'],
            //         'STATUS' => $srok_new,
            //         'KTO_IZMENIL' => $arFields['CREATE_BY'],
            //         // 'DATA_IZMENENIYA' => $date,
            //     )
            // );
            // $id = $rsIB->Add($arFieldsOld);

        }


        if($arFields["IBLOCK_ID"] == 87)
        {
			$arr_prop = array();
                foreach ($arFields['PROPERTY_VALUES'] as $key => $field) {
                    $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("ID"=>$key));
                    if($ar_props = $db_props->Fetch()){
                        $arr_prop[$ar_props['CODE']] = $ar_props;
                    }
                    
               }
            if(is_array($arr_prop['OBORUDOVANIE']['VALUE'])){
				foreach ($arr_prop['OBORUDOVANIE']['VALUE'] as $key => $value) {
					CIBlockElement::SetPropertyValuesEx($value, false, array('DATA_PEREMESHCHENIYA' => $arr_prop['DATA_PEREMESHCHENIYA']['VALUE']));
					// CIBlockElement::SetPropertyValuesEx($value, false, array('STATUS_OBORUDOVANIYA' => 93));
					CIBlockElement::SetPropertyValuesEx($value, false, array('MOL_V_OTDELE' => $arr_prop['FIO']['VALUE']));
				}
			}else{
				CIBlockElement::SetPropertyValuesEx($arr_prop['OBORUDOVANIE']['VALUE'], false, array('DATA_PEREMESHCHENIYA' => $arr_prop['DATA_PEREMESHCHENIYA']['VALUE']));
					// CIBlockElement::SetPropertyValuesEx($value, false, array('STATUS_OBORUDOVANIYA' => 93));
				CIBlockElement::SetPropertyValuesEx($arr_prop['OBORUDOVANIE']['VALUE'], false, array('MOL_V_OTDELE' => $arr_prop['FIO']['VALUE']));
			}
           

            $rsUser = CUser::GetByID($arr_prop['FIO']['VALUE']);
            $arUser_fio = $rsUser->Fetch();

            $rsUser = CUser::GetByID($arr_prop['MOL']['VALUE']);
            $arUser_mol = $rsUser->Fetch();
            if($arr_prop['MOL']['VALUE'] != $arr_prop['FIO_KTO_PEREDAET']['VALUE']){
                $rsUser = CUser::GetByID($arr_prop['FIO_KTO_PEREDAET']['VALUE']);
                $arUser_fiz_vlad = $rsUser->Fetch();

                $outputVar = '<tr>
                <td style="text-align:center;border-bottom: 1px solid #000;vertical-align:bottom;">'.$arUser_fiz_vlad['PERSONAL_PAGER'].'</td>
                <td style="text-align:center;border-bottom: 1px solid #000;vertical-align:bottom;">'.$arUser_fiz_vlad['WORK_POSITION'].'</td>
                </tr>
                <tr>
                    <td style="text-align:center">ФИО</td>
                    <td style="text-align:center">Должность</td>
                </tr>';


            }

           

            

            $rsElement = CIBlockElement::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "ID" => $arr_prop['OBORUDOVANIE']['VALUE'],
                ),
                false,
                false,
                $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_INVENTARNYY_NOMER", "PROPERTY_ZAVODSKOY_NOMER")
            );
            while($arElement = $rsElement->fetch()) {
                $elemets[] = $arElement;
            }

            include($_SERVER["DOCUMENT_ROOT"]."/tcpdf/tcpdf.php");

            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            // Устанавливаем моноширинный шрифт по умолчанию
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            // Устанавливаем автоматические разрывы страниц
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            // Устанавливаем шрифт
            $pdf->SetFont('dejavusans', '', 10, '', true);
            // Добавляем страницу
            $pdf->AddPage();
            $pdf->setCellHeightRatio(1.5);
// Создаем новый PDF документ
$accounts = array('name'=>'angle');//assumed array
$k=0;
//print_r($accounts);
$txt = <<<EOD
<style>
table.bordered, th.bordered, td.bordered {
    border: 1px solid black;
  }
</style>
<h3 style="width: 30%; text-align: center;"> "АКТ
        Приема-передачи оборудования
        (ООО ""Автодор-Инжиниринг"")"
</h3> 
<p></p>
<table>
    <tr>
        <td style="border-bottom: 1px solid #000;">{$arr_prop['DATA_PEREMESHCHENIYA']['VALUE']}</td>
    </tr>
    <tr>
        <td>Дата передачи</td>
    </tr>
</table>
<p>Мы, нижеподписавшиеся:</p>
<table>
    <tr>
        <td style="text-align:center; border-bottom: 1px solid #000;">{$arUser_mol['PERSONAL_PAGER']}</td>
        <td style="text-align:center;border-bottom: 1px solid #000;">{$arUser_mol['WORK_POSITION']}</td>
    </tr>
    <tr>
        <td style="text-align:center">ФИО</td>
        <td style="text-align:center">Должность</td>
    </tr>

        {$outputVar}
        <tr>
            <td style="text-align:center; border-bottom: 1px solid #000;">{$arUser_fio['PERSONAL_PAGER']}</td>
            <td style="text-align:center;border-bottom: 1px solid #000;">{$arUser_fio['WORK_POSITION']}</td>
        </tr>
        <tr>
            <td style="text-align:center">ФИО</td>
            <td style="text-align:center">Должность</td>
        </tr>
</table>
<p>Составили настоящий Акт о нижеследующем:</p>
<table>
    <tr>
        <td style="border-bottom: 1px solid #000;"></td>
    </tr>
    <tr>
        <td>ФИО</td>
    </tr>
</table>
<p>принял следующее оборудование в исправном внешнем и техническом состоянии:</p>
<table class="bordered">

    <tr style="vertical-align:center;">
        <th colspan="1" class="bordered">№<br>п/п</th>
        <th colspan="3" class="bordered">"Заводской/
        серийный №"</th>
        <th colspan="3" class="bordered">Инвентарный №</th>
        <th colspan="5"  class="bordered">Наименование, характеристики</th>
        <th colspan="2" class="bordered">Комплектация</th>
        <th colspan="1" class="bordered">Ед. изм.</th>
        <th colspan="1" class="bordered">Кол-во</th>
    </tr>
EOD;
foreach($elemets as $key=>$element){
    $k++;
$txt.=<<<EOD
<tr style="vertical-align:middle;">
       <td  colspan="1" class="bordered" style="text-align:center; border-bottom: 1px solid #000;line-height: 4;">{$k}</td>
       <td colspan="3" class="bordered" style="text-align:center;border-bottom: 1px solid #000;">{$element['PROPERTY_ZAVODSKOY_NOMER_VALUE']}</td>
        <td colspan="3" class="bordered" style="text-align:center;border-bottom: 1px solid #000;">{$element['PROPERTY_INVENTARNYY_NOMER_VALUE']}</td>
       <td colspan="5" class="bordered" style="text-align:center;border-bottom: 1px solid #000;">{$element['NAME']}</td>
        <td colspan="2" class="bordered" style="text-align:center;border-bottom: 1px solid #000;"></td>
    <td colspan="1" class="bordered" style="text-align:center;border-bottom: 1px solid #000;">шт. </td>
       <td colspan="1" class="bordered" style="text-align:center;border-bottom: 1px solid #000;">1</td>
 </tr>
EOD;
 }
$txt.=<<<EOD

</table>
<p>Сторона, принявшая вышеуказанное оборудование по данному акту, обязуется нести полную материальную ответственность в случаях утраты, кражи, различного рода повреждений и иных обстоятельств, оказавших негативные последствия на его техническое состояние и комплектный вид.</p>
<p>Акт составлен в 4 экз. на 1 л.</p>
<p>МОЛ:</p>
<table>
    <tr style="border-bottom: 1px solid #000;">
        <td style="text-align:center;border-bottom: 1px solid #000;"></td>
        <td style="text-align:center;border-bottom: 1px solid #000;">{$arUser_mol['PERSONAL_PAGER']}</td>
    </tr>
    <tr>
        <td style="text-align:center">Подпись</td>
        <td style="text-align:center">ФИО</td>
    </tr>
</table>
<p>Передал:</p>
<table>
    <tr style="border-bottom: 1px solid #000;">
        <td style="text-align:center;border-bottom: 1px solid #000;"></td>
        <td style="text-align:center;border-bottom: 1px solid #000;">{$arUser_fiz_vlad['PERSONAL_PAGER']}</td>
    </tr>
    <tr>
        <td style="text-align:center">Подпись</td>
        <td style="text-align:center">ФИО</td>
    </tr>
</table>
<p>Принял:</p>
<table>
    <tr style="border-bottom: 1px solid #000;">
        <td style="text-align:center;border-bottom: 1px solid #000;"></td>
        <td style="text-align:center;border-bottom: 1px solid #000;">{$arUser_fio['PERSONAL_PAGER']}</td>
    </tr>
    <tr>
        <td style="text-align:center">Подпись</td>
        <td style="text-align:center">ФИО</td>
    </tr>
</table>
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $txt, 0, 1, 0, true, '', true);
// Закрываем и выводим PDF документ
$pdf->Output($_SERVER["DOCUMENT_ROOT"].'Акт ПП '.$element['ID'].'.pdf' , 'F');

$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'Акт ПП '.$element['ID'].'.pdf');


CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array('DOKUMENT' => array("VALUE"=>$arFile,"DESCRIPTION"=>'dsasadasd')));


                        
        
          
    $log .= date("Y.m.d G:i:s") . "\n";
           $log .= print_r($arFields , 1);
           $log .= "\n------------------------\n";
           $log .= print_r($arr_prop , 1);
           $log .= "\n------------------------\n";

           file_put_contents( $_SERVER["DOCUMENT_ROOT"].'/result_eq.log', $log, FILE_APPEND);
        }
    }
}
?>