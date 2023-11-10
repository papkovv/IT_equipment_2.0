<? 
function iblockID($code) {
    //return \Bitrix\Iblock\IblockTable::getList(['filter'=>['CODE'=>$code]])->Fetch()["ID"];
    if (CModule::IncludeModule('iblock')){   
        $resc = CIBlock::GetList([], Array('CODE' => $code));
        while($arrc = $resc->Fetch())
            return $cc_name = $arrc["ID"];
    }
}