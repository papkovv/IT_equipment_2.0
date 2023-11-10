<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//color schemes
if($arParams["USE_THEMES"])
	$arThemes = CGridOptions::GetThemes($this->GetFolder());
else
	$arThemes = array();
?>

<div class="bx-interface-form">

	<script type="text/javascript">
		var bxForm_<?=$arParams["FORM_ID"]?> = null;
	</script>

	<? if($arParams["SHOW_FORM_TAG"]):?>
	<form name="form_<?=$arParams["FORM_ID"]?>" id="form_<?=$arParams["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>" method="POST" enctype="multipart/form-data">

		<?=bitrix_sessid_post();?>
		<input type="hidden" id="<?=$arParams["FORM_ID"]?>_active_tab" name="<?=$arParams["FORM_ID"]?>_active_tab" value="<?=htmlspecialcharsbx($arResult["SELECTED_TAB"])?>">
		<?endif?>
		<table cellspacing="0" class="bx-edit-tabs" width="100%">
			<tr>
				<td class="bx-tab-indent"><div class="empty"></div></td>
				<?
				$nTabs = count($arResult["TABS"]);
				foreach($arResult["TABS"] as $tab):
					$bSelected = ($tab["id"] == $arResult["SELECTED_TAB"]);

					$callback = '';
					if($tab['onselect_callback'] <> '')
					{
						$callback = trim($tab['onselect_callback']);
						if(!preg_match('#^[a-z0-9-_\.]+$#i', $callback))
						{
							$callback = '';
						}
					}
					?>
					<td title="<?=htmlspecialcharsbx($tab["title"])?>" id="tab_cont_<?=$tab["id"]?>" class="bx-tab-container<?=($bSelected? "-selected":"")?>" onclick="<? if($callback <> ''):?><?= $callback ?>('<?=$tab["id"]?>');<?endif?>bxForm_<?=$arParams["FORM_ID"]?>.SelectTab('<?=$tab["id"]?>');" onmouseover="if(window.bxForm_<?=$arParams["FORM_ID"]?>){bxForm_<?=$arParams["FORM_ID"]?>.HoverTab('<?=$tab["id"]?>', true);}" onmouseout="if(window.bxForm_<?=$arParams["FORM_ID"]?>){bxForm_<?=$arParams["FORM_ID"]?>.HoverTab('<?=$tab["id"]?>', false);}">
						<table cellspacing="0">
							<tr>
								<td class="bx-tab-left<?=($bSelected? "-selected":"")?>" id="tab_left_<?=$tab["id"]?>"><div class="empty"></div></td>
								<td class="bx-tab<?=($bSelected? "-selected":"")?>" id="tab_<?=$tab["id"]?>"><?=htmlspecialcharsbx($tab["name"])?></td>
								<td class="bx-tab-right<?=($bSelected? "-selected":"")?>" id="tab_right_<?=$tab["id"]?>"><div class="empty"></div></td>
							</tr>
						</table>
					</td>
				<?
				endforeach;
				?>
				<td width="100%"<?if($USER->IsAuthorized() && $arParams["SHOW_SETTINGS"] == true):?> ondblclick="bxForm_<?=$arParams["FORM_ID"]?>.ShowSettings()"<?endif?> style="white-space:nowrap; text-align:right">
					<?if(count($arResult["TABS"]) > 1 && $arParams["CAN_EXPAND_TABS"] == true):?>
						<a href="javascript:void(0)" onclick="bxForm_<?=$arParams["FORM_ID"]?>.ToggleTabs();" title="<?echo GetMessage("interface_form_show_all")?>" id="bxForm_<?=$arParams["FORM_ID"]?>_expand_link" class="bx-context-button bx-down"><span></span></a>
					<?endif?>
					<?if($arParams["SHOW_SETTINGS"] || !empty($arThemes)):?>
						<a href="javascript:void(0)" onclick="bxForm_<?=$arParams["FORM_ID"]?>.menu.ShowMenu(this, bxForm_<?=$arParams["FORM_ID"]?>.settingsMenu);" title="<?echo GetMessage("interface_form_settings")?>" class="bx-context-button bx-form-menu"><span></span></a>
					<?endif;?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" class="bx-edit-tab">
			<tr>
				<td>
					<?
//					$order = array('sort' => 'asc');
//					$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
//					$rsUsers = CUser::GetList($order, $tmp);
//					while($user = $rsUsers->fetch()){
//						$arUsers[] = $user;
//					}
//					debug($arUsers);

					//					debug($arFields);

					if ($_GET['element_id'] != 0) {
						$db_props = CIBlockElement::GetProperty($_GET['list_id'], $_GET['element_id'], array("sort" => "asc"));
						if($ar_props = $db_props->Fetch()) {
							$res = CIBlockElement::GetByID($ar_props['VALUE']);
							if($ar_res = $res->GetNext()) {}
//								debug($ar_res['NAME']);
						}
					}

					if ($_GET['element_id'] != 0) {
						$db_props = CIBlockElement::GetProperty($_GET['list_id'], $_GET['element_id'], array("sort" => "asc"), Array("CODE"=>"ACTUAL_USER"));
						if($ar_prop = $db_props->Fetch()) {
							$res = CIBlockElement::GetByID($ar_prop['VALUE']);
							if($ar_user = $res->GetNext()) {}
//								debug($ar_user);
						}
					}

					if ($_GET['element_id'] != 0) {
						$db_props_mol = CIBlockElement::GetProperty($_GET['list_id'], $_GET['element_id'], array("sort" => "asc"), Array("CODE"=>"MOL"));
						if($ar_prop_mol = $db_props_mol->Fetch()) {
							$res_mol = CIBlockElement::GetByID($ar_prop_mol['VALUE']);
							if($ar_user_mol = $res_mol->GetNext()) {}
//								debug($ar_user);
						}
					}

					$bWasRequired = false;
					foreach($arResult["TABS"] as $tab):
						?>
						<div id="inner_tab_<?=$tab["id"]?>" class="bx-edit-tab-inner"<?if($tab["id"] <> $arResult["SELECTED_TAB"]) echo ' style="display:none;"'?>>
							<div style="height: 100%;">
								<?if($tab["title"] <> ''):?>
									<div class="bx-edit-tab-title">
										<table cellpadding="0" cellspacing="0" border="0" class="bx-edit-tab-title">
											<tr>
												<?
												if($tab["icon"] <> ""):
													?>
													<td class="bx-icon"><div class="<?=htmlspecialcharsbx($tab["icon"])?>"></div></td>
												<?
												endif
												?>
												<td class="bx-form-title"><?=htmlspecialcharsbx($tab["title"])?></td>
											</tr>
										</table>
									</div>
								<?endif;?>

								<div class="bx-edit-table">
									<table cellpadding="0" cellspacing="0" border="0" class="bx-edit-table <?=(isset($tab["class"]) ? $tab['class'] : '')?>" id="<?=$tab["id"]?>_edit_table">
										<?
										$i = 0;
										$j = 0;
										$cnt = count($tab["fields"]);
										$prevType = '';
										foreach($tab["fields"] as $field):
											$style = '';
											if(isset($field["show"]))
											{
												if($field["show"] == "N")
												{
													$style = "display:none;";
												}
											}

											$i++;
											if(!is_array($field))
												continue;

											$className = array();
											if($i == 1)
													$className[] = 'bx-top';
//											if($_GET['list_id'] == 152) {
//												if($j) {
//													$className[] = 'bx-top';
//													$j = 0;
//												}
//												if ($field['id'] == 'NAME') {
//													$style = 'display: none';
//													$j++;
//												}
//											} else {
//												if($i == 1)
//													$className[] = 'bx-top';
//											}
											if($i == $cnt)
												$className[] = 'bx-bottom';
											if($prevType == 'section')
												$className[] = 'bx-after-heading';

											if($field['class'] <> '')
											{
												$className[] = $field['class'];
											}?>
											<?if ($field['id'] != ''):?>
											<tr<?if(!empty($className)):?> class="<?=implode(' ', $className)?>"<?endif?><?if(!empty($style)):?> style="<?= $style ?>"<?endif?>>
												<?
												if($field["type"] == 'section'):
													?>
													<td colspan="2" class="bx-heading"><?=htmlspecialcharsbx($field["name"])?></td>
												<?
												else:
													$val = (isset($field["value"])? $field["value"] : $arParams["~DATA"][$field["id"]]);
													$valEncoded = '';
													if(!is_array($val))
														$valEncoded = htmlspecialcharsbx(htmlspecialcharsback($val));

													//default attributes
													if(!is_array($field["params"]))
														$field["params"] = array();
													if($field["type"] == '' || $field["type"] == 'text')
													{
														if($field["params"]["size"] == '')
															$field["params"]["size"] = "30";
													}
													elseif($field["type"] == 'textarea')
													{
														if($field["params"]["cols"] == '')
															$field["params"]["cols"] = "40";
														if($field["params"]["rows"] == '')
															$field["params"]["rows"] = "3";
													}
													elseif($field["type"] == 'date')
													{
														if($field["params"]["size"] == '')
															$field["params"]["size"] = "10";
													}

													$params = '';
													if(is_array($field["params"]) && $field["type"] <> 'file')
													{
														foreach($field["params"] as $p=>$v)
															$params .= ' '.$p.'="'.$v.'"';
													}

													if($field["colspan"] <> true):
														if($field["required"])
															$bWasRequired = true;
														?>
														<td class="bx-field-name<?if($field["type"] <> 'label') echo' bx-padding'?>"<?if($field["title"] <> '') echo ' title="'.htmlspecialcharsEx($field["title"]).'"'?>><?=($field["required"]? '<span class="required">*</span>':'')?><? if($field["name"] <> ''):?><?= htmlspecialcharsEx($field["name"]) ?>:<?endif?></td>
													<?
													endif
													?>
													<td class="bx-field-value <?=$field['id']?>"<?=($field["colspan"]? ' colspan="2"':'')?>>
														<?
														if ($field['id'] == "PROPERTY_1407"):
															$sections = Array();
															$rsParentSection = CIBlockSection::GetByID(2210);
															if ($arParentSection = $rsParentSection->GetNext())
															{
																$arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
																$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);

																while ($arSect = $rsSect->GetNext())
																{
																	// получаем подразделы
																	$sections[] = $arSect['ID'];
																}
															}

															$arFields = Array();
															$arSelect = Array();
															$arFilter = Array("SECTION_ID"=>$sections, "ACTIVE"=>"Y");
															$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
															$i = 0;
															while($ob = $res->GetNextElement())
															{
																if ($ob->GetFields()['IBLOCK_ID'] == 61) {
																	$arFields[] = $ob->GetFields();
																}
															}
//															debug($arFields[0]);
															?>
															<div class="search-1407">
																<input autocomplete="off" id="input-search-1407" class="<?=$field['id']?>" type="text" name="<?=$field["id"]?>" size="30" value="<?=$ar_user['NAME']?>">
																<div class="search-list-block-1407">
																	<select data-id="<?=$ar_user['ID']?>" data-name="<?=$ar_user['NAME']?>" class="height-to-zero" name="<?=$field['id']?>" id="search-list-1407" size="0">
																		<?
																		foreach ($arFields as $arField):
																			$res = CIBlockSection::GetByID($arField['IBLOCK_SECTION_ID']);
																			if($ar_res = $res->GetNext()):?>
																				<option data-department="<?=$ar_res['NAME']?>" value="<?=$arField['ID']?>"><?=$arField['NAME']?></option>
																			<? endif; ?>
																		<? endforeach; ?>
																		<option selected disabled class="height-to-zero"></option>
																	</select>
																</div>
															</div>

														<?elseif ($field['id'] == "PROPERTY_1404"):
															$arSelect = Array("ID", "NAME");
															$arFilter = Array("IBLOCK_ID"=>153, "ACTIVE"=>"Y");
															$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
															$arFields = array();
															while($ob = $res->GetNextElement())
															{
																$arFields[] = $ob->GetFields();

															}
//															debug($arFields);?>
															<div class="search-1404">
																<input readonly="readonly" autocomplete="off" required id="input-search-1404" class="<?=$field['id']?>" type="text" name="<?=$field["id"]?>" size="30" value="<?=$ar_res['NAME']?>">
																<div class="search-list-block-1404">
																	<select data-id="<?=$ar_props['VALUE']?>" data-name="<?=$ar_res['NAME']?>" class="height-to-zero" name="<?=$field['id']?>" id="search-list-1404" size="0">
																		<? foreach ($arFields as $arField):?>
																			<option value="<?=$arField['ID']?>"><?=$arField['NAME']?></option>
																		<? endforeach; ?>
																		<option selected disabled class="height-to-zero"></option>
																	</select>
																</div>
															</div>
															<? elseif ($field['id'] == "PROPERTY_1409"):?>
															<div class="search-1409">
																<input readonly="readonly" autocomplete="off" id="input-search-1409" class="<?=$field['id']?>" type="text" name="<?=$field["id"]?>" size="30" value="<?=$ar_user_mol['NAME']?>">
																<div class="search-list-block-1409">
																	<select data-id="<?=$ar_user_mol['ID']?>" data-name="<?=$ar_user_mol['NAME']?>" class="height-to-zero" name="<?=$field['id']?>" id="search-list-1409" size="0">
																		<?
																		foreach ($arFields as $arField):
																			$res = CIBlockSection::GetByID($arField['IBLOCK_SECTION_ID']);
																			if($ar_res = $res->GetNext()):?>
																				<option data-department="<?=$ar_res['NAME']?>" value="<?=$arField['ID']?>"><?=$arField['NAME']?></option>
																			<? endif; ?>
																		<? endforeach; ?>
																		<option selected disabled class="height-to-zero"></option>
																	</select>
																</div>
															</div>
														<? else:
															$arFilterCustom = Array("IBLOCK_ID"=>$_GET['list_id'], "ID"=>$_GET['element_id']);
															$resCustom = CIBlockElement::GetList(Array(), $arFilterCustom); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
															if ($obCustom = $resCustom->GetNextElement()){; // переходим к след элементу, если такой есть
																$arFieldsCustom = $obCustom->GetFields(); // поля элемента
//																debug($arFields);
																$arPropsCustom = $obCustom->GetProperties(); // свойства элемента
//																debug($arPropsCustom);
															}
															$fieldId = str_replace('PROPERTY_', '', $field['id']);
															$fieldId = str_replace('[]', '', $fieldId);
															foreach ($arPropsCustom as $arPropCustom) {
//																debug($arPropCustom);
																if ($arPropCustom['ID'] == $fieldId) {
																	$value = $arPropCustom['VALUE'];
																	$code = $arPropCustom['CODE'];
																}
																if ($arPropCustom['CODE'] == 'NOTE') {
																	$noteId = $arPropCustom['ID'];
																}
															}
														switch($field["type"]):
															case 'label':
															case 'custom':?>
														<?
//															debug($field['id']);
//															debug($noteId);
//														debug($code);
															if ($code != 'NOTE' && $code != 'MODEL' && $code != 'SERIAL_NUMBER'):?>
															<input readonly="readonly" autocomplete="off" class="<?=$field['id']?>" type="text" name="<?=$field["id"]?>" size="30" value="<?=$value?>">
														<?else:
																echo $val;
														endif;
																break;
															case 'checkbox':
																?>
																<input type="hidden" name="<?=$field["id"]?>" value="N">
																<input type="checkbox" name="<?=$field["id"]?>" value="Y"<?=($val == "Y"? ' checked':'')?><?=$params?>>
																<?
																break;
															case 'textarea':
																?>
																<textarea name="<?=$field["id"]?>"<?=$params?>><?=$valEncoded?></textarea>
																<?
																break;
															case 'list':
															case 'select':
																?>
															<select data-id="123" name="<?=$field["id"]?>"<?=$params?>>
																<?
																if(is_array($field["items"])):
																	if(!is_array($val))
																		$val = array($val);
																	foreach($field["items"] as $k=>$v):
																		?>
																		<option data-id='<?=$val?>' <?if($_GET['type'] == 272):?><?if($k == 278):?> selected <?endif;?><?endif;?> <?if($_GET['type'] == 274):?><?if($k == 279):?> selected <?endif;?><?endif;?> <?if($_GET['type'] == 276):?><?if($k == 278):?> selected <?endif;?><?endif;?> value="<?=htmlspecialcharsbx($k)?>"<?=(in_array($k, $val)? ' selected':'')?>><?=htmlspecialcharsbx($v)?></option>
																	<?
																	endforeach;
																	?>
																	</select>
																<?
																endif;
																break;
															case 'file':
																$arDefParams = array("iMaxW"=>150, "iMaxH"=>150, "sParams"=>"border=0", "strImageUrl"=>"", "bPopup"=>true, "sPopupTitle"=>false, "size"=>20);
																foreach($arDefParams as $k=>$v)
																	if(!array_key_exists($k, $field["params"]))
																		$field["params"][$k] = $v;

																echo CFile::InputFile($field["id"], $field["params"]["size"], $val);
																if($val <> '')
																	echo '<br>'.CFile::ShowImage($val, $field["params"]["iMaxW"], $field["params"]["iMaxH"], $field["params"]["sParams"], $field["params"]["strImageUrl"], $field["params"]["bPopup"], $field["params"]["sPopupTitle"]);

																break;
															case 'date':
															case 'date_short':
																?>
																<?$APPLICATION->IncludeComponent(
																"bitrix:main.calendar",
																"",
																array(
																	"SHOW_INPUT"=>"Y",
																	"INPUT_NAME"=>$field["id"],
																	"INPUT_VALUE"=>$val,
																	"INPUT_ADDITIONAL_ATTR"=>$params,
																	"SHOW_TIME" => $field["type"] === 'date'? 'Y' : 'N',
																),
																$component,
																array("HIDE_ICONS"=>true)
															);?>
																<?
																break;
															default:
																?>
																<input readonly="readonly" type="text" name="<?=$field["id"]?>" value="<?=$valEncoded?>"<?=$params?>>
																<?
																break;
														endswitch;
														endif;?>
													</td>
												<?endif?>
											</tr>
											<?endif;?>
										<?
											$prevType = $field["type"];
										endforeach;
										?>
									</table>
								</div>
							</div>
						</div>
					<?
					endforeach;
					?>
				</td>
			</tr>
		</table>
		<? if(isset($arParams["BUTTONS"])):?>
			<div class="bx-buttons">
<!--				--><?//if($arParams["~BUTTONS"]["standard_buttons"] !== false):?>
<!--					--><?//if($arParams["BUTTONS"]["back_url"] <> ''):?>
						<input type="submit" name="save" value="<?echo GetMessage("interface_form_save")?>" title="<?echo GetMessage("interface_form_save_title")?>" />
<!--					--><?//endif?>
					<input type="submit" name="apply" value="<?echo GetMessage("interface_form_apply")?>" title="<?echo GetMessage("interface_form_apply_title")?>" />
<!--					--><?//if($arParams["BUTTONS"]["back_url"] <> ''):?>
						<input type="button" value="<?echo GetMessage("interface_form_cancel")?>" name="cancel" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($arParams["~BUTTONS"]["back_url"]))?>'" title="<?echo GetMessage("interface_form_cancel_title")?>" />
<!--					--><?//endif?>
<!--				--><?//endif?>
				<?=$arParams["~BUTTONS"]["custom_html"]?>
			</div>
			<div class="bx-buttons-description">
				<div>
					Сохранить - сохраняет и переносит на список элементов.
				</div>
				<div>
					Применить - cохраняет и перемещает в карточку созданого или отредактированого элемента.
				</div>
			</div>
		<?endif?>
		<?if($arParams["SHOW_FORM_TAG"]):?>
	</form>
<?endif?>

	<?if($USER->IsAuthorized() && $arParams["SHOW_SETTINGS"] == true):?>
		<div style="display:none">

			<div id="form_settings_<?=$arParams["FORM_ID"]?>">
				<table width="100%">
					<tr class="section">
						<td><?echo GetMessage("interface_form_tabs")?></td>
					</tr>
					<tr>
						<td align="center">
							<table>
								<tr>
									<td style="background-image:none" nowrap>
										<select style="min-width:150px;" name="tabs" size="10" ondblclick="this.form.tab_edit_btn.onclick()" onchange="bxForm_<?=$arParams["FORM_ID"]?>.OnSettingsChangeTab()">
										</select>
									</td>
									<td style="background-image:none">
										<div style="margin-bottom:5px"><input type="button" name="tab_up_btn" value="<?echo GetMessage("intarface_form_up")?>" title="<?echo GetMessage("intarface_form_up_title")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.TabMoveUp()"></div>
										<div style="margin-bottom:5px"><input type="button" name="tab_down_btn" value="<?echo GetMessage("intarface_form_up_down")?>" title="<?echo GetMessage("intarface_form_down_title")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.TabMoveDown()"></div>
										<div style="margin-bottom:5px"><input type="button" name="tab_add_btn" value="<?echo GetMessage("intarface_form_add")?>" title="<?echo GetMessage("intarface_form_add_title")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.TabAdd()"></div>
										<div style="margin-bottom:5px"><input type="button" name="tab_edit_btn" value="<?echo GetMessage("intarface_form_edit")?>" title="<?echo GetMessage("intarface_form_edit_title")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.TabEdit()"></div>
										<div style="margin-bottom:5px"><input type="button" name="tab_del_btn" value="<?echo GetMessage("intarface_form_del")?>" title="<?echo GetMessage("intarface_form_del_title")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.TabDelete()"></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="section">
						<td><?echo GetMessage("intarface_form_fields")?></td>
					</tr>
					<tr>
						<td align="center">
							<table>
								<tr>
									<td style="background-image:none" nowrap>
										<div style="margin-bottom:5px"><?echo GetMessage("intarface_form_fields_available")?></div>
										<select style="min-width:150px;" name="all_fields" multiple size="12" ondblclick="this.form.add_btn.onclick()" onchange="bxForm_<?=$arParams["FORM_ID"]?>.ProcessButtons()">
										</select>
									</td>
									<td style="background-image:none">
										<div style="margin-bottom:5px"><input type="button" name="add_btn" value="&gt;" title="<?echo GetMessage("intarface_form_add_field")?>" style="width:30px;" disabled onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldsAdd()"></div>
										<div style="margin-bottom:5px"><input type="button" name="del_btn" value="&lt;" title="<?echo GetMessage("intarface_form_del_field")?>" style="width:30px;" disabled onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldsDelete()"></div>
									</td>
									<td style="background-image:none" nowrap>
										<div style="margin-bottom:5px"><?echo GetMessage("intarface_form_fields_on_tab")?></div>
										<select style="min-width:150px;" name="fields" multiple size="12" ondblclick="this.form.del_btn.onclick()" onchange="bxForm_<?=$arParams["FORM_ID"]?>.ProcessButtons()">
										</select>
									</td>
									<td style="background-image:none">
										<div style="margin-bottom:5px"><input type="button" name="up_btn" value="<?echo GetMessage("intarface_form_up")?>" title="<?echo GetMessage("intarface_form_up_title")?>" style="width:80px;" disabled onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldsMoveUp()"></div>
										<div style="margin-bottom:5px"><input type="button" name="down_btn" value="<?echo GetMessage("intarface_form_up_down")?>" title="<?echo GetMessage("intarface_form_down_title")?>" style="width:80px;" disabled onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldsMoveDown()"></div>
										<div style="margin-bottom:5px"><input type="button" name="field_add_btn" value="<?echo GetMessage("intarface_form_add")?>" title="<?echo GetMessage("intarface_form_add_sect")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldAdd()"></div>
										<div style="margin-bottom:5px"><input type="button" name="field_edit_btn" value="<?echo GetMessage("intarface_form_edit")?>" title="<?echo GetMessage("intarface_form_edit_field")?>" style="width:80px;" onclick="bxForm_<?=$arParams["FORM_ID"]?>.FieldEdit()"></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?if($arResult["IS_ADMIN"]):?>
						<tr class="section">
							<td><?echo GetMessage("interface_form_common")?></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="set_default_settings" id="set_default_settings_<?=$arParams["FORM_ID"]?>" onclick="BX('delete_users_settings_<?=$arParams["FORM_ID"]?>').disabled = !this.checked;"><label for="set_default_settings_<?=$arParams["FORM_ID"]?>"><?echo GetMessage("interface_form_common_set")?></label></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="delete_users_settings" id="delete_users_settings_<?=$arParams["FORM_ID"]?>" disabled><label for="delete_users_settings_<?=$arParams["FORM_ID"]?>"><?echo GetMessage("interface_form_common_del")?></label></td>
						</tr>
					<?endif;?>
				</table>
			</div>

		</div>
	<?
	endif //$GLOBALS['USER']->IsAuthorized()
	?>

	<?
	$variables = array(
		"mess"=>array(
			"collapseTabs"=>GetMessage("interface_form_close_all"),
			"expandTabs"=>GetMessage("interface_form_show_all"),
			"settingsTitle"=>GetMessage("intarface_form_settings"),
			"settingsSave"=>GetMessage("interface_form_save"),
			"tabSettingsTitle"=>GetMessage("intarface_form_tab"),
			"tabSettingsSave"=>"OK",
			"tabSettingsName"=>GetMessage("intarface_form_tab_name"),
			"tabSettingsCaption"=>GetMessage("intarface_form_tab_title"),
			"fieldSettingsTitle"=>GetMessage("intarface_form_field"),
			"fieldSettingsName"=>GetMessage("intarface_form_field_name"),
			"sectSettingsTitle"=>GetMessage("intarface_form_sect"),
			"sectSettingsName"=>GetMessage("intarface_form_sect_name"),
		),
		"ajax"=>array(
			"AJAX_ID"=>$arParams["AJAX_ID"],
			"AJAX_OPTION_SHADOW"=>($arParams["AJAX_OPTION_SHADOW"] == "Y"),
		),
		"settingWndSize"=>CUtil::GetPopupSize("InterfaceFormSettingWnd"),
		"tabSettingWndSize"=>CUtil::GetPopupSize("InterfaceFormTabSettingWnd", array('width'=>400, 'height'=>200)),
		"fieldSettingWndSize"=>CUtil::GetPopupSize("InterfaceFormFieldSettingWnd", array('width'=>400, 'height'=>150)),
		"component_path"=>$component->GetRelativePath(),
		"template_path"=>$this->GetFolder(),
		"sessid"=>bitrix_sessid(),
		"current_url"=>$APPLICATION->GetCurPageParam("", array("bxajaxid", "AJAX_CALL")),
		"GRID_ID"=>$arParams["THEME_GRID_ID"],
	);
	?>
	<script type="text/javascript">
		const mol_user = document.getElementById('input-search-1409');
		const mol_list = document.getElementById('search-list-1409');
		const mol_options = mol_list.children;
		let mol_check = 0;


		if (mol_list.dataset.id !== "") {
			mol_list.value = mol_list.dataset.id;
		}

		for (let i = 0, n = mol_options.length-1; i < n; i++) {
			let mol_option = mol_options[i];
			mol_option.addEventListener('click', (e) => {
				mol_user.value = e.target.textContent;
				mol_check = 1;
				mol_list.dataset.id = "";
				mol_list.dataset.name = "";
				changeSelectorValueMol(mol_check);
				changeSelectSizeMol(0);
			})
		}

		// mol_user.addEventListener('focus', (e) => {
		// 	searchFromInputMol(e.target.value);
		// });

		// mol_user.addEventListener('input', (e) => {
		// 	searchFromInputMol(e.target.value);
		// })

		// $(document).mouseup(function (e) {
		// 	var container = $(".search-1409");
		// 	if (container.has(e.target).length === 0){
		// 		changeSelectSizeMol(0);
		// 	}
		// });

		function changeSelectSizeMol(size) {
			if (size < 1) {
				mol_list.classList.add("height-to-zero");
			}
			if (size !== 1) {
				setTimeout(() => {
					mol_list.size = size;
				}, 0);
			}
		}

		function searchFromInputMol(value_mol) {
			let size = 1;
			let arr_mol = new Array();
			arr_mol = value_mol.split(" ");
			for (let i = 0, n = mol_options.length-1; i < n; i++) {
				let mol_option = mol_options[i];

				let match_mol = 1;
				for (let i = 0; i < arr_mol.length; i++) {
					if (!mol_option.textContent.toLowerCase().includes(arr_mol[i].toLowerCase())) {
						match_mol = 0;
					}
				}
				if (match_mol != 0) {
					mol_option.hidden = false;
					size++;
				} else {
					mol_option.hidden = true;
				}
				if (size > 10)
					size = 10;
				changeSelectSizeMol(size);
				mol_list.classList.remove("height-to-zero");
			}
			size--;
			changeSelectSizeMol(size);
		}

		function changeSelectorValueMol(flag) {
			if (flag == 0) {
				mol_list.value = "";
			}
		}

		// mol_user.addEventListener('focusout', () => {
		// 	mol_list.value = "";
		// 	for (let i = 0, n = mol_options.length-1; i < n; i++) {
		// 		let mol_option = mol_options[i];
		// 		if (mol_option.text != mol_user.value) {
		// 			mol_check = 0;
		// 		} else {
		// 			mol_list.value = mol_option.value;
		// 			mol_check = 1;
		// 			break;
		// 		}
		// 	}
		// 	if (mol_check == 0) {
		// 		mol_user.value = "";
		// 	}
		// 	if (mol_user.value == "") {
		// 		mol_list.dataset.id = "";
		// 		mol_list.dataset.name = "";
		// 		mol_list.value = "";
		// 		mol_check = 0;
		// 	}
		// 	changeSelectorValueMol(mol_check);
		// });

		//----------------------------
		const fact_user = document.getElementById('input-search-1407');
		const fact_list = document.getElementById('search-list-1407');
		const fact_options = fact_list.children;
		let fact_check = 0;
		// let department = document.querySelector('.PROPERTY_1408 input');

		if (fact_list.dataset.id !== "") {
			fact_list.value = fact_list.dataset.id;
		}

		for (let i = 0, n = fact_options.length-1; i < n; i++) {
			let fact_option = fact_options[i];
			fact_option.addEventListener('click', (e) => {
				fact_user.value = e.target.textContent;
				fact_check = 1;
				fact_list.dataset.id = "";
				fact_list.dataset.name = "";
				// department.value = fact_option.dataset.department;
				changeSelectorValueFact(fact_check);
				changeSelectSizeFact(0);
			})
		}

		fact_user.addEventListener('focus', (e) => {
			searchFromInputFact(e.target.value);
		});

		fact_user.addEventListener('input', (e) => {
			searchFromInputFact(e.target.value);
		})

		$(document).mouseup(function (e) {
			var container = $(".search-1407");
			if (container.has(e.target).length === 0){
				changeSelectSizeFact(0);
			}
		});

		function changeSelectSizeFact(size) {
			if (size < 1) {
				fact_list.classList.add("height-to-zero");
			}
			if (size !== 1) {
				setTimeout(() => {
					fact_list.size = size;
				}, 0);
			}
		}

		function searchFromInputFact(value) {
			let size = 1;
			let arr = new Array();
			arr = value.split(" ");
			for (let i = 0, n = fact_options.length-1; i < n; i++) {
				let fact_option = fact_options[i];

				let match = 1;
				for (let i = 0; i < arr.length; i++) {
					if (!fact_option.textContent.toLowerCase().includes(arr[i].toLowerCase())) {
						match = 0;
					}
				}
				if (match != 0) {
					fact_option.hidden = false;
					size++;
				} else {
					fact_option.hidden = true;
				}
				if (size > 10)
					size = 10;
				changeSelectSizeFact(size);
				fact_list.classList.remove("height-to-zero");
			}
			size--;
			changeSelectSizeFact(size);
		}

		function changeSelectorValueFact(flag) {
			if (flag == 0) {
				fact_list.value = "";
				// department.value = "";
			}
		}

		fact_user.addEventListener('focusout', () => {
			for (let i = 0, n = fact_options.length-1; i < n; i++) {
				let fact_option = fact_options[i];
				if (fact_option.text != fact_user.value) {
					fact_check = 0;
				} else {
					fact_list.value = fact_option.value
					// department.value = fact_option.dataset.department;
					fact_check = 1;
					break;
				}
			}
			if (fact_check == 0) {
				fact_user.value = "";
			}
			if (fact_user.value == "") {
				fact_list.dataset.id = "";
				fact_list.dataset.name = "";
				fact_list.value = "";
				// department.value = "";
				fact_check = 0;
			}
			changeSelectorValueFact(fact_check);
		});

		//------------------------
		const search = document.getElementById('input-search-1404');
		const list = document.getElementById('search-list-1404');
		const options = list.children;
		let check = 0;

		let name;
		let invNumber = document.querySelector('.PROPERTY_1410 > input');
		let field_name = document.querySelector('.NAME > input');

		if (list.dataset.id !== "") {
			list.value = list.dataset.id;
		}

		for (let i = 0, n = options.length-1; i < n; i++) {
			let option = options[i];
			option.addEventListener('click', (e) => {
				search.value = e.target.textContent;
				check = 1;
				list.dataset.id = "";
				list.dataset.name = "";
				changeSelectorValue(check);
				name = search.value + ' ' + invNumber.value;
				field_name.value = name;
				changeSelectSize(0);
			})
		}

		// При фокусе инпута показывает список
		// search.addEventListener('focus', (e) => {
		// 	searchFromInput(e.target.value);
		// });

		// $(document).mouseup(function (e) {
		// 	var container = $(".search-1404");
		// 	if (container.has(e.target).length === 0){
		// 		changeSelectSize(0);
		// 	}
		// });
		//
		// // Добавляем эвент при нажатия на клавиши
		// search.addEventListener('input', (e) => {
		// 	searchFromInput(e.target.value);
		// })

		function changeSelectSize(size) {
			if (size < 1) {
				list.classList.add("height-to-zero");
			}
			if (size !== 1) {
				setTimeout(() => {
					list.size = size;
				}, 0);
			}
		}

		// Проходим по каждому элементу списка
		function searchFromInput(value) {
			let size = 1;
			for (let i = 0, n = options.length-1; i < n; i++) {
				let option = options[i];

				// Если текст элемента содержит наше строку то показываем её, в противоположном слкчае скрываем
				if (option.textContent.toLowerCase().includes(value.toLowerCase())) {
					option.hidden = false;
					size++;
				} else {
					option.hidden = true;
				}
				if (size > 10)
					size = 10
				changeSelectSize(size);
				list.classList.remove("height-to-zero");
			}
			size--;
			changeSelectSize(size);
		}

		// search.addEventListener('focusout', () => {
		// 	for (let i = 0, n = options.length-1; i < n; i++) {
		// 		let option = options[i];
		// 		if (option.text != search.value) {
		// 			check = 0;
		// 		} else {
		// 			list.value = option.value;
		// 			check = 1;
		// 			break;
		// 		}
		// 	}
		// 	if (check == 0) {
		// 		search.value = "";
		// 	}
		// 	if (search.value == "") {
		// 		list.dataset.id = "";
		// 		list.dataset.name = "";
		// 		list.value = "";
		// 		check = 0;
		// 	}
		// 	changeSelectorValue(check);
		// 	name = search.value + ' ' + invNumber.value;
		// 	field_name.value = name;
		// });

		function changeSelectorValue(flag) {
			if (flag == 0) {
				list.value = "";
			}
		}

		invNumber.onchange = function () {
			name = search.value + ' ' +invNumber.value;
			field_name.value = name;
		}

		if (list.dataset.id !== "" && list.dataset.name !== "") {
			name = list.dataset.name + ' ' + invNumber.value;
			field_name.value = name;
		}

		var formSettingsDialog<?=$arParams["FORM_ID"]?>;

		bxForm_<?=$arParams["FORM_ID"]?> = new BxInterfaceForm('<?=$arParams["FORM_ID"]?>', <?=CUtil::PhpToJsObject(array_keys($arResult["TABS"]))?>);
		bxForm_<?=$arParams["FORM_ID"]?>.vars = <?=CUtil::PhpToJsObject($variables)?>;
		<?if($arParams["SHOW_SETTINGS"] == true):?>
		bxForm_<?=$arParams["FORM_ID"]?>.oTabsMeta = <?=CUtil::PhpToJsObject($arResult["TABS_META"])?>;
		bxForm_<?=$arParams["FORM_ID"]?>.oFields = <?=CUtil::PhpToJsObject($arResult["AVAILABLE_FIELDS"])?>;
		<?endif?>
		<?
		$settingsMenu = array();
		if($arParams["SHOW_SETTINGS"])
		{
			$settingsMenu[] = array(
				'TEXT' => GetMessage("intarface_form_mnu_settings"),
				'TITLE' => GetMessage("intarface_form_mnu_settings_title"),
				'ONCLICK' => 'bxForm_'.$arParams["FORM_ID"].'.ShowSettings()',
				'DEFAULT' => true,
				'DISABLED' => ($USER->IsAuthorized()? false:true),
				'ICONCLASS' => 'form-settings'
			);
			if(!empty($arResult["OPTIONS"]["tabs"]))
			{
				if($arResult["OPTIONS"]["settings_disabled"] == "Y")
				{
					$settingsMenu[] = array(
						'TEXT' => GetMessage("intarface_form_mnu_on"),
						'TITLE' => GetMessage("intarface_form_mnu_on_title"),
						'ONCLICK' => 'bxForm_'.$arParams["FORM_ID"].'.EnableSettings(true)',
						'DISABLED' => ($USER->IsAuthorized()? false:true),
						'ICONCLASS' => 'form-settings-on'
					);
				}
				else
				{
					$settingsMenu[] = array(
						'TEXT' => GetMessage("intarface_form_mnu_off"),
						'TITLE' => GetMessage("intarface_form_mnu_off_title"),
						'ONCLICK' => 'bxForm_'.$arParams["FORM_ID"].'.EnableSettings(false)',
						'DISABLED' => ($USER->IsAuthorized()? false:true),
						'ICONCLASS' => 'form-settings-off'
					);
				}
			}
		}
		if(!empty($arThemes))
		{
			$themeItems = array();
			foreach($arThemes as $theme)
			{
				$themeItems[] = array(
					'TEXT' => $theme["name"].($theme["theme"] == $arResult["GLOBAL_OPTIONS"]["theme"]? ' '.GetMessage("interface_form_default"):''),
					'ONCLICK' => 'bxForm_'.$arParams["FORM_ID"].'.SetTheme(this, \''.$theme["theme"].'\')',
					'ICONCLASS' => ($theme["theme"] == $arResult["OPTIONS"]["theme"] || $theme["theme"] == "grey" && $arResult["OPTIONS"]["theme"] == ''? 'checked' : '')
				);
			}

			$settingsMenu[] = array(
				'TEXT' => GetMessage("interface_form_colors"),
				'TITLE' => GetMessage("interface_form_colors_title"),
				'CLASS' => 'bx-grid-themes-menu-item',
				'MENU' => $themeItems,
				'DISABLED' => ($USER->IsAuthorized()? false:true),
				'ICONCLASS' => 'form-themes'
			);
		}
		?>
		bxForm_<?=$arParams["FORM_ID"]?>.settingsMenu = <?=CUtil::PhpToJsObject($settingsMenu)?>;

		<?if($arResult["OPTIONS"]["expand_tabs"] == "Y"):?>
		BX.ready(function(){bxForm_<?=$arParams["FORM_ID"]?>.ToggleTabs(true);});
		<?endif?>
	</script>

</div>

<?if($bWasRequired):?>
	<div class="bx-form-notes"><span class="required">*</span><?echo GetMessage("interface_form_required")?></div>
<?endif?>

