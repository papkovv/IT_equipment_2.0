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

$arResult["FIELDS"]["PROPERTY_926"]["SETTINGS"] = [
	"SHOW_ADD_FORM" => "Y",
	"SHOW_EDIT_FORM" => "Y",
	"ADD_READ_ONLY_FIELD" => "N",
	"EDIT_READ_ONLY_FIELD" => "N",
	"SHOW_FIELD_PREVIEW" => "N",
];

$arResult["FIELDS"]["PROPERTY_927"]["SETTINGS"] = [
	"SHOW_ADD_FORM" => "Y",
	"SHOW_EDIT_FORM" => "Y",
	"ADD_READ_ONLY_FIELD" => "N",
	"EDIT_READ_ONLY_FIELD" => "N",
	"SHOW_FIELD_PREVIEW" => "N",
];

$arResult["FIELDS"]["PROPERTY_928"]["SETTINGS"] = [
	"SHOW_ADD_FORM" => "Y",
	"SHOW_EDIT_FORM" => "Y",
	"ADD_READ_ONLY_FIELD" => "N",
	"EDIT_READ_ONLY_FIELD" => "N",
	"SHOW_FIELD_PREVIEW" => "N",
];


/*echo '<pre>';
//print_r($arResult["FIELDS"]);
echo '</pre>';*/
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
<?if($_GET['list_id'] == 87 && $_GET['element_id'] == 0){
	 $rsElement = CIBlockElement::GetList(
        $arOrder  = array("ID" => "DESC"),
        $arFilter = array(
            "IBLOCK_ID"    => 85,
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
            // print_r( $arResult['FORM_DATA']);
            // echo "</pre>";
}?>

<div class="pagetitle-container pagetitle-align-right-container <?=$pagetitleAlignRightContainer?>">

	<?if($_GET['element_id'] != 0):?><a class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-themes" href="<?=$dir?>" style="margin-right: 20px;">Отмена редактирования</a><?endif;?>
	<a href="<?=$arResult["LIST_SECTION_URL"]?>" class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-themes">
		<?=GetMessage("CT_BLEE_TOOLBAR_RETURN_LIST_ELEMENT")?>
	</a>
	<?if($listAction):?>
		<?if(in_array(1, $arGroups)):?>
			<span id="lists-title-action" class="ui-btn ui-btn-sm ui-btn-light-border ui-btn-dropdown ui-btn-themes">
				<?=GetMessage("CT_BLEE_TOOLBAR_ACTION")?>
			</span>
		<?endif;?>
	<?endif;?>
</div>
<?
if($isBitrix24Template)
{
	$this->EndViewTarget();
}

$tabElement = array();
$cuctomHtml = "";
foreach($arResult["FIELDS"] as $fieldId => $field) {
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
			if(count($arEvents)) {
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

if(isset($arResult["RIGHTS"])) {
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
/*echo '<pre>';
//print_r($arResult['ELEMENT_PROPS']);
echo '</pre>';*/

//debug($arResult["FORM_DATA"]);
$APPLICATION->IncludeComponent(
	"bitrix:main.interface.form",
	"add-edit",
	array(
		"FORM_ID"=>$arResult["FORM_ID"],
		"TABS"=>$arTabs,
		"BUTTONS"=>array(
			"standard_buttons" => $arParams["CAN_EDIT"],
			"back_url"=>$arResult["BACK_URL"],
			"custom_html"=>$cuctomHtml,
		),
		"DATA"=>$arResult["FORM_DATA"],
		"SHOW_SETTINGS"=>"Y",
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
/*echo '<pre>';
    //print_r($newArr);
    echo '</pre>';*/
foreach ($newArr as $key => $field) {
	//echo $field['ID'].'</br>';
	//$key = $field['CODE'];
	foreach ($arResult['ELEMENT_PROPS'] as $keys => $value) {
		if($field['ID'] == $value['ID']){
			
			if(count($value['VALUES_LIST']) > 1){
				//echo $field['ID'].'</br>';
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
    //print_r($newArr);
    echo '</pre>';*/

?>
<main class="equip_page">
        <div class="container lk">
            <div class="row">
                <div class="col-9 px-0">
                    <div class="page">
                        <h1><?=$arResult['ELEMENT_FIELDS']['NAME']?></h1>
						<?if($_GET['list_id'] == 151):?>
										<?
											$rsUser = CUser::GetByID($newArr['KTO_IZMENIL']['VALUE']);
											$arUser = $rsUser->Fetch();
//							debug($arUser);
											$newArr['KTO_IZMENIL']['VALUE_NAME'] = $arUser['LAST_NAME'].' '.$arUser['NAME'];

											?>
										<p class="delete-margin-bottom">Изменил: <?=$newArr['KTO_IZMENIL']['VALUE_NAME']?></p>
									<?endif;?>
                        <div class="page__container delete-margin-top">
                            <div class="employee__card">
                                <div class="employee__card__header">

                                    <!-- <div class="employee__card__info">
                                        <h2 class="employee__card__fio"><?if(!empty($arUser['NAME'])):?><?=$arUser['LAST_NAME']. ' ' . $arUser['NAME']. ' ' . $arUser['PERSONAL_PAGER']?><?else:?><?=$arFields['NAME']?><?endif;?></h2>
                                        <p class="employee__card__title__job"><?if(!empty($arUser['WORK_POSITION'])){echo $arUser['WORK_POSITION'];}else{echo $arProps['POSITION']['VALUE'];}?></p>
                                    </div> -->



                                </div>
                                <div class="employee__card__body">
                                    <div class="employee__card__container">
<?if($_GET['list_id'] == 152 || $_GET['list_id'] == 155):?>


										<div class="employee__card__block">
											<?

//											$arFilter = Array("IBLOCK_ID"=>155,);
//											$res = CIBlockElement::GetList(Array(), $arFilter); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
//											while ($ob = $res->GetNextElement()){; // переходим к след элементу, если такой есть
//												$arFields = $ob->GetFields(); // поля элемента
//												debug($arFields);
//												$arProps = $ob->GetProperties(); // свойства элемента
//												debug($arProps);
//											}


//											debug($newArr);
											$res = CIBlockElement::GetByID($newArr['TYPE']['VALUE']);
											if($ar_res = $res->GetNext())
												$newArr['TYPE']['VALUE_NAME'] = $ar_res['NAME'];?>

											<?//if(!empty($newArr['ACTUAL_USER']['VALUE'])):
											$factUser = CUser::GetByID($newArr['ACTUAL_USER']['VALUE']);
											$factUserInfo = $factUser->Fetch();?>
											<div class="employee__card__item">
												<p class="employee__card__title"><?=$newArr['ACTUAL_USER']['NAME']?></p>
												<?if(!empty($newArr['TIP']['HINT'])):?>
													<div class="equipment_hint">
														<span class="equip_help"><?=$newArr['ACTUAL_USER']['HINT']?></span>
													</div>
												<?endif;?>
												<?
												$res = CIBlockElement::GetByID($newArr['ACTUAL_USER']['VALUE']);
												if($ar_res = $res->GetNext())
													$newArr['ACTUAL_USER']['VALUE_NAME'] = $ar_res['NAME'];
												?>
<!--												<div class="employee__card__item__option">-->
<!--													<p><a href="https://testportal.avtodor-eng.ru/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=&user_id=--><?php //=$newArr['ACTUAL_USER']['VALUE']?><!--">--><?php //=$newArr['ACTUAL_USER']['VALUE_NAME']?><!--</a></p>-->
<!--												</div>-->
												<div class="employee__card__item__option">
													<div class="option_changed">
														<p><a href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['ACTUAL_USER']['VALUE']?>"><?=$newArr['ACTUAL_USER']['VALUE_NAME']?></a>
															<?if (strlen($newArr['ACTUAL_USER']['VALUE']) > 0):?>
															<br>
														<a class="history" href="?mode=view&list_id=151&section_id=0&list_section_id=&change_fact=<?=$_GET['element_id']?>" >История перемещения</a>
															<?endif;?>
														</p>
													</div>
												</div>
											</div>
											<!--											--><?//endif;?>

											<?//if(!empty($newArr['TYPE']['VALUE'])):
												$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TYPE']['IBLOCK_ID'], "ID"=>$newArr['TYPE']['VALUE']));
												while($enum_fields = $property_enums->GetNext())
												{
													$newArr['TYPE']['VALUE'] = $enum_fields["VALUE"];
												}
//												$fio = urlUser($newArr['FIO']['VALUE']);
											?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['TYPE']['NAME']?></p>
														<?if(!empty($newArr['TYPE']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['TYPE']['HINT']?></span>
															</div>
														<?endif;?>


													<div class="employee__card__item__option">
														<p><a href="?mode=edit&list_id=153&section_id=0&element_id=<?=$newArr['TYPE']['VALUE']?>&list_section_id="><?=$newArr['TYPE']['VALUE_NAME']?></a></p>
													</div>
												</div>
<!--											--><?//endif;?>
<!--											--><?//if(!empty($newArr['MODEL']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['MODEL']['NAME']?></p>
														<?if(!empty($newArr['MODEL']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['MODEL']['HINT']?></span>
															</div>
														<?endif;?>


													<div class="employee__card__item__option">
															<p><?=$newArr['MODEL']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['SERIAL_NUMBER']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['SERIAL_NUMBER']['NAME']?></p>
														<?if(!empty($newArr['SERIAL_NUMBER']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['SERIAL_NUMBER']['HINT']?></span>
															</div>
														<?endif;?>


													<div class="employee__card__item__option">
															<p><?=$newArr['SERIAL_NUMBER']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['VENDOR_CODE']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['VENDOR_CODE']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['VENDOR_CODE']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['VENDOR_CODE']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['VENDOR_CODE']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

											<?//if(!empty($newArr['INVENTORY_NUMBER']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['INVENTORY_NUMBER']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['INVENTORY_NUMBER']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['INVENTORY_NUMBER']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['INVENTORY_NUMBER']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
											<!--											--><?//endif;?>

<!--											<div class="employee__card__line_title">-->
<!--												<h2>Вид номенклатуры</h2>-->
<!--											</div>-->
<!---->
<!--											--><?////if(!empty($newArr['NOMENCLATURE_TYPE_NAME']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['NOMENCLATURE_TYPE_NAME']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['NOMENCLATURE_TYPE_LINK']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['NOMENCLATURE_TYPE_LINK']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>

<!--											<div class="employee__card__line_title">-->
<!--												<h2>Единица измерения</h2>-->
<!--											</div>-->

<!--											--><?//if(!empty($newArr['QUANTITY']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['QUANTITY']['NAME']?></p>
														<?if(!empty($newArr['QUANTITY']['HINT'])):?>
															<div class="equipment_hint">
																<span class="equip_help"><?=$newArr['QUANTITY']['HINT']?></span>
															</div>
														<?endif;?>


													<div class="employee__card__item__option">
															<p><?=$newArr['QUANTITY']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['UNIT_NAME']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['UNIT_NAME']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['UNIT_NAME']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['UNIT_NAME']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['UNIT_NAME']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['UNIT_LINK']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['UNIT_LINK']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['UNIT_LINK']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['UNIT_LINK']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['UNIT_LINK']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['UNIT_CODE']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['UNIT_CODE']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['UNIT_CODE']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['UNIT_CODE']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['UNIT_CODE']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['MOL']['VALUE'])):?>
<!--												<div class="employee__card__line_title">-->
<!--													<h2>МОЛ</h2>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['MOL']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['MOL']['NAME']?></p>
													<?if(!empty($newArr['MOL']['HINT'])):?>
														<div class="equipment_hint">
															<span class="equip_help"><?=$newArr['MOL']['HINT']?></span>
														</div>
													<?endif;?>
													<?
													$res = CIBlockElement::GetByID($newArr['MOL']['VALUE']);
													if($ar_res = $res->GetNext())
														$newArr['MOL']['VALUE_NAME'] = $ar_res['NAME'];
													?>
<!--													<div class="employee__card__item__option">-->
<!--														<a href="https://testportal.avtodor-eng.ru/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=&user_id=--><?php //=$newArr['MOL']['VALUE']?><!--">--><?php //=$newArr['MOL']['VALUE_NAME']?><!--</a>-->
<!--													</div>-->
													<div class="employee__card__item__option">
														<div class="option_changed">
															<p><a href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['MOL']['VALUE']?>"><?=$newArr['MOL']['VALUE_NAME']?></a>
																<?if (strlen($newArr['MOL']['VALUE'])):?>
																<br>
															<a class="history" href="?mode=view&list_id=151&section_id=0&list_section_id=&change_mol=<?=$_GET['element_id']?>" >История перемещения</a>
															<?endif;?>
															</p>
														</div>
													</div>
												</div>
<!--											--><?//endif;?>
<!--											--><?//if(!empty($newArr['UID_MOL']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['UID_MOL']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['UID_MOL']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['UID_MOL']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['UID_MOL']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['CODE_MOL']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['CODE_MOL']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['CODE_MOL']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['CODE_MOL']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['CODE_MOL']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--

											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--												<div class="employee__card__line_title">-->
<!--													<h2>ТМЦ</h2>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['TMC_NAME']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['TMC_NAME']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['TMC_NAME']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['TMC_NAME']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['TMC_NAME']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['TMC_FULL_NAME']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['TMC_FULL_NAME']['NAME']?></p>
													<?if(!empty($newArr['TMC_FULL_NAME']['HINT'])):?>
														<div class="equipment_hint">
															<span class="equip_help"><?=$newArr['TMC_FULL_NAME']['HINT']?></span>
														</div>
													<?endif;?>


													<div class="employee__card__item__option">
														<p><?=$newArr['TMC_FULL_NAME']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['TMC_UID']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['TMC_UID']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['TMC_UID']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['TMC_UID']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['TMC_CODE']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['TMC_CODE']['NAME']?></p>
													<?if(!empty($newArr['TMC_CODE']['HINT'])):?>
														<div class="equipment_hint">
															<span class="equip_help"><?=$newArr['TMC_CODE']['HINT']?></span>
														</div>
													<?endif;?>


													<div class="employee__card__item__option">
														<p><?=$newArr['TMC_CODE']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--												<div class="employee__card__line_title">-->
<!--													<h2>Подразделение</h2>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['SUBDIVISION_CODE']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['SUBDIVISION_CODE']['NAME']?><!--</p>-->
<!--													--><?//if(!empty($newArr['SUBDIVISION_CODE']['HINT'])):?>
<!--														<div class="equipment_hint">-->
<!--															<span class="equip_help">--><?php //=$newArr['SUBDIVISION_CODE']['HINT']?><!--</span>-->
<!--														</div>-->
<!--													--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--														<p>--><?php //=$newArr['SUBDIVISION_CODE']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?////endif;?>
<!---->
<!--											--><?////if(!empty($newArr['SUBDIVISION_LINK']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['SUBDIVISION_LINK']['NAME']?><!--</p>-->
<!--														--><?//if(!empty($newArr['SUBDIVISION_LINK']['HINT'])):?>
<!--															<div class="equipment_hint">-->
<!--																<span class="equip_help">--><?php //=$newArr['SUBDIVISION_LINK']['HINT']?><!--</span>-->
<!--															</div>-->
<!--														--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--															<p>--><?php //=$newArr['SUBDIVISION_LINK']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['STRUCTURAL_SUBDIVISION']['VALUE'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['STRUCTURAL_SUBDIVISION']['NAME']?></p>
													<?if(!empty($newArr['STRUCTURAL_SUBDIVISION']['HINT'])):?>
														<div class="equipment_hint">
															<span class="equip_help"><?=$newArr['STRUCTURAL_SUBDIVISION']['HINT']?></span>
														</div>
													<?endif;?>


													<div class="employee__card__item__option">
														<p><?=$newArr['STRUCTURAL_SUBDIVISION']['VALUE']?></p>
													</div>
												</div>
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['SUBDIVISION_NAME_FULL']['VALUE'])):?>
<!--												<div class="employee__card__item">-->
<!--													<p class="employee__card__title">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['NAME']?><!--</p>-->
<!--														--><?//if(!empty($newArr['SUBDIVISION_NAME_FULL']['HINT'])):?>
<!--															<div class="equipment_hint">-->
<!--																<span class="equip_help">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['HINT']?><!--</span>-->
<!--															</div>-->
<!--														--><?//endif;?>
<!---->
<!---->
<!--													<div class="employee__card__item__option">-->
<!--															<p>--><?php //=$newArr['SUBDIVISION_NAME_FULL']['VALUE']?><!--</p>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['NOTE']['VALUE']['TEXT'])):?>
<!--												<div class="employee__card__line_title">-->
<!--													<h2>Примечание</h2>-->
<!--												</div>-->
<!--											--><?//endif;?>

<!--											--><?//if(!empty($newArr['NOTE']['VALUE']['TEXT'])):?>
												<div class="employee__card__item">
													<p class="employee__card__title"><?=$newArr['NOTE']['NAME']?></p>
													<?if(!empty($newArr['NOTE']['HINT'])):?>
														<div class="equipment_hint">
															<span class="equip_help"><?=$newArr['NOTE']['HINT']?></span>
														</div>
													<?endif;?>


													<div class="employee__card__item__option">
														<p><?=$newArr['NOTE']['VALUE']['TEXT']?></p>
													</div>
												</div>
<!--											--><?//endif;?>
										</div>

<?elseif($_GET['list_id'] == 151):?>
	<div class="employee__card__block">
		<?$check = 1;?>

		<?
		$changedElementDB = CIBlockElement::GetByID($newArr['CHANGED_ELEMENT']['VALUE']);
		$changedElement = $changedElementDB->Fetch();?>
		<div class="employee__card__item">
			<p class="employee__card__title"><?=$newArr['CHANGED_ELEMENT']['NAME']?></p>
			<?if(!empty($newArr['CHANGED_ELEMENT']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['CHANGED_ELEMENT']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p><a href="?mode=edit&list_id=152&section_id=0&element_id=<?=$newArr['CHANGED_ELEMENT']['VALUE']?>&list_section_id="><?=$changedElement['NAME']?></a></p>
			</div>
		</div>
		<?if ($changedElement['NAME'] != $newArr['EQUIPMENT_NAME_STARYY']['VALUE']):?>
		<?$check++;?>
		<div class="employee__card__item">
			<p class="employee__card__title" style="color:red"><?=$newArr['EQUIPMENT_NAME_STARYY']['NAME']?></p>
			<?if(!empty($newArr['EQUIPMENT_NAME_STARYY']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['EQUIPMENT_NAME_STARYY']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="color:red"><?=$newArr['EQUIPMENT_NAME_STARYY']['VALUE']?></p>
			</div>
		</div>
		<?endif;?>
		<?$check++;
//		$db_props = CIBlockElement::GetProperty($_GET['list_id'], $_GET['element_id'], array("sort" => "asc"), Array("CODE"=>"DATE_TIME_CHANGE"));
//		if($ar_props = $db_props->Fetch())
//			debug($ar_props);
//		?>
		<div class="employee__card__item">
			<p class="employee__card__title"><?=$newArr['DATE_TIME_CHANGE']['NAME']?></p>
			<?if(!empty($newArr['DATE_TIME_CHANGE']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['DATE_TIME_CHANGE']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p><?=$newArr['DATE_TIME_CHANGE']['VALUE']?></p>
			</div>
		</div>
	<?//if(!empty($newArr['ACTUAL_USER']['VALUE'])):
	$res = CIBlockElement::GetByID($newArr['ACTUAL_USER']['VALUE']);
	if($ar_res = $res->GetNext())
		$newArr['ACTUAL_USER']['VALUE_NAME'] = $ar_res['NAME'];
	$resStaryy = CIBlockElement::GetByID($newArr['ACTUAL_USER_STARYY']['VALUE']);
	if($ar_res = $resStaryy->GetNext())
		$newArr['ACTUAL_USER_STARYY']['VALUE_NAME'] = $ar_res['NAME'];
	$check++;?>
		<?if ($newArr['ACTUAL_USER']['VALUE'] != $newArr['ACTUAL_USER_STARYY']['VALUE']):
			$check++;
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
		?>
			<? if ($check%2 == 0):?>
			<div class="employee__card__item">

			</div>
		<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
		?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['ACTUAL_USER']['NAME']?></p>
			<?if(!empty($newArr['FIO']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['ACTUAL_USER']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p><a style="<?=$colorGreen?>" href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['ACTUAL_USER']['VALUE']?>"><?=$newArr['ACTUAL_USER']['VALUE_NAME']?></a></p>
			</div>
		</div>
	<?if($newArr['ACTUAL_USER']['VALUE'] != $newArr['ACTUAL_USER_STARYY']['VALUE']):?>
		<div class="employee__card__item">
			<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['ACTUAL_USER']['NAME']?></p>
			<?if(!empty($newArr['ACTUAL_USER']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['ACTUAL_USER']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<div class="option_changed">
					<p style="color:red"><a style="<?=$colorRed?>" href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['ACTUAL_USER_STARYY']['VALUE']?>"><?=$newArr['ACTUAL_USER_STARYY']['VALUE_NAME']?></a></p>
				</div>
			</div>
		</div>
	<?endif;?>

		<?//if(!empty($newArr['TYPE']['VALUE'])):
		$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$newArr['TYPE']['IBLOCK_ID'], "ID"=>$newArr['TYPE']['VALUE']));
		while($enum_fields = $property_enums->GetNext())
		{
			$newArr['TYPE']['VALUE'] = $enum_fields["VALUE"];
		}
		$res = CIBlockElement::GetByID($newArr['TYPE']['VALUE']);
		if($ar_res = $res->GetNext())
			$newArr['TYPE']['VALUE_NAME'] = $ar_res['NAME'];
		$res = CIBlockElement::GetByID($newArr['TYPE_STARYY']['VALUE']);
		if($ar_res = $res->GetNext())
			$newArr['TYPE_STARYY']['VALUE_NAME'] = $ar_res['NAME'];
		//												$fio = urlUser($newArr['FIO']['VALUE']);
		$check++;
		if ($newArr['TYPE']['VALUE'] != $newArr['TYPE_STARYY']['VALUE']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['TYPE']['NAME']?></p>
			<?if(!empty($newArr['TYPE']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['TYPE']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p><a style="<?=$colorGreen?>" href="?mode=edit&list_id=153&section_id=0&element_id=<?=$newArr['TYPE']['VALUE']?>&list_section_id="><?=$newArr['TYPE']['VALUE_NAME']?></a></p>
			</div>
		</div>
		<?if($newArr['TYPE']['VALUE'] != $newArr['TYPE_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['TYPE']['NAME']?></p>
				<?if(!empty($newArr['TYPE']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['TYPE']['HINT']?></span>
					</div>
				<?endif;
//				debug($newArr['']);?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p style="color:red"><a style="<?=$colorRed?>" href="?mode=edit&list_id=153&section_id=0&element_id=<?=$newArr['TYPE_STARYY']['VALUE']?>&list_section_id="><?=$newArr['TYPE_STARYY']['VALUE_NAME']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>
		<!--											--><?//if(!empty($newArr['MODEL']['VALUE'])):?>
		<? $check++;if ($newArr['MODEL']['VALUE'] != $newArr['MODEL_STARYY']['VALUE']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<? $check++;endif; ?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['MODEL']['NAME']?></p>
			<?if(!empty($newArr['MODEL']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['MODEL']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['MODEL']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['MODEL']['VALUE'] != $newArr['MODEL_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['MODEL']['NAME']?></p>
				<?if(!empty($newArr['MODEL']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['MODEL']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p style="color:red"><a style="<?=$colorRed?>" ><?=$newArr['MODEL_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['SERIAL_NUMBER']['VALUE'])):?>
		<?$check++; if ($newArr['SERIAL_NUMBER']['VALUE'] != $newArr['SERIAL_NUMBER_STARYY']['VALUE']):;
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;
//		debug($check%2);?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['SERIAL_NUMBER']['NAME']?></p>
			<?if(!empty($newArr['SERIAL_NUMBER']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['SERIAL_NUMBER']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['SERIAL_NUMBER']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['SERIAL_NUMBER']['VALUE'] != $newArr['SERIAL_NUMBER_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['SERIAL_NUMBER']['NAME']?></p>
				<?if(!empty($newArr['SERIAL_NUMBER']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['SERIAL_NUMBER']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p style="color:red"><a style="<?=$colorRed?>"><?=$newArr['SERIAL_NUMBER_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['VENDOR_CODE']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['VENDOR_CODE']['VALUE'] != $newArr['VENDOR_CODE_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif; ?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['VENDOR_CODE']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['VENDOR_CODE']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['VENDOR_CODE']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['VENDOR_CODE']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['VENDOR_CODE']['VALUE'] != $newArr['VENDOR_CODE_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['VENDOR_CODE']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['VENDOR_CODE']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['VENDOR_CODE']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--">--><?php //=$newArr['VENDOR_CODE_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<?//if(!empty($newArr['INVENTORY_NUMBER']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['INVENTORY_NUMBER']['VALUE'] != $newArr['INVENTORY_NUMBER_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif; ?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['INVENTORY_NUMBER']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['INVENTORY_NUMBER']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['INVENTORY_NUMBER']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['INVENTORY_NUMBER']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['INVENTORY_NUMBER']['VALUE'] != $newArr['INVENTORY_NUMBER_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['INVENTORY_NUMBER']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['INVENTORY_NUMBER']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['INVENTORY_NUMBER']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--">--><?php //=$newArr['INVENTORY_NUMBER_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

<!--		<div class="employee__card__line_title">-->
<!--			<h2>Вид номенклатуры</h2>-->
<!--		</div>-->
<!---->
<!--													--><?////if(!empty($newArr['NOMENCLATURE_TYPE_NAME']['VALUE'])):?>
<!--		--><?// if($newArr['NOMENCLATURE_TYPE_NAME']['VALUE'] != $newArr['NOMENCLATURE_TYPE_NAME_STARYY']['VALUE']) {
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//		}
//		else {
//			$colorGreen = '';
//			$colorRed = '';
//		}
//		?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['NOMENCLATURE_TYPE_NAME']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['NOMENCLATURE_TYPE_NAME']['VALUE'] != $newArr['NOMENCLATURE_TYPE_NAME_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['NOMENCLATURE_TYPE_NAME']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_NAME']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['NOMENCLATURE_TYPE_NAME_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['NOMENCLATURE_TYPE_LINK']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['NOMENCLATURE_TYPE_LINK']['VALUE'] != $newArr['NOMENCLATURE_TYPE_LINK_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['NOMENCLATURE_TYPE_LINK']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['NOMENCLATURE_TYPE_LINK']['VALUE'] != $newArr['NOMENCLATURE_TYPE_LINK_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['NOMENCLATURE_TYPE_LINK']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['NOMENCLATURE_TYPE_LINK']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['NOMENCLATURE_TYPE_LINK_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>

<!--		<div class="employee__card__line_title">-->
<!--			<h2>Единица измерения</h2>-->
<!--		</div>-->

		<!--											--><?//if(!empty($newArr['QUANTITY']['VALUE'])):?>
		<? if($newArr['QUANTITY']['VALUE'] != $newArr['QUANTITY_STARYY']['VALUE']) {
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
		}
		else {
			$colorGreen = '';
			$colorRed = '';
		}
		?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['QUANTITY']['NAME']?></p>
			<?if(!empty($newArr['QUANTITY']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['QUANTITY']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['QUANTITY']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['QUANTITY']['VALUE'] != $newArr['QUANTITY_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['QUANTITY']['NAME']?></p>
				<?if(!empty($newArr['QUANTITY']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['QUANTITY']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p style="color:red"><a style="<?=$colorRed?>" ><?=$newArr['QUANTITY_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['UNIT_NAME']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['UNIT_NAME']['VALUE'] != $newArr['UNIT_NAME_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif; ?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['UNIT_NAME']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['UNIT_NAME']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['UNIT_NAME']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['UNIT_NAME']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['UNIT_NAME']['VALUE'] != $newArr['UNIT_NAME_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['UNIT_NAME']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['UNIT_NAME']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['UNIT_NAME']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['UNIT_NAME_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['UNIT_LINK']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['UNIT_LINK']['VALUE'] != $newArr['UNIT_LINK_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['UNIT_LINK']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['UNIT_LINK']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['UNIT_LINK']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['UNIT_LINK']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['UNIT_LINK']['VALUE'] != $newArr['UNIT_LINK_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['UNIT_LINK']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['UNIT_LINK']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['UNIT_LINK']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['UNIT_LINK_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['UNIT_CODE']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['UNIT_CODE']['VALUE'] != $newArr['UNIT_CODE_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['UNIT_CODE']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['UNIT_CODE']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['UNIT_CODE']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['UNIT_CODE']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['UNIT_CODE']['VALUE'] != $newArr['UNIT_CODE_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['UNIT_CODE']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['UNIT_CODE']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['UNIT_CODE']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p style="color:red"><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['UNIT_CODE_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['MOL']['VALUE'])):?>
<!--		<div class="employee__card__line_title">-->
<!--			<h2>МОЛ</h2>-->
<!--		</div>-->
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['MOL']['VALUE'])):?>
		<? if($newArr['MOL']['VALUE'] != $newArr['MOL_STARYY']['VALUE']) {
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
		}
		else {
			$colorGreen = '';
			$colorRed = '';
		}
		?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['MOL']['NAME']?></p>
			<?if(!empty($newArr['MOL']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['MOL']['HINT']?></span>
				</div>
			<?endif;?>
			<?
			$res = CIBlockElement::GetByID($newArr['MOL']['VALUE']);
			if($ar_res = $res->GetNext())
				$newArr['MOL']['VALUE_NAME'] = $ar_res['NAME'];
			$res = CIBlockElement::GetByID($newArr['MOL_STARYY']['VALUE']);
			if($ar_res = $res->GetNext())
				$newArr['MOL_STARYY']['VALUE_NAME'] = $ar_res['NAME'];
			?>
			<!--													<div class="employee__card__item__option">-->
			<!--														<a href="https://testportal.avtodor-eng.ru/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=&user_id=--><?php //=$newArr['MOL']['VALUE']?><!--">--><?php //=$newArr['MOL']['VALUE_NAME']?><!--</a>-->
			<!--													</div>-->
			<div class="employee__card__item__option">
				<div class="option_changed">
					<p><a style="<?=$colorGreen?>" href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['MOL']['VALUE']?>"><?=$newArr['MOL']['VALUE_NAME']?></a></p>
				</div>
			</div>
		</div>
		<?if($newArr['MOL']['VALUE'] != $newArr['MOL_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['MOL']['NAME']?></p>
				<?if(!empty($newArr['MOL']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['MOL']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p><a style="<?=$colorRed?>" href="?mode=view&list_id=152&section_id=0&list_section_id=&user_id=<?=$newArr['MOL_STARYY']['VALUE']?>"><?=$newArr['MOL_STARYY']['VALUE_NAME']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>
		<!--											--><?//if(!empty($newArr['UID_MOL']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['UID_MOL']['VALUE'] != $newArr['UID_MOL_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['UID_MOL']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['UID_MOL']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['UID_MOL']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['UID_MOL']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['UID_MOL']['VALUE'] != $newArr['UID_MOL_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['UID_MOL']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['UID_MOL']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['UID_MOL']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['UID_MOL_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['CODE_MOL']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['CODE_MOL']['VALUE'] != $newArr['CODE_MOL_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['CODE_MOL']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['CODE_MOL']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['CODE_MOL']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['CODE_MOL']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['CODE_MOL']['VALUE'] != $newArr['CODE_MOL_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['CODE_MOL']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['CODE_MOL']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['CODE_MOL']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['CODE_MOL_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<!--

        											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--		<div class="employee__card__line_title">-->
<!--			<h2>ТМЦ</h2>-->
<!--		</div>-->
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['TMC_NAME']['VALUE'])):?>
<!--		--><?// if($newArr['TMC_NAME']['VALUE'] != $newArr['TMC_NAME_STARYY']['VALUE']) {
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//		}
//		else {
//			$colorGreen = '';
//			$colorRed = '';
//		}
//		?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['TMC_NAME']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['TMC_NAME']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['TMC_NAME']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['TMC_NAME']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['TMC_NAME']['VALUE'] != $newArr['TMC_NAME_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['TMC_NAME']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['TMC_NAME']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['TMC_NAME']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['TMC_NAME_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['TMC_FULL_NAME']['VALUE'])):?>
		<?$check++;?>
		<?if ($newArr['TMC_FULL_NAME']['VALUE'] != $newArr['TMC_FULL_NAME_STARYY']['VALUE']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['TMC_FULL_NAME']['NAME']?></p>
			<?if(!empty($newArr['TMC_FULL_NAME']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['TMC_FULL_NAME']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['TMC_FULL_NAME']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['TMC_FULL_NAME']['VALUE'] != $newArr['TMC_FULL_NAME_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['TMC_FULL_NAME']['NAME']?></p>
				<?if(!empty($newArr['TMC_FULL_NAME']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['TMC_FULL_NAME']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p><a style="<?=$colorRed?>" ><?=$newArr['TMC_FULL_NAME_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['TMC_UID']['VALUE'] != $newArr['TMC_UID_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['TMC_UID']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['TMC_UID']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['TMC_UID']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['TMC_UID']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['TMC_UID']['VALUE'] != $newArr['TMC_UID_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['TMC_UID']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['TMC_UID']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['TMC_UID']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['TMC_UID_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['TMC_CODE']['VALUE'])):?>
		<?$check++;?>
		<?if ($newArr['TMC_CODE']['VALUE'] != $newArr['TMC_CODE_STARYY']['VALUE']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['TMC_CODE']['NAME']?></p>
			<?if(!empty($newArr['TMC_CODE']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['TMC_CODE']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['TMC_CODE']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['TMC_CODE']['VALUE'] != $newArr['TMC_CODE_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['TMC_CODE']['NAME']?></p>
				<?if(!empty($newArr['TMC_CODE']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['TMC_CODE']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p><a style="<?=$colorRed?>" ><?=$newArr['TMC_CODE_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['TMC_UID']['VALUE'])):?>
<!--		<div class="employee__card__line_title">-->
<!--			<h2>Подразделение</h2>-->
<!--		</div>-->
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['SUBDIVISION_CODE']['VALUE'])):?>
<!--		--><?// if($newArr['SUBDIVISION_CODE']['VALUE'] != $newArr['SUBDIVISION_CODE_STARYY']['VALUE']) {
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//		}
//		else {
//			$colorGreen = '';
//			$colorRed = '';
//		}
//		?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['SUBDIVISION_CODE']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['SUBDIVISION_CODE']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['SUBDIVISION_CODE']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['SUBDIVISION_CODE']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['SUBDIVISION_CODE']['VALUE'] != $newArr['SUBDIVISION_CODE_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['SUBDIVISION_CODE']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['SUBDIVISION_CODE']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['SUBDIVISION_CODE']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['SUBDIVISION_CODE_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
<!--													--><?////endif;?>
<!---->
<!--													--><?////if(!empty($newArr['SUBDIVISION_LINK']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['SUBDIVISION_LINK']['VALUE'] != $newArr['SUBDIVISION_LINK_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['SUBDIVISION_LINK']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['SUBDIVISION_LINK']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['SUBDIVISION_LINK']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['SUBDIVISION_LINK']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['SUBDIVISION_LINK']['VALUE'] != $newArr['SUBDIVISION_LINK_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['SUBDIVISION_LINK']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['SUBDIVISION_LINK']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['SUBDIVISION_LINK']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['SUBDIVISION_LINK_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['STRUCTURAL_SUBDIVISION']['VALUE'])):?>
		<?$check++;?>
		<?if ($newArr['STRUCTURAL_SUBDIVISION']['VALUE'] != $newArr['STRUCTURAL_SUBDIVISION_STARYY']['VALUE']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['STRUCTURAL_SUBDIVISION']['NAME']?></p>
			<?if(!empty($newArr['STRUCTURAL_SUBDIVISION']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['STRUCTURAL_SUBDIVISION']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['STRUCTURAL_SUBDIVISION']['VALUE']?></p>
			</div>
		</div>
		<?if($newArr['STRUCTURAL_SUBDIVISION']['VALUE'] != $newArr['STRUCTURAL_SUBDIVISION_STARYY']['VALUE']):
			$check++;?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['STRUCTURAL_SUBDIVISION']['NAME']?></p>
				<?if(!empty($newArr['STRUCTURAL_SUBDIVISION']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['STRUCTURAL_SUBDIVISION']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p><a style="<?=$colorRed?>" ><?=$newArr['STRUCTURAL_SUBDIVISION_STARYY']['VALUE']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['SUBDIVISION_NAME_FULL']['VALUE'])):?>
<!--		--><?//$check++;?>
<!--		--><?//if ($newArr['SUBDIVISION_NAME_FULL']['VALUE'] != $newArr['SUBDIVISION_NAME_FULL_STARYY']['VALUE']):
//			$colorGreen = 'color: green';
//			$colorRed = 'color: red';
//			?>
<!--			--><?// if ($check%2 == 0):?>
<!--				<div class="employee__card__item">-->
<!---->
<!--				</div>-->
<!--			--><?//endif;
//			$check++;?>
<!--		--><?//else:
//			$colorGreen = '';
//			$colorRed = '';
//			?>
<!--		--><?//endif;?>
<!--		<div class="employee__card__item">-->
<!--			<p style="--><?php //=$colorGreen?><!--" class="employee__card__title">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['NAME']?><!--</p>-->
<!--			--><?//if(!empty($newArr['SUBDIVISION_NAME_FULL']['HINT'])):?>
<!--				<div class="equipment_hint">-->
<!--					<span class="equip_help">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['HINT']?><!--</span>-->
<!--				</div>-->
<!--			--><?//endif;?>
<!---->
<!---->
<!--			<div class="employee__card__item__option">-->
<!--				<p style="--><?php //=$colorGreen?><!--" >--><?php //=$newArr['SUBDIVISION_NAME_FULL']['VALUE']?><!--</p>-->
<!--			</div>-->
<!--		</div>-->
<!--		--><?//if($newArr['SUBDIVISION_NAME_FULL']['VALUE'] != $newArr['SUBDIVISION_NAME_FULL_STARYY']['VALUE']):
//			$check++;?>
<!--			<div class="employee__card__item">-->
<!--				<p class="employee__card__title" style="--><?php //=$colorRed?><!--">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['NAME']?><!--</p>-->
<!--				--><?//if(!empty($newArr['SUBDIVISION_NAME_FULL']['HINT'])):?>
<!--					<div class="equipment_hint">-->
<!--						<span class="equip_help">--><?php //=$newArr['SUBDIVISION_NAME_FULL']['HINT']?><!--</span>-->
<!--					</div>-->
<!--				--><?//endif;?>
<!---->
<!---->
<!--				<div class="employee__card__item__option">-->
<!--					<div class="option_changed">-->
<!--						<p><a style="--><?php //=$colorRed?><!--" >--><?php //=$newArr['SUBDIVISION_NAME_FULL_STARYY']['VALUE']?><!--</a></p>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		--><?//endif;?>
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['NOTE']['VALUE']['TEXT'])):?>
<!--		<div class="employee__card__line_title">-->
<!--			<h2>Примечание</h2>-->
<!--		</div>-->
		<!--											--><?//endif;?>

		<!--											--><?//if(!empty($newArr['NOTE']['VALUE']['TEXT'])):?>
		<?$check++;?>
		<? if ($newArr['NOTE']['VALUE']['TEXT'] != $newArr['NOTE_STARYY']['VALUE']['TEXT']):
			$colorGreen = 'color: green';
			$colorRed = 'color: red';
			?>
			<? if ($check%2 == 0):?>
				<div class="employee__card__item">

				</div>
			<?endif;
			$check++;?>
		<?else:
			$colorGreen = '';
			$colorRed = '';
			?>
		<?endif;?>
		<div class="employee__card__item">
			<p style="<?=$colorGreen?>" class="employee__card__title"><?=$newArr['NOTE']['NAME']?></p>
			<?if(!empty($newArr['NOTE']['HINT'])):?>
				<div class="equipment_hint">
					<span class="equip_help"><?=$newArr['NOTE']['HINT']?></span>
				</div>
			<?endif;?>


			<div class="employee__card__item__option">
				<p style="<?=$colorGreen?>" ><?=$newArr['NOTE']['VALUE']['TEXT']?></p>
			</div>
		</div>
		<?if($newArr['NOTE']['VALUE'] != $newArr['NOTE_STARYY']['VALUE']):?>
			<div class="employee__card__item">
				<p class="employee__card__title" style="<?=$colorRed?>"><?=$newArr['NOTE']['NAME']?></p>
				<?if(!empty($newArr['NOTE']['HINT'])):?>
					<div class="equipment_hint">
						<span class="equip_help"><?=$newArr['NOTE']['HINT']?></span>
					</div>
				<?endif;?>


				<div class="employee__card__item__option">
					<div class="option_changed">
						<p><a style="<?=$colorRed?>" ><?=$newArr['NOTE_STARYY']['VALUE']['TEXT']?></a></p>

					</div>
				</div>
			</div>
		<?endif;?>
		<!--											--><?//endif;?>
	</div>
<?endif;?>


                </div>
				</div>
            </div>
        </div>
		</div>
		</div>
                <div class="col-3 px-0">
					<?if ($_GET['list_id'] == 152):?>
						<a href="<?=$dir?>&edit=yes" class="btn">Редактировать</a>
					<?endif;?>
					<?if ($_GET['list_id'] == 152):?>
<!--					<div class="widget__container lk">-->
						<div class="widget__block lk margin-top-10">
							<div class="favorites__block">
						<a data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn decommission">Списать оборудование</a>
							</div>
						</div>
<!--					</div>-->
						<script>
							$('.decommission').on('click', function(){
								var id = $(this).data('id');
								// console.log(id);
								$.ajax({
									type: 'POST',
									url: '/decommission.php',
									data: {ID: id},
									success: function(data) {

										console.log(data);
										window.location.href = '/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=';
										// location.reload();
										// location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
									},
									error:  function(xhr, str){
										alert('Возникла ошибка: ' + xhr.responseCode);
									}

								});
							});
						</script>
					<? endif;?>
					<?if ($_GET['list_id'] == 155):?>
						<a data-id="<?=$arResult['ELEMENT_FIELDS']['ID']?>"  class="btn restore">Восстановить оборудование</a>
						<script>
							$('.restore').on('click', function(){
								var id = $(this).data('id');
								// console.log(id);
								$.ajax({
									type: 'POST',
									url: '/restore.php',
									data: {ID: id},
									success: function(data) {

										// console.log(data);
										window.location.href = '/it-equipment-2/?mode=view&list_id=155&section_id=0&list_section_id=';
										// location.reload();
										// location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
									},
									error:  function(xhr, str){
										alert('Возникла ошибка: ' + xhr.responseCode);
									}

								});
							});
						</script>
					<? endif;?>
					<? if ($_GET['list_id'] != 153 && $_GET['list_id'] != 151):?>
						<div class="widget__container lk">
							<div class="widget__block lk">
								<div class="favorites__block">
									<h2 class="widget__title lk"><a class="history" href="?mode=view&list_id=151&section_id=0&list_section_id=&change=<?=$_GET['element_id']?>">История МЦ</a></h2>
								</div>
							</div>
						</div>
					<?endif;?>

					<? if ($_GET['list_id'] == 151):?>
						<div class="widget__container lk">
							<div class="widget__block lk">
								<div class="favorites__block">
									<h2 class="widget__title lk"><a class="history" href="?mode=view&list_id=151&section_id=0&list_section_id=&change=<?=$newArr['CHANGED_ELEMENT']['VALUE']?>">История МЦ</a></h2>
								</div>
							</div>
						</div>
					<?endif;?>
				<?if(in_array(1, $arGroups) && $_GET['list_id'] == 152 || in_array(38, $arGroups) && $_GET['list_id'] == 152 || $_GET['list_id'] == 153):?>


<!--										 <button data-id="--><?php //=$arResult['ELEMENT_FIELDS']['ID']?><!--"  class="btn add_archive">Переместить в архив</button>-->




<!--					<div class="history_mc">-->
<!--					<a href="?mode=view&list_id=151&section_id=0&list_section_id=&change=--><?php //=$_GET['element_id']?><!--" class="btn">История МЦ</a>-->
<!--					</div>-->
				<?endif;?>

                </div>
            </div>
        </div>
    </main>
<?endif;?>

