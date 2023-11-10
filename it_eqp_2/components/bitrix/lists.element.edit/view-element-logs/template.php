<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Loader,
\Bitrix\Disk;
global $USER;
$arGroups = $USER->GetUserGroupArray();
// echo "<pre>"; print_r($arGroups); echo "</pre>";
if($_GET['edit'] == 'yes' || $_GET['element_id'] == 0):
CJSCore::Init(array('window', 'lists'));
Bitrix\Main\UI\Extension::load("ui.buttons");
$dir = $APPLICATION->GetCurPageParam("edit=no", array("edit", "d")); 
// echo $dir;
$jsClass = 'ListsElementEditClass_'.$arResult['RAND_STRING'];
$urlTabBp = CHTTP::urlAddParams(
	$APPLICATION->GetCurPageParam("", array($arResult["FORM_ID"]."_active_tab")),
	array($arResult["FORM_ID"]."_active_tab" => "tab_bp")
);
$socnetGroupId = $arParams["SOCNET_GROUP_ID"] ? $arParams["SOCNET_GROUP_ID"] : 0;
$sectionId = $arResult["SECTION_ID"] ? $arResult["SECTION_ID"] : 0;

$listAction = array();
if (isset($arResult["LIST_COPY_ELEMENT_URL"]))
{
	if($arResult["CAN_ADD_ELEMENT"])
	{
		$listAction[] = array(
			"id" => "copyElement",
			"text" => GetMessage("CT_BLEE_TOOLBAR_COPY_ELEMENT"),
			"url" => $arResult["LIST_COPY_ELEMENT_URL"]
		);
	}
}

if (CLists::isEnabledLockFeature($arResult["IBLOCK_ID"]) &&
	$arResult["ELEMENT_ID"] && ($arResult["CAN_FULL_EDIT"] ||
	!CIBlockElement::WF_IsLocked($arResult["ELEMENT_ID"], $lockedBy, $dateLock)))
{
	$listAction[] = [
		"id" => "unLockElement",
		"text" => GetMessage("CT_BLEE_UN_LOCK_ELEMENT"),
		"action" => "BX.Lists['".$jsClass."'].unLock();"
	];
}

if($arResult["CAN_DELETE_ELEMENT"])
{
	$listAction[] = array(
		"id" => "deleteElement",
		"text" => $arResult["IBLOCK"]["ELEMENT_DELETE"],
		"action" => "BX.Lists['".$jsClass."'].elementDelete('form_".$arResult["FORM_ID"]."',
			'".GetMessage("CT_BLEE_TOOLBAR_DELETE_WARNING")."')",
	);
}

$isBitrix24Template = (SITE_TEMPLATE_ID == "bitrix24");
$pagetitleAlignRightContainer = "lists-align-right-container";
if($isBitrix24Template)
{
	$this->SetViewTarget("pagetitle", 100);
	$pagetitleAlignRightContainer = "";
}
elseif(!IsModuleInstalled("intranet"))
{
	$APPLICATION->SetAdditionalCSS("/bitrix/js/lists/css/intranet-common.css");
}
?>
<?if($_GET['list_id'] == 48 && $_GET['element_id'] == 0){
	 $rsElement = CIBlockElement::GetList(
        $arOrder  = array("ID" => "DESC"),
        $arFilter = array(
            "IBLOCK_ID"    => 48,
        ),
        false,
        array("nTopCount" => 1),
        $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
       );
       while($arElement = $rsElement->GetNextElement()) {
        $el = $arElement->GetFields();
        $el["PROPERTIES"] = $arElement->GetProperties();
        $old_items[$el['ID']] = $el;
            $last_id = $el["ID"];
            $item['name'] = intval($last_id+1);
			$item['name'] = "№" . $item['name'] . " Дата: " . date("d.m.Y G:i:s");
        
       } 
	  
			$arResult['FORM_DATA']['NAME'] = $item['name'];
			// echo "<pre>";
            // print_r( $arResult['FILES']);
            // echo "</pre>";
}?>

<div class="pagetitle-container pagetitle-align-right-container <?=$pagetitleAlignRightContainer?>">
	<?if($_GET['element_id'] != 0):?><a href="<?=$dir?>" style="margin-right: 20px;">Отмена редактирования</a><?endif;?>
	<a href="<?=$arResult["LIST_SECTION_URL"]?>" class="ui-btn ui-btn-sm ui-btn-link ui-btn-themes lists-list-back">
		<?=GetMessage("CT_BLEE_TOOLBAR_RETURN_LIST_ELEMENT")?>
	</a>
	<?if($listAction):?>
	<span id="lists-title-action" class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-dropdown ui-btn-themes">
		<?=GetMessage("CT_BLEE_TOOLBAR_ACTION")?>
	</span>
	<?endif;?>
</div>
<?
if($isBitrix24Template)
{
	$this->EndViewTarget();
}

$tabElement = array();
$cuctomHtml = "";
foreach($arResult["FIELDS"] as $fieldId => $field)
{
	$field["LIST_SECTIONS_URL"] = $arParams["~LIST_SECTIONS_URL"];
	$field["SOCNET_GROUP_ID"] = $socnetGroupId;
	$field["LIST_ELEMENT_URL"] = $arParams["~LIST_ELEMENT_URL"];
	$field["LIST_FILE_URL"] = $arParams["~LIST_FILE_URL"];
	$field["IBLOCK_ID"] = $arResult["IBLOCK_ID"];
	$field["SECTION_ID"] = intval($arParams["~SECTION_ID"]);
	$field["ELEMENT_ID"] = $arResult["ELEMENT_ID"];
	$field["FIELD_ID"] = $fieldId;
	$field["VALUE"] = $arResult["FORM_DATA"]["~".$fieldId];
	$field["COPY_ID"] = $arResult["COPY_ID"];
	$preparedData = \Bitrix\Lists\Field::prepareFieldDataForEditForm($field);
	if($preparedData)
	{
		$tabElement[] = $preparedData;
		if(!empty($preparedData["customHtml"]))
		{
			$cuctomHtml .= $preparedData["customHtml"];
		}
	}
}

$tabSection = array(
	array(
		"id" => "IBLOCK_SECTION_ID",
		"name" => $arResult["IBLOCK"]["SECTIONS_NAME"],
		"type" => "list",
		"items" => $arResult["LIST_SECTIONS"],
		"params" => array("size" => 15),
	),
);

$arTabs = array(
	array("id" => "tab_el", "name" => $arResult["IBLOCK"]["ELEMENT_NAME"], "icon" => "", "fields" => $tabElement),
	array("id" => "tab_se", "name" => $arResult["IBLOCK"]["SECTION_NAME"], "icon" => "", "fields" => $tabSection)
);

if(CModule::IncludeModule("bizproc") && CBPRuntime::isFeatureEnabled() && $arResult["IBLOCK"]["BIZPROC"] != "N")
{
	$arCurrentUserGroups = $GLOBALS["USER"]->GetUserGroupArray();
	if(!$arResult["ELEMENT_FIELDS"] || $arResult["ELEMENT_FIELDS"]["CREATED_BY"] == $GLOBALS["USER"]->GetID())
	{
		$arCurrentUserGroups[] = "author";
	}

	$DOCUMENT_TYPE = "iblock_".$arResult["IBLOCK_ID"];
	CBPDocument::AddShowParameterInit("iblock", "only_users", $DOCUMENT_TYPE);

	$arTab2Fields = array();
	$arTab2Fields[] = array(
		"id" => "BIZPROC_WF_STATUS",
		"name" => GetMessage("CT_BLEE_BIZPROC_PUBLISHED"),
		"type" => "label",
		"value" => $arResult["ELEMENT_FIELDS"]["BP_PUBLISHED"]=="Y"? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")
	);

	$bizProcIndex = 0;
	$arDocumentStates = CBPDocument::GetDocumentStates(
		BizProcDocument::generateDocumentComplexType($arParams["IBLOCK_TYPE_ID"], $arResult["IBLOCK_ID"]),
		($arResult["ELEMENT_ID"] > 0) ? BizProcDocument::getDocumentComplexId(
			$arParams["IBLOCK_TYPE_ID"], $arResult["ELEMENT_ID"]) : null,
		"Y"
	);

	$cuctomHtml .= '<input type="hidden" name="stop_bizproc" id="stop_bizproc" value="">';

	$runtime = CBPRuntime::GetRuntime();
	$runtime->StartRuntime();
	$documentService = $runtime->GetService("DocumentService");

	foreach ($arDocumentStates as $arDocumentState)
	{
		$templateId = intval($arDocumentState["TEMPLATE_ID"]);
		$templateConstants = CBPWorkflowTemplateLoader::getTemplateConstants($templateId);

		if(
			empty($arDocumentState["TEMPLATE_PARAMETERS"]) &&
			empty($arDocumentState["ID"]) &&
			empty($templateConstants) &&
			!CIBlockRights::UserHasRightTo($arResult["IBLOCK_ID"], $arResult["IBLOCK_ID"], 'iblock_edit')
		)
		{
			continue;
		}

		$bizProcIndex++;

		if ($arResult["ELEMENT_ID"] > 0)
		{
			$canViewWorkflow = CBPDocument::CanUserOperateDocument(
				CBPCanUserOperateOperation::ViewWorkflow,
				$GLOBALS["USER"]->GetID(),
				BizProcDocument::getDocumentComplexId($arParams["IBLOCK_TYPE_ID"], $arResult["ELEMENT_ID"]),
				array("AllUserGroups" => $arCurrentUserGroups, "DocumentStates" => $arDocumentStates,
					"WorkflowId" => $arDocumentState["ID"])
			);
		}
		else
		{
			$canViewWorkflow = CBPDocument::CanUserOperateDocumentType(
				CBPCanUserOperateOperation::StartWorkflow,
				$GLOBALS["USER"]->GetID(),
				BizProcDocument::generateDocumentComplexType($arParams["IBLOCK_TYPE_ID"], $arResult["IBLOCK_ID"]),
				array("sectionId"=> intval($arResult["SECTION_ID"]), "AllUserGroups" => $arCurrentUserGroups,
					"DocumentStates" => $arDocumentStates, "WorkflowId" => $arDocumentState["ID"])
			);
		}

		if($canViewWorkflow)
		{
			$arTab2Fields[] = array(
				"id" => "BIZPROC_TITLE".$bizProcIndex,
				"name" => $arDocumentState["TEMPLATE_NAME"],
				"type" => "section",
			);

			if (mb_strlen($arDocumentState["ID"]) && mb_strlen($arDocumentState["WORKFLOW_STATUS"]))
			{
				if (CBPDocument::CanUserOperateDocument(
					CBPCanUserOperateOperation::StartWorkflow,
					$GLOBALS["USER"]->GetID(),
					BizProcDocument::getDocumentComplexId($arParams["IBLOCK_TYPE_ID"], $arResult["ELEMENT_ID"]),
					array("UserGroups" => $arCurrentUserGroups)
				))
				{
					$arTab2Fields[] = array(
						"id" => "BIZPROC_STOP".$bizProcIndex,
						"name" => GetMessage("CT_BLEE_BIZPROC_STOP_LABEL"),
						"type" => "label",
						"value" => '<a href="javascript:void(0)"
						onclick="BX.Lists[\''.$jsClass.'\'].completeWorkflow(\''.$arDocumentState["ID"].'\',
						\'stop\')">'.GetMessage("CT_BLEE_BIZPROC_STOP").'</a>'
					);
				}
			}

			$arTab2Fields[] = array(
				"id" => "BIZPROC_NAME".$bizProcIndex,
				"name" => GetMessage("CT_BLEE_BIZPROC_NAME"),
				"type" => "label",
				"value" => htmlspecialcharsbx($arDocumentState["TEMPLATE_NAME"]),
			);

			if($arDocumentState["TEMPLATE_DESCRIPTION"]!='')
				$arTab2Fields[] = array(
					"id" => "BIZPROC_DESC".$bizProcIndex,
					"name" => GetMessage("CT_BLEE_BIZPROC_DESC"),
					"type" => "label",
					"value" => htmlspecialcharsbx($arDocumentState["TEMPLATE_DESCRIPTION"]),
				);

			if($arDocumentState["STATE_MODIFIED"] <> '')
			{
				$arTab2Fields[] = array(
					"id" => "BIZPROC_DATE".$bizProcIndex,
					"name" => GetMessage("CT_BLEE_BIZPROC_DATE"),
					"type" => "label",
					"value" => htmlspecialcharsbx($arDocumentState["STATE_MODIFIED"]),
				);
			}

			if($arDocumentState["STATE_NAME"] <> '')
			{
				$backUrl = CHTTP::urlAddParams(
					$APPLICATION->GetCurPageParam("", array($arResult["FORM_ID"]."_active_tab")),
					array($arResult["FORM_ID"]."_active_tab" => "tab_bp")
				);
				$url = CHTTP::urlAddParams(str_replace(
					array("#list_id#", "#document_state_id#", "#group_id#"),
					array($arResult["IBLOCK_ID"], $arDocumentState["ID"], $arParams["SOCNET_GROUP_ID"]),
					$arParams["~BIZPROC_LOG_URL"]
				),
					array("back_url" => $backUrl),
					array("skip_empty" => true, "encode" => true)
				);

				if($arDocumentState["ID"] <> '')
				{
					$arTab2Fields[] = array(
						"id" => "BIZPROC_STATE".$bizProcIndex,
						"name" => GetMessage("CT_BLEE_BIZPROC_STATE"),
						"type" => "label",
						"value" => '<a href="'.htmlspecialcharsbx($url).'">'.($arDocumentState["STATE_TITLE"] <> ''? htmlspecialcharsbx($arDocumentState["STATE_TITLE"]) : htmlspecialcharsbx($arDocumentState["STATE_NAME"])).'</a>',
					);

					$canDeleteWorkflow = CBPDocument::CanUserOperateDocument(
						CBPCanUserOperateOperation::CreateWorkflow,
						$GLOBALS["USER"]->GetID(),
						BizProcDocument::getDocumentComplexId($arParams["IBLOCK_TYPE_ID"], $arResult["ELEMENT_ID"]),
						array("UserGroups" => $arCurrentUserGroups)
					);

					if($canDeleteWorkflow)
					{
						$arTab2Fields[] = array(
							"id" => "BIZPROC_DELETE".$bizProcIndex,
							"name" => GetMessage("CT_BLEE_BIZPROC_DELETE_LABEL"),
							"type" => "label",
							"value" => '<a href="javascript:void(0)"
								onclick="BX.Lists[\''.$jsClass.'\'].completeWorkflow(\''.$arDocumentState["ID"].'\',
								\'delete\')">'.GetMessage("CT_BLEE_BIZPROC_DELETE").'</a>'
						);
					}
				}
				else
				{
					$arTab2Fields[] = array(
						"id" => "BIZPROC_STATE".$bizProcIndex,
						"name" => GetMessage("CT_BLEE_BIZPROC_STATE"),
						"type" => "label",
						"value" => ($arDocumentState["STATE_TITLE"] <> ''? $arDocumentState["STATE_TITLE"] : $arDocumentState["STATE_NAME"]),
					);
				}
			}

			$arWorkflowParameters = $arDocumentState["TEMPLATE_PARAMETERS"];
			if(!is_array($arWorkflowParameters))
				$arWorkflowParameters = array();
			$formName = $arResult["form_id"];
			$bVarsFromForm = $arResult["VARS_FROM_FORM"];
			if($arDocumentState["ID"] == '' && $templateId > 0)
			{
				$arParametersValues = array();
				$keys = array_keys($arWorkflowParameters);
				foreach ($keys as $key)
				{
					$v = ($bVarsFromForm ? $_REQUEST["bizproc".$templateId."_".$key] :
						$arWorkflowParameters[$key]["Default"]);
					if (!is_array($v))
					{
						$arParametersValues[$key] = $v;
					}
					else
					{
						$keys1 = array_keys($v);
						foreach ($keys1 as $key1)
						{
							$arParametersValues[$key][$key1] = $v[$key1];
						}
					}
				}

				foreach ($arWorkflowParameters as $parameterKey => $arParameter)
				{
					$parameterKeyExt = "bizproc".$templateId."_".$parameterKey;

					$html = $documentService->GetFieldInputControl(
						BizProcDocument::generateDocumentComplexType($arParams["IBLOCK_TYPE_ID"],$arResult["IBLOCK_ID"]),
						$arParameter,
						array("Form" => "start_workflow_form1", "Field" => $parameterKeyExt),
						$arParametersValues[$parameterKey],
						false,
						true
					);

					$arTab2Fields[] = array(
						"id" => $parameterKeyExt.$bizProcIndex,
						"required" => $arParameter["Required"],
						"name" => $arParameter["Name"],
						"title" => $arParameter["Description"],
						"type" => "label",
						"value" => '<div>' . $html . '</div>',
					);
				}

				if(!empty($templateConstants) &&
					CIBlockRights::UserHasRightTo($arResult["IBLOCK_ID"], $arResult["IBLOCK_ID"], 'iblock_edit'))
				{
					$listTemplateId = array();
					$listTemplateId[$templateId]['ID'] = $templateId;
					$listTemplateId[$templateId]['NAME'] = $arDocumentState["TEMPLATE_NAME"];
					$arTab2Fields[] = array(
						"id" => "BIZPROC_CONSTANTS".$bizProcIndex,
						"name" => GetMessage("CT_BLEE_BIZPROC_CONSTANTS_LABLE"),
						"type" => "label",
						"value" => '<a href="javascript:void(0)" id="lists-fill-constants-'.$bizProcIndex.'"
							onclick="BX.Lists[\''.$jsClass.'\'].fillConstants('.CUtil::PhpToJSObject($listTemplateId).');">'.
							GetMessage("CT_BLEE_BIZPROC_CONSTANTS_FILL").'</a>',
					);
				}
			}

			$arEvents = CBPDocument::GetAllowableEvents($GLOBALS["USER"]->GetID(), $arCurrentUserGroups, $arDocumentState);
			if(count($arEvents))
			{
				$html = '';
				$html .= '<input type="hidden" name="bizproc_id_'.$bizProcIndex.'" value="'.$arDocumentState["ID"].'">';
				$html .= '<input type="hidden" name="bizproc_template_id_'.$bizProcIndex.'" value="'.
					$arDocumentState["TEMPLATE_ID"].'">';
				$html .= '<select name="bizproc_event_'.$bizProcIndex.'">';
				$html .= '<option value="">'.GetMessage("CT_BLEE_BIZPROC_RUN_CMD_NO").'</option>';
				foreach ($arEvents as $e)
				{
					$html .= '<option value="'.htmlspecialcharsbx($e["NAME"]).'"'.($_REQUEST["bizproc_event_".
						$bizProcIndex] == $e["NAME"]? " selected": "").'>'.htmlspecialcharsbx($e["TITLE"]).'</option>';
				}
				$html .='</select>';

				$arTab2Fields[] = array(
					"id" => "BIZPROC_RUN_CMD".$bizProcIndex,
					"name" => GetMessage("CT_BLEE_BIZPROC_RUN_CMD"),
					"type" => "label",
					"value" => $html,
				);
			}

			if($arDocumentState["ID"] <> '')
			{
				$arTasks = CBPDocument::GetUserTasksForWorkflow($GLOBALS["USER"]->GetID(), $arDocumentState["ID"]);
				if(count($arTasks) > 0)
				{
					$html = '';
					foreach($arTasks as $arTask)
					{
						$backUrl = CHTTP::urlAddParams(
							$APPLICATION->GetCurPageParam("", array($arResult["FORM_ID"]."_active_tab")),
							array($arResult["FORM_ID"]."_active_tab" => "tab_bp")
						);

						$url = CHTTP::urlAddParams(str_replace(
							array("#list_id#", "#section_id#", "#element_id#", "#task_id#", "#group_id#"),
							array($arResult["IBLOCK_ID"], intval($arResult["SECTION_ID"]),
								$arResult["ELEMENT_ID"], $arTask["ID"], $arParams["SOCNET_GROUP_ID"]),
							$arParams["~BIZPROC_TASK_URL"]
						),
							array("back_url" => $backUrl),
							array("skip_empty" => true, "encode" => true)
						);

						$html .= '<a href="'.htmlspecialcharsbx($url).'" title="'.strip_tags(
								$arTask["DESCRIPTION"]).'">'.$arTask["NAME"].'</a><br />';
					}

					$arTab2Fields[] = array(
						"id" => "BIZPROC_TASKS".$bizProcIndex,
						"name" => GetMessage("CT_BLEE_BIZPROC_TASKS"),
						"type" => "label",
						"value" => $html,
					);
				}
			}
		}
	}

	if(!$bizProcIndex)
	{
		$arTab2Fields[] = array(
			"id" => "BIZPROC_NO",
			"name" => GetMessage("CT_BLEE_BIZPROC_NA_LABEL"),
			"type" => "label",
			"value" => GetMessage("CT_BLEE_BIZPROC_NA")
		);
	}

	$cuctomHtml .= '<input type="hidden" name="bizproc_index" value="'.$bizProcIndex.'">';

	if($arResult["ELEMENT_ID"])
	{
		$bStartWorkflowPermission = CBPDocument::CanUserOperateDocument(
			CBPCanUserOperateOperation::StartWorkflow,
			$USER->GetID(),
			BizProcDocument::getDocumentComplexId($arParams["IBLOCK_TYPE_ID"], $arResult["ELEMENT_ID"]),
			array("AllUserGroups" => $arCurrentUserGroups, "DocumentStates" => $arDocumentStates,
				"WorkflowId" => $arDocumentState["TEMPLATE_ID"])
		);
		if($bStartWorkflowPermission)
		{
			$arTab2Fields[] = array(
				"id" => "BIZPROC_NEW",
				"name" => GetMessage("CT_BLEE_BIZPROC_NEW"),
				"type" => "section",
			);

			$backUrl = CHTTP::urlAddParams(
				$APPLICATION->GetCurPageParam("", array($arResult["FORM_ID"]."_active_tab")),
				array($arResult["FORM_ID"]."_active_tab" => "tab_bp")
			);

			$url = CHTTP::urlAddParams(str_replace(
					array("#list_id#", "#section_id#", "#element_id#", "#group_id#"),
					array($arResult["IBLOCK_ID"], intval($arResult["SECTION_ID"]), $arResult["ELEMENT_ID"],
						$arParams["SOCNET_GROUP_ID"]),
					$arParams["~BIZPROC_WORKFLOW_START_URL"]
				),
				array("back_url" => $backUrl, "sessid" => bitrix_sessid()),
				array("skip_empty" => true, "encode" => true)
			);

			$arTab2Fields[] = array(
				"id" => "BIZPROC_NEW_START",
				"name" => GetMessage("CT_BLEE_BIZPROC_START"),
				"type" => "custom",
				"colspan" => true,
				"value" => '<a href="'.htmlspecialcharsbx($url).'">'.GetMessage("CT_BLEE_BIZPROC_START").'</a>',
			);
		}
	}

	$arTabs[] = array("id"=>"tab_bp", "name"=>GetMessage("CT_BLEE_BIZPROC_TAB"), "icon"=>"", "fields"=>$arTab2Fields);
}

if(isset($arResult["RIGHTS"]))
{
	ob_start();
	IBlockShowRights(
		/*$entity_type=*/'element',
		/*$iblock_id=*/$arResult["IBLOCK_ID"],
		/*$id=*/$arResult["ELEMENT_ID"],
		/*$section_title=*/"",
		/*$variable_name=*/"RIGHTS",
		/*$arPossibleRights=*/$arResult["TASKS"],
		/*$arActualRights=*/$arResult["RIGHTS"],
		/*$bDefault=*/true,
		/*$bForceInherited=*/$arResult["ELEMENT_ID"] <= 0
	);
	$rights_html = ob_get_contents();
	ob_end_clean();

	$rights_fields = array(
		array(
			"id"=>"RIGHTS",
			"name"=>GetMessage("CT_BLEE_ACCESS_RIGHTS"),
			"type"=>"custom",
			"colspan"=>true,
			"value"=>$rights_html,
		),
	);
	$arTabs[] = array(
		"id"=>"tab_rights",
		"name"=>GetMessage("CT_BLEE_TAB_ACCESS"),
		"icon"=>"",
		"fields"=>$rights_fields,
	);
}

$cuctomHtml .= '<input type="hidden" name="action" id="action" value="">';
if(!$arParams["CAN_EDIT"])
	$cuctomHtml .= '<input type="button" value="'.GetMessage("CT_BLEE_FORM_CANCEL").
		'" name="cancel" onclick="window.location=\''.htmlspecialcharsbx(CUtil::addslashes(
				$arResult["~LIST_SECTION_URL"])).'\'" title="'.GetMessage("CT_BLEE_FORM_CANCEL_TITLE").'" />';

$lockStatus = CLists::isEnabledLockFeature($arResult["IBLOCK_ID"]) && $arResult["ELEMENT_ID"] && $arParams["CAN_EDIT"];
if ($lockStatus)
{
	$APPLICATION->IncludeComponent(
		"bitrix:lists.lock.status.widget",
		"",
		[
			"ELEMENT_ID" => $arResult["ELEMENT_ID"],
			"ELEMENT_NAME" => $arResult["IBLOCK"]["ELEMENT_NAME"]
		],
		$component, ["HIDE_ICONS" => "Y"]
	);
}

$APPLICATION->IncludeComponent(
	"bitrix:main.interface.form",
	"",
	array(
		"FORM_ID"=>$arResult["FORM_ID"],
		"TABS"=>$arTabs,
		"BUTTONS"=>array(
			"standard_buttons" => $arParams["CAN_EDIT"],
			"back_url"=>$arResult["BACK_URL"],
			"custom_html"=>$cuctomHtml,
		),
		"DATA"=>$arResult["FORM_DATA"],
		"SHOW_SETTINGS"=>"N",
		"THEME_GRID_ID"=>$arResult["GRID_ID"],
	),
	$component, array("HIDE_ICONS" => "Y")
);
?>

<div id="lists-notify-admin-popup" style="display:none;">
	<div id="lists-notify-admin-popup-content" class="lists-notify-admin-popup-content">
	</div>
</div>


<script type="text/javascript">
	BX.ready(function () {
		BX.Lists['<?=$jsClass?>'] = new BX.Lists.ListsElementEditClass({
			randomString: '<?=$arResult['RAND_STRING']?>',
			urlTabBp: '<?=$urlTabBp?>',
			iblockTypeId: '<?=$arParams["IBLOCK_TYPE_ID"]?>',
			iblockId: '<?=$arResult["IBLOCK_ID"]?>',
			elementId: '<?=$arResult["ELEMENT_ID"]?>',
			socnetGroupId: '<?=$socnetGroupId?>',
			sectionId: '<?= $sectionId ?>',
			isConstantsTuned: <?= $arResult["isConstantsTuned"] ? 'true' : 'false' ?>,
			elementUrl: '<?= $arResult["ELEMENT_URL"] ?>',
			sectionUrl: '<?= $arResult["LIST_SECTION_URL"] ?>',
			listAction: <?=\Bitrix\Main\Web\Json::encode($listAction)?>,
			lockStatus: <?=($lockStatus ? 'true' : 'false')?>
		});

		BX.message({
			CT_BLEE_BIZPROC_SAVE_BUTTON: '<?=GetMessageJS("CT_BLEE_BIZPROC_SAVE_BUTTON")?>',
			CT_BLEE_BIZPROC_CANCEL_BUTTON: '<?=GetMessageJS("CT_BLEE_BIZPROC_CANCEL_BUTTON")?>',
			CT_BLEE_BIZPROC_CONSTANTS_FILL_TITLE: '<?=GetMessageJS("CT_BLEE_BIZPROC_CONSTANTS_FILL_TITLE")?>',
			CT_BLEE_BIZPROC_NOTIFY_TITLE: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_TITLE")?>',
			CT_BLEE_BIZPROC_SELECT_STAFF_SET_RESPONSIBLE: '<?=GetMessageJS("CT_BLEE_BIZPROC_SELECT_STAFF_SET_RESPONSIBLE")?>',
			CT_BLEE_BIZPROC_NOTIFY_ADMIN_TEXT_ONE: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_ADMIN_TEXT_ONE")?>',
			CT_BLEE_BIZPROC_NOTIFY_ADMIN_TEXT_TWO: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_ADMIN_TEXT_TWO")?>',
			CT_BLEE_BIZPROC_NOTIFY_ADMIN_MESSAGE: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_ADMIN_MESSAGE")?>',
			CT_BLEE_BIZPROC_NOTIFY_ADMIN_MESSAGE_BUTTON: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_ADMIN_MESSAGE_BUTTON")?>',
			CT_BLEE_BIZPROC_NOTIFY_ADMIN_BUTTON_CLOSE: '<?=GetMessageJS("CT_BLEE_BIZPROC_NOTIFY_ADMIN_BUTTON_CLOSE")?>',
			CT_BLEE_DELETE_POPUP_TITLE: '<?=GetMessageJS("CT_BLEE_DELETE_POPUP_TITLE")?>',
			CT_BLEE_DELETE_POPUP_ACCEPT_BUTTON: '<?=GetMessageJS("CT_BLEE_DELETE_POPUP_ACCEPT_BUTTON")?>',
			CT_BLEE_DELETE_POPUP_CANCEL_BUTTON: '<?=GetMessageJS("CT_BLEE_DELETE_POPUP_CANCEL_BUTTON")?>'
		});

		if(BX["viewElementBind"])
		{
			BX.viewElementBind(
				'form_<?=$arResult["FORM_ID"]?>',
				{showTitle: true},
				{attr: 'data-bx-viewer'}
			);
		}
	});
</script>
<?else:?>
	<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$dir = $APPLICATION->GetCurUri();
$newArr = $arResult['FIELDS'];
foreach ($newArr as $key => $field) {
	// echo $field['ID'];
	foreach ($arResult['ELEMENT_PROPS'] as $keys => $value) {
		if($field['ID'] == $value['ID']){
			if(count($value['VALUES_LIST']) > 1){
				$newArr[$key]['HINT'] = $value['HINT'];
				foreach ($value['VALUES_LIST'] as $key_list => $value_list) {
					$newArr[$key]['VALUE'][] = $value_list;
					
				}
				
			}else{
				$newArr[$key]['VALUE'] = $value['VALUE'];
				$newArr[$key]['HINT'] = $value['HINT'];
			}
			
		}
	}
}

foreach ($newArr as $key => $field) {
	
	$newArr[$field['CODE']] = $newArr[$key];
	unset($newArr[$key]);
}
 /*echo '<pre>';
     print_r( $newArr['OBORUDOVANIE_STARYY']);
     echo '</pre>';

	echo '<pre>';
     print_r( $newArr['OBORUDOVANIE']);
     echo '</pre>';*/

$rsUser = CUser::GetByID($newArr['KTO_IZMENIL']['VALUE']);
													$arUser = $rsUser->Fetch();
													$p226 = $newArr['KTO_IZMENIL']['VALUE'];
													$newArr['KTO_IZMENIL']['VALUE'] = $arUser['PERSONAL_PAGER'];

?>
<main class="equip_page">
        <div class="container lk">
            <div class="row">
                <div class="col-9 px-0">
                    <div class="page">
                        <h1><?=$arResult['ELEMENT_FIELDS']['NAME']?></h1>
                        <div class="page__container">
                            <div class="employee__card">
                                <div class="employee__card__header">
                                   
                                    <div class="employee__card__info" style="margin-left:0;">
                                        <h2 class="employee__card__fio">Изменил: <?=$newArr['KTO_IZMENIL']['VALUE']?>  </h2>
                                        <p class="employee__card__title__job"><?if(!empty($arUser['WORK_POSITION'])){echo $arUser['WORK_POSITION'];}else{echo $arProps['POSITION']['VALUE'];}?></p>
                                    </div>
									
									<?if($_GET['list_id'] == 47):?>
										
										<!-- <a href="?mode=edit&list_id=48&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>" class="btn">Перемещение</a> -->
									<?endif;?>
                                    
                                </div>
                                <div class="employee__card__body">
                                    <div class="employee__card__container">
<?if($_GET['list_id'] == 94):?>
	
										<div class="employee__card__block">
											<?if(!empty($newArr['INVENTARNYY_NOMER']['VALUE'])):?>
												<?if($newArr['INVENTARNYY_NOMER']['VALUE'] == $newArr['INVENTARNYY_NOMER_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['INVENTARNYY_NOMER']['NAME']?></p>
															<?if(!empty($newArr['INVENTARNYY_NOMER']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['INVENTARNYY_NOMER']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['INVENTARNYY_NOMER']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['INVENTARNYY_NOMER']['NAME']?> был изменен</p>
															<?if(!empty($newArr['INVENTARNYY_NOMER']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['INVENTARNYY_NOMER']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['INVENTARNYY_NOMER_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['INVENTARNYY_NOMER']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['ZAVODSKOY_NOMER']['VALUE'])):?>
												<?if($newArr['ZAVODSKOY_NOMER']['VALUE'] == $newArr['ZAVODSKOY_NOMER_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['ZAVODSKOY_NOMER']['NAME']?></p>
															<?if(!empty($newArr['ZAVODSKOY_NOMER']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['ZAVODSKOY_NOMER']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['ZAVODSKOY_NOMER']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['ZAVODSKOY_NOMER']['NAME']?> был изменен</p>
															<?if(!empty($newArr['ZAVODSKOY_NOMER']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['ZAVODSKOY_NOMER']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['ZAVODSKOY_NOMER_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['ZAVODSKOY_NOMER']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['DATA_POSTANOVKI_NA_UCHET']['VALUE'])):?>
												<?if($newArr['DATA_POSTANOVKI_NA_UCHET']['VALUE'] == $newArr['DATA_POSTANOVKI_NA_UCHET_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['DATA_POSTANOVKI_NA_UCHET']['NAME']?></p>
															<?if(!empty($newArr['DATA_POSTANOVKI_NA_UCHET']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DATA_POSTANOVKI_NA_UCHET']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['DATA_POSTANOVKI_NA_UCHET']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['DATA_POSTANOVKI_NA_UCHET']['NAME']?> был изменен</p>
															<?if(!empty($newArr['DATA_POSTANOVKI_NA_UCHET']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DATA_POSTANOVKI_NA_UCHET']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['DATA_POSTANOVKI_NA_UCHET_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['DATA_POSTANOVKI_NA_UCHET']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['TERRITORIALNOE_PODRAZDELENIE']['VALUE'])):?>
												<?if($newArr['TERRITORIALNOE_PODRAZDELENIE']['VALUE'] == $newArr['TERRITORIALNOE_PODRAZDELENIE_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['NAME']?></p>
															<?if(!empty($newArr['TERRITORIALNOE_PODRAZDELENIE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['NAME']?> был изменен</p>
															<?if(!empty($newArr['TERRITORIALNOE_PODRAZDELENIE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['TERRITORIALNOE_PODRAZDELENIE_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['TERRITORIALNOE_PODRAZDELENIE']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['VALUE'])):?>
												<?if($newArr['OTDEL_V_PODRAZDELENII']['VALUE'] == $newArr['OTDEL_V_PODRAZDELENII_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['OTDEL_V_PODRAZDELENII']['NAME']?></p>
															<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['OTDEL_V_PODRAZDELENII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['OTDEL_V_PODRAZDELENII']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['OTDEL_V_PODRAZDELENII']['NAME']?> был изменен</p>
															<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['OTDEL_V_PODRAZDELENII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['OTDEL_V_PODRAZDELENII_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['OTDEL_V_PODRAZDELENII']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['MOL_PODRAZDELENIYA']['VALUE'])):?>
												<?
													
													$rsUser = CUser::GetByID($newArr['MOL_PODRAZDELENIYA']['VALUE']);
													$arUser = $rsUser->Fetch();
													$p226 = $newArr['MOL_PODRAZDELENIYA']['VALUE'];
													$newArr['MOL_PODRAZDELENIYA']['VALUE'] = $arUser['PERSONAL_PAGER'];

													// echo $newArr['MOL_PODRAZDELENIYA']['VALUE'];

													$rsUser2 = CUser::GetByID($newArr['MOL_PODRAZDELENIYA_STARYY']['VALUE']);
													
													$arUser2 = $rsUser2->Fetch();

												// 	echo "<pre>";
												// 	print_r($arUser2);
												// echo "</pre>";
													$p226 = $newArr['MOL_PODRAZDELENIYA_STARYY']['VALUE'];
													$newArr['MOL_PODRAZDELENIYA_STARYY']['VALUE'] = $arUser2['PERSONAL_PAGER'];

													
													
													?>
												<?if($newArr['MOL_PODRAZDELENIYA']['VALUE'] == $newArr['MOL_PODRAZDELENIYA_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['MOL_PODRAZDELENIYA']['NAME']?></p>
															<?if(!empty($newArr['MOL_PODRAZDELENIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['MOL_PODRAZDELENIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['MOL_PODRAZDELENIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<?
														
														
														
														?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['MOL_PODRAZDELENIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['MOL_PODRAZDELENIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['MOL_PODRAZDELENIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['MOL_PODRAZDELENIYA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['MOL_PODRAZDELENIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['TIP_OBORUDOVANIYA']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TIP_OBORUDOVANIYA']['IBLOCK_ID'], "ID"=>$newArr['TIP_OBORUDOVANIYA']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['TIP_OBORUDOVANIYA']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TIP_OBORUDOVANIYA']['IBLOCK_ID'], "ID"=>$newArr['TIP_OBORUDOVANIYA']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['TIP_OBORUDOVANIYA']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['TIP_OBORUDOVANIYA']['VALUE'] == $newArr['TIP_OBORUDOVANIYA']['VALUE']):?>
													<?
													
													

													?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['TIP_OBORUDOVANIYA']['NAME']?></p>
															<?if(!empty($newArr['TIP_OBORUDOVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['TIP_OBORUDOVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['TIP_OBORUDOVANIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['TIP_OBORUDOVANIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['TIP_OBORUDOVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['TIP_OBORUDOVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['TIP_OBORUDOVANIYA']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['TIP_OBORUDOVANIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['MOL_V_OTDELE']['VALUE'])):?>
												<?
													
													$rsUser = CUser::GetByID($newArr['MOL_V_OTDELE']['VALUE']);
													$arUser = $rsUser->Fetch();
													$p226 = $newArr['MOL_V_OTDELE']['VALUE'];
													$newArr['MOL_V_OTDELE']['VALUE'] = $arUser['PERSONAL_PAGER'];

													// echo $newArr['MOL_V_OTDELE']['VALUE'];

													$rsUser2 = CUser::GetByID($newArr['MOL_V_OTDELE_STARYY']['VALUE']);
													$arUser2 = $rsUser2->Fetch();
													$p226 = $newArr['MOL_V_OTDELE_STARYY']['VALUE'];
													$newArr['MOL_V_OTDELE_STARYY']['VALUE'] = $arUser2['PERSONAL_PAGER'];
													?>
												<?if($newArr['MOL_V_OTDELE']['VALUE'] == $newArr['MOL_V_OTDELE_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['MOL_V_OTDELE']['NAME']?></p>
															<?if(!empty($newArr['MOL_V_OTDELE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['MOL_V_OTDELE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['MOL_V_OTDELE']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<?
														
														
														
														?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['MOL_V_OTDELE']['NAME']?> был изменен</p>
															<?if(!empty($newArr['MOL_V_OTDELE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['MOL_V_OTDELE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['MOL_V_OTDELE_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['MOL_V_OTDELE']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['VALUE'])):?>
												<?if($newArr['OTDEL_V_PODRAZDELENII']['VALUE'] == $newArr['OTDEL_V_PODRAZDELENII_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['OTDEL_V_PODRAZDELENII']['NAME']?></p>
															<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['OTDEL_V_PODRAZDELENII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['OTDEL_V_PODRAZDELENII']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['OTDEL_V_PODRAZDELENII']['NAME']?> был изменен</p>
															<?if(!empty($newArr['OTDEL_V_PODRAZDELENII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['OTDEL_V_PODRAZDELENII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['OTDEL_V_PODRAZDELENII_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['OTDEL_V_PODRAZDELENII']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['VALUE'])):?>
												<?if($newArr['DATA_PEREMESHCHENIYA']['VALUE'] == $newArr['DATA_PEREMESHCHENIYA_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['DATA_PEREMESHCHENIYA']['NAME']?></p>
															<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DATA_PEREMESHCHENIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['DATA_PEREMESHCHENIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['DATA_PEREMESHCHENIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DATA_PEREMESHCHENIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['DATA_PEREMESHCHENIYA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['DATA_PEREMESHCHENIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['STATUS_OBORUDOVANIYA']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['STATUS_OBORUDOVANIYA']['IBLOCK_ID'], "ID"=>$newArr['STATUS_OBORUDOVANIYA']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['STATUS_OBORUDOVANIYA']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['STATUS_OBORUDOVANIYA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['STATUS_OBORUDOVANIYA_STARYY']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['STATUS_OBORUDOVANIYA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['STATUS_OBORUDOVANIYA']['VALUE'] == $newArr['STATUS_OBORUDOVANIYA_STARYY']['VALUE']):?>
													<?
													
													

													?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['STATUS_OBORUDOVANIYA']['NAME']?></p>
															<?if(!empty($newArr['STATUS_OBORUDOVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['STATUS_OBORUDOVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['STATUS_OBORUDOVANIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['STATUS_OBORUDOVANIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['STATUS_OBORUDOVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['STATUS_OBORUDOVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['STATUS_OBORUDOVANIYA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['STATUS_OBORUDOVANIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE'])):?>
												<?if($newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE'] == $newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['NAME']?></p>
															<?if(!empty($newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['NAME']?> был изменен</p>
															<?if(!empty($newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['STOIMOST_PERVONACHALNAYA_BEZ_NDS']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['NEOBKHODIMOST_POVERKI']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['NEOBKHODIMOST_POVERKI']['IBLOCK_ID'], "ID"=>$newArr['NEOBKHODIMOST_POVERKI']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['NEOBKHODIMOST_POVERKI']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['POVERKA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['POVERKA_STARYY']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['POVERKA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['NEOBKHODIMOST_POVERKI']['VALUE'] == $newArr['POVERKA_STARYY']['VALUE']):?>
													<?
													
													

													?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['NEOBKHODIMOST_POVERKI']['NAME']?></p>
															<?if(!empty($newArr['NEOBKHODIMOST_POVERKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NEOBKHODIMOST_POVERKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['NEOBKHODIMOST_POVERKI']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['NEOBKHODIMOST_POVERKI']['NAME']?> был изменен</p>
															<?if(!empty($newArr['NEOBKHODIMOST_POVERKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NEOBKHODIMOST_POVERKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['POVERKA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['NEOBKHODIMOST_POVERKI']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['ATTESTATSIYA']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['ATTESTATSIYA']['IBLOCK_ID'], "ID"=>$newArr['ATTESTATSIYA']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['ATTESTATSIYA']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['ATTESTATSIYA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['ATTESTATSIYA_STARYY']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['ATTESTATSIYA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['ATTESTATSIYA']['VALUE'] == $newArr['ATTESTATSIYA_STARYY']['VALUE']):?>
													
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['ATTESTATSIYA']['NAME']?></p>
															<?if(!empty($newArr['ATTESTATSIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['ATTESTATSIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['ATTESTATSIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['ATTESTATSIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['ATTESTATSIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['ATTESTATSIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['ATTESTATSIYA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['ATTESTATSIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['PERIODICHNOST_ATTESTATSII']['VALUE'])):?>
												<?if($newArr['PERIODICHNOST_ATTESTATSII']['VALUE'] == $newArr['DATA_ATTESTATSII_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['PERIODICHNOST_ATTESTATSII']['NAME']?></p>
															<?if(!empty($newArr['PERIODICHNOST_ATTESTATSII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['PERIODICHNOST_ATTESTATSII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['PERIODICHNOST_ATTESTATSII']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['PERIODICHNOST_ATTESTATSII']['NAME']?> был изменен</p>
															<?if(!empty($newArr['PERIODICHNOST_ATTESTATSII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['PERIODICHNOST_ATTESTATSII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['DATA_ATTESTATSII_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['PERIODICHNOST_ATTESTATSII']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['KALIBROVKA']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['KALIBROVKA']['IBLOCK_ID'], "ID"=>$newArr['KALIBROVKA']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['KALIBROVKA']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['KALIBROVKA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['KALIBROVKA_STARYY']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['KALIBROVKA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['KALIBROVKA']['VALUE'] == $newArr['KALIBROVKA_STARYY']['VALUE']):?>
													
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['KALIBROVKA']['NAME']?></p>
															<?if(!empty($newArr['KALIBROVKA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['KALIBROVKA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['KALIBROVKA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['KALIBROVKA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['KALIBROVKA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['KALIBROVKA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['KALIBROVKA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['KALIBROVKA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['LIZING']['VALUE'])):?>
												<?if($newArr['LIZING']['VALUE'] == $newArr['LIZING_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['LIZING']['NAME']?></p>
															<?if(!empty($newArr['LIZING']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['LIZING']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['LIZING']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['LIZING']['NAME']?> был изменен</p>
															<?if(!empty($newArr['LIZING']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['LIZING']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['LIZING_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['LIZING']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['KOMENTY']['VALUE'])):?>
												<?if($newArr['KOMENTY']['VALUE'] == $newArr['KOMENTY_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['KOMENTY']['NAME']?></p>
															<?if(!empty($newArr['KOMENTY']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['KOMENTY']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['KOMENTY']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['KOMENTY']['NAME']?> был изменен</p>
															<?if(!empty($newArr['KOMENTY']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['KOMENTY']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['KOMENTY_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['KOMENTY']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['NOMER_POVERKI']['VALUE'])):?>
												<?if($newArr['NOMER_POVERKI']['VALUE'] == $newArr['NOMER_POVERKI_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['NOMER_POVERKI']['NAME']?></p>
															<?if(!empty($newArr['NOMER_POVERKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_POVERKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['NOMER_POVERKI']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['NOMER_POVERKI']['NAME']?> был изменен</p>
															<?if(!empty($newArr['NOMER_POVERKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_POVERKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['NOMER_POVERKI_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['NOMER_POVERKI']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['NOMER_ATESTATSII']['VALUE'])):?>
												<?if($newArr['NOMER_ATESTATSII']['VALUE'] == $newArr['NOMER_ATESTATSII_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['NOMER_ATESTATSII']['NAME']?></p>
															<?if(!empty($newArr['NOMER_ATESTATSII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_ATESTATSII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['NOMER_ATESTATSII']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['NOMER_ATESTATSII']['NAME']?> был изменен</p>
															<?if(!empty($newArr['NOMER_ATESTATSII']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_ATESTATSII']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['NOMER_ATESTATSII_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['NOMER_ATESTATSII']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['NOMER_KALIBROVKI']['VALUE'])):?>
												<?if($newArr['NOMER_KALIBROVKI']['VALUE'] == $newArr['NOMER_KALIBROVKI_STARYY']['VALUE']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['NOMER_KALIBROVKI']['NAME']?></p>
															<?if(!empty($newArr['NOMER_KALIBROVKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_KALIBROVKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['NOMER_KALIBROVKI']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['NOMER_KALIBROVKI']['NAME']?> был изменен</p>
															<?if(!empty($newArr['NOMER_KALIBROVKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['NOMER_KALIBROVKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['NOMER_KALIBROVKI_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['NOMER_KALIBROVKI']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['SOSTAVNOE']['VALUE'])):?>
												<?if($newArr['SOSTAVNOE']['VALUE']['TEXT'] == $newArr['SOSTAVNOE_STARYY']['VALUE']['TEXT']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['SOSTAVNOE']['NAME']?></p>
															<?if(!empty($newArr['SOSTAVNOE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['SOSTAVNOE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['SOSTAVNOE']['VALUE']['TEXT']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['SOSTAVNOE']['NAME']?> был изменен</p>
															<?if(!empty($newArr['SOSTAVNOE']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['SOSTAVNOE']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['SOSTAVNOE_STARYY']['VALUE']['TEXT']?></p>
																<p style="color:green">Новое значение: <?=$newArr['SOSTAVNOE']['VALUE']['TEXT']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['V_SOSTAV_VKHODIT']['VALUE'])):?>
												<?if($newArr['V_SOSTAV_VKHODIT']['VALUE']['TEXT'] == $newArr['V_SOSTAV_VKHODIT_STARYY']['VALUE']['TEXT']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['V_SOSTAV_VKHODIT']['NAME']?></p>
															<?if(!empty($newArr['V_SOSTAV_VKHODIT']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['V_SOSTAV_VKHODIT']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['V_SOSTAV_VKHODIT']['VALUE']['TEXT']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['V_SOSTAV_VKHODIT']['NAME']?> был изменен</p>
															<?if(!empty($newArr['V_SOSTAV_VKHODIT']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['V_SOSTAV_VKHODIT']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['V_SOSTAV_VKHODIT_STARYY']['VALUE']['TEXT']?></p>
																<p style="color:green">Новое значение: <?=$newArr['V_SOSTAV_VKHODIT']['VALUE']['TEXT']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['DOGOVOR_OBSLUZHIVANIYA']['VALUE'])):?>
												<?if($newArr['DOGOVOR_OBSLUZHIVANIYA']['VALUE']['TEXT'] == $newArr['DOGOVOR_OBSLUZHIVANIYA_STARYY']['VALUE']['TEXT']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['NAME']?></p>
															<?if(!empty($newArr['DOGOVOR_OBSLUZHIVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['VALUE']['TEXT']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['DOGOVOR_OBSLUZHIVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['DOGOVOR_OBSLUZHIVANIYA_STARYY']['VALUE']['TEXT']?></p>
																<p style="color:green">Новое значение: <?=$newArr['DOGOVOR_OBSLUZHIVANIYA']['VALUE']['TEXT']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['DOKUMENT_POSTAVKI']['VALUE'])):?>
												<?if($newArr['DOKUMENT_POSTAVKI']['VALUE']['TEXT'] == $newArr['DOKUMENT_POSTAVKI_STARYY']['VALUE']['TEXT']):?>
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['DOKUMENT_POSTAVKI']['NAME']?></p>
															<?if(!empty($newArr['DOKUMENT_POSTAVKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DOKUMENT_POSTAVKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['DOKUMENT_POSTAVKI']['VALUE']['TEXT']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['DOKUMENT_POSTAVKI']['NAME']?> был изменен</p>
															<?if(!empty($newArr['DOKUMENT_POSTAVKI']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['DOKUMENT_POSTAVKI']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['DOKUMENT_POSTAVKI_STARYY']['VALUE']['TEXT']?></p>
																<p style="color:green">Новое значение: <?=$newArr['DOKUMENT_POSTAVKI']['VALUE']['TEXT']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											<?if(!empty($newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'])):?>
												<?
													
													$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['IBLOCK_ID'], "ID"=>$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE']));
													while($enum_fields = $property_enums->GetNext())
													{
														$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] = $enum_fields["VALUE"];
													}

													$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY']['VALUE']));
													while($enum_fields2 = $property_enums2->GetNext())
													{
														$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
													}

													?>
												<?if($newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE'] == $newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY']['VALUE']):?>
													
													<div class="employee__card__item">
														<p class="employee__card__title"><?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['NAME']?></p>
															<?if(!empty($newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
																<p><?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE']?></p>
														</div>
													</div>
												<?else:?>
													<div class="employee__card__item">
														<p class="employee__card__title" style="color:red"><?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['NAME']?> был изменен</p>
															<?if(!empty($newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['HINT'])):?>
																<div class="equipment_hint">
																	<span class="equip_help"><?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['HINT']?></span>
																</div>
															<?endif;?>													
														
															
														<div class="employee__card__item__option">
															<div class="option_changed">
																<p style="color:red">Старое значение: <?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA_STARYY']['VALUE']?></p>
																<p style="color:green">Новое значение: <?=$newArr['PRIZNAK_METROLOGICHESKOGO_OBSLUZHIVANIYA']['VALUE']?></p>
															</div>	
														</div>
													</div>
												<?endif;?>	
											<?endif;?>
											
											
											
											
								
											<div class="doc_wrapper" style="display: flex;align-items: flex-start; width: 100%;">
											<?if(!empty($newArr['UF_FILE_ID']['VALUE']) || !empty($newArr['UF_FOLDER_ID']['VALUE']) || !empty($newArr['UF_FILE_ID_STARYY']['VALUE']) || !empty($newArr['UF_FOLDER_ID_STARYY']['VALUE'])):?>
																<h2>Прикрепленные документы</h2>
											<?endif;?>					

																<?if(in_array(1, $arGroups)  || in_array(20, $arGroups)):?>
																	
																		<!-- <button id="button" class="file_add_popup" data-page="<?=$arResult['ELEMENT_FIELDS']['ID']?>" data-iblock="47">Добавить документы</button> -->
																	
																<?endif;?>
															</div>
											<?if(!empty($newArr['UF_FILE_ID']['VALUE']) || !empty($newArr['UF_FOLDER_ID']['VALUE']) || !empty($newArr['UF_FILE_ID_STARYY']['VALUE']) || !empty($newArr['UF_FOLDER_ID_STARYY']['VALUE'])):?>
															
													<?

														if ( !Loader::includeModule('disk') )
														{
														throw new Exception("Не подклчюен модуль диска");
														}
														$driver = Disk\Driver::getInstance();
														$storage = \Bitrix\Disk\Storage::loadById(1467);//знаем идентификатор хранилища 
													$securityContext = $driver->getFakeSecurityContext();
													// $securityContext = $storage->getCurrentUserSecurityContext();

														
													$files = array();
													$folder = array();
													$arFolderList = [];
													$arFileList = [];
													$folder_test =[];
													if(!empty($newArr['UF_FILE_ID']['VALUE']) && $newArr['UF_FILE_ID']['VALUE'][0] != 0 ):
														if(!is_array($newArr['UF_FILE_ID']['VALUE'])){
															$newArr['UF_FILE_ID']['VALUE'] = array(0 => $newArr['UF_FILE_ID']['VALUE']);
														}
														
															foreach ($newArr['UF_FILE_ID']['VALUE'] as $key => $value) {
																	$files[] = \Bitrix\Disk\File::loadById($value, array('STORAGE'));
																
															}
														endif;?>
														<?if(!empty($newArr['UF_FOLDER_ID']['VALUE'])):
														if(!is_array($newArr['UF_FOLDER_ID']['VALUE'])){
															$newArr['UF_FOLDER_ID']['VALUE'] = array(0 => $newArr['UF_FOLDER_ID']['VALUE']);
														}
															foreach ($newArr['UF_FOLDER_ID']['VALUE'] as $key => $value) {
														?>

															<?$folder[] = \Bitrix\Disk\Folder::loadById($value, array('STORAGE'));
															
															}	
															?>
														
														<?endif;?>
														<?
														if(!empty($folder)){
															foreach ($folder as $key => $value) {
															
																getRecurciveFolder( $value, $securityContext, $arFolderList );
																}
														}
													// 	echo "<pre>";
													//    print_r($arFolderList);
													//    echo "</pre>";
														
														
														foreach ($arFolderList as $key => $folderList) {
															foreach ($folderList['CHILDRENS'] as $key => $file) {
																
															
															if(empty($file['IS_FOLDER'])){
																$arFileList[] = $file;
															}
															if(!empty($file['CHILDRENS'])){
																foreach ($file['CHILDRENS'] as $key => $value) {
																	if(empty($value['IS_FOLDER'])){
																		$arFileList[] = $value;
																	}
																	if(!empty($value['CHILDRENS'])){
																		foreach ($value['CHILDRENS'] as $key => $second_file) {
																			if(empty($second_file['IS_FOLDER'])){
																				$arFileList[] = $second_file;
																			}
																		}
																	}
																}
															}
														}
														}
													// 	if ( $files[0] instanceof Disk\File )
													// {
														
													//    $urlManager = Disk\Driver::getInstance()->getUrlManager();
													//    $downloadUrl = $urlManager->getUrlForShowFile($files[0]);
													// }

													//    echo "<pre>";
													//    print_r($arFolderList);
													//    echo "</pre>";
														?>
														<div class="files_container doc_wrapper">
															<?foreach ($files as $key => $file) {
																$file_type = substr(strrchr($file->getName(), "."), 1);
																$userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
													// 			   echo "<pre>";
													//    print_r($file);
													//    echo "</pre>";
																$category = CUserFieldEnum::GetList(array(), array("ID" => $userFieldsObject['UF_FILE_CATEG']['VALUE']))->GetNext()["VALUE"];
																?>
																<a href="/disk/downloadFile/<?=$file->getId();?>/?&ncc=1&filename=<?=$file->getName();?>" class="card__document">
																								<div class="document__img ui-icon ui-icon-file ui-icon-file-pdf">
																								
																								<?if($file_type == 'xls'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/XLSIcons.svg" />
																								<?elseif($file_type == 'doc' || $file_type == 'docx'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIcons.svg" />
																								<?elseif($file_type == 'pdf'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/PDFIcon.svg" />
																								<?else:?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIconV2.svg" />
																								<?endif;?>
																								</div>
																								<div class="document__block">
																								<?if(!empty($category) && $category != 'Обычная'):?>
																									<div class="document__header">
																										<p class="orange"><?=$category?></p>
																									</div>
																									<?endif;?>
																									<div class="document__body">
																										<p><?=$file->getName();?></p>
																									</div>
																								</div>
																								</a>
																<!-- <a href="/disk/downloadFile/<?=$file->getId();?>/?&ncc=1&filename=<?=$file->getName();?>" class="file_view"><?=$file->getName();?></a> -->
															<?}?>
														<?if(!empty($arFileList)):?>

																<?foreach ($arFileList as $key => $file) {
																$file_type = substr(strrchr($file['NAME'], "."), 1);
																			// $userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
																// 			   echo "<pre>";
																//    print_r($file);
																//    echo "</pre>";
																$category = CUserFieldEnum::GetList(array(), array("ID" => $file['UF_FILE_CATEG']['VALUE']))->GetNext()["VALUE"];
																?>
																<a href="/disk/downloadFile/<?=$file['ID'];?>/?&ncc=1&filename=<?=$file['NAME'];?>" class="card__document">
																								<div class="document__img ui-icon ui-icon-file ui-icon-file-pdf">
																								
																								<?if($file_type == 'xls'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/XLSIcons.svg" />
																								<?elseif($file_type == 'doc' || $file_type == 'docx'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIcons.svg" />
																								<?elseif($file_type == 'pdf'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/PDFIcon.svg" />
																								<?else:?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIconV2.svg" />
																								<?endif;?>
																								</div>
																								<div class="document__block">
																								<?if(!empty($category) && $category != 'Обычная'):?>
																									<div class="document__header">
																										<p class="orange"><?=$category?></p>
																									</div>
																									<?endif;?>
																									<div class="document__body">
																										<p><?=$file['NAME'];?></p>
																									</div>
																								</div>
																								</a>
																<!-- <a href="/disk/downloadFile/<?=$file['ID'];?>/?&ncc=1&filename=<?=$file['NAME'];?>" class="file_view"><?=$file['NAME'];?></a> -->
																<?}?>
															
														<?endif;?>
													</div>
													</div>
											<?endif;?>										
											
<?elseif($_GET['list_id'] == 95):?>
	
	<div class="employee__card__block">
		
		<?if(!empty($newArr['OBORUDOVANIE']['VALUE'])):?>
			<?
				
				$rsElement = CIBlockElement::GetList(
					$arOrder  = array("SORT" => "ASC"),
					$arFilter = array(
						"ACTIVE"    => "Y",
						"ID" => $newArr['OBORUDOVANIE']['VALUE'],
					),
					false,
					false,
					$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL", "PROPERTY_*")
				);
				while($arElement = $rsElement->fetch()) {
					$newArr['OBORUDOVANIE']['VALUE'] = $arElement['NAME'];
					$url = $arElement['DETAIL_PAGE_URL'];
				}

				$rsElement2 = CIBlockElement::GetList(
					$arOrder  = array("SORT" => "ASC"),
					$arFilter = array(
						"ACTIVE"    => "Y",
						"ID" => $newArr['OBORUDOVANIE_STARYY']['VALUE'],
					),
					false,
					false,
					$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL", "PROPERTY_*")
				);
				while($arElement2 = $rsElement2->fetch()) {
					$newArr['OBORUDOVANIE_STARYY']['VALUE'] = $arElement2['NAME'];
					$url2 = $arElement2['DETAIL_PAGE_URL'];
				}

				
				
				?>
			<?if($newArr['OBORUDOVANIE']['VALUE'] == $newArr['OBORUDOVANIE_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['OBORUDOVANIE']['NAME']?></p>
						<?if(!empty($newArr['OBORUDOVANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OBORUDOVANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['OBORUDOVANIE']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<?
					
					
					
					?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['OBORUDOVANIE']['NAME']?> был изменен</p>
						<?if(!empty($newArr['OBORUDOVANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OBORUDOVANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['OBORUDOVANIE_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['OBORUDOVANIE']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		
		
		<?if(!empty($newArr['TIP_OBSLUZHIVANIYA']['VALUE'])):?>
			<?
				
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TIP_OBSLUZHIVANIYA']['IBLOCK_ID'], "ID"=>$newArr['TIP_OBSLUZHIVANIYA']['VALUE']));
				while($enum_fields = $property_enums->GetNext())
				{
					$newArr['TIP_OBSLUZHIVANIYA']['VALUE'] = $enum_fields["VALUE"];
				}

				$property_enums2 = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TIP_OBSLUZHIVANIYA_STARYY']['IBLOCK_ID'], "ID"=>$newArr['TIP_OBSLUZHIVANIYA_STARYY']['VALUE']));
				while($enum_fields2 = $property_enums2->GetNext())
				{
					$newArr['TIP_OBSLUZHIVANIYA_STARYY']['VALUE'] = $enum_fields2["VALUE"];
				}

				?>
			<?if($newArr['TIP_OBSLUZHIVANIYA']['VALUE'] == $newArr['TIP_OBSLUZHIVANIYA_STARYY']['VALUE']):?>
				<?
				
				

				?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['TIP_OBSLUZHIVANIYA']['NAME']?></p>
						<?if(!empty($newArr['TIP_OBSLUZHIVANIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['TIP_OBSLUZHIVANIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['TIP_OBSLUZHIVANIYA']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['TIP_OBSLUZHIVANIYA']['NAME']?> был изменен</p>
						<?if(!empty($newArr['TIP_OBSLUZHIVANIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['TIP_OBSLUZHIVANIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['TIP_OBSLUZHIVANIYA_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['TIP_OBSLUZHIVANIYA']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>
		<?endif;?>		
		<?if(!empty($newArr['DATA_POVERKI']['VALUE'])):?>
			<?if($newArr['DATA_POVERKI']['VALUE'] == $newArr['DATA_POVERKI_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['DATA_POVERKI']['NAME']?></p>
						<?if(!empty($newArr['DATA_POVERKI']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['DATA_POVERKI']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['DATA_POVERKI']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['DATA_POVERKI']['NAME']?> был изменен</p>
						<?if(!empty($newArr['DATA_POVERKI']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['DATA_POVERKI']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['DATA_POVERKI_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['DATA_POVERKI']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['SROK_DEYSTVIYA']['VALUE'])):?>
			<?if($newArr['SROK_DEYSTVIYA']['VALUE'] == $newArr['SROK_DEYSTVIYA_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['SROK_DEYSTVIYA']['NAME']?></p>
						<?if(!empty($newArr['SROK_DEYSTVIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['SROK_DEYSTVIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['SROK_DEYSTVIYA']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['SROK_DEYSTVIYA']['NAME']?> был изменен</p>
						<?if(!empty($newArr['SROK_DEYSTVIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['SROK_DEYSTVIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['SROK_DEYSTVIYA_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['SROK_DEYSTVIYA']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['OPISANIE']['VALUE'])):?>
			<?if($newArr['OPISANIE']['VALUE'] == $newArr['OPISANIE_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['OPISANIE']['NAME']?></p>
						<?if(!empty($newArr['OPISANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OPISANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['OPISANIE']['VALUE']['TEXT']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['OPISANIE']['NAME']?> был изменен</p>
						<?if(!empty($newArr['OPISANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OPISANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['OPISANIE_STARYY']['VALUE']['TEXT']?></p>
							<p style="color:green">Новое значение: <?=$newArr['OPISANIE']['VALUE']['TEXT']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>
				
		<?endif;?>
		<?if(!empty($newArr['FOTO']['VALUE'])):?>
			<?if($newArr['FOTO']['VALUE'] == $newArr['FOTO_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['FOTO']['NAME']?></p>
						<?if(!empty($newArr['FOTO']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FOTO']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['FOTO']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['FOTO']['NAME']?> был изменен</p>
						<?if(!empty($newArr['FOTO']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FOTO']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['FOTO_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['FOTO']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>
				
		<?endif;?>
						</div>
		
<?elseif($_GET['list_id'] == 96):?>
	
	<div class="employee__card__block">
	<?if(!empty($newArr['OBORUDOVANIE']['VALUE'])):?>
			<?
				
				$rsElement = CIBlockElement::GetList(
					$arOrder  = array("SORT" => "ASC"),
					$arFilter = array(
						"ACTIVE"    => "Y",
						"ID" => $newArr['OBORUDOVANIE']['VALUE'],
					),
					false,
					false,
					$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL", "PROPERTY_*")
				);
				while($arElement = $rsElement->fetch()) {
					$newArr['OBORUDOVANIE']['VALUE'] = $arElement['NAME'];
					$url = $arElement['DETAIL_PAGE_URL'];
				}

				$rsElement2 = CIBlockElement::GetList(
					$arOrder  = array("SORT" => "ASC"),
					$arFilter = array(
						"ACTIVE"    => "Y",
						"ID" => $newArr['OBORUDOVANIE_STARYY']['VALUE'],
					),
					false,
					false,
					$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL", "PROPERTY_*")
				);
				while($arElement2 = $rsElement2->fetch()) {
					$newArr['OBORUDOVANIE_STARYY']['VALUE'] = $arElement2['NAME'];
					$url2 = $arElement2['DETAIL_PAGE_URL'];
				}

				
				
				?>
			<?if($newArr['OBORUDOVANIE']['VALUE'] == $newArr['OBORUDOVANIE_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['OBORUDOVANIE']['NAME']?></p>
						<?if(!empty($newArr['OBORUDOVANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OBORUDOVANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['OBORUDOVANIE']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<?
					
					
					
					?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['OBORUDOVANIE']['NAME']?> был изменен</p>
						<?if(!empty($newArr['OBORUDOVANIE']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['OBORUDOVANIE']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['OBORUDOVANIE_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['OBORUDOVANIE']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['VALUE'])):?>
			
			<?if($newArr['DATA_PEREMESHCHENIYA']['VALUE'] == $newArr['DATA_PEREMESHCHENIYA_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['DATA_PEREMESHCHENIYA']['NAME']?></p>
						<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['DATA_PEREMESHCHENIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['DATA_PEREMESHCHENIYA']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['DATA_PEREMESHCHENIYA']['NAME']?> был изменен</p>
						<?if(!empty($newArr['DATA_PEREMESHCHENIYA']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['DATA_PEREMESHCHENIYA']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['DATA_PEREMESHCHENIYA_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['DATA_PEREMESHCHENIYA']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['MOL']['VALUE'])):?>
			<?
				
				$rsUser = CUser::GetByID($newArr['MOL']['VALUE']);
				$arUser = $rsUser->Fetch();
				$p226 = $newArr['MOL']['VALUE'];
				$newArr['MOL']['VALUE'] = $arUser['PERSONAL_PAGER'];

				// echo $newArr['MOL']['VALUE'];

				$rsUser2 = CUser::GetByID($newArr['MOL_STARYY']['VALUE']);
				
				$arUser2 = $rsUser2->Fetch();

			// 	echo "<pre>";
			// 	print_r($arUser2);
			// echo "</pre>";
				$p226 = $newArr['MOL_STARYY']['VALUE'];
				$newArr['MOL_STARYY']['VALUE'] = $arUser2['PERSONAL_PAGER'];

				
				
				?>
			<?if($newArr['MOL']['VALUE'] == $newArr['MOL_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['MOL']['NAME']?></p>
						<?if(!empty($newArr['MOL']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['MOL']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['MOL']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<?
					
					
					
					?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['MOL']['NAME']?> был изменен</p>
						<?if(!empty($newArr['MOL']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['MOL']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['MOL_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['MOL']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['FIO_KTO_PEREDAET']['VALUE'])):?>
			<?
				
				$rsUser = CUser::GetByID($newArr['FIO_KTO_PEREDAET']['VALUE']);
				$arUser = $rsUser->Fetch();
				$p226 = $newArr['FIO_KTO_PEREDAET']['VALUE'];
				$newArr['FIO_KTO_PEREDAET']['VALUE'] = $arUser['PERSONAL_PAGER'];

				// echo $newArr['MOL']['VALUE'];

				$rsUser2 = CUser::GetByID($newArr['FIO_KTO_PEREDAET_STARYY']['VALUE']);
				
				$arUser2 = $rsUser2->Fetch();

			// 	echo "<pre>";
			// 	print_r($arUser2);
			// echo "</pre>";
				$p226 = $newArr['FIO_KTO_PEREDAET_STARYY']['VALUE'];
				$newArr['FIO_KTO_PEREDAET_STARYY']['VALUE'] = $arUser2['PERSONAL_PAGER'];

				
				
				?>
			<?if($newArr['FIO_KTO_PEREDAET']['VALUE'] == $newArr['FIO_KTO_PEREDAET_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['FIO_KTO_PEREDAET']['NAME']?></p>
						<?if(!empty($newArr['FIO_KTO_PEREDAET']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FIO_KTO_PEREDAET']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['FIO_KTO_PEREDAET']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<?
					
					
					
					?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['FIO_KTO_PEREDAET']['NAME']?> был изменен</p>
						<?if(!empty($newArr['FIO_KTO_PEREDAET']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FIO_KTO_PEREDAET']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['FIO_KTO_PEREDAET_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['FIO_KTO_PEREDAET']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['FIO']['VALUE'])):?>
			<?
				
				$rsUser = CUser::GetByID($newArr['FIO']['VALUE']);
				$arUser = $rsUser->Fetch();
				$p226 = $newArr['FIO']['VALUE'];
				$newArr['FIO']['VALUE'] = $arUser['PERSONAL_PAGER'];

				// echo $newArr['MOL']['VALUE'];

				$rsUser2 = CUser::GetByID($newArr['FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY']['VALUE']);
				
				$arUser2 = $rsUser2->Fetch();

			// 	echo "<pre>";
			// 	print_r($arUser2);
			// echo "</pre>";
				$p226 = $newArr['FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY']['VALUE'];
				$newArr['FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY']['VALUE'] = $arUser2['PERSONAL_PAGER'];

				
				
				?>
			<?if($newArr['FIO']['VALUE'] == $newArr['FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['FIO']['NAME']?></p>
						<?if(!empty($newArr['FIO']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FIO']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['FIO']['VALUE']?></p>
					</div>
				</div>
			<?else:?>
				<?
					
					
					
					?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['FIO']['NAME']?> был изменен</p>
						<?if(!empty($newArr['FIO']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['FIO']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['FIO_ZA_KEM_ZAKREPLYAETSYA_STARYY']['VALUE']?></p>
							<p style="color:green">Новое значение: <?=$newArr['FIO']['VALUE']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		<?if(!empty($newArr['KOMMENTARIY']['VALUE'])):?>
			
			<?if($newArr['KOMMENTARIY']['VALUE'] == $newArr['KOMMENTARIY_STARYY']['VALUE']):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['KOMMENTARIY']['NAME']?></p>
						<?if(!empty($newArr['KOMMENTARIY']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['KOMMENTARIY']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$newArr['KOMMENTARIY']['VALUE']['TEXT']?></p>
					</div>
				</div>
			<?else:?>
				<div class="employee__card__item">
					<p class="employee__card__title" style="color:red"><?=$newArr['KOMMENTARIY']['NAME']?> был изменен</p>
						<?if(!empty($newArr['KOMMENTARIY']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['KOMMENTARIY']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<div class="option_changed">
							<p style="color:red">Старое значение: <?=$newArr['KOMMENTARIY_STARYY']['VALUE']['TEXT']?></p>
							<p style="color:green">Новое значение: <?=$newArr['KOMMENTARIY']['VALUE']['TEXT']?></p>
						</div>	
					</div>
				</div>
			<?endif;?>	
		<?endif;?>
		
		
		
		
						</div>
											
<?elseif($_GET['list_id'] == 52):?>
										<div class="employee__card__block">
											<?if(!empty($newArr['PROPERTY_277']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_277']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_277']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_277']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_277']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>	
											<?if(!empty($newArr['PROPERTY_278']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_278']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_278']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_278']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_278']['VALUE']?></p> 
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_279']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_279']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_279']['HINT'])):?>
															<div class="equipment_hint"> 
																<span class="equip_help"><?=$newArr['PROPERTY_279']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_279']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
										
											<?if(!empty($newArr['PROPERTY_281']['VALUE'])):
												
												$rsUser = CUser::GetByID($newArr['PROPERTY_281']['VALUE']);
												$arUser = $rsUser->Fetch();
												$newArr['PROPERTY_281']['VALUE'] = $arUser['PERSONAL_PAGER'];
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_281']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_281']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_281']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_281']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_282']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_282']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_282']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_282']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_298']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_282']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_282']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_282']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_282']['VALUE'] = $enum_fields["VALUE"];
												}
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_282']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_282']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_282']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTPROPERTY_282Y_244']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_283']['VALUE'])):
												
												$rsUser = CUser::GetByID($newArr['PROPERTY_283']['VALUE']);
												$arUser = $rsUser->Fetch();
												$newArr['PROPERTY_283']['VALUE'] = $arUser['PERSONAL_PAGER'];
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_283']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_283']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_283']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_283']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											
											
											<?if(!empty($newArr['PROPERTY_284']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_284']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_284']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_284']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_284']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											
											<?if(!empty($newArr['PROPERTY_288']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_288']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_288']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_288']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
														<?if(is_array($newArr['PROPERTY_288']['VALUE'])):
															
																foreach ($newArr['PROPERTY_288']['VALUE'] as $key => $value) {
																	$arElement = CIBlockElement::GetByID($value)->fetch();
																		if(is_array($arElement)) {
																			$name = $arElement['NAME'];
																		}
																
															
															?>
																<a class="link__title" href="?mode=edit&list_id=47&section_id=0&element_id=<?=$arElement['ID']?>&list_section_id="><?=$name?></a>
															<?}?>
														<?else:
															
															
																$arElement = CIBlockElement::GetByID($newArr['PROPERTY_288']['VALUE'])->fetch();
																if(is_array($arElement)) {
																	$newArr['PROPERTY_288']['VALUE'] = $arElement['NAME'];
																}
															?>	
															<a class="link__title" href="?mode=edit&list_id=47&section_id=0&element_id=<?=$arElement['ID']?>&list_section_id="><?=$newArr['PROPERTY_288']['VALUE']?></a>
														<?endif;?>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_213']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_213']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_213']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_213']['VALUE'] = $enum_fields["VALUE"];
												}
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_213']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_213']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_213']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_213']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											
											<?if(!empty($newArr['PROPERTY_228']['VALUE'])):?>
											<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_214']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_214']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_214']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<?
                            
														
															if(is_array($newArr['PROPERTY_228']['VALUE'])){
																foreach ($newArr['PROPERTY_228']['VALUE'] as $key => $value) {
																	
																	$doc = CFile::GetPath($value);
																	$file_name = substr(strrchr($doc, "/"), 1);
																	$type = substr(strrchr($doc, "."), 1);
																	// echo $type;
																	if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
															?>			<div class="img_eq">
																			<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
																		</div>	
																	<?else:?>
																		<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
																	<?endif;?>
																<?}
															}
															
														else{
															$doc = CFile::GetPath($newArr['PROPERTY_228']['VALUE']);
																	$file_name = substr(strrchr($doc, "/"), 1);
																	$type = substr(strrchr($doc, "."), 1);
																	// echo $type;
																	if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
															?>
																		<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
																	<?else:?>
																		<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
																	<?endif;?>
														<?}
														

													?>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_289']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_289']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_289']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_289']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_289']['VALUE']['TEXT']?></p>
													</div>
												</div>
											<?endif;?>
											
										
											
											<?if(!empty($newArr['PROPERTY_274']['VALUE'])):
												
												
												
												?>
												
											

											

											                                           
                                            
                                            <!-- <a class="show__more bdotted" href="#">Добавить собсвенный пункт</a> -->
                                        </div>
										<div class="equip_gallery">
											<h2>Фото</h2>
											<div class="gallery_items">
											<?
                            
														
													if(is_array($newArr['PROPERTY_274']['VALUE'])){
														foreach ($newArr['PROPERTY_274']['VALUE'] as $key => $value) {
															
															$doc = CFile::GetPath($value);
															
													?>	
													<img class="gallery_item" src="<?=$doc?>" alt="">		
													<!-- <div class="gallery_item" style="background-image: url(<?=$doc?>)"></div> -->
														
														<?}
													}else{
													$doc = CFile::GetPath($newArr['PROPERTY_274']['VALUE']);?>
																<div class="gallery_item" style="background-image: url(<?=$doc?>)"></div>	
															
												<?}
												

											?>
											</div>
											
											<?endif;?>
											
										</div>									
	<?elseif($_GET['list_id'] == 48):?>	
		
		<div class="employee__card__block">
		<?if(!empty($newArr['PROPERTY_217']['VALUE'])):?>
			<div class="employee__card__item">
				<p class="employee__card__title"><?=$newArr['PROPERTY_217']['NAME']?></p>
					<?if(!empty($newArr['PROPERTY_217']['HINT'])):?>
						<div class="equipment_hint">
							<span class="equip_help"><?=$newArr['PROPERTY_217']['HINT']?></span>
						</div>
					<?endif;?>													
				
					
				<div class="employee__card__item__option">
						<p><?=$newArr['PROPERTY_217']['VALUE']?></p>
				</div>
			</div>
		<?endif;?>
		<?if(!empty($newArr['PROPERTY_218']['VALUE'])):?>
			<div class="employee__card__item">
				<p class="employee__card__title"><?=$newArr['PROPERTY_218']['NAME']?></p>
					<?if(!empty($newArr['PROPERTY_218']['HINT'])):?>
						<div class="equipment_hint">
							<span class="equip_help"><?=$newArr['PROPERTY_218']['HINT']?></span>
						</div>
					<?endif;?>													
				
					
				<div class="employee__card__item__option">
						<p><?=$newArr['PROPERTY_218']['VALUE']?></p>
				</div>
			</div>
		<?endif;?>
		<?if(!empty($newArr['PROPERTY_312']['VALUE'])):
												
			$rsUser = CUser::GetByID($newArr['PROPERTY_312']['VALUE']);
			$arUser = $rsUser->Fetch();
			$mol = $arUser['PERSONAL_PAGER'];
			
			?>
			<div class="employee__card__item">
				<p class="employee__card__title"><?=$newArr['PROPERTY_312']['NAME']?></p>
					<?if(!empty($newArr['PROPERTY_312']['HINT'])):?>
						<div class="equipment_hint">
							<span class="equip_help"><?=$newArr['PROPERTY_312']['HINT']?></span>
						</div>
					<?endif;?>													
				
					
				<div class="employee__card__item__option">
						<p><?=$mol?></p>
				</div>
			</div>
		<?endif;?>
		<?if(!empty($newArr['PROPERTY_241']['VALUE'])):
				
				$rsUser = CUser::GetByID($newArr['PROPERTY_241']['VALUE']);
				$arUser = $rsUser->Fetch();
				$fiz_vlad = $arUser['PERSONAL_PAGER'];
				
				?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_241']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_241']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_241']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$fiz_vlad?></p>
					</div>
				</div>
			<?endif;?>
			<?if(!empty($newArr['PROPERTY_219']['VALUE'])):
												
				$rsUser = CUser::GetByID($newArr['PROPERTY_219']['VALUE']);
				$arUser = $rsUser->Fetch();
				$komu = $arUser['PERSONAL_PAGER'];
				
				?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_219']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_219']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_219']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
							<p><?=$komu?></p>
					</div>
				</div>
			<?endif;?>
			<?if(!empty($newArr['PROPERTY_220']['VALUE'])):?>
				<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_220']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_220']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_220']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
						<?if(is_array($newArr['PROPERTY_220']['VALUE'])):
							
								foreach ($newArr['PROPERTY_220']['VALUE'] as $key => $value) {
									$arElement = CIBlockElement::GetByID($value)->fetch();
										if(is_array($arElement)) {
											$name = $arElement['NAME'];
										}
								
							
							?>
								<a class="link__title" href="?mode=edit&list_id=47&section_id=0&element_id=<?=$arElement['ID']?>&list_section_id="><?=$name?></a>
							<?}?>
						<?else:
							
							
								$arElement = CIBlockElement::GetByID($newArr['PROPERTY_220']['VALUE'])->fetch();
								if(is_array($arElement)) {
									$newArr['PROPERTY_220']['VALUE'] = $arElement['NAME'];
								}
							?>	
							<a class="link__title" href="?mode=edit&list_id=47&section_id=0&element_id=<?=$arElement['ID']?>&list_section_id="><?=$newArr['PROPERTY_220']['VALUE']?></a>
						<?endif;?>
					</div>
				</div>
			<?endif;?>
			<?if(!empty($newArr['PROPERTY_275']['VALUE'])):?>
			<div class="employee__card__item">
				<p class="employee__card__title"><?=$newArr['PROPERTY_275']['NAME']?></p>
					<?if(!empty($newArr['PROPERTY_275']['HINT'])):?>
						<div class="equipment_hint">
							<span class="equip_help"><?=$newArr['PROPERTY_275']['HINT']?></span>
						</div>
					<?endif;?>													
				
					
				<div class="employee__card__item__option">
						<p><?=$newArr['PROPERTY_275']['VALUE']['TEXT']?></p>
				</div>
			</div>
		<?endif;?>
		
		
			
		</div>										
    <?else:?>	
    <div class="employee__card__block">
        <?
        
        foreach ($newArr as $key => $field) {
// 											   echo '<pre>';
//   print_r( $field);
//   echo '</pre>';								
            if($key != 'NAME' && !empty($field['VALUE']) || $key != 'NAME' && !empty($field['VALUE']['TEXT']) || $key != 'NAME' && $field['TYPE'] == 'PREVIEW_PICTURE'):
			if($field['ID'] != '325' && $field['ID'] != '326' && $field['ID'] != '327' && $field['ID'] != '328'):
            if($field['TYPE'] == 'L'){
                $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$field['IBLOCK_ID'], "ID"=>$field['VALUE']));
                    while($enum_fields = $property_enums->GetNext())
                    {
                        $field['VALUE'] = $enum_fields["VALUE"];
                    }
            
            }
            if($field['TYPE'] == 'E'){
                $arElement = CIBlockElement::GetByID($field['VALUE'])->fetch();
                if(is_array($arElement)) {
                    $field['VALUE'] = $arElement['NAME'];
                }
            
            }
            if($field['TYPE'] == 'S:employee'){
                $rsUser = CUser::GetByID($field['VALUE']);
                $arUser = $rsUser->Fetch();
                $field['VALUE'] = $arUser['PERSONAL_PAGER'];
            }
            if($field['TYPE'] == 'PREVIEW_PICTURE'){
                $field['VALUE'] = CFile::GetPath($arResult['ELEMENT_FIELDS']['PREVIEW_PICTURE']);
            }
            if($field['TYPE'] == 'S:HTML'){
                $field['VALUE'] = $field['VALUE']['TEXT'];
            }
            

            ?>
            <div class="employee__card__item">
                <p class="employee__card__title"><?=$field['NAME']?><?if(!empty($field['HINT'])):?>
                    <div class="equipment_hint">
                        <span class="equip_help"><?=$field['HINT']?></span>
                    </div><?endif;?>
                </p>
                
                    
                <div class="employee__card__item__option">
                    <?if($field['TYPE'] == 'E'):?>
                        <a href="?mode=edit&list_id=47&section_id=0&element_id=<?=$arElement['ID']?>&list_section_id="><?=$field['VALUE']?></a>
                    <?elseif($field['TYPE'] == 'PREVIEW_PICTURE'):?>
                        <img src="<?=$field['VALUE']?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
                    <?elseif($field['TYPE'] == 'F'):?>
                        <?
                            
                            if($field['TYPE'] == 'F'){
                                if(is_array($field['VALUE'])){
                                    foreach ($field['VALUE'] as $key => $value) {
                                        
                                        $doc = CFile::GetPath($value);
                                        $file_name = substr(strrchr($doc, "/"), 1);
                                        $type = substr(strrchr($doc, "."), 1);
                                        // echo $type;
                                        if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
                                ?>			<div class="img_eq">
                                                <img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
                                            </div>	
                                        <?else:?>
                                            <a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
                                        <?endif;?>
                                    <?}
                                }
                                
                            else{
                                $doc = CFile::GetPath($field['VALUE']);
                                        $file_name = substr(strrchr($doc, "/"), 1);
                                        $type = substr(strrchr($doc, "."), 1);
                                        // echo $type;
                                        if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
                                ?>
                                            <img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
                                        <?else:?>
                                            <a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
                                        <?endif;?>
                            <?}
                            }

                        ?>
                        
                    <?else:?>	
                        <p><?=$field['VALUE']?></p>
                    <?endif;?>
                </div>
            </div>
        <?
        endif;	
	endif;
    }
        
        ?>
		<div style="display: flex;align-items: flex-start; width: 100%;">
											<?if(!empty($newArr['PROPERTY_325']['VALUE']) || !empty($newArr['PROPERTY_326']['VALUE'])):?>
																<h2>Прикрепленные документы</h2>
											<?endif;?>					

																<?if(in_array(1, $arGroups)  || in_array(20, $arGroups)):?>
																	
																		<button id="button" class="file_add_popup" data-page="<?=$arResult['ELEMENT_FIELDS']['ID']?>" data-iblock="51">Добавить документы</button>
																	
																<?endif;?>
															</div>
											<?if(!empty($newArr['PROPERTY_325']['VALUE']) || !empty($newArr['PROPERTY_326']['VALUE'])):?>
															
													<?

														if ( !Loader::includeModule('disk') )
														{
														throw new Exception("Не подклчюен модуль диска");
														}
														$driver = Disk\Driver::getInstance();
														$storage = \Bitrix\Disk\Storage::loadById(1467);//знаем идентификатор хранилища 
													$securityContext = $driver->getFakeSecurityContext();
													// $securityContext = $storage->getCurrentUserSecurityContext();

														
													$files = array();
													$folder = array();
													$arFolderList = [];
													$arFileList = [];
													$folder_test =[];
													if(!empty($newArr['PROPERTY_325']['VALUE']) && $newArr['PROPERTY_325']['VALUE'][0] != 0 ):
														if(!is_array($newArr['PROPERTY_325']['VALUE'])){
															$newArr['PROPERTY_325']['VALUE'] = array(0 => $newArr['PROPERTY_325']['VALUE']);
														}
														
															foreach ($newArr['PROPERTY_325']['VALUE'] as $key => $value) {
																	$files[] = \Bitrix\Disk\File::loadById($value, array('STORAGE'));
																
															}
														endif;?>
														<?if(!empty($newArr['PROPERTY_326']['VALUE'])):
														if(!is_array($newArr['PROPERTY_326']['VALUE'])){
															$newArr['PROPERTY_326']['VALUE'] = array(0 => $newArr['PROPERTY_326']['VALUE']);
														}
															foreach ($newArr['PROPERTY_326']['VALUE'] as $key => $value) {
														?>

															<?$folder[] = \Bitrix\Disk\Folder::loadById($value, array('STORAGE'));
															
															}	
															?>
														
														<?endif;?>
														<?
														if(!empty($folder)){
															foreach ($folder as $key => $value) {
															
																getRecurciveFolder( $value, $securityContext, $arFolderList );
																}
														}
													// 	echo "<pre>";
													//    print_r($arFolderList);
													//    echo "</pre>";
														
														
														foreach ($arFolderList as $key => $folderList) {
															foreach ($folderList['CHILDRENS'] as $key => $file) {
																
															
															if(empty($file['IS_FOLDER'])){
																$arFileList[] = $file;
															}
															if(!empty($file['CHILDRENS'])){
																foreach ($file['CHILDRENS'] as $key => $value) {
																	if(empty($value['IS_FOLDER'])){
																		$arFileList[] = $value;
																	}
																	if(!empty($value['CHILDRENS'])){
																		foreach ($value['CHILDRENS'] as $key => $second_file) {
																			if(empty($second_file['IS_FOLDER'])){
																				$arFileList[] = $second_file;
																			}
																		}
																	}
																}
															}
														}
														}
													// 	if ( $files[0] instanceof Disk\File )
													// {
														
													//    $urlManager = Disk\Driver::getInstance()->getUrlManager();
													//    $downloadUrl = $urlManager->getUrlForShowFile($files[0]);
													// }

													//    echo "<pre>";
													//    print_r($arFolderList);
													//    echo "</pre>";
														?>
														<div class="files_container">
															<?foreach ($files as $key => $file) {
																$file_type = substr(strrchr($file->getName(), "."), 1);
																$userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
													// 			   echo "<pre>";
													//    print_r($file);
													//    echo "</pre>";
																$category = CUserFieldEnum::GetList(array(), array("ID" => $userFieldsObject['UF_FILE_CATEG']['VALUE']))->GetNext()["VALUE"];
																?>
																<a href="/disk/downloadFile/<?=$file->getId();?>/?&ncc=1&filename=<?=$file->getName();?>" class="card__document">
																								<div class="document__img ui-icon ui-icon-file ui-icon-file-pdf">
																								
																								<?if($file_type == 'xls'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/XLSIcons.svg" />
																								<?elseif($file_type == 'doc' || $file_type == 'docx'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIcons.svg" />
																								<?elseif($file_type == 'pdf'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/PDFIcon.svg" />
																								<?else:?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIconV2.svg" />
																								<?endif;?>
																								</div>
																								<div class="document__block">
																								<?if(!empty($category) && $category != 'Обычная'):?>
																									<div class="document__header">
																										<p class="orange"><?=$category?></p>
																									</div>
																									<?endif;?>
																									<div class="document__body">
																										<p><?=$file->getName();?></p>
																									</div>
																								</div>
																								</a>
																<!-- <a href="/disk/downloadFile/<?=$file->getId();?>/?&ncc=1&filename=<?=$file->getName();?>" class="file_view"><?=$file->getName();?></a> -->
															<?}?>
														<?if(!empty($arFileList)):?>

																<?foreach ($arFileList as $key => $file) {
																$file_type = substr(strrchr($file['NAME'], "."), 1);
																			// $userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
																// 			   echo "<pre>";
																//    print_r($file);
																//    echo "</pre>";
																$category = CUserFieldEnum::GetList(array(), array("ID" => $file['UF_FILE_CATEG']['VALUE']))->GetNext()["VALUE"];
																?>
																<a href="/disk/downloadFile/<?=$file['ID'];?>/?&ncc=1&filename=<?=$file['NAME'];?>" class="card__document">
																								<div class="document__img ui-icon ui-icon-file ui-icon-file-pdf">
																								
																								<?if($file_type == 'xls'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/XLSIcons.svg" />
																								<?elseif($file_type == 'doc' || $file_type == 'docx'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIcons.svg" />
																								<?elseif($file_type == 'pdf'):?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/PDFIcon.svg" />
																								<?else:?>
																									<img src="<?=SITE_TEMPLATE_PATH?>/img/icon/DocIconV2.svg" />
																								<?endif;?>
																								</div>
																								<div class="document__block">
																								<?if(!empty($category) && $category != 'Обычная'):?>
																									<div class="document__header">
																										<p class="orange"><?=$category?></p>
																									</div>
																									<?endif;?>
																									<div class="document__body">
																										<p><?=$file['NAME'];?></p>
																									</div>
																								</div>
																								</a>
																<!-- <a href="/disk/downloadFile/<?=$file['ID'];?>/?&ncc=1&filename=<?=$file['NAME'];?>" class="file_view"><?=$file['NAME'];?></a> -->
																<?}?>
															
														<?endif;?>
													</div>
											<?endif;?>
											<?IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/shared/index.php");?>
<div id="overlay"></div>
<div id="popup">
    <div class="popupcontrols">
        <span id="popupclose">X</span>
    </div>
    <div class="popupcontent">
        <h1>Выберите документы</h1>
		<?$APPLICATION->IncludeComponent(
	"bitrix:disk.common", 
	"disk1", 
	array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/docs/dokumenty-na-oboroudvanie",
		"STORAGE_ID" => "1467",
		"COMPONENT_TEMPLATE" => "disk1"
	),
	false
);?>
    </div>
	<div class="picked_files">
		<h2>Выбранные файлы</h2>
	<?if(!empty($newArr['PROPERTY_325']['VALUE'])):?>
				<?$files = array();
				$file_path = array();
		$arFolderList = [];
		foreach ($newArr['PROPERTY_325']['VALUE'] as $key => $value) {
				$files[] = \Bitrix\Disk\File::loadById($value, array('STORAGE'));
		}
	endif;	
		if(!empty($newArr['PROPERTY_326']['VALUE'])):
			foreach ($newArr['PROPERTY_326']['VALUE'] as $key => $value) {
			?>

			<?
			// $folder[] = \Bitrix\Disk\Folder::loadById($value, array('STORAGE'));
			
			}
			?>

   		<?endif;?>
		<?
		if(!empty($folder)){
			foreach ($folder as $key => $value) {
			
				getRecurciveFolder( $value, $securityContext, $arFolderList );
				}
		}
		
		//    echo "<pre>";
		//    print_r($folder);
		//    echo "</pre>";
			?>
		<?foreach ($files as $key => $file) {
			$file_type = substr(strrchr($file->getName(), "."), 1);
			$userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
			if(!is_array($newArr['PROPERTY_327']['VALUE'])){
				$newArr['PROPERTY_327']['VALUE'] = array(0=>$newArr['PROPERTY_327']['VALUE']);
			}
			$file_path =$newArr['PROPERTY_327']['VALUE'][$key];
			$category = CUserFieldEnum::GetList(array(), array("ID" => $userFieldsObject['UF_FILE_CATEG']['VALUE']))->GetNext()["VALUE"];
			?>
			<div class='file_item_container'><div class='bx-file-icon-container-small bx-disk-file-icon <?if($file_type == 'xls'):?>icon-xls<?elseif($file_type == 'doc' || $file_type == 'docx'):?>icon-doc<?elseif($file_type == 'pdf'):?>icon-pdf<?elseif($file_type == 'pptx'):?>icon-ppt<?else:?><?endif;?>'></div><div class='file_pick'data-is-file="true" data-is-folder="false" data-id="<?=$file->getId();?>"><?=$file->getName();?></div><div class='js-disk-breadcrumbs-folder-link'></div><div class='nav_file_item'><?=$file_path?></div><div class='file_del'>X</div></div>
		<?}?>
		<?if(!empty($folder)):?>
		<?foreach ($folder as $key => $file) {
			// $userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($file);
			if(!is_array($newArr['PROPERTY_328']['VALUE'])){
				$newArr['PROPERTY_328']['VALUE'] = array(0=>$newArr['PROPERTY_328']['VALUE']);
			}
			$file_path =$newArr['PROPERTY_328']['VALUE'][$key];
			?>
			<div class='file_item_container'><div class='bx-file-icon-container-small  bx-disk-folder-icon'></div><div class='file_pick' data-is-folder="true" data-is-file="false" data-id="<?=$file['ID'];?>"><?=$file->getName();?></div><div class='js-disk-breadcrumbs-folder-link'></div><div class='nav_file_item'><?=$file_path?></div><div class='file_del'>X</div></div>
		<?}?>
		<?endif;?>
		
	
	</div>
	<button class="button_add_fele_on_page">Сохранить</button>
	<div class="results_file"></div>
</div>
<?
	/**
 * Один из примеров рекурсивной функции по созданию древовидной структуры
 * @param Disk\BaseObjcet $diskObject 
 * @param Disk\SecurityContext $securitycontext 
 * @param array &$arFolderList 
 * @return void
 */
function getRecurciveFolder( $diskObject, $securitycontext, &$arFolderList )
{
   
   if ( $diskObject instanceof Disk\Folder )
   {
      $arFolder = [
        'ID' => $diskObject->getId(),
         'NAME'      => $diskObject->getName(),
         'PARENT_ID'      => $diskObject->getParentId(),
         'IS_FOLDER' => true,
         'CHILDRENS' => [],
      ];

      $arChildrens = $diskObject->getChildren($securitycontext);
      
      foreach ($arChildrens as $childObject)
      {
         getRecurciveFolder( $childObject, $securitycontext, $arFolder['CHILDRENS'] );
      }

      $arFolderList[] = $arFolder;
          

      
   }
   else
   {
    $userFieldsObject = Disk\Driver::getInstance()->getUserFieldManager()->getFieldsForObject($diskObject);
    
    $date = $diskObject->getCreateTime();
   $date_preview = $date->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "YYYY-MM-DD")));
   $str = strtotime($date_preview);
//    var_dump($date_preview);
   if(!empty($_GET['DATESTART']) && !empty($_GET['DATEEND'])){
    $start_date = str_replace('/','.', $_GET['DATESTART']);
    $end_date = str_replace('/','.', $_GET['DATEEND']);
    if ($arrStart = ParseDateTime($start_date, "MM.DD.YYYY"))
    {
        $normalStartDate = $arrStart["YYYY"].'-'.$arrStart["MM"].'-'. $arrStart["DD"];
        $strStart = strtotime($normalStartDate);
        // echo $normalStartDate;
        // var_dump($normalStartDate);
    }
    if ($arrEnd = ParseDateTime($end_date, "MM.DD.YYYY"))
    {
        $normalEndDate = $arrEnd["YYYY"].'-'.$arrEnd["MM"].'-'. $arrEnd["DD"];
        $strEnd = strtotime($normalEndDate);
        // echo $normalEndDate;
        // var_dump($normalEndDate);
    }
    // echo $date_preview. '-' .$normalStartDate;
    // if($normalStartDate < $date_preview){
    //     var_dump($date_preview);
    // }
    
    if($normalStartDate <= $date_preview && $normalEndDate >= $date_preview){
        // echo 'qqqqq';
        $arFolderList[] = [
            'ID'      => $diskObject->getId(),
             'NAME'      => $diskObject->getName(),
             'DATE'      => $diskObject->getCreateTime(),
             'IS_FOLDER' => false,
             'USER_PROP' => $userFieldsObject,
          ];
       }
   }else{
    $arFolderList[] = [
        'ID'      => $diskObject->getId(),
         'NAME'      => $diskObject->getName(),
         'DATE'      => $diskObject->getCreateTime(),
         'IS_FOLDER' => false,
         'USER_PROP' => $userFieldsObject,
      ];
   }
   
      
  
//       echo "<pre>";
// print_r($arFolderList);
// echo "</pre>";
   }
//    $resObjects = \Bitrix\Disk\Internals\ObjectTable::getList([
//     'select' => ['*'],
//     'filter' => [
//         '=ID' => 32,
//     ]
// ]);
// if ($arObject = $resObjects->fetch()) {
//        echo "<pre>";
//    print_r($arObject);
//    echo "</pre>";
// }

 
} 

?>
       
        
        <!-- <a class="show__more bdotted" href="#">Добавить собсвенный пункт</a> -->
    </div>
    <?endif;?>
                                    </div>
                                </div>
                                <div class="employee__card__footer">
                                    <!-- <div class="cours__card orange lk">
                                        <div class="cours__card__head">
                                            <p class="cours__card__title">Пройти курсы повышения квалификации</p>
                                        </div>
                                        <div class="cours__card__body">
                                            <p class="cours__card__description">Курсы и тесты для каждой профессии</p>
                                        </div>
                                        <div class="cours__card__footer">
                                            <a class="btn orange" href="#">Перейти в LMS-систему</a>
                                        </div>
                                    </div> -->
                                   
                                    <!-- <div class="widget__block lk">
                                        <div class="block__title widget">
                                            <a class="widget__title" href="#">Мои документы</a>
                                        </div>
                                        <div class="widget__block__items">
                                            <a class="link" href="#">
                                                <p class="link__title">Отчет за 1 квартал 2020 года</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Презентация продукта</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Пример заявления на отпуск за 20.02.22</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Пример подачи отчета</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Отчет за 4 квартал 2021</p>
                                            </a>
                                        </div>
                                        <a class="show__more" href="#">Показать еще</a>
                                    </div> -->
                                    <!-- <div class="widget__block lk">
                                        <div class="block__title widget">
                                            <a class="widget__title" href="#">Сертификаты</a>
                                        </div>
                                        <div class="widget__block__items">
                                            <a class="link" href="#">
                                                <p class="link__title">Отчет за 1 квартал 2020 года</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Презентация продукта</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Пример заявления на отпуск за 20.02.22</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Пример подачи отчета</p>
                                            </a>
                                            <a class="link" href="#">
                                                <p class="link__title">Отчет за 4 квартал 2021</p>
                                            </a>
                                        </div>
                                        <a class="show__more" href="#">Показать еще</a>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3 px-0">
				<?if(in_array(1, $arGroups)  || in_array(20, $arGroups)):?>
					
					
										<!-- <button data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn add_archive">Переместить в архив</button> -->
										<!-- <a href="<?=$dir?>&edit=yes" class="btn">Редактировать</a> -->
				<?endif;?>
				
				<?if($_GET['list_id'] == 48):?>
					<div class="widget__block lk right_side">
						<div class="widget__block__items">
						<?if(!empty($newArr['PROPERTY_313']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_313']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_313']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$status = $enum_fields["VALUE"];
												}
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_313']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_313']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_313']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$status?></p>
													</div>
												</div>
											<?endif;?>
					
			<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_311']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_311']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_311']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
					<?

						
							if(is_array($newArr['PROPERTY_311']['VALUE'])){
								foreach ($newArr['PROPERTY_311']['VALUE'] as $key => $value) {
									
									$doc = CFile::GetPath($value);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>			<div class="img_eq">
											<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
										</div>	
									<?else:?>
										<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
									<?endif;?>
								<?}
							}
							
						else{
							$doc = CFile::GetPath($newArr['PROPERTY_311']['VALUE']);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>
										<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
									<?else:?>
										<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
									<?endif;?>
						<?}
						

					?>
					</div>
				</div>
			
			
			<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_314']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_314']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_314']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
					<?

						
							if(is_array($newArr['PROPERTY_314']['VALUE'])){
								foreach ($newArr['PROPERTY_314']['VALUE'] as $key => $value) {
									
									$doc = CFile::GetPath($value);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>			<div class="img_eq">
											<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
										</div>	
									<?else:?>
										<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
									<?endif;?>
								<?}
							}
							
						else{
							$doc = CFile::GetPath($newArr['PROPERTY_314']['VALUE']);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>
										<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
									<?else:?>
										<?if(!empty($newArr['PROPERTY_314']['VALUE'])):?>
											<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
										<?elseif(empty($newArr['PROPERTY_314']['VALUE']) && $newArr['PROPERTY_312']['VALUE'] == $newArr['PROPERTY_241']['VALUE']):?>
											<p>-- //-- </p>
										<?else:?>	
											<?
												global $USER;
												$userID = $USER->GetID();
															// echo $userID;
												if($userID == $newArr['PROPERTY_312']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 143):
												?>
													<form class="update_file">
														<input type="hidden" name='ID' value="<?=$arResult['ELEMENT_FIELDS']['ID']?>">
														<input type="hidden" name='NAME' value="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
														<input type="hidden" name='KUDA' value="<?=$newArr['PROPERTY_217']['VALUE']?>">
														<input type="hidden" name='DATA_PEREMESHCHENIYA' value="<?=$newArr['PROPERTY_218']['VALUE']?>">
														<input type="hidden" name='MOL' value="<?=$newArr['PROPERTY_312']['VALUE']?>">
														<input type="hidden" name='FIO_KTO_PEREDAET' value="<?=$newArr['PROPERTY_241']['VALUE']?>">
														<input type="hidden" name='FIO' value="<?=$newArr['PROPERTY_219']['VALUE']?>">
														<input type="hidden" name='OBORUDOVANIE' value="<?=$newArr['PROPERTY_220']['VALUE']?>">
														<input type="hidden" name='KOMMENTARIY' value="<?=$newArr['PROPERTY_275']['VALUE']['TEXT']?>">
														<input type="hidden" name='DOKUMENT' value="<?=$newArr['PROPERTY_311']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_MOL_OM' value="<?=$newArr['PROPERTY_314']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' value="<?=$newArr['PROPERTY_315']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_VSEMI' value="<?=$newArr['PROPERTY_316']['VALUE']?>">
														<input type="hidden" name='STATUS' value="<?=$newArr['PROPERTY_313']['VALUE']?>">
														<input type="file" name='FILE'>
														<div class="btn save_file">Сохранить</div>
													</form>
													
												<?endif;?>
										<?endif;?>
									<?endif;?>
						<?}
						

					?>
					</div>
				</div>
			
			
			<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_315']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_315']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_315']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
					<?

						
							if(is_array($newArr['PROPERTY_315']['VALUE'])){
								foreach ($newArr['PROPERTY_315']['VALUE'] as $key => $value) {
									
									$doc = CFile::GetPath($value);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>			<div class="img_eq">
											<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
										</div>	
									<?else:?>
										<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
									<?endif;?>
								<?}
							}
							
						else{
							$doc = CFile::GetPath($newArr['PROPERTY_315']['VALUE']);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>
										<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
									<?else:?>
										<?if(!empty($newArr['PROPERTY_315']['VALUE'])):?>
											<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
										<?elseif(empty($newArr['PROPERTY_315']['VALUE']) && $newArr['PROPERTY_312']['VALUE'] == $newArr['PROPERTY_241']['VALUE']):?>
											<?
												global $USER;
												$userID = $USER->GetID();
												if($userID == $newArr['PROPERTY_241']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 143):
												?>
													<form class="update_file">
														<input type="hidden" name='ID' value="<?=$arResult['ELEMENT_FIELDS']['ID']?>">
														<input type="hidden" name='NAME' value="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
														<input type="hidden" name='KUDA' value="<?=$newArr['PROPERTY_217']['VALUE']?>">
														<input type="hidden" name='DATA_PEREMESHCHENIYA' value="<?=$newArr['PROPERTY_218']['VALUE']?>">
														<input type="hidden" name='MOL' value="<?=$newArr['PROPERTY_312']['VALUE']?>">
														<input type="hidden" name='FIO_KTO_PEREDAET' value="<?=$newArr['PROPERTY_241']['VALUE']?>">
														<input type="hidden" name='FIO' value="<?=$newArr['PROPERTY_219']['VALUE']?>">
														<input type="hidden" name='OBORUDOVANIE' value="<?=$newArr['PROPERTY_220']['VALUE']?>">
														<input type="hidden" name='KOMMENTARIY' value="<?=$newArr['PROPERTY_275']['VALUE']['TEXT']?>">
														<input type="hidden" name='DOKUMENT' value="<?=$newArr['PROPERTY_311']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_MOL_OM' value="<?=$newArr['PROPERTY_314']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' value="<?=$newArr['PROPERTY_315']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_VSEMI' value="<?=$newArr['PROPERTY_316']['VALUE']?>">
														<input type="hidden" name='STATUS' value="<?=$newArr['PROPERTY_313']['VALUE']?>">
														<input type="file" name='FILE'>
														<div class="btn save_file">Сохранить</div>
													</form>
													
												<?endif;?>
										<?else:?>
											<?
												global $USER;
												$userID = $USER->GetID();
															// echo $userID;
												if($userID == $newArr['PROPERTY_241']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 144):
												?>
													<form class="update_file">
														<input type="hidden" name='ID' value="<?=$arResult['ELEMENT_FIELDS']['ID']?>">
														<input type="hidden" name='NAME' value="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
														<input type="hidden" name='KUDA' value="<?=$newArr['PROPERTY_217']['VALUE']?>">
														<input type="hidden" name='DATA_PEREMESHCHENIYA' value="<?=$newArr['PROPERTY_218']['VALUE']?>">
														<input type="hidden" name='MOL' value="<?=$newArr['PROPERTY_312']['VALUE']?>">
														<input type="hidden" name='FIO_KTO_PEREDAET' value="<?=$newArr['PROPERTY_241']['VALUE']?>">
														<input type="hidden" name='FIO' value="<?=$newArr['PROPERTY_219']['VALUE']?>">
														<input type="hidden" name='OBORUDOVANIE' value="<?=$newArr['PROPERTY_220']['VALUE']?>">
														<input type="hidden" name='KOMMENTARIY' value="<?=$newArr['PROPERTY_275']['VALUE']['TEXT']?>">
														<input type="hidden" name='DOKUMENT' value="<?=$newArr['PROPERTY_311']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_MOL_OM' value="<?=$newArr['PROPERTY_314']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' value="<?=$newArr['PROPERTY_315']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_VSEMI' value="<?=$newArr['PROPERTY_316']['VALUE']?>">
														<input type="hidden" name='STATUS' value="<?=$newArr['PROPERTY_313']['VALUE']?>">
														<input type="file" name='FILE'>
														<div class="btn save_file">Сохранить</div>
													</form>
													
												<?endif;?>
										<?endif;?>
									<?endif;?>
						<?}
						

					?>
					</div>
				</div>
			
			
			<div class="employee__card__item">
					<p class="employee__card__title"><?=$newArr['PROPERTY_316']['NAME']?></p>
						<?if(!empty($newArr['PROPERTY_316']['HINT'])):?>
							<div class="equipment_hint">
								<span class="equip_help"><?=$newArr['PROPERTY_316']['HINT']?></span>
							</div>
						<?endif;?>													
					
						
					<div class="employee__card__item__option">
					<?

						
							if(is_array($newArr['PROPERTY_316']['VALUE'])){
								foreach ($newArr['PROPERTY_316']['VALUE'] as $key => $value) {
									
									$doc = CFile::GetPath($value);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>			<div class="img_eq">
											<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
										</div>	
									<?else:?>
										<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
									<?endif;?>
								<?}
							}
							
						else{
							$doc = CFile::GetPath($newArr['PROPERTY_316']['VALUE']);
									$file_name = substr(strrchr($doc, "/"), 1);
									$type = substr(strrchr($doc, "."), 1);
									// echo $type;
									if($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'svg' || $type == 'bmp'):
							?>
										<img src="<?=$doc?>" width="200" alt="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">	
									<?else:?>
										<?if(!empty($newArr['PROPERTY_316']['VALUE'])):?>
											<a class="docs_eqp" href="<?=$doc?>" download><?=$file_name?></a>
										<?else:?>
											<?
												global $USER;
												$userID = $USER->GetID();
															// echo $userID;
												if($userID == $newArr['PROPERTY_219']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 145):
												?>
													<form class="update_file">
														<input type="hidden" name='ID' value="<?=$arResult['ELEMENT_FIELDS']['ID']?>">
														<input type="hidden" name='NAME' value="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
														<input type="hidden" name='KUDA' value="<?=$newArr['PROPERTY_217']['VALUE']?>">
														<input type="hidden" name='DATA_PEREMESHCHENIYA' value="<?=$newArr['PROPERTY_218']['VALUE']?>">
														<input type="hidden" name='MOL' value="<?=$newArr['PROPERTY_312']['VALUE']?>">
														<input type="hidden" name='FIO_KTO_PEREDAET' value="<?=$newArr['PROPERTY_241']['VALUE']?>">
														<input type="hidden" name='FIO' value="<?=$newArr['PROPERTY_219']['VALUE']?>">
														<input type="hidden" name='OBORUDOVANIE' value="<?=$newArr['PROPERTY_220']['VALUE']?>">
														<input type="hidden" name='KOMMENTARIY' value="<?=$newArr['PROPERTY_275']['VALUE']['TEXT']?>">
														<input type="hidden" name='DOKUMENT' value="<?=$newArr['PROPERTY_311']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_MOL_OM' value="<?=$newArr['PROPERTY_314']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' value="<?=$newArr['PROPERTY_315']['VALUE']?>">
														<input type="hidden" name='DOKUMENT_PODPISANNYY_VSEMI' value="<?=$newArr['PROPERTY_316']['VALUE']?>">
														<input type="hidden" name='STATUS' value="<?=$newArr['PROPERTY_313']['VALUE']?>">
														<input type="file" name='FILE'>
														<div class="btn save_file">Сохранить</div>
													</form>
													
												<?endif;?>
										<?endif;?>
									<?endif;?>
						<?}
						

					?>
					</div>
				</div>
			
						<?
							// global $USER;
							// $userID = $USER->GetID();
							// 			// echo $userID;
							// if($userID == $newArr['PROPERTY_312']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 143 || $userID == $newArr['PROPERTY_219']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 145 || $userID == $newArr['PROPERTY_241']['VALUE'] && $newArr['PROPERTY_313']['VALUE'] == 144 ||  $userID == 1):
							?>
								<!-- <form class="update_file">
									<input type="hidden" name='ID' value="<?=$arResult['ELEMENT_FIELDS']['ID']?>">
									<input type="hidden" name='NAME' value="<?=$arResult['ELEMENT_FIELDS']['NAME']?>">
									<input type="hidden" name='KUDA' value="<?=$newArr['PROPERTY_217']['VALUE']?>">
									<input type="hidden" name='DATA_PEREMESHCHENIYA' value="<?=$newArr['PROPERTY_218']['VALUE']?>">
									<input type="hidden" name='MOL' value="<?=$newArr['PROPERTY_312']['VALUE']?>">
									<input type="hidden" name='FIO_KTO_PEREDAET' value="<?=$newArr['PROPERTY_241']['VALUE']?>">
									<input type="hidden" name='FIO' value="<?=$newArr['PROPERTY_219']['VALUE']?>">
									<input type="hidden" name='OBORUDOVANIE' value="<?=$newArr['PROPERTY_220']['VALUE']?>">
									<input type="hidden" name='KOMMENTARIY' value="<?=$newArr['PROPERTY_275']['VALUE']['TEXT']?>">
									<input type="hidden" name='DOKUMENT' value="<?=$newArr['PROPERTY_311']['VALUE']?>">
									<input type="hidden" name='DOKUMENT_PODPISANNYY_MOL_OM' value="<?=$newArr['PROPERTY_314']['VALUE']?>">
									<input type="hidden" name='DOKUMENT_PODPISANYY_MOL_I_FIZ_VLAD' value="<?=$newArr['PROPERTY_315']['VALUE']?>">
									<input type="hidden" name='DOKUMENT_PODPISANNYY_VSEMI' value="<?=$newArr['PROPERTY_316']['VALUE']?>">
									<input type="hidden" name='STATUS' value="<?=$newArr['PROPERTY_313']['VALUE']?>">
									<input type="file" name='FILE'>
									<div class="btn save_file">Сохранить</div>
								</form> -->
								
							<?
						// endif;
						?>
						</div>
					</div>	
					<?endif;?>	
					<?if(in_array(1, $arGroups) && $_GET['list_id'] == 47 || in_array(20, $arGroups) && $_GET['list_id'] == 47):?>					
                    	<div class="widget__container lk">
						

                        <div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Поверки</h2>
											<?if($newArr['PROPERTY_214']['VALUE'] != 98 && !empty($newArr['PROPERTY_214']['VALUE'])):?>	
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_214']['VALUE']?>">Добавить</a>
											<?endif;?>
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_214']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_214']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_214']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_214']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_214']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_214']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_214']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_214']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_214']['VALUE'] != 'не требуется'):?>		
							<?if(!empty($newArr['PROPERTY_237']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_237']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_237']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_237']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_237']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
							<?if(!empty($newArr['PROPERTY_238']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_238']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_238']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_238']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_238']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_239']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_239']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_239']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_239']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_239']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_240']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_240']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_240']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_240']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_240']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 136,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Атестация</h2>
											<?if($newArr['PROPERTY_229']['VALUE'] != 99 && !empty($newArr['PROPERTY_229']['VALUE'])):?>	
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_229']['VALUE']?>">Добавить</a>
											<?endif;?>	
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_229']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_229']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_229']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_229']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_229']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_229']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_229']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_229']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_229']['VALUE'] != 'не требуется'):?>		
							<?if(!empty($newArr['PROPERTY_230']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_230']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_230']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_230']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_230']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
							<?if(!empty($newArr['PROPERTY_231']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_231']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_231']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_231']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_231']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_232']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_232']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_232']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_232']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_232']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 137,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Калибровка</h2>
											<?if($newArr['PROPERTY_233']['VALUE'] != 101 && !empty($newArr['PROPERTY_233']['VALUE'])):?>
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_233']['VALUE']?>">Добавить</a>
											<?endif;?>
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_233']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_233']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_233']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_233']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_233']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_233']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_233']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_233']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_233']['VALUE'] != 'не требуется'):?>		
							<?if(!empty($newArr['PROPERTY_234']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_234']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_234']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_234']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_234']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
							<?if(!empty($newArr['PROPERTY_235']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_235']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_235']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_235']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_235']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_236']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_236']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_236']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_236']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_236']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 138,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
                       
						<?
						$elements = array();
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("ID" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 48,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
							),
							false,
							array("nTopCount" => 3),
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_STATUS")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}
							// 					 echo "<pre>";
							// print_r( $elements);
							// echo "</pre>"; 
						
						
						?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Перемещение</h2>
											<a class="add__link" href="?mode=edit&list_id=48&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&mol=<?=$p226?>&fakt=<?=$p227?>&molname=<?=$newArr['PROPERTY_226']['VALUE']?>&faktname=<?=$newArr['PROPERTY_227']['VALUE']?>">Добавить</a>
										</div>
							<?if(!empty($elements)):
							?>
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_205']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_205']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_205']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_205']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_205']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_207']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_207']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_207']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_207']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_207']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_209']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_209']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_209']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_209']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_209']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_211']['VALUE'])):
												
												// $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_211']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_211']['VALUE']));
												// while($enum_fields = $property_enums->GetNext())
												// {
												// 	$newArr['PROPERTY_211']['VALUE'] = $enum_fields["VALUE"];
												// }
												
												?>
												<!-- <div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_211']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_211']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_211']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_211']['VALUE']?></p></a>
													</div>
												</div> -->
											<?endif;?>
											<?if(!empty($elements[0]['PROPERTY_STATUS_VALUE'])):?>

												<div class="employee__card__item">
													<p class="employee__card__title">Статус</p>													
													
														
													<div class="employee__card__item__option">
													<p><?=$elements[0]['PROPERTY_STATUS_VALUE']?></p></a>
													</div>
												</div>
											<?endif;?>	

							<?
								
								foreach ($elements as $key => $value) {?>
									<a class="link" href="?mode=edit&list_id=48&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?}
								
								?>
                                
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
                    </div>
					<?endif;?>
					<?if(in_array(1, $arGroups) && $_GET['list_id'] == 52 || in_array(20, $arGroups) && $_GET['list_id'] == 52):?>					
                    	<div class="widget__container lk">
						

                        <div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Поверки</h2>
											<?if($newArr['PROPERTY_290']['VALUE'] != 134 && !empty($newArr['PROPERTY_290']['VALUE'])):?>	
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_290']['VALUE']?>">Добавить</a>
											<?endif;?>
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_290']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_290']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_290']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_290']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_290']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_290']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_290']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_290']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_290']['VALUE'] != 'не требуется'):?>	
							<?if(!empty($newArr['PROPERTY_292']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_292']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_292']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_292']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_292']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_293']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_293']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_293']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_293']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_293']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_294']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_294']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_294']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_294']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_294']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 136,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Атестация</h2>
											<?if($newArr['PROPERTY_300']['VALUE'] != 139 && !empty($newArr['PROPERTY_300']['VALUE'])):?>	
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_300']['VALUE']?>">Добавить</a>
											<?endif;?>	
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_300']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_300']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_300']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_300']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_300']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_300']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_300']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_300']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_300']['VALUE'] != 'не требуется'):?>		
							<?if(!empty($newArr['PROPERTY_301']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_301']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_301']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_301']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_301']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
							<?if(!empty($newArr['PROPERTY_302']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_302']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_302']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_302']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_302']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_303']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_303']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_303']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_303']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_303']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 137,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Калибровка</h2>
											<?if($newArr['PROPERTY_304']['VALUE'] != 141 && !empty($newArr['PROPERTY_304']['VALUE'])):?>
												<a class="add__link" href="?mode=edit&list_id=51&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>&type=<?=$newArr['PROPERTY_304']['VALUE']?>">Добавить</a>
											<?endif;?>
										</div>
                           
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_304']['VALUE'])):
												
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_304']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_304']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['PROPERTY_304']['VALUE'] = $enum_fields["VALUE"];
												} 
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_304']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_304']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_304']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_304']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
						<?if($newArr['PROPERTY_304']['VALUE'] != 'не требуется'):?>		
							<?if(!empty($newArr['PROPERTY_305']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_305']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_305']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_305']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_305']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
							<?if(!empty($newArr['PROPERTY_306']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_306']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_306']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_306']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_306']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_307']['VALUE'])):
												
												
												
												?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_307']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_307']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_307']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_307']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											
									<?endif;?>
											
											<?
						
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("created" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 51,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
								"PROPERTY_TIP_OBSLUZHIVANIYA" => 138,
							),
							false,
							false,
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_*")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}

						// 					echo '<pre>';
						// print_r( $elements);
						// echo '</pre>';
						if(!empty($elements)):
						?>
								<?
								$k=0;
								foreach ($elements as $key => $value) {
									if($k<3):
									?>
									<a class="link" href="?mode=edit&list_id=51&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?
									$k++;
									endif;
								}
								
								?>
                               
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
                       
						<?
						$elements = array();
						$rsElement = CIBlockElement::GetList(
							$arOrder  = array("ID" => "DESC"),
							$arFilter = array(
								"ACTIVE"    => "Y",
								"IBLOCK_ID" => 48,
								"PROPERTY_OBORUDOVANIE" => $arResult['ELEMENT_FIELDS']['ID'],
							),
							false,
							array("nTopCount" => 3),
							$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE",  "PROPERTY_STATUS")
						);
						while($arElement = $rsElement->fetch()) {
							$elements[] = $arElement;
						}
							// 					 echo "<pre>";
							// print_r( $elements);
							// echo "</pre>"; 
						
						
						?>
						<div class="widget__block lk">
						<div class="favorites__block">
											<h2 class="widget__title lk">Перемещение</h2>
											<a class="add__link" href="?mode=edit&list_id=48&section_id=0&element_id=0&list_section_id=&item-id=<?=$arResult['ELEMENT_FIELDS']['ID']?>&item-name=<?=$arResult['ELEMENT_FIELDS']['NAME']?>">Добавить</a>
										</div>
							<?if(!empty($elements)):
							?>
                            <div class="widget__block__items">
							<?if(!empty($newArr['PROPERTY_280']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_280']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_280']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_280']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_280']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_284']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_284']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_284']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_284']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_284']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_285']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_285']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_285']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_285']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
															<p><?=$newArr['PROPERTY_285']['VALUE']?></p>
													</div>
												</div>
											<?endif;?>
											<?if(!empty($newArr['PROPERTY_287']['VALUE'])):
												
												// $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['PROPERTY_211']['IBLOCK_ID'], "ID"=>$newArr['PROPERTY_211']['VALUE']));
												// while($enum_fields = $property_enums->GetNext())
												// {
												// 	$newArr['PROPERTY_211']['VALUE'] = $enum_fields["VALUE"];
												// }
												
												?>
												<!-- <div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['PROPERTY_287']['NAME']?></p>
														<?if(!empty($newArr['PROPERTY_287']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['PROPERTY_287']['HINT']?></span>
															</div>
														<?endif;?>													
													
														
													<div class="employee__card__item__option">
													<p><?=$newArr['PROPERTY_287']['VALUE']?></p></a>
													</div>
												</div> -->
											<?endif;?>
											<?if(!empty($elements[0]['PROPERTY_STATUS_VALUE'])):?>

												<div class="employee__card__item">
													<p class="employee__card__title">Статус</p>													
													
														
													<div class="employee__card__item__option">
													<p><?=$elements[0]['PROPERTY_STATUS_VALUE']?></p></a>
													</div>
												</div>
											<?endif;?>	

							<?
								
								foreach ($elements as $key => $value) {?>
									<a class="link" href="?mode=edit&list_id=48&section_id=0&element_id=<?=$value['ID']?>&list_section_id=">
										<p class="link__title"><?=$value['NAME']?></p>
									</a>
								<?}
								
								?>
                                
                            </div>
                            <!-- <a class="show__more" href="#">Показать еще</a> -->
                        </div>
						<?endif;?>
                    </div>
					<?endif;?>
					<?if(in_array(1, $arGroups) && $_GET['list_id'] == 47 || in_array(20, $arGroups) && $_GET['list_id'] == 47):?>
										<!-- <button href="#zatemnenie" data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn  popup_archive">Переместить в архив</button> -->
										<a href="#zatemnenie"  class="btn  popup_archive">Переместить в архив</a>
									
										<div id="zatemnenie">
      <div id="okno">
        Вы уверены что хотите перенести в <?=$arResult['ELEMENT_FIELDS']['NAME']?> архив?<br>
        <a href="#" class="close">Х</a>
		<button data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn add_archive">Переместить в архив</button>
      </div>
    </div>
									<?endif;?>	

					<?if(in_array(1, $arGroups) && $_GET['list_id'] == 52 || in_array(20, $arGroups) && $_GET['list_id'] == 52):?>	
						<!-- <button data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn recover">Востановить</button> -->
						<a href="#zatemnenie"  class="btn  popup_archive">Востановить</a>
									
										<div id="zatemnenie">
      <div id="okno">
        Вы уверены что хотите востановить <?=$arResult['ELEMENT_FIELDS']['NAME']?>?<br>
        <a href="#" class="close">Х</a>
		<button data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn recover">Востановить</button>
      </div>
    </div>


										<!-- <a href="<?=$dir?>&edit=yes" class="btn">Редактировать</a> -->
					<?endif;?>		


				
                </div>
            </div>
        </div>
    </main>
<?endif;?>

