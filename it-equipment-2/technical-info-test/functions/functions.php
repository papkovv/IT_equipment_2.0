<?
    CModule::IncludeModule('iblock');
    function watch($iblock_ID, $arFields, $arValues, $key) {
        $el = new CIBlockElement;
        for ($i = 0; $i < COUNT($arFields); $i++) {
            if ($arFields[$i] != 'none') {
                if ($arFields[$i] == 'name') {
                    $name = $arValues[$i];
                } else if ($arFields[$i] == 'xml_id') {
                    $xmlID = $arValues[$i];
                    $PROP[801] = $xmlID;
                } else if (!empty(trim($arValues[$key])) && $i == $key) {
                    $arSelect = Array("ID", "XML_ID");
                    $arFilter = Array("IBLOCK_ID"=>$iblock_ID, "XML_ID" => $arValues[$key], "ACTIVE"=>"Y");
                    $elementDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
                    while($element = $elementDB->GetNextElement()) {
                        $element = $element->GetFields();
                        $elementID = $element['ID'];
                    }
                    $PROP[$arFields[$i]] = $elementID;
                } else {
                    $PROP[$arFields[$i]] = $arValues[$i];
                }
            }
        }

        return $PROP;
}

    function iblockAdd($iblock_ID, $arFields, $arValues, $key) {
        global $USER;
        $checkName = 0;
        $name = '1';
        $el = new CIBlockElement;
        for ($i = 0; $i < COUNT($arFields); $i++) {
            if ($arFields[$i] != 'none') {
                $res = CIBlockProperty::GetByID($arFields[$i]);
                if($ar_code = $res->GetNext()) {
                    if ($ar_code['CODE'] == 'TYPE' || $ar_code['CODE'] == 'INVENTORY_NUMBER') {
                        $checkName = 1;
                    }
                };
            }
        }
        for ($i = 0; $i < COUNT($arFields); $i++) {
            if ($arFields[$i] != 'none') {
                if ($arFields[$i] == 'xml_id') {
                    $xmlID = $arValues[$i];
                    $PROP[801] = $xmlID;
                } else if (!empty(trim($arValues[$key])) && $i == $key) {
                    $arSelect = Array("ID", "XML_ID");
                    $arFilter = Array("IBLOCK_ID"=>$iblock_ID, "XML_ID" => $arValues[$key], "ACTIVE"=>"Y");
                    $elementDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
                    while($element = $elementDB->GetNextElement()) {
                        $element = $element->GetFields();
                        $elementID = $element['ID'];
                    }
                    $PROP[$arFields[$i]] = $elementID;
                } else {
                    if ($checkName) {
                        $res = CIBlockProperty::GetByID($arFields[$i]);
                        if ($ar_code = $res->GetNext()) {
                            /*if ($ar_code['CODE'] == 'TYPE') {
                                $arSelect = array("ID", "IBLOCK_ID", "NAME");
                                $arFilter = array("IBLOCK_ID" => 153, "ACTIVE" => "Y");
                                $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);

                                $check = 0;
                                while ($ob = $res->GetNextElement()) {
                                    $arFieldsType = $ob->GetFields();
                                    {
                                        if ($arFieldsType['NAME'] == $arValues[$i]) {
                                            $check = 1;
                                            if ($name != '') {
                                                $name = $arValues[$i].' '.$name;
                                            } else {
                                                $name = $arValues[$i];
                                            }
                                            $PROP[$arFields[$i]] = $arFieldsType['ID'];
                                        }
                                    }
                                }
                                if (!$check) {
                                    $el = new CIBlockElement;
                                    $arLoadProductArray = array(
                                        "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                        "IBLOCK_ID" => 153,
                                        "NAME" => $arValues[$i],
                                        "ACTIVE" => "Y",            // активен
                                    );
                                    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                                        if ($name != '') {
                                            $name = $arValues[$i].' '.$name;
                                        } else {
                                            $name = $arValues[$i];
                                        }
                                        $PROP[$arFields[$i]] = $PRODUCT_ID;
                                    }
                                }
                            } else if ($ar_code['CODE'] == 'INVENTORY_NUMBER') {
                                if ($name != '') {
                                    $name = $name.' '.$arValues[$i];
                                } else {
                                    $name = $arValues[$i];
                                }
                                $PROP[$arFields[$i]] = $arValues[$i];
                            } else {*/
                                $PROP[$arFields[$i]] = $arValues[$i];
                            /*}*/
                        }
                    }
                }
            }

        }
        $arrElement = [];
        $arSelect = Array("ID", "XML_ID");
        $arFilter = Array("IBLOCK_ID"=>$iblock_ID, "XML_ID" => $xmlID, "ACTIVE"=>"Y");
        $elementDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        while($element = $elementDB->GetNextElement())
        {
            $element = $element->GetFields();
            if ($xmlID == $element['XML_ID']) {
                $arrElement = $element;
            }
        }

//        $name = 'Test';

        if (!empty($arrElement)) {
            $arLoadProductArray = Array(
                "ACTIVE"         => "Y",
                "NAME"           => $name,
                "XML_ID"         => $xmlID,
                "MODIFIED_BY"    => $GLOBALS['USER']->GetID(),
                "IBLOCK_SECTION" => false,
                "PROPERTY_VALUES"=> $PROP,
            );

            $PRODUCT_ID = $arrElement['ID'];
            if ($res = $el->Update($PRODUCT_ID, $arLoadProductArray)) {
                $result['id'] = $res;
                $result['status'] = true;
            } else {
                $result['error'] = $el->LAST_ERROR;
                $result['status'] = false;
            }
        } else {
            $arLoadProductArray = Array(
                "ACTIVE_FROM" => date('d.m.Y H:i:s'),
                "MODIFIED_BY" => $GLOBALS['USER']->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "XML_ID" => $xmlID,
                "IBLOCK_ID" => $iblock_ID,
                "NAME" => $name,
                "ACTIVE" => "Y",
                "PROPERTY_VALUES"=> $PROP,
            );

            if($newElement = $el->Add($arLoadProductArray)) {

//                $arFilter = Array("IBLOCK_ID"=>$iblock_ID, "ID"=>$newElement);
//                $res = CIBlockElement::GetList(Array(), $arFilter); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
//                if ($ob = $res->GetNextElement()){; // переходим к след элементу, если такой есть
//                    $arFields = $ob->GetFields(); // поля элемента
////    debug($arFields);
//                    $arProps = $ob->GetProperties(); // свойства элемента
////                    debug($arProps['TYPE']['VALUE']);
//                }
//
//                $arSelect = Array("ID", "NAME");
//                $arFilter = Array("IBLOCK_ID"=>153, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
//                $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
//                while($ob = $res->GetNextElement())
//                {
//                    $arFieldsType[] = $ob->GetFields();
//                }
////                debug($arFieldsType);
//
//                $flag = 0;
//                $findId = '';
//                foreach ($arFieldsType as $arField) {
//                    if ($arProps['TYPE']['VALUE'] == $arField['NAME']) {
//                        $flag = 1;
//                        $findId = $arField['ID'];
//                    }
//                }
//
//                if ($flag == 0) {
//                    $el = new CIBlockElement;
//                    $arLoadProductArray = Array(
//                        "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
//                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
//                        "IBLOCK_ID"      => 153,
//                        "NAME"           => $arProps['TYPE']['VALUE'],
//                        "ACTIVE"         => "Y",            // активен
//                    );
//                    if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
//                        CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('TYPE' => $PRODUCT_ID));
//                    }
//                } else {
//                    CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('TYPE' => $findId));
//                }
//
//                if ($arProps['ACTUAL_USER'] != '') {
//                    $sections = array();
//                    $rsParentSection = CIBlockSection::GetByID(2210);
//                    if ($arParentSection = $rsParentSection->GetNext()) {
//                        $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
//                        $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
//
//                        while ($arSect = $rsSect->GetNext()) {
//                            // получаем подразделы
//                            $sections[] = $arSect['ID'];
//                        }
//                    }
//
//                    $arSelect = array();
//                    $arFilter = array("SECTION_ID" => $sections, "ACTIVE" => "Y");
//                    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
//                    $i = 0;
//                    while ($ob = $res->GetNextElement()) {
//                        if ($ob->GetFields()['IBLOCK_ID'] == 61) {
//                            $arFieldsFact[] = $ob->GetFields();
//                        }
//                    }
////                debug($arFieldsType);
//
//                    $flag = 0;
//                    $findId = '';
//                    foreach ($arFieldsFact as $arField) {
//                        if ($arProps['ACTUAL_USER']['VALUE'] == $arField['NAME']) {
//                            $flag = 1;
//                            $findId = $arField['ID'];
//                        }
//                    }
//
//                    if ($flag == 0) {
////                        CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('ACTUAL_USER' => '', 'NOTE'=> $arProps['NOTE']['VALUE'].'Фактический пользователь: '.$arProps['ACTUAL_USER']['VALUE']));
//                    } else {
//                        CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('ACTUAL_USER' => $findId));
//                    }
//                }
////
//                if ($arProps['MOL'] != '') {
//                    $sections = array();
//                    $rsParentSection = CIBlockSection::GetByID(2210);
//                    if ($arParentSection = $rsParentSection->GetNext()) {
//                        $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
//                        $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
//
//                        while ($arSect = $rsSect->GetNext()) {
//                            // получаем подразделы
//                            $sections[] = $arSect['ID'];
//                        }
//                    }
//
//                    $arSelect = array();
//                    $arFilter = array("SECTION_ID" => $sections, "ACTIVE" => "Y");
//                    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
//                    $i = 0;
//                    while ($ob = $res->GetNextElement()) {
//                        if ($ob->GetFields()['IBLOCK_ID'] == 61) {
//                            $arFieldsMOL[] = $ob->GetFields();
//                        }
//                    }
////                debug($arFieldsType);
//
//                    $flag = 0;
//                    $findId = '';
//                    foreach ($arFieldsMOL as $arField) {
//                        if ($arProps['MOL']['VALUE'] == $arField['NAME']) {
//                            $flag = 1;
//                            $findId = $arField['ID'];
//                        }
//                    }
//
//                    if ($flag == 0) {
//                        CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('MOL' => '', 'NOTE'=> $arProps['NOTE']['VALUE'].'МОЛ: '.$arProps['MOL']['VALUE']));
//                    } else {
//                        CIBlockElement::SetPropertyValuesEx($newElement, $iblock_ID, array('MOL' => $findId));
//                    }
//                }

                $result['id'] = $newElement;
                $result['status'] = true;
            } else {
                $result['error'] = $el->LAST_ERROR;
                $result['status'] = false;
            }
        }
        return $result;
        
    }

    /*function searchPropertyValue($propertyID, $value, $IBLOCK_ID) {
        $propertyDB = CIBlockProperty::GetByID($propertyID);
        if($property = $propertyDB->GetNext()) {
            $arProperty = $property;
        }
    
        if ($arProperty["PROPERTY_TYPE"] == 'L') {
            $propertyEnums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$arProperty['CODE']));
            while($enumFields = $propertyEnums->GetNext()) {
                if (stringChecking($enumFields['VALUE'], $value)) {
                    $result = $enumFields['ID'];
                }
            }
            
        } else {
            $result = $value;
        }
        
        return $result;
    }*/


// Получение списка свойств
    function propertiesList($iblock_ID) {
        $propertiesDB = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblock_ID));
        while ($prop = $propertiesDB->GetNext()) {
            $arrPropety[] = $prop;
        }

        return $arrPropety;
    }
    

    function kama_parse_csv_file($file_path, $col_delimiter = '', $row_delimiter = '', $file_encodings = ['cp1251','UTF-8']){

        if( ! file_exists( $file_path ) ){
            return false;
        }
        $cont = trim( file_get_contents( $file_path ) );
    
        $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding( $cont, $file_encodings ) );
    
        unset( $cont );
    
        // определим разделитель
        if( ! $row_delimiter ){
            $row_delimiter = "\r\n";
            if( false === strpos($encoded_cont, "\r\n") )
                $row_delimiter = "\n";
        }
    
        $lines = explode( $row_delimiter, trim($encoded_cont) );
        $lines = array_filter( $lines );
        $lines = array_map( 'trim', $lines );
    
        // авто-определим разделитель из двух возможных: ';' или ','.
        // для расчета берем не больше 30 строк
        if( ! $col_delimiter ){
            $lines10 = array_slice( $lines, 0, 30 );
    
            // если в строке нет одного из разделителей, то значит другой точно он...
            foreach( $lines10 as $line ){
                if( ! strpos( $line, ',') ) $col_delimiter = ';';
                if( ! strpos( $line, ';') ) $col_delimiter = ',';
    
                if( $col_delimiter ) break;
            }
    
            // если первый способ не дал результатов, то погружаемся в задачу и считаем кол разделителей в каждой строке.
            // где больше одинаковых количеств найденного разделителя, тот и разделитель...
            if( ! $col_delimiter ){
                $delim_counts = array( ';'=>array(), ','=>array() );
                foreach( $lines10 as $line ){
                    $delim_counts[','][] = substr_count( $line, ',' );
                    $delim_counts[';'][] = substr_count( $line, ';' );
                }
    
                $delim_counts = array_map( 'array_filter', $delim_counts ); // уберем нули
    
                // кол-во одинаковых значений массива - это потенциальный разделитель
                $delim_counts = array_map( 'array_count_values', $delim_counts );
    
                $delim_counts = array_map( 'max', $delim_counts ); // берем только макс. значения вхождений
    
                if( $delim_counts[';'] === $delim_counts[','] )
                    return array('Не удалось определить разделитель колонок.');
    
                $col_delimiter = array_search( max($delim_counts), $delim_counts );
            }
    
        }
    
        $data = [];
        foreach( $lines as $key => $line ){
            $data[] = str_getcsv( $line, $col_delimiter ); // linedata
            unset( $lines[$key] );
        }
    
        return $data;
    }

?>