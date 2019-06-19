<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arParams["DISPLAY_IMG_WIDTH"]) $arParams["DISPLAY_IMG_WIDTH"] = "640";
if(!$arParams["DISPLAY_IMG_HEIGHT"]) $arParams["DISPLAY_IMG_HEIGHT"] = "360";

foreach ($arResult["ITEMS"] as $key => &$arElement){
    if($arElement["IBLOCK_SECTION_ID"]>0){
        $rsSection = CIBlockSection::GetByID($arElement["IBLOCK_SECTION_ID"]);
        if($arSection = $rsSection->GetNext()){
            $arResult["ITEMS"][$key]["SECTION_SALEMODEL"] = $arSection;
        }
    }
    if($arElement["PROPERTIES"]["SALON_ID"]["VALUE"] > 0){
        $arFilterSalon = array('IBLOCK_ID'=>8, 'ID'=>$arElement["PROPERTIES"]["SALON_ID"]["VALUE"], 'ACTIVE'=>'Y');
        $rsElementsSalon = CIBlockElement::GetList(array(), $arFilterSalon, false, false, Array(
            "IBLOCK_ID",
            "IBLOCK_SECTION_ID",
            "ID",
            "LANG_DIR",
            "DETAIL_PAGE_URL",
            "NAME",
            "PROPERTY_SHEDULE",
            "PROPERTY_PHONE",
            "PROPERTY_DISCOUNT",
            "PREVIEW_TEXT",
            "PROPERTY_METRO",
            "PROPERTY_DISCOUNT",
            "PROPERTY_FIRMSALON",
            "PROPERTY_SMARTI",

        ));
        if($arSalon = $rsElementsSalon->GetNext()){

            $res = CIBlockSection::GetByID($arSalon["IBLOCK_SECTION_ID"]);
            if($ar_res = $res->GetNext()){
                $item = $ar_res["NAME"];
            }

            $arElement["SALON"] = Array(
                "DETAIL_PAGE_URL"=> $arSalon["DETAIL_PAGE_URL"],
                "NAME" => $arSalon["NAME"],
                "PHONE"=> $arSalon["PROPERTY_PHONE_VALUE"],
                "PHONE_TEL"=> $arFields["PROPERTY_PHONE_DESCRIPTION"],
                "ADDRESS"=>htmlspecialchars_decode($arSalon["PREVIEW_TEXT"]),
                "CLOCK"=>$arSalon["PROPERTY_SHEDULE_VALUE"],
                "ID" =>$arSalon["ID"],
                "IBLOCK_SECTION_ID" =>$arSalon["IBLOCK_SECTION_ID"],
                "CITY_SALON" =>$item,
                "METRO_SALON" =>$arSalon["PROPERTY_METRO_VALUE"],
                "PROPERTY_DISCOUNT" =>$arSalon["PROPERTY_DISCOUNT_VALUE"],
                "PROPERTY_FIRMSALON" =>$arSalon["PROPERTY_FIRMSALON_VALUE"],
                "PROPERTY_SMARTI" =>$arSalon["PROPERTY_SMARTI_VALUE"],
            );

            $arProp["CITY_SALON"][$arSalon["IBLOCK_SECTION_ID"]] = $item;
            foreach ($arSalon["PROPERTY_METRO_VALUE"] as $key => $val) {
                $arProp["METRO_SALON"][$key] = $val;
            }
            if ($arSalon["PROPERTY_FIRMSALON_VALUE"] === "да"){
                $arProp["TYPE_SALON"][] = "Фирменный салон";
            }
            if ($arSalon["PROPERTY_DISCOUNT_VALUE"] === "да"){
                $arProp["TYPE_SALON"][] = "Дисконт-центр";
            }
            if ($arSalon["PROPERTY_SMARTI_VALUE"] === "да"){
                $arProp["TYPE_SALON"][] = "Салон Smarti";
            }
            if ((!$arSalon["PROPERTY_FIRMSALON_VALUE"]) && (!$arSalon["PROPERTY_DISCOUNT_VALUE"]) && (!$arSalon["PROPERTY_SMARTI_VALUE"])){
                $arProp["TYPE_SALON"][] = "Дилерский салон";
            }
            /*
            foreach ($arProp["TYPE_SALON"] as $el) {
                $arProp["TYPE_SALON"][] = $el;
            }
            */
        }
    }



    if($arElement["DETAIL_PICTURE"]){
        $arFilter = '';
        if($arParams["SHARPEN"] != 0){
            $arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
        }
        $arFileTmp = CFile::ResizeImageGet(
            $arElement["DETAIL_PICTURE"],
            array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
            BX_RESIZE_IMAGE_EXACT,
            true, $arFilter
        );

        $arElement["PREVIEW_PIC"] = array(
            "SRC" => $arFileTmp["src"],
            'WIDTH' => $arFileTmp["width"],
            'HEIGHT' => $arFileTmp["height"],
        );
    } else {
        $arElement["PREVIEW_PIC"] = array(
            "SRC" => $arElement["PROPERTIES"]["DETAIL_PICTURE_PATH"]["VALUE"]
        );
    }



}

if(count($arProp["CITY_SALON"]) > 0) {
    natsort($arProp["CITY_SALON"]);
    $arResult["ADE_FILTER_ITEMS"]["CITY_SALON"] = array_unique($arProp["CITY_SALON"]);
}
if(count($arProp["METRO_SALON"]) > 0) {
    natsort($arProp["METRO_SALON"]);
    $arResult["ADE_FILTER_ITEMS"]["METRO_SALON"] = array_unique($arProp["METRO_SALON"]);
}
if(count($arProp["TYPE_SALON"]) > 0) {
    natsort($arProp["TYPE_SALON"]);
    $arResult["ADE_FILTER_ITEMS"]["TYPE_SALON"] = array_unique($arProp["TYPE_SALON"]);
}

if($arParams["CURENT_SALON_ID"]>0){
    $arSort = array("id"=>"asc");
    $arFilter = array("IBLOCK_ID" => "6", "ACTIVE" => "Y", "PROPERTY_SALON_ID" => $arParams["CURENT_SALON_ID"]);
    $arSelect = array("ID", "IBLOCK_ID", "PROPERTY_YANDEX_TRANSFORMATION_MECHANISM", "PROPERTY_YANDEX_COLOR", "PROPERTY_YANDEX_TYPE");

    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    if($res->SelectedRowsCount()>0){
        while($ar_fields = $res->GetNext()){
            if($ar_fields["PROPERTY_YANDEX_TRANSFORMATION_MECHANISM_VALUE"]) {
                $arProp["TRANSFORMATION_MECHANISM"][] = $ar_fields["PROPERTY_YANDEX_TRANSFORMATION_MECHANISM_VALUE"];
            }
            if($ar_fields["PROPERTY_YANDEX_COLOR_VALUE"]) {
                $arProp["COLOR"][] = $ar_fields["PROPERTY_YANDEX_COLOR_VALUE"];
            }
            if($ar_fields["PROPERTY_YANDEX_TYPE_VALUE"]) {
                $arProp["TYPE"][] = $ar_fields["PROPERTY_YANDEX_TYPE_VALUE"];
            }
        }
        natsort($arProp["TRANSFORMATION_MECHANISM"]);
        $arResult["ADE_FILTER_ITEMS"]["TRANSFORMATION_MECHANISM"] = array_unique($arProp["TRANSFORMATION_MECHANISM"]);
        natsort($arProp["COLOR"]);
        $arResult["ADE_FILTER_ITEMS"]["COLOR"] = array_unique($arProp["COLOR"]);
        natsort($arProp["TYPE"]);
        $arResult["ADE_FILTER_ITEMS"]["TYPE"] = array_unique($arProp["TYPE"]);
    }
}
