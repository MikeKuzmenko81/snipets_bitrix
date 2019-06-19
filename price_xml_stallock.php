<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule('iblock');
$dataFileName = $_SERVER["DOCUMENT_ROOT"] . "/1c/data_stallock.txt";
$data = explode ("\n\r", file_get_contents($dataFileName));

$i = 0;
foreach($data as $key => $value ){
    $t = trim($value);

    if($t != ""){
        $aaa = explode(">\t<", substr($t, 1, -1));
    }

    //strtr($aaa[9], " ", "");
    $aaa[4] = preg_replace("/\s/u","",$aaa[4]); //euro
    $aaa[5] = preg_replace("/\s/u","",$aaa[5]); //euro
    $aaa[6] = preg_replace("/\s/u","",$aaa[6]); //euro
    $aaa[7] = preg_replace("/\s/u","",$aaa[7]); //euro
    $aaa[8] = preg_replace("/\s/u","",$aaa[8]); //rub
    $aaa[9] = preg_replace("/\s/u","",$aaa[9]); //rub
    $aaa[10] = preg_replace("/\s/u","",$aaa[10]); //rub
    $aaa[11] = preg_replace("/\s/u","",$aaa[11]); //rub

    echo "<pre>";
    print_r($aaa);
    echo "<pre>";

    $IBLOCK_ID = 14;
    $ELEMENT_ID = 0;

    $PROPERTY_VALUES = array(
        "NAME_IN_1C" => $aaa[1],
        "VENDOR_IN_1C" => $aaa[2],
        "ARTIKUL_IN_1C" => $aaa[3],
    );

    $arFilter = array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "XML_ID" => $aaa[0],
    );
    $arSelect = Array("ID", "NAME");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->Fetch()){
        echo "ELEMENT_ID = " . $ob["ID"];
        $ELEMENT_ID = $ob["ID"];
    }
    if ($ELEMENT_ID != 0) {
        CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES);
    }

    /*$el = new CIBlockElement;

    $PROP = array();
    $PROP["NAME_IN_1C"] = $aaa[1]; //114
    $PROP["VENDOR_IN_1C"] = $aaa[2]; //113
    $PROP["ARTIKUL_IN_1C"] = $aaa[3]; //115*/

    /*
    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
        "PROPERTY_VALUES"=> $PROP,
    );
    $res = $el->Update($ob["ID"], $arLoadProductArray);
    */

    /*for ($j = 3; j <= 6; $j++) {
        $arFields = Array(
            "PRODUCT_ID" => $ob["ID"],
            "CATALOG_GROUP_ID" => $j,//array(3, 4, 5, 6, 7, 8, 9, 10),//$PRICE_TYPE_ID,
            "PRICE" => $aaa[$j+1],//array($aaa[4], $aaa[5], $aaa[6], $aaa[7], $aaa[8], $aaa[9], $aaa[10], $aaa[11]), //29.95,
            "CURRENCY" => "EUR"
        );
        CPrice::Update($ob["ID"], $arFields);
    }
    for ($j = 7; j <= 10; $j++) {
        $arFields = Array(
            "PRODUCT_ID" => $ob["ID"],
            "CATALOG_GROUP_ID" => $j,//array(3, 4, 5, 6, 7, 8, 9, 10),//$PRICE_TYPE_ID,
            "PRICE" => $aaa[$j+1],//array($aaa[4], $aaa[5], $aaa[6], $aaa[7], $aaa[8], $aaa[9], $aaa[10], $aaa[11]), //29.95,
            "CURRENCY" => "RUB"
        );
        CPrice::Update($ob["ID"], $arFields);
    }*/

    // добавление коэффициента единицы измерения товара
    $result = \Bitrix\Catalog\MeasureRatioTable::add(array(
        'PRODUCT_ID' => $ob["ID"],
        'RATIO' => "" //коэффициент_единицы_измерения
    ) );
    // добавление цены
    if(!empty($aaa[4])){
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 3, //ID_типа_цены,
            'PRICE' => $aaa[4], // str_replace(" ","",$aaa[4]),
            'CURRENCY' => "EUR"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 4, //ID_типа_цены,
            'PRICE' => $aaa[5], // str_replace(" ","",$aaa[5]),
            'CURRENCY' => "EUR"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 5, //ID_типа_цены,
            'PRICE' => $aaa[6], // str_replace(" ","",$aaa[6]),
            'CURRENCY' => "EUR"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 6, //ID_типа_цены,
            'PRICE' => $aaa[7], //str_replace(" ","",$aaa[7]),
            'CURRENCY' => "EUR"
        ));
    }
    else {
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 7, //ID_типа_цены,
            'PRICE' => $aaa[8], // str_replace(" ","",$aaa[8]),
            'CURRENCY' => "RUB"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 8, //ID_типа_цены,
            'PRICE' => $aaa[9], // str_replace(" ","",$aaa[9]),
            'CURRENCY' => "RUB"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 9, //ID_типа_цены,
            'PRICE' => $aaa[10],
            'CURRENCY' => "RUB"
        ));
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 10, //ID_типа_цены,
            'PRICE' => $aaa[11], //str_replace(" ","",$aaa[11]),
            'CURRENCY' => "RUB"
        ));
    }
    /*$priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 3, //ID_типа_цены,
        'PRICE' => $aaa[4], // str_replace(" ","",$aaa[4]),
        'CURRENCY' => "EUR"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 4, //ID_типа_цены,
        'PRICE' => $aaa[5], // str_replace(" ","",$aaa[5]),
        'CURRENCY' => "EUR"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 5, //ID_типа_цены,
        'PRICE' => $aaa[6], // str_replace(" ","",$aaa[6]),
        'CURRENCY' => "EUR"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 6, //ID_типа_цены,
        'PRICE' => $aaa[7], //str_replace(" ","",$aaa[7]),
        'CURRENCY' => "EUR"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 7, //ID_типа_цены,
        'PRICE' => $aaa[8], // str_replace(" ","",$aaa[8]),
        'CURRENCY' => "RUB"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 8, //ID_типа_цены,
        'PRICE' => $aaa[9], // str_replace(" ","",$aaa[9]),
        'CURRENCY' => "RUB"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 9, //ID_типа_цены,
        'PRICE' => $aaa[10],
        'CURRENCY' => "RUB"
    ));
    $priceId = CPrice::Add(array(
        'PRODUCT_ID' => $ob["ID"],
        'CATALOG_GROUP_ID' => 10, //ID_типа_цены,
        'PRICE' => $aaa[11], //str_replace(" ","",$aaa[11]),
        'CURRENCY' => "RUB"
    ));*/

/*    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => array(
                array("ROZNICA_EUR", $aaa[4], "EUR"),
                array("MELKO_OPTS_EUR", $aaa[5], "EUR"),
                array("OPTS_EUR", $aaa[6], "EUR"),
                array("KRUPNO_OPTS_EUR", $aaa[7], "EUR"),
                array("ROZNICA_RUB", $aaa[8], "RUB"),
                array("MELKO_OPTS_RUB", $aaa[9], "RUB"),
                array("OPTS_RUB", $aaa[10], "RUB"),
                array("KRUPNO_OPTS_RUB", $aaa[11], "RUB")
        )
            //"MELKO_OPTS_EUR", "OPTS_EUR", "KRUPNO_OPTS_EUR", "ROZNICA_RUB", "MELKO_OPTS_RUB", "OPTS_RUB", "KRUPNO_OPTS_RUB"), //3,
        //"PRICE" => array($aaa[4], $aaa[5], $aaa[6], $aaa[7], $aaa[8], $aaa[9], $aaa[10], $aaa[11]),
        //"CURRENCY" => "RUB"
    );
    CPrice::Update($ob["ID"], $arFields);*/

    /*EURO*/
    /*$arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 3,
        "PRICE" => $aaa[4],
        "CURRENCY" => "EUR"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 4,
        "PRICE" => $aaa[5],
        "CURRENCY" => "EUR"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 5,
        "PRICE" => $aaa[6],
        "CURRENCY" => "EUR"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 6,
        "PRICE" => $aaa[7],
        "CURRENCY" => "EUR"
    );
    CPrice::Update($ob["ID"], $arFields);*/
    /*RUB*/
    /*$arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 7,
        "PRICE" => $aaa[8],
        "CURRENCY" => "RUB"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 8,
        "PRICE" => $aaa[9],
        "CURRENCY" => "RUB"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 9,
        "PRICE" => $aaa[10],
        "CURRENCY" => "RUB"
    );
    CPrice::Update($ob["ID"], $arFields);
    $arFields = Array(
        "PRODUCT_ID" => $ob["ID"],
        "CATALOG_GROUP_ID" => 10,
        "PRICE" => $aaa[11],
        "CURRENCY" => "RUB"
    );
    CPrice::Update($ob["ID"], $arFields);*/


    $i++;
    //if ($i > 2){break;}
    //if( $ob["ID"] == ){break;}
}


/*Новая версия через функцию*/

<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule('iblock');
$dataFileName = $_SERVER["DOCUMENT_ROOT"] . "/1c/data_stallock.txt";
$data = explode ("\n\r", file_get_contents($dataFileName));

$counter = 0;
foreach($data as $key => $value ){
    $t = trim($value);

    if($t != ""){
        $aaa = explode(">\t<", substr($t, 1, -1));
    }

    //strtr($aaa[9], " ", "");
    $aaa[4] = preg_replace("/\s/u","",$aaa[4]); //euro
    $aaa[5] = preg_replace("/\s/u","",$aaa[5]); //euro
    $aaa[6] = preg_replace("/\s/u","",$aaa[6]); //euro
    $aaa[7] = preg_replace("/\s/u","",$aaa[7]); //euro
    $aaa[8] = preg_replace("/\s/u","",$aaa[8]); //rub
    $aaa[9] = preg_replace("/\s/u","",$aaa[9]); //rub
    $aaa[10] = preg_replace("/\s/u","",$aaa[10]); //rub
    $aaa[11] = preg_replace("/\s/u","",$aaa[11]); //rub

    /*
    echo "<pre>";
    print_r($aaa);
    echo "<pre>";
    */

    $IBLOCK_ID = 14;
    $ELEMENT_ID = 0;

    $PROPERTY_VALUES = array(
        "NAME_IN_1C" => $aaa[1],
        "VENDOR_IN_1C" => $aaa[2],
        "ARTIKUL_IN_1C" => $aaa[3],
    );

    $arFilter = array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "XML_ID" => $aaa[0],
    );
    $arSelect = Array("ID", "NAME");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->Fetch()){
        echo "ELEMENT_ID = " . $ob["ID"];
        $ELEMENT_ID = $ob["ID"];
    }
    if ($ELEMENT_ID != 0) {
        CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES);
        $counter++;
    }

    // добавление коэффициента единицы измерения товара
    $result = \Bitrix\Catalog\MeasureRatioTable::add(array(
        'PRODUCT_ID' => $ob["ID"],
        'RATIO' => "" //коэффициент_единицы_измерения
    ) );
    // добавление цены
    if(!empty($aaa[4])){
        //БАЗОВАЯ ЦЕНА
        setPrice($ob["ID"],1 , $aaa[4], "EUR");

        /*$priceId = CPrice::SetBasePrice(
            $ob["ID"],
            $aaa[4],
            "EUR"
        );*/
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => , //ID_типа_цены,
            'PRICE' => $aaa[4], // str_replace(" ","",$aaa[4]),
            'CURRENCY' => "EUR"
        ));*/
        //МЕЛКИЙ ОПТ
        setPrice($ob["ID"],3 , $aaa[5], "EUR");
        /* $priceId = CPrice::Add(array(
             'PRODUCT_ID' => $ob["ID"],
             'CATALOG_GROUP_ID' => 3, //ID_типа_цены,
             'PRICE' => $aaa[5], // str_replace(" ","",$aaa[5]),
             'CURRENCY' => "EUR"
         ));*/
        //ОПТ
        setPrice($ob["ID"],2 , $aaa[6], "EUR");
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 2, //ID_типа_цены,
            'PRICE' => $aaa[6], // str_replace(" ","",$aaa[6]),
            'CURRENCY' => "EUR"
        ));*/
        //КРУПНЫЙ ОПТ
        setPrice($ob["ID"],4 , $aaa[7], "EUR");
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 4, //ID_типа_цены,
            'PRICE' => $aaa[7], //str_replace(" ","",$aaa[7]),
            'CURRENCY' => "EUR"
        ));*/
    }
    else {
        //БАЗОВАЯ ЦЕНА
        setPrice($ob["ID"],1 , $aaa[8], "RUB");
        /*$priceId = CPrice::SetBasePrice(
            $ob["ID"],
            $aaa[8],
            "RUB"
        );*/
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => , //ID_типа_цены,
            'PRICE' => $aaa[8], // str_replace(" ","",$aaa[8]),
            'CURRENCY' => "RUB"
        ));*/
        //МЕЛКИЙ ОПТ
        setPrice($ob["ID"],3 , $aaa[9], "RUB");
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 3, //ID_типа_цены,
            'PRICE' => $aaa[9], // str_replace(" ","",$aaa[9]),
            'CURRENCY' => "RUB"
        ));*/
        //ОПТ
        setPrice($ob["ID"],2 , $aaa[10], "RUB");
        /*$priceId = CPrice::Add(array(
            'PRODUCT_ID' => $ob["ID"],
            'CATALOG_GROUP_ID' => 2, //ID_типа_цены,
            'PRICE' => $aaa[10],
            'CURRENCY' => "RUB"
        ));*/
        //КРУПНЫЙ ОПТ
        setPrice($ob["ID"],4 , $aaa[11], "RUB");
        /* $priceId = CPrice::Add(array(
             'PRODUCT_ID' => $ob["ID"],
             'CATALOG_GROUP_ID' => 4, //ID_типа_цены,
             'PRICE' => $aaa[11], //str_replace(" ","",$aaa[11]),
             'CURRENCY' => "RUB"
         ));*/
    }

    //if ($i > 2){break;}
    //if( $ob["ID"] == 422){break;}
}

echo 'Изменено ' . $counter . 'элементов';

function setPrice($id, $priceType, $priceVal, $currency){
    $PRODUCT_ID = $id;
    $PRICE_TYPE_ID = $priceType;

    $arFields = Array(
        "PRODUCT_ID" => $PRODUCT_ID,
        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
        "PRICE" => $priceVal,
        "CURRENCY" => $currency
    );

    $res = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
        )
    );

    if ($arr = $res->Fetch())
    {
        CPrice::Update($arr["ID"], $arFields);
    }
    else
    {
        CPrice::Add($arFields);
    }
}
