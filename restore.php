<?
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if(!empty($_POST['ID'])){

    function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }
    function str2url($str) {
        // переводим в транслит
        $str = rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        return $str;
    }

    $rsElement = CIBlockElement::GetList(
        $arOrder  = array("SORT" => "ASC"),
        $arFilter = array(
            // "ACTIVE"    => "Y",
            "ID" => $_POST['ID'],
        ),
        false,
        false,
        $arSelectFields = array("ID", "NAME", "XML_ID", "IBLOCK_ID", "CODE", "PROPERTY_*")
    );
    while($arElement = $rsElement->GetNextElement()) {
        // $archive_items =  $arElement;
        $archive_items = $arElement->GetFields();
        $archive_items["PROPERTIES"] = $arElement->GetProperties();
    }



//     echo '<pre>';
//     print_r( $archive_items['PROPERTIES']['PEREODICHNOST_KALIBROVKI']['VALUE']);
//     echo '</pre>';
//    die();


//    if($archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'СИ'){
//        $archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] = 290;
//    }elseif($archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'Испытательное'){
//        $archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] =291;
//    }elseif($archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] == 'вспомогательное'){
//        $archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] =292;
//    }else{
//        $archive_items['PROPERTIES']['TIP_OBORUDOVANIYA']['VALUE'] = '';
//    }
//
//    if($archive_items['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] == 'НЕ ОБСЛУЖИВАЕТСЯ'){
//        $archive_items['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] = 319;
//    }elseif($archive_items['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] == 'Обслуживается'){
//        $archive_items['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] =320;
//    }else{
//        $archive_items['PROPERTIES']['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] = '';
//    }
//
//
//    if(strtolower($archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) == 'не требуется'){
//        $archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] = 293;
//    }elseif(strtolower($archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) == 'требуется' || !empty($archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE']) && $archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] != 'не требуется'){
//        $archive_items['PROPERTIES']['NEOBKHODIMOST_POVERKI']['VALUE'] = 294;
//    }
//
//    if(strtolower($archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE']) == 'не требуется'){
//        $archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 295;
//    }elseif(strtolower($archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE']) == 'требуется' || !empty($archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE']) && $archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE'] != 'не требуется'){
//        $archive_items['PROPERTIES']['ATTESTATSIYA']['VALUE'] = 296;
//    }
//
//    if(strtolower($archive_items['PROPERTIES']['KALIBROVKA']['VALUE']) == 'не требуется'){
//        $archive_items['PROPERTIES']['KALIBROVKA']['VALUE'] = 297;
//    }elseif(strtolower($archive_items['PROPERTIES']['KALIBROVKA']['VALUE']) == 'требуется' || !empty($archive_items['PROPERTIES']['KALIBROVKA']['VALUE']) && $archive_items['PROPERTIES']['KALIBROVKA']['VALUE'] != 'не требуется'){
//        $archive_items['PROPERTIES']['KALIBROVKA']['VALUE'] = 298;
//    }
    $userAdd = $GLOBALS['USER']->GetID();
    // $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>47, "CODE"=>"DATA_POSTANOVKI_NA_UCHET", "VALUE" => $item['PROPERTIES']['DATA_POSTANOVKI_NA_UCHET']['VALUE']));
    //     while($enum_fields = $property_enums->GetNext())
    //     {
    //     echo $enum_fields["ID"]." - ".$enum_fields["VALUE"]."<br>";
    //     }

    // $CODE = str_replace(' ','-',rus2translit($item['NAME']));
    debug($archive_items);

    $rsElement = new CIBlockElement;
    $arFields = array(
        "ACTIVE"            => "Y",
        "NAME"              => $archive_items['NAME'],
        "IBLOCK_ID" => 152,
        "XML_ID" => $archive_items['XML_ID'],
        // "CODE" => $CODE,
        "PROPERTY_VALUES"   => array(
            'TYPE' => $archive_items['PROPERTIES']['TYPE']['VALUE'],
            'MODEL' => $archive_items['PROPERTIES']['MODEL']['VALUE'],
            'SERIAL_NUMBER' => $archive_items['PROPERTIES']['SERIAL_NUMBER']['VALUE'],
            'ACTUAL_USER' => $archive_items['PROPERTIES']['ACTUAL_USER']['VALUE'],
            'QUANTITY' => $archive_items['PROPERTIES']['QUANTITY']['VALUE'],
            'UID_MOL' => $archive_items['PROPERTIES']['UID_MOL']['VALUE'],
            'CODE_MOL' => $archive_items['PROPERTIES']['CODE_MOL']['VALUE'],
            'MOL' => $archive_items['PROPERTIES']['MOL']['VALUE'],
            'TMC_UID' => $archive_items['PROPERTIES']['TMC_UID']['VALUE'],
            'TMC_CODE' => $archive_items['PROPERTIES']['TMC_CODE']['VALUE'],
            'TMC_NAME' => $archive_items['PROPERTIES']['TMC_NAME']['VALUE'],
            'TMC_FULL_NAME' => $archive_items['PROPERTIES']['TMC_FULL_NAME']['VALUE'],
            'VENDOR_CODE' => $archive_items['PROPERTIES']['VENDOR_CODE']['VALUE'],
            'SUBDIVISION_LINK' => $archive_items['PROPERTIES']['SUBDIVISION_LINK']['VALUE'],
            'SUBDIVISION_CODE' => $archive_items['PROPERTIES']['SUBDIVISION_CODE']['VALUE'],
            'SUBDIVISION_NAME_FULL' => $archive_items['PROPERTIES']['SUBDIVISION_NAME_FULL']['VALUE'],
            'STRUCTURAL_SUBDIVISION' => $archive_items['PROPERTIES']['STRUCTURAL_SUBDIVISION']['VALUE'],
            'NOMENCLATURE_TYPE_LINK' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_LINK']['VALUE'],
            'NOMENCLATURE_TYPE_NAME' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_NAME']['VALUE'],
            'UNIT_LINK' => $archive_items['PROPERTIES']['UNIT_LINK']['VALUE'],
            'UNIT_CODE' => $archive_items['PROPERTIES']['UNIT_CODE']['VALUE'],
            'UNIT_NAME' => $archive_items['PROPERTIES']['UNIT_NAME']['VALUE'],
            'INVENTORY_NUMBER' => $archive_items['PROPERTIES']['INVENTORY_NUMBER']['VALUE'],
            'NOTE' => $archive_items['PROPERTIES']['NOTE']['VALUE']['TEXT'],
            'REFURBISHED_ITEM' => $archive_items['ID'],

        )
    );
    if($id = $rsElement->Add($arFields)) {

        $name = 'Восстановлен элемент: ' .$archive_items['NAME']. ' в '. date("G:i:s d.m.Y");
        $date_time = date("d.m.Y G:i:s");
        $rsIB = new CIBlockElement;
        $arFieldsOld = array(
            "ACTIVE"    => "Y",
            "NAME" => $name,
            "IBLOCK_ID" => 151,
            "PROPERTY_VALUES"   => array(
                'EQUIPMENT_NAME_STARYY' => $archive_items['NAME'],
                'TYPE_STARYY' => $archive_items['PROPERTIES']['TYPE']['VALUE'],
                'MODEL_STARYY' => $archive_items['PROPERTIES']['MODEL']['VALUE'],
                'SERIAL_NUMBER_STARYY' => $archive_items['PROPERTIES']['SERIAL_NUMBER']['VALUE'],
                'ACTUAL_USER_STARYY' => $archive_items['PROPERTIES']['ACTUAL_USER']['VALUE'],
                'QUANTITY_STARYY' => $archive_items['PROPERTIES']['QUANTITY']['VALUE'],
                'UID_MOL_STARYY' => $archive_items['PROPERTIES']['UID_MOL']['VALUE'],
                'CODE_MOL_STARYY' => $archive_items['PROPERTIES']['CODE_MOL']['VALUE'],
                'MOL_STARYY' => $archive_items['PROPERTIES']['MOL']['VALUE'],
                'TMC_UID_STARYY' => $archive_items['PROPERTIES']['TMC_UID']['VALUE'],
                'TMC_CODE_STARYY' => $archive_items['PROPERTIES']['TMC_CODE']['VALUE'],
                'TMC_NAME_STARYY' => $archive_items['PROPERTIES']['TMC_NAME']['VALUE'],
                'TMC_FULL_NAME_STARYY' => $archive_items['PROPERTIES']['TMC_FULL_NAME']['VALUE'],
                'VENDOR_CODE_STARYY' => $archive_items['PROPERTIES']['VENDOR_CODE']['VALUE'],
                'SUBDIVISION_LINK_STARYY' => $archive_items['PROPERTIES']['SUBDIVISION_LINK']['VALUE'],
                'SUBDIVISION_CODE_STARYY' => $archive_items['PROPERTIES']['SUBDIVISION_CODE']['VALUE'],
                'SUBDIVISION_NAME_FULL_STARYY' => $archive_items['PROPERTIES']['SUBDIVISION_NAME_FULL']['VALUE'],
                'STRUCTURAL_SUBDIVISION_STARYY' => $archive_items['PROPERTIES']['STRUCTURAL_SUBDIVISION']['VALUE'],
                'NOMENCLATURE_TYPE_LINK_STARYY' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_LINK']['VALUE'],
                'NOMENCLATURE_TYPE_NAME_STARYY' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_NAME']['VALUE'],
                'UNIT_LINK_STARYY' => $archive_items['PROPERTIES']['UNIT_LINK']['VALUE'],
                'UNIT_CODE_STARYY' => $archive_items['PROPERTIES']['UNIT_CODE']['VALUE'],
                'UNIT_NAME_STARYY' => $archive_items['PROPERTIES']['UNIT_NAME']['VALUE'],
                'INVENTORY_NUMBER_STARYY' => $archive_items['PROPERTIES']['INVENTORY_NUMBER']['VALUE'],
                'NOTE_STARYY' => $archive_items['PROPERTIES']['NOTE']['VALUE']['TEXT'],
                'TYPE' => $archive_items['PROPERTIES']['TYPE']['VALUE'],
                'MODEL' => $archive_items['PROPERTIES']['MODEL']['VALUE'],
                'SERIAL_NUMBER' => $archive_items['PROPERTIES']['SERIAL_NUMBER']['VALUE'],
                'ACTUAL_USER' => $archive_items['PROPERTIES']['ACTUAL_USER']['VALUE'],
                'QUANTITY' => $archive_items['PROPERTIES']['QUANTITY']['VALUE'],
                'UID_MOL' => $archive_items['PROPERTIES']['UID_MOL']['VALUE'],
                'CODE_MOL' => $archive_items['PROPERTIES']['CODE_MOL']['VALUE'],
                'MOL' => $archive_items['PROPERTIES']['MOL']['VALUE'],
                'TMC_UID' => $archive_items['PROPERTIES']['TMC_UID']['VALUE'],
                'TMC_CODE' => $archive_items['PROPERTIES']['TMC_CODE']['VALUE'],
                'TMC_NAME' => $archive_items['PROPERTIES']['TMC_NAME']['VALUE'],
                'TMC_FULL_NAME' => $archive_items['PROPERTIES']['TMC_FULL_NAME']['VALUE'],
                'VENDOR_CODE' => $archive_items['PROPERTIES']['VENDOR_CODE']['VALUE'],
                'SUBDIVISION_LINK' => $archive_items['PROPERTIES']['SUBDIVISION_LINK']['VALUE'],
                'SUBDIVISION_CODE' => $archive_items['PROPERTIES']['SUBDIVISION_CODE']['VALUE'],
                'SUBDIVISION_NAME_FULL' => $archive_items['PROPERTIES']['SUBDIVISION_NAME_FULL']['VALUE'],
                'STRUCTURAL_SUBDIVISION' => $archive_items['PROPERTIES']['STRUCTURAL_SUBDIVISION']['VALUE'],
                'NOMENCLATURE_TYPE_LINK' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_LINK']['VALUE'],
                'NOMENCLATURE_TYPE_NAME' => $archive_items['PROPERTIES']['NOMENCLATURE_TYPE_NAME']['VALUE'],
                'UNIT_LINK' => $archive_items['PROPERTIES']['UNIT_LINK']['VALUE'],
                'UNIT_CODE' => $archive_items['PROPERTIES']['UNIT_CODE']['VALUE'],
                'UNIT_NAME' => $archive_items['PROPERTIES']['UNIT_NAME']['VALUE'],
                'INVENTORY_NUMBER' => $archive_items['PROPERTIES']['INVENTORY_NUMBER']['VALUE'],
                'NOTE' => $archive_items['PROPERTIES']['NOTE']['VALUE']['TEXT'],
                'KTO_IZMENIL' => $userAdd,
                'CHANGED_ELEMENT' => $archive_items['ID'],
                'DATE_TIME_CHANGE' => $date_time,

            )
        );
        $id = $rsIB->Add($arFieldsOld);

        $arFilter = Array("IBLOCK_ID"=>152,);
        $res = CIBlockElement::GetList(Array(), $arFilter); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
        while ($ob = $res->GetNextElement()){; // переходим к след элементу, если такой есть
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties(); // свойства элемента
            if ($arProps['REFURBISHED_ITEM']['VALUE'] == $archive_items['ID']) {
                $retiredId = $arFields['ID'];
            }
        }


        $arFilter = Array("IBLOCK_ID"=>151);
        $res = CIBlockElement::GetList(Array(), $arFilter); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
        while ($ob = $res->GetNextElement()){; // переходим к след элементу, если такой есть
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties(); // свойства элемента
            if ($arProps['CHANGED_ELEMENT']['VALUE'] == $archive_items['ID']) {
                $elementsToUpdate[] = $arFields['ID'];
            }
        }

        if ($elementsToUpdate) {
            for ($i = 0; $i < count($elementsToUpdate); $i++) {
                CIBlockElement::SetPropertyValuesEx($elementsToUpdate[$i], 151, array('CHANGED_ELEMENT' => $retiredId));
            }
        }


//
//        $rsElement = CIBlockElement::GetList(
//            $arOrder  = array("SORT" => "ASC"),
//            $arFilter = array(
//                "ACTIVE"    => "Y",
//                "IBLOCK_ID" => 87,
//                "PROPERTY_OBORUDOVANIE" => $_POST['ID'],
//            ),
//            false,
//            false,
//            $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
//        );
//        while($arElement = $rsElement->GetNextElement()) {
//            // $archive_items =  $arElement;
//            $archive_transports[] = $arElement->GetFields();
//            $archive_transports["PROPERTIES"] = $arElement->GetProperties();
//        }
//
//        // if(is_array($archive_transports))
//
//        foreach ($archive_transports as $key => $archive_transport) {
//            echo '<pre>';
//            print_r( $archive_transport);
//            echo '</pre>';
//            // CIBlockElement::SetPropertyValuesEx($archive_transport['ID'], 87, ['OBORUDOVANIE' => $id]);
//            CIBlockElement::SetPropertyValueCode($archive_transport['ID'], "OBORUDOVANIE", $id);
//        }
//
//        $rsElement = CIBlockElement::GetList(
//            $arOrder  = array("SORT" => "ASC"),
//            $arFilter = array(
//                "ACTIVE"    => "Y",
//                "IBLOCK_ID" => 86,
//                "PROPERTY_OBORUDOVANIE" => $_POST['ID'],
//            ),
//            false,
//            false,
//            $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
//        );
//        while($arElement = $rsElement->GetNextElement()) {
//            // $archive_items =  $arElement;
//            $archive_obslug[] = $arElement->GetFields();
//            $archive_obslug["PROPERTIES"] = $arElement->GetProperties();
//        }
//
//        // if(is_array($archive_transports))
//
//        foreach ($archive_obslug as $key => $archive_obslu) {
//            echo '<pre>';
//            print_r( $archive_obslu);
//            echo '</pre>';
//            // CIBlockElement::SetPropertyValuesEx($archive_transport['ID'], 87, ['OBORUDOVANIE' => $id]);
//            CIBlockElement::SetPropertyValueCode($archive_obslu['ID'], "OBORUDOVANIE", $id);
//        }
        CIBlockElement::Delete($archive_items['ID']);
//        // $rsElement = new CIBlockElement;
//        // $arFields = array(
//        //     "ACTIVE"    => "N",
//        //     //...
//        // );
//        // $rsIDElement = $rsElement->Update($_POST['ID'], $arFields);

    } else {
        echo "Error:" . $rsElement->LAST_ERROR;
    }



}