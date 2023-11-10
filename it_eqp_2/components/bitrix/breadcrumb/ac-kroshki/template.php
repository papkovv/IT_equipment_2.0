<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

//we can't use $APPLICATION->SetAdditionalCSS() here because we are inside the buffered function GetNavChain()

$strReturn .= '<ul class="breadcrumbs">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$arrow = ($index > 0? '<li><a class="breadcrumb">/</a></li>' : '');

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= $arrow.'<li><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" class="breadcrumb">'
					.$title.
				'</a></li>';				

	}
	else
	{
//        if ($_GET['list_id'] == 152) {
//            $link = '?mode=view&list_id=152&section_id=0&list_section_id=';
//        } else
//            $link = '#';
		$strReturn .= $arrow.'<li><a href="'.$arResult[$index]["LINK"].'" class="breadcrumb active">'
		.$title.
	'</a></li>';
	}
}

$strReturn .= '</ul>';

return $strReturn;
