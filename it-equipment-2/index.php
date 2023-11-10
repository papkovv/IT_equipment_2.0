<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("IT оборудование 2.0");
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:lists",
    "ac-lists",
    array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "IBLOCK_TYPE_ID" => "it_eqp_2",
        "SEF_MODE" => "N",
        "COMPONENT_TEMPLATE" => "ac-lists",
        "VARIABLE_ALIASES" => array(
            "list_id" => "list_id",
            "field_id" => "field_id",
            "section_id" => "section_id",
            "element_id" => "element_id",
            "file_id" => "file_id",
            "mode" => "mode",
            "document_state_id" => "document_state_id",
            "task_id" => "task_id",
            "ID" => "ID",
        )
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
