<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

class CNews extends CBitrixComponent
{
    public $arResult;
    
    public function onPrepareComponentParams($arParams)
	{
	    
	    $arParams["IBLOCK_TYPE"] = $arParams['IBLOCK_TYPE'] ?? 0;
		$arParams['IBLOCK_ID'] = (int)($arParams['IBLOCK_ID'] ?? 0);
		$arParams["FILTER_NAME"] = !empty($arParams['FILTER_NAME']) ? $arParams['FILTER_NAME'] : "arrFilter";
            
		return $arParams;
	}
	
	public function executeComponent(){
	    global $APPLICATION;
	    
	    $filterName = $this->arParams["FILTER_NAME"];
        global ${$filterName};
        $arrFilter = ${$filterName};
        if(!is_array($arrFilter)) $arrFilter = array();
	    
	    if($this->startResultCache())
        {
            $this->arResult = [
                "IBLOCK_ID" => $this->arParams['IBLOCK_ID'],
                "IBLOCK_TYPE" => $this->arParams['IBLOCK_TYPE'],
                "DISPLAY_DATE" => $this->arParams['DISPLAY_DATE'],
                "DISPLAY_NAME" => $this->arParams['DISPLAY_NAME'],
                "USE_FILTER" => $this->arParams['USE_FILTER'],
                "FILTER_NAME" => $this->arParams['FILTER_NAME'],
                "FILTER_FIELD_CODE" => $this->arParams['FILTER_FIELD_CODE'],
                "FILTER_FIELD_CODE_LIST" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "FILTER_SETTINGS"),
                "FORM_ACTION" => htmlspecialcharsbx($APPLICATION->GetCurPage()),
            ];
            
            $field = $this->arParams['FILTER_FIELD_CODE'][0];
            $fieldName = $filterName . '_ff[' . $field . ']';
            $label = $this->arResult["FILTER_FIELD_CODE_LIST"]["VALUES"][$field] ?? $field;
            
            if (in_array($field, ["ID", "XML_ID"])) {
                $this->arResult["FILTER_FIELDS"] = [
                    [
                        "NAME" => $label . " (от)", 
                        "INPUT" => '<input type="text" name="'.$fieldName.'[LEFT]" value="'.htmlspecialcharsbx($arrFilter[$field][0] ?? $arrFilter[">=".$field]).'">'
                    ],
                    [
                        "NAME" => $label . " (до)", 
                        "INPUT" => '<input type="text" name="'.$fieldName.'[RIGHT]" value="'.htmlspecialcharsbx($arrFilter[$field][1] ?? $arrFilter["<=".$field]).'">'
                    ]
                ];
            } elseif((strpos($field, "DATE_ACTIVE") !== false || in_array($field, ["TIMESTAMP_X", "SHOW_COUNTER_START"]))) {
                if(in_array($field, ["TIMESTAMP_X", "SHOW_COUNTER_START"])){
                    $mask[0] = $field;
                    $mask[1] = $field;
                }
                else{
                    
                    $mask[0] = "DATE_ACTIVE_FROM";
                    $mask[1] = "DATE_ACTIVE_TO";
                }
                $this->arResult["FILTER_FIELDS"] = [
                    [
                        "NAME" => $label . " (от)", 
                        "INPUT" => '<input type="date" name="'.$fieldName.'['.$mask[0].']" value="'.htmlspecialcharsbx($arrFilter[$field][0] ?? $arrFilter["<=".$mask[0]]).'">'
                    ],
                    [
                        "NAME" => $label . " (до)", 
                        "INPUT" => '<input type="date" name="'.$fieldName.'['.$mask[1].']" value="'.htmlspecialcharsbx($arrFilter[$field][1] ?? $arrFilter[">=".$mask[1]]).'">'
                    ]
                ];
            }
            else{
                if(strpos($field, "DATE")){
                    $this->arResult["FILTER_FIELDS"][] = [
                        "NAME" => $label,
                        "INPUT" => '<input type="date" name="'.$fieldName.'" value="'.htmlspecialcharsbx($arrFilter[$field]).'">'
                    ];
                }
                else{
                    $this->arResult["FILTER_FIELDS"][] = [
                        "NAME" => $label,
                        "INPUT" => '<input type="text" name="'.$fieldName.'" value="'.htmlspecialcharsbx($arrFilter[$field]).'">'
                    ];
                }
            }
             
                
            $this->arResult["ITEMS"] = $this->news($this->arParams['IBLOCK_ID'], $this->arParams['IBLOCK_TYPE']);
            
            $this->includeComponentTemplate();
        }
	}
	
	public function news($IBLOCK_ID, $IBLOCK_TYPE){
	    $iblock = new \CIBlock;
	    $section = new \CIBlockSection;
	    $element = new \CIBlockElement;
	    
	    if (!Loader::includeModule('iblock'))
        {
        	return;
        }
        
        $listIblock = [];    //Получение списка инфоблоков по их типу *начало*
        
        if ($IBLOCK_ID > 0){    //Проверка указан ли определённый инфоблок
            $resIblock = $iblock->GetList(
            	Array(), 
            	Array(
            		'ID'=>$IBLOCK_ID, 
            		'ACTIVE'=>'Y', 
            		"CNT_ACTIVE"=>"Y", 
            	)
            );
        }
        else{
            $resIblock = $iblock->GetList(
            	Array(), 
            	Array(
            		'TYPE'=>$IBLOCK_TYPE, 
            		'ACTIVE'=>'Y', 
            		"CNT_ACTIVE"=>"Y", 
            	)
            );
        }
        
        while($ar_res = $resIblock->Fetch())
        {
        	$listIblock["listIblock"][$ar_res['NAME']] = $ar_res['ID'];
        	
        	$arFilter = Array("IBLOCK_ID"=>IntVal($ar_res['ID']), "ACTIVE"=>"Y"); //Получение списка элемента инфоблоков и группировка их по ID инфоблока *начало*
            
            if (isset($_GET[$this->arParams["FILTER_NAME"]."_ff"])) {
                $filter = $_GET[$this->arParams["FILTER_NAME"]."_ff"];
                if (!empty($filter["ID"]["LEFT"])){
                    $arFilter[">=ID"] = $filter["ID"]["LEFT"];
                } 
                if (!empty($filter["ID"]["RIGHT"])){
                    $arFilter["<=ID"] = $filter["ID"]["RIGHT"];
                } 
                if (!empty($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_FROM"])){
                    $arFilter[">=DATE_ACTIVE_FROM"] = date("DD.MM.YYYY HH:MI:SS", strtotime($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_FROM"]));
                }
                if (!empty($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_TO"])){
                    $arFilter["<=DATE_ACTIVE_TO"] = date("DD.MM.YYYY HH:MI:SS", strtotime($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_TO"]));
                }
                if (!empty($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_FROM"])){
                    $arFilter[">=DATE_ACTIVE_FROM"] = date("DD.MM.YYYY HH:MI:SS", strtotime($filter["DATE_ACTIVE_FROM"]["DATE_ACTIVE_FROM"]));
                }
                if (!empty($filter[$this->arParams['FILTER_FIELD_CODE'][0]]) && (strpos($this->arParams['FILTER_FIELD_CODE'][0], "DATE_ACTIVE") === false && !in_array($this->arParams['FILTER_FIELD_CODE'][0], ["TIMESTAMP_X", "SHOW_COUNTER_START"]))){
                    $arFilter[$this->arParams['FILTER_FIELD_CODE'][0]] = $filter[$this->arParams['FILTER_FIELD_CODE'][0]];
                }
            }
            
            $res = $element->GetList(Array(), $arFilter);
            while($ob = $res->GetNextElement())
            {
            	$arFields = $ob->GetFields();
            	$listIblock["listElementsByIDIblock"][$ar_res['ID']][$arFields["NAME"]] = $arFields;
            	
            	if ($this->arParams["DISPLAY_PICTURE"] == "Y"){
            	    $listIblock["listElementsByIDIblock"][$ar_res['ID']][$arFields["NAME"]]["PREVIEW_PICTURE_PATH"] = CFile::GetPath($arFields["PREVIEW_PICTURE"]);
            	}
            }//Получение списка элемента инфоблоков и группировка их по ID инфоблока *конец*
            
        }                   //Получение списка инфоблоков по их типу *конец*
        
        
        
	    return $listIblock;
	}
}?>