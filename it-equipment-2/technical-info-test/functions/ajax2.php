<? global $USER;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?>

<?
require './functions.php';
global $iblockID, $arElement, $arFields, $add, $all, $arrAddElement, $keyXML_ID, $keyParent, $arrResultXML, $arrResult;
$iblockID = $_POST['iblockID'];
$arElement = json_decode($_POST['file']);
$arFields = $_POST['fields'];

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

//Получение всех свойств инфоблока
$propertiesDB = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID));
while ($property = $propertiesDB->GetNext()) {
    $properties[$property['ID']] = [
        "CODE" => $property['CODE'],
        "NAME" => $property['NAME'],
        "PROPERTY_TYPE" => $property['PROPERTY_TYPE'],
        "USER_TYPE" => $property['USER_TYPE'],
    ];
}

//Получение массива всех пользователей
function searchUser($surname) {
    global $USER;
    $usersDB = CUser::GetList(
        ($by="id"),
        ($order="desc"),
        array("ACTIVE" => "Y", "GROUP_ID" => [3], 'LAST_NAME' => $surname),
        array("SELECT" => array("ID", "UF_DEPARTMENT", "LOGIN", "NAME", "LAST_NAME", "SECOND_NAME"))
    );
    $users = [];
    while ($user = $usersDB->Fetch()) {
        $arSelect = Array("ID", "NAME", "CODE");
        $arFilter = Array("ACTIVE"=>"Y", "PROPERTY_USER" => $user['ID']);
        $iblockDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        while($element = $iblockDB->GetNextElement()) {
            $arFieldsElement = $element->GetFields();
            if (!empty($arFieldsElement)) {
                $result[$user['ID']] = [
                    "ID" => $user['ID'],
                    "NAME" => $user['NAME'],
                    "SECOND_NAME" => $user['SECOND_NAME'],
                    "LOGIN" => $user['LOGIN'],
                    "UF_DEPARTMENT" => $user['UF_DEPARTMENT'],
                ];
                /*$surnameUser = mb_strtolower($user['LAST_NAME'], 'UTF-8');
                $users[$surnameUser][$user['ID']] = [
                    "ID" => $user['ID'],
                    "NAME" => $user['NAME'],
                    "SECOND_NAME" => $user['SECOND_NAME'],
                    "LOGIN" => $user['LOGIN'],
                    "UF_DEPARTMENT" => $user['UF_DEPARTMENT'],
                ];*/
            }
        }


    }
    return $result;
}
$usersDB = CUser::GetByLogin('technical');
while ($user = $usersDB->Fetch()) {
    $technicalUser = $user["ID"];
}

$arrResult = [];
$arrResultXML = [];
$add = 0;
$all = 0;
$arrAddElement = [];
$error = (object) [];
$keyXML_ID = array_search('xml_id', $arFields);

//    if ($keyXML_ID === false) {
//        $error->error1 = 'Выбирете столбец с внешним кодом<br>';
//    }
//    if ($keyParent === false) {
//        $error->error2 = 'Выбирете столбец с родителями<br>';
//    }


/*if (!isset($error->error1) && !isset($error->error2)) {
    $error = [];
    for ($i = 1; $i < COUNT($arElement); $i++) {
        if (!in_array($arElement[$i][$keyXML_ID], $arrAddElement)) {
            if (empty(trim($arElement[$i][$keyParent]))) {
                $level = 1;
                $tree[$level][] = $arElement[$i][$keyXML_ID];
                $tree = addTree($arElement, $arFields, $arrAddElement, $arElement[$i], $i, $keyXML_ID, $keyParent, $tree, ++$level);
            } else if (in_array($arElement[$i][$keyParent], $arrAddElement)) {

            } else {

            }
        }
    }
}

function addTree($arElement, $arFields, $arAdd, $element, $iterator, $keyID, $keyParent, $tree, $level) {

    for ($j = 1; $j < COUNT($arElement); $j++) {
        if ($iterator != $j && !in_array($arElement[$j][$keyID], $arAdd) && stringChecking($arElement[$iterator][$keyID], $arElement[$j][$keyParent])) {
            $tree[$level][] = $arElement[$j][$keyID];
            $otvet = addTree($arElement, $arFields, $arAdd, $arElement[$j], $j, $keyID, $keyParent, $tree, ++$level);


        }
    }
    return $tree[$level];
}*/
function addElement($element, $level = 1) {
    $result = [];
    global $arElement, $keyXML_ID, $keyParent, $arrResultXML, $arrResult, $all, $arrAddElement;
    $all++;

    for ($j = 1; $j < COUNT($arElement); $j++) {
        if (stringChecking($element[$keyXML_ID], $arElement[$j][$keyParent]) && !in_array($arElement[$j][$keyXML_ID], $arrAddElement)) {
            if (array_key_exists($level, $arrResultXML)) {
                if (!in_array($arElement[$j][$keyXML_ID], $arrResultXML[$level])) {
                    $arrResultXML[$level][] = $arElement[$j][$keyXML_ID];
                    $arrResult[$level][] = $arElement[$j];
                }
            } else {
                $arrResultXML[$level][] = $arElement[$j][$keyXML_ID];
                $arrResult[$level][] = $arElement[$j];
            }
            $arrAddElement[] = $arElement[$j][$keyXML_ID];
            addElement($arElement[$j], $level+1);
        }
    }

    return true;
}

if (!isset($error->error1) && !isset($error->error2)) {
    $error = [];
    $arPropertyEnums = [];
    $arEqp = [];
    $users = [];
    foreach ($arElement as $keyArElement => $element) {
        if ($keyArElement != 0) {
            $element[] = '';
            $lastField = count($element) - 1;
            foreach ($element as $keyField => $fieldElement) {
                $fieldElement = preg_replace('/\s/', ' ', $fieldElement);
                $fieldElement = trim($fieldElement);
                if (array_key_exists($arFields[$keyField], $properties)) {
                    $property = $properties[$arFields[$keyField]];

                    if ($property['PROPERTY_TYPE'] == 'S' && $property['USER_TYPE'] == 'Date') {

                        if (!empty($fieldElement)) {
                            $arr = ParseDateTime($fieldElement, "DD.MM.YYYY");

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
                            $new_date = $arr["DD"] . '.' . $arr['MM'] . '.' . $arr['YYYY'];
                            $dateFormat = FormatDate("d.m.Y", MakeTimeStamp($new_date));
                            $fieldElement = $dateFormat;

                        }
                    } else if ($property['PROPERTY_TYPE'] == 'S' && $property['USER_TYPE'] == 'employee') {
                        $user = $fieldElement;
                        if (!empty($user)) {
                            $fio = explode(' ', $user);
                            $surname = $fio[0];


                            $surname = mb_strtolower($surname, 'UTF-8');
                            if (array_key_exists($surname, $users)) {
                                $userItem = $users[$surname];
                            } else {
                                $userQuery = searchUser($surname);
                                if (!empty($userQuery)) {
                                    $users[$surname] = $userQuery;

                                    $userItem = $users[$surname];
                                }
                            }

                            if (!empty($userItem)) {
                                foreach ($userItem as $item) {
                                    $name = $item['NAME'];
                                    $secondName = $item['SECOND_NAME'];
                                    $department = $item['UF_DEPARTMENT'][0];
                                    $nameInitials = explode('.', trim($fio[1]))[0];
                                    $secondNameInitials = explode('.', trim($fio[1]))[1];
                                    if ((mb_substr($name, 0, 1) === $nameInitials || empty($nameInitials)) && (mb_substr($secondName, 0, 1) === $secondNameInitials || empty($secondName) || empty($secondNameInitials)) && !empty($department)) {
                                        $fieldElement = $item['ID'];
                                    }
                                }
                            }
                            if (!is_numeric($fieldElement)) {
                                $fieldElement = $technicalUser;

                                if ($arFields[$keyField] == "555") {
                                    $error[$element[$keyXML_ID]] = 'Не найден МОЛ: ' . $user . ' (Добавлен в примечание)';
                                    $element[$lastField] .= 'Мол: ' . $user;
                                } else if ($arFields[$keyField] == "557") {
                                    $error[$element[$keyXML_ID]] = 'Не найден Фактический владеющий: ' . $user . '(Добавлен в примечание)';
                                    $element[$lastField] .= 'Фактический владеющий: ' . $user;
                                }
                            }
                        }
                    } else if ($property['PROPERTY_TYPE'] == 'L') {
                        if (!array_key_exists($property['CODE'], $arPropertyEnums)) {
                            $propertyEnums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => $iblockID, "CODE" => $property['CODE']));
                            while ($enumFields = $propertyEnums->GetNext()) {
                                $arPropertyEnums[$property['CODE']][$enumFields['VALUE']] = $enumFields['ID'];
                            }
                        }

                        if (stringChecking($property['CODE'], "ATTESTATSIYA") || stringChecking($property['CODE'], "KALIBROVKA") || stringChecking($property['CODE'], "NEOBKHODIMOST_POVERKI")) {
                            foreach ($arPropertyEnums[$property['CODE']] as $propertyKey => $propertyItem) {
                                if (empty(trim($fieldElement))) {
                                    if (stringChecking($propertyKey, "Не требуется")) {
                                        $fieldElement = $propertyItem;
                                    }
                                } else {
                                    if (stringChecking($propertyKey, $fieldElement)) {
                                        $fieldElement = $propertyItem;
                                    }
                                }
                            }
                        } else {

                            foreach ($arPropertyEnums[$property['CODE']] as $propertyKey => $propertyItem) {
                                if (stringChecking($propertyKey, $fieldElement)) {
                                    $fieldElement = $propertyItem;
                                }
                            }
                        }


                    }
                }
                if ($keyField != $lastField) {
                    $element[$keyField] = $fieldElement;
                }


            }

            $arElement[$keyArElement] = $element;
        }
    }

    for ($i = 1; $i < COUNT($arElement); $i++) {
        if (!in_array($arElement[$i], $arrAddElement)) {
            $arrResult[0][] = $arElement[$i];
            $arrResultXML[0][] = $arElement[$i];
            $arrAddElement[] = $arElement[$i];
            addElement($arElement[$i]);
//                if (empty(trim($arElement[$i][$keyParent]))) {
//                    $arrResult[0][] = $arElement[$i];
//                    $arrResultXML[0][] = $arElement[$i][$keyXML_ID];
//                    $arrAddElement[] = $arElement[$i][$keyXML_ID];
//                    addElement($arElement[$i]);
//                } else if (!in_array($arElement[$i][$keyParent], $arrAddElement)) {
//                    if (!array_key_exists($arElement[$i][$keyParent], $arEqp)) {
//                        $arSelect = Array("ID", "XML_ID");
//                        $arFilter = Array("IBLOCK_ID"=>$iblockID, "XML_ID" => $arElement[$i][$keyParent], "ACTIVE"=>"Y");
//                        $elementDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
//                        while($element = $elementDB->GetNextElement()) {
//                            $element = $element->GetFields();
//                            $arEqp[$element['XML_ID']] = $element['ID'];
//                        }
//                    }
//
//                    if (!empty($arEqp[$arElement[$i][$keyParent]])) {
//                        $arrResult[0][] = $arElement[$i];
//                        $arrResultXML[0][] = $arElement[$i][$keyXML_ID];
//                        $arrAddElement[] = $arElement[$i][$keyXML_ID];
//                        addElement($arElement[$i]);
//                    }
//                }
        }
    }
    $i = 1;
    //debug($users);
    $rsParentSection = CIBlockSection::GetByID(2210);
    if ($arParentSection = $rsParentSection->GetNext()) {
        $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
        $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);

        while ($arSect = $rsSect->GetNext()) {
            // получаем подразделы
            $sections[] = $arSect['ID'];
        }
    }

    $arSelect = array();
    $arFilter = array("SECTION_ID" => $sections, "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $i = 0;
    while ($ob = $res->GetNextElement()) {
        if ($ob->GetFields()['IBLOCK_ID'] == 61) {
            $arFieldsFact[] = $ob->GetFields();
        }
    }
    $mem = 0;
    foreach ($arrResult as $arrEqp) {
        foreach ($arrEqp as $eqp) {
            $name = '';
            $note = '';
            for ($i = 0; $i < count($arFields); $i++) {
                $res = CIBlockProperty::GetByID($arFields[$i]);
                if ($ar_code = $res->GetNext()) {
                    if ($ar_code['CODE'] == 'TYPE') {
                        $arSelect = array("ID", "IBLOCK_ID", "NAME");
                        $arFilter = array("IBLOCK_ID" => 153, "ACTIVE" => "Y");
                        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);

                        $check = 0;
                        while ($ob = $res->GetNextElement()) {
                            $arFieldsType = $ob->GetFields();
                            {
                                if ($arFieldsType['NAME'] == $eqp[$i]) {
                                    $check = 1;
                                    if ($name != '') {
                                        $name = $eqp[$i] . ' ' . $name;
                                    } else {
                                        $name = $eqp[$i];
                                    }
                                    $eqp[$i] = $arFieldsType['ID'];
                                }
                            }
                        }
                        if (!$check) {
                            $el = new CIBlockElement;
                            $arLoadProductArray = array(
                                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                "IBLOCK_ID" => 153,
                                "NAME" => $eqp[$i],
                                "ACTIVE" => "Y",            // активен
                            );
                            if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                                if ($name != '') {
                                    $name = $eqp[$i] . ' ' . $name;
                                } else {
                                    $name = $eqp[$i];
                                }
                                $eqp[$i] = $PRODUCT_ID;
                            }
                        }
                    } else if ($ar_code['CODE'] == 'INVENTORY_NUMBER') {
                        if ($name != '') {
                            $name = $name . ' ' . $eqp[$i];
                        } else {
                            $name = $eqp[$i];
                        }
                    } else if ($ar_code['CODE'] == 'MOL') {
                        $checkMOL = 0;
                        foreach ($arFieldsFact as $arFieldFact) {
                            if ($arFieldFact['NAME'] == $eqp[$i]) {
                                $eqp[$i] = $arFieldFact['ID'];
                                $checkMOL = 1;
                            }
                        }
                        if (!$checkMOL) {
                            if ($note != '') {
                                $note = $note . ' МОЛ: ' . $eqp[$i];
                            } else {
                                $note = 'МОЛ: ' . $eqp[$i];
                            }
                        }
                    } else if ($ar_code['CODE'] == 'ACTUAL_USER') {
                        $checkActualUser = 0;
                        foreach ($arFieldsFact as $arFieldFact) {
                            if ($arFieldFact['NAME'] == $eqp[$i]) {
                                $eqp[$i] = $arFieldFact['ID'];
                                $checkActualUser = 1;
                            }
                        }
                        if (!$checkActualUser) {
                            if ($note != '') {
                                $note = $note . ' Фактический пользователь: ' . $eqp[$i];
                            } else {
                                $note = 'Фактический пользователь: ' . $eqp[$i];
                            }
                        }
                    } else if ($ar_code['CODE'] == 'NOTE') {
                        if ($note != '') {
                            if ($eqp[$i] != '') {
                                $eqp[$i] .= $note;
                            }
                        }
                    }
                }
                $response = iblockAdd($iblockID, $arFields, $eqp, $keyParent, $name);
                if ($response['status'] == false) {
                    $error[$i] = $response['error'];
                } else if ($response['status'] == true) {
                    $add++;
                }
                $i++;
            }
        }
    }
}
    $result = [
        "status" => true,
        "error" => $error,
        "add" => $add,
        "all" => $all,
        "element" => $arElement,
        "fields" => $arFields,
        "eqp" => $eqp,
        "mem" => $mem,
        "keyParent" => $keyParent,
        "addElementsXML" => $arrResultXML,
        "addElements" => $arrResult,
    ];
    echo json_encode($result);



    ?>