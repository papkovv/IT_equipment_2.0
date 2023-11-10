<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <?$APPLICATION->ShowHead();?>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/css/resetStyle.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/libs/bootstrap-5.2.0-beta1-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/libs/bootstrap-5.2.0-beta1-dist/css/bootstrap.min.css.map">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/libs/slick/slick-theme.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/libs/daterangepicker/css/daterangepicker.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/libs/slick/slick.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/css/fonts.css";>
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/css/style.css">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH; ?>/css/media.css">
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/bootstrap-5.2.0-beta1-dist/js/bootstrap.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/daterangepicker/js/moment.min.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/daterangepicker/js/daterangepicker.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/jquery.maskedinput-master/src/jquery.maskedinput.js"></script>
   
</head>
<?$APPLICATION->ShowPanel();?>
<?
global $USER;
global $isAdmin;
$isAdmin = false;
$userID = $USER->GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();
$arUserGroups = $USER->GetUserGroupArray();
$rsLMSAdminGroup = CGroup::GetList ($by = "c_sort", $order = "asc");
while($arGroup = $rsLMSAdminGroup->Fetch()){
    if ($arGroup["STRING_ID"] == "lmsadmins" || $arGroup["STRING_ID"] == "lmsmanagers" ){
        if (in_array($arGroup["ID"], $arUserGroups)){
            $isAdmin = true;
        }
    }
}
$isQuestionpage = strpos($APPLICATION->GetCurUri(), "course.php?") && strpos($APPLICATION->GetCurUri(), "&TEST_ID=");
$classHidden = "";
if ($isQuestionpage ) $classHidden = "visually-hidden";

?>
<style>
    .header-logout-icon{
        width:20px;
        height:20px;
        
    }
    .header-logout.nav__link {
        background: #989898;
        padding: 10px;
        border-radius: 10px;
    }
    .header-logout.nav__link:hover {
        background: #FF5100;
    }
</style>
<body class="d-flex flex-column h-100">
    <header>
        <div class="container desktop <?= $classHidden?>">
            <div class="row">
                <div class="col p-0 d-flex justify-content-between flex-wrap border-bottom pb-4">
                    <a class="logo" href="/it-equipment-2/">
                        <img src="<?= SITE_TEMPLATE_PATH; ?>/img/Other/logoOrange.svg" class="logo__img"/>
                        <p class="logo__title">Учет IT оборудования 2.0</p>
                    </a>
                    <div class="header__bar course">
                        <ul class="nav mt-0">
                            <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"lms_multilevel",
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"DELAY" => "N",
		"MAX_LEVEL" => "2",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"COMPONENT_TEMPLATE" => "lms_multilevel",
		"IS_LMSADMIN" => $isAdmin
	),
	false
);?>
                            <li>
                                <a title="Выход" class="header-logout nav__link" href="<?=$APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), ["login","logout","register","forgot_password","change_password"]);?>">
                                    <img class="header-logout-icon" src="<?= SITE_TEMPLATE_PATH; ?>/img/icon/logout.svg">                                    
                               </a>                            
                            </li>
                        </ul>                        
                    </div>
                </div>
            </div>            
        </div>
    </header>
    <div class="container desktop <?= $classHidden?>">
        <div class="row">
            <div class="col p-0">                    
                <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "ac-kroshki", Array(
                    "PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
                    "SITE_ID" => "s1",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
                    "START_FROM" => "0",	// Номер пункта, начиная с которого будет построена навигационная цепочка
                ),
                false
                );?>                    
            </div>                
        </div>
    </div>       
    <main class="mb-5">
<div class="container">
 <div class="row">
  <div class="col p-0">
        
								