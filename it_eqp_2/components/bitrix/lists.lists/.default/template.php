<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

CJSCore::Init(array('lists'));
\Bitrix\Main\UI\Extension::load("ui.buttons");
$randString = $component->randString();
$jsClass = 'ListsIblockClass_'.$randString;

$claim = false;
$title = GetMessage("CT_BLL_TOOLBAR_ADD_TITLE_LIST");
if($arParams["IBLOCK_TYPE_ID"] == COption::GetOptionString("lists", "livefeed_iblock_type_id"))
{
	$title = GetMessage("CT_BLL_TOOLBAR_ADD_TITLE_PROCESS");
	$claim = true;
}
$isBitrix24Template = (SITE_TEMPLATE_ID == "bitrix24");

if(!IsModuleInstalled("intranet"))
{
	$APPLICATION->SetAdditionalCSS("/bitrix/js/lists/css/intranet-common.css");
}
if($arParams['CAN_EDIT']): ?>
<div class="pagetitle-container pagetitle-align-right-container">	
	<? if($claim && $arParams['CAN_EDIT']): ?>
		<a class="ui-btn ui-btn-light-border ui-btn-themes" href="<?= $arParams["CATALOG_PROCESSES_URL"] ?>" title="<?= GetMessage("CT_BLL_TOOLBAR_TRANSITION_PROCESSES") ?>">
			<?= GetMessage("CT_BLL_TOOLBAR_TRANSITION_PROCESSES") ?>
		</a>
	<? endif; ?>
	<? if($arParams["IBLOCK_TYPE_ID"] != "lists" && $arParams["IBLOCK_TYPE_ID"] != "lists_socnet" && empty($arResult["ITEMS"])): ?>
		<button class="ui-btn ui-btn-light-border ui-btn-themes" id="bx-lists-default-processes" onclick="javascript:BX.Lists['<?=$jsClass?>'].createDefaultProcesses();" title="<?= GetMessage("CT_BLL_TOOLBAR_ADD_DEFAULT") ?>">
			<?= GetMessage("CT_BLL_TOOLBAR_ADD_DEFAULT") ?></button>
	<? endif; ?>
	<input type="hidden" id="bx-lists-select-site" value="<?= SITE_ID ?>" />
</div>
<? endif;
	if($isBitrix24Template)
		$this->EndViewTarget();
?>
<div class="main__service__items">
<? foreach($arResult["ITEMS"] as $item): ?>
	<a class="link card" href="<?= $item['LIST_URL']?>">
		<?= $item['DESCRIPTION'] ?>
		<p><?= $item['NAME'] ?></p>
	</a>
<? endforeach; ?>
	<a class="link card" href="<?$_SERVER['DOCUMENT_ROOT']?>/it-equipment-2/technical-info/">
		<img src="/local/templates/it_eqp_2/img/icon/Files.svg">
		<p>Импорт оборудования (в работе)</p>
	</a>
<?
$dir = $APPLICATION->GetCurDir();
if($dir=='/equipment/'):
?>
<a class="link card" href="/equipment/documents/">
<img src="/upload/iblock/b46/1830a5gr07pm1zxd43ypni8ljcq89vw7/eqdocs.png" border="0" alt="" width="34" height="30">
		<p>Договора по обслуживанию оборудования</p>
	</a>
</div>
<?endif;?>
<script type="text/javascript">
	BX(function () {
		BX.Lists['<?=$jsClass?>'] = new BX.Lists.ListsIblockClass({
			randomString: '<?= $randString ?>'
		});
	});
</script>