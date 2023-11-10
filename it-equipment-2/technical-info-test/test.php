<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(""); ?><?$APPLICATION->IncludeComponent(
	"eqp:import.exel", 
	".default", 
	array(
		"TEMPLATE_FOR_FILE" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"FILE" => "/upload/file.csv",
		"IBLOCK_TYPE" => "equipment",
		"IBLOCK_ID" => "85"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>