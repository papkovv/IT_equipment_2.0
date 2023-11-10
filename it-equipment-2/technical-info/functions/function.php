<?
    CModule::IncludeModule('iblock');
    function iblockAdd($iblock_ID, $arFields, $arValues, $key) {
        $el = new CIBlockElement;
        for ($i = 0; $i < COUNT($arFields); $i++) {
            if ($arFields[$i] != 'none') {
                if ($arFields[$i] == 'name-equipment') {
                    $name = $arValues[$i];
                } else if ($arFields[$i] == 'xml_id-equipment') {
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