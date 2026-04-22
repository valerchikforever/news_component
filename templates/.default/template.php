<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<div class="news-list">


<? if($arResult["USE_FILTER"] == "Y"){ ?>
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
	<table class="data-table" cellspacing="0" cellpadding="2">
	<thead>
		<tr>
			<td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE");?></td>
		</tr>
	</thead>
	<tbody>
		<?foreach($arResult["FILTER_FIELDS"] as $arFilterField):?>
            <tr>
                <td valign="top"><?=$arFilterField["NAME"]?>:</td>
                <td valign="top"><?=$arFilterField["INPUT"]?></td>
            </tr>
		<?endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<input type="submit" value="<?=GetMessage("IBLOCK_SET_FILTER");?>" />
                <input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER");?>" />
            </td>
		</tr>
	</tfoot>
	</table>
</form>
<?}?>


<? 
foreach($arResult["ITEMS"]["listElementsByIDIblock"] as $arIblock){?>
<?  foreach($arIblock as $arItem){
    
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
    
    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
    <p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <img
						class="preview_picture"
						border="0"
						src="<?=$arItem["PREVIEW_PICTURE_PATH"]?>"
						style="float:left"
						/>
        <?if($arResult["DISPLAY_DATE"]!="N" && $arItem["DATE_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DATE_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arResult["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
		    <b><?echo $arItem["NAME"]?></b><br /></a>
		<?endif;?>
		<?if($arResult["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<div style="clear:both"></div>
    </p>
<?    }
}?>
</div>