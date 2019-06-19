<?
// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("MyClass", "OnAfterIBlockElementUpdateHandler"));

class MyClass
{
    // создаем обработчик события "OnAfterIBlockElementUpdate"
    function OnAfterIBlockElementUpdateHandler(&$arFields)
    {

        echo "<pre>";
        print_r($arFields);
        echo "</pre>";

        //die();


        if($arFields["RESULT"]){
            //AddMessage2Log("Запись с кодом ".$arFields["ID"]." изменена.");
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_VENDOR_ID");
            $arFilter = Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"], "ID"=>$arFields["ID"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->fetch()){
                echo "<pre>-------------------------------------------------";
                print_r($ob);
                echo "</pre>";
            }

            $arSelect = Array("ID", "IBLOCK_ID", "XML_ID", "NAME");
            $arFilter = Array("IBLOCK_ID"=>6, "XML_ID"=>$ob["PROPERTY_VENDOR_ID_VALUE"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($sec_ob = $res->fetch()) {
                echo "<pre>--------------------------------------------------";
                print_r($sec_ob);
                echo "</pre>";
            }
            CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], array("VENDOR_ID" => 333), "VENDOR_ID");

            //TEST
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
            $arFilter = Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"], "ID"=>$arFields["ID"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->fetch()){
                echo "<pre>---------------------------------------------------";
                print_r($ob);
                echo "</pre>";
            }
            die();
        }
        /*
        else{
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
        }
        */
    }
}
?>




<?
CModule::IncludeModule("iblock");
$local= $_SERVER["DOCUMENT_ROOT"] . '/imp_int/bitrix_test.csv';
$f=fopen($local,"r");
$i = 1;
while (($data = fgetcsv($f, 3000, ";", PHP_EOL)) !== FALSE) {
    if($i > 1){
        $arSelect = Array("ID", "IBLOCK_ID", "XML_ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=>6, "XML_ID"=>$data["1"]);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($vendorOb = $res->fetch()){
            $data["1"] = $vendorOb["ID"];
        }
        fputcsv($f, $data, ";", "", PHP_EOL);
    }
    $i++;
}
fclose($f);


// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("MyClass", "OnAfterIBlockElementUpdateHandler"));

class MyClass
{
    // создаем обработчик события "OnAfterIBlockElementUpdate"
    function OnAfterIBlockElementUpdateHandler(&$arFields)
    {

        AddMessage2Log($arFields);

        if($arFields["RESULT"]){
            //AddMessage2Log("Запись с кодом ".$arFields["ID"]." изменена.");
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_VENDOR_ID");
            $arFilter = Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"], "ID"=>$arFields["ID"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->fetch()){

            }
            AddMessage2Log($ob);

            $arSelect = Array("ID", "IBLOCK_ID", "XML_ID", "NAME");
            $arFilter = Array("IBLOCK_ID"=>6, "XML_ID"=>$ob["PROPERTY_VENDOR_ID_VALUE"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($sec_ob = $res->fetch()) {

            }
            AddMessage2Log($sec_ob);

            CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], array("VENDOR_ID" => $sec_ob["ID"]), "VENDOR_ID");

            //TEST

            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_VENDOR_ID");
            $arFilter = Array("IBLOCK_ID"=>$arFields["IBLOCK_ID"], "ID"=>$arFields["ID"]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->fetch()){
                echo "<pre>---------------------------------------------------";
                print_r($ob);
                echo "</pre>";
            }
            AddMessage2Log($ob);
            //die();

        }
        /*
        else{
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
        }
        */
    }
}
?>
<script>
    function loadForm() {
        var site = document.location.href;
        console.log(site);
        document.getElementById('site-id').setAttribute('value', site);
    }
</script>
