<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?> 

<?  
    require './functions.php';
    $iblockID = $_POST['iblockID'];
    $arElement = json_decode($_POST['file']);
    $arFields = $_POST['fields'];
    $add = 0;
    $all = 0;
    $arrAddElement = [];
    $error = (object) [];
    $propertyDB = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID, "CODE" => $_POST['codeParent']));
    while ($prop = $propertyDB->GetNext()) {
        $propertyID = $prop['ID'];
    }
    $keyXML_ID = array_search('xml_id-equipment', $arFields);
    $keyParent = array_search($propertyID, $arFields);
    if ($keyXML_ID === false) {
        $error->error1 = 'Выбирете столбец с внешним кодом<br>';
    }
    if ($keyParent === false) {
        $error->error2 = 'Выбирете столбец с родителями<br>';
    }

    if (!isset($error->error1) && !isset($error->error2)) {
        $error = [];
        for ($i = 1; $i < COUNT($arElement); $i++) {
            if (!in_array($arElement[$i][$keyXML_ID], $arrAddElement)) {
                if (empty(trim($arElement[$i][$keyParent]))) {
                    $otvet = addElement($arElement[$i], $i, $arrAddElement, $add, $error);
                    if ($otvet['status'] != false) {
                        $arrAddElement = $otvet['arrAdd'];
                        $add = $otvet['add'];
                        
                    } else {
                        $error = $otvet['error'];
                    }
                    //debug($otvet);
                } else if (in_array($arElement[$i][$keyParent], $arrAddElement)) {
                    $otvet = addElement($arElement[$i], $i, $arrAddElement, $add, $error);
                    if ($otvet['status'] != false) {
                        $arrAddElement = $otvet['arrAdd'];
                        $add = $otvet['add'];
                    } else {
                        $error = $otvet['error'];
                    }
                } else {
                    $arSelect = Array("ID", "XML_ID");
                    $arFilter = Array("IBLOCK_ID"=>$iblockID, "XML_ID" => $arElement[$i][$keyXML_ID], "ACTIVE"=>"Y");
                    $elementDB = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
                    while($element = $elementDB->GetNextElement()) {  
                        $element = $element->GetFields(); 
                        $elementID = $element['ID'];
                    }

                    if (!empty($elementID)) {
                        $otvet = addElement($arElement[$i], $i, $arrAddElement, $add, $error);
                        if ($otvet['status'] != false) {
                            $arrAddElement = $otvet['arrAdd'];
                            $add = $otvet['add'];
                        } else {
                            $error = $otvet['error'];
                        }
                    }
                }
            } 
            $all++;
        }
    }
    

    /*$response = iblockAdd($iblockID, $arFields, $arElement[$i]);
        if (array_key_exists('error', $response)) {
            $error[$i] = $response['error'];
        } 
        if ($response['status']) {
            $add++;
            $arrAddElement[] = $arElement[$i][$keyXML_ID];
            for ($j = 1; $j < COUNT($arElement); $j++) {
                if ($i != $j && !in_array($arElement[$j][$keyXML_ID], $arrAddElement) && $arElement[$i][$keyXML_ID] == $arElement[$j][$keyParent]) {
                    addElement($arElement[$j])
                }
            }
        }
        $all++;*/

    function addElement($element, $iterator, $arAdd, $i_add, $error) {
        $result = [];
        $iblockID = $_POST['iblockID'];
        $arElement = json_decode($_POST['file']);
        $arFields = $_POST['fields'];
        $propertyDB = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID, "CODE" => $_POST['codeParent']));
        while ($prop = $propertyDB->GetNext()) {
            $propertyID = $prop['ID'];
        }
        $keyXML_ID = array_search('xml_id-equipment', $arFields);
        $keyParent = array_search($propertyID, $arFields);
        $response = iblockAdd($iblockID, $arFields, $element, $keyParent);
        if ($response['status'] == false) {
            $result['status'] = false;
            $error[$iterator] = $response['error'];
            $result['error'] = $error;
        } else if ($response['status'] == true){
            $result['status'] = true;
            $i_add++;
            
            $arAdd[] = $arElement[$iterator][$keyXML_ID];
            //$result['arrAdd'] = $arAdd;
            for ($j = 1; $j < COUNT($arElement); $j++) {
                if ($iterator != $j && !in_array($arElement[$j][$keyXML_ID], $arAdd) && stringChecking($arElement[$iterator][$keyXML_ID], $arElement[$j][$keyParent])) {
                    $otvet = addElement($arElement[$j], $j, $arAdd, $i_add, $error);
                    if ($otvet['status'] != false) {
                        $arAdd = $otvet['arrAdd'];
                        $i_add = $otvet['add'];
                    } else {
                        $error[$j] = $otvet['error'];
                    }
                }
            }
            $result['error'] = $error;
            $result['arrAdd'] = $arAdd;
            $result['add'] = $i_add;
        }
    
        return $result;
    }

    $result = [
        "status" => true,
        "error" => $error,
        "add" => $add,
        "all" => $all,
        "fields" => $arFields
    ];
    echo json_encode($result);
    
   
   
?>