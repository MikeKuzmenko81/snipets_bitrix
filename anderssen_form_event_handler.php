<?
function my_onAfterResultAddUpdate($WEB_FORM_ID, $RESULT_ID)
{

    global $APPLICATION;
    CModule::IncludeModule('iblock');
    CModule::IncludeModule('form');
    CModule::IncludeModule('statistic');

    //const VERSION = '0.0.0';



    $current_domen = "anderssen";
    //$current_email = "anderssen.sofas@yandex.ru";
    // $current_email = "rek3@anderssen.ru";
    $current_email = "anderssen-mag@yandex.ru";
    ///12-07-2018
    //$current_hash = "d6a3c2ef87574466764734ba07cc6dc5";
    //$current_hash = "8ff7b9856eec8e8983c3fa297aac2ef2";
    // $current_hash = "db23373b81ca985e97414321514984ef";
    //$current_hash = "70d1a092760e0ae71370c0d44315e2cc";

    $current_hash = "c4803bd1eb9d6873ae59833a5a46ec1447a22d7a";


    $amo = new amoCRM($current_domen, $current_email, $current_hash, true, true);
    $amo->Auth();

    // $account_info = $amo->ViewAll();
    //print_r($account_info);
    /*$STATUSES = $amo->parseStatusesCRM($account_info);
    echo "<pre>";

    print_r($STATUSES);
    echo "</pre>";
    die("wasq");*/
//    $arAnswer = CFormResult::GetDataByID(
//        $RESULT_ID,
//        array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
//        $arResult, $arAnswer2);
//var_dump($arAnswer);exit();
    if ($WEB_FORM_ID == 7)  ////////////////Заявки на ремонт оформляются на другого пользователя AmoCRM
    {




        $arAnswer = CFormResult::GetDataByID(
            $RESULT_ID,
            array("NAME", "PHONE", "EMAIL", "COMMENT", "CITY", "ORDERNUM", "MODEL_NAME", "CALLTIME", "PHOTO"),
            $arResult,
            $arAnswer2);


        $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
        $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
        $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
        $CITY = $arAnswer["CITY"]["0"]["USER_TEXT"];
        $ORDERNUM = $arAnswer["ORDERNUM"]["0"]["USER_TEXT"];
        $MODEL_NAME = $arAnswer["MODEL_NAME"]["0"]["USER_TEXT"];
        $CALLTIME = $arAnswer["CALLTIME"]["0"]["USER_TEXT"];

        $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
        $info = 'Заявка с сайта'. "\n";
        $info .= 'Имя: '. $NAME. "\n";
        $info .= 'Телефон: '. $PHONE. "\n";
        $info .= 'Email: '.$EMAIL. "\n";
        $info .= 'Город: '.$CITY. "\n";
        $info .= 'Номер заказа: '.$ORDERNUM. "\n";
        $info .= 'Наименование изделия: '.$MODEL_NAME. "\n";
        $info .= 'Удобное время звонка: '.$CALLTIME. "\n";


        //$path = $_SERVER["DOCUMENT_ROOT"]."/images/news.gif"; // путь к файлу
        //$path = $arAnswer["PHOTO"];
        //$arPhoto = CFile::MakeFileArray($path);
        //CFormResult::SetField($RESULT_ID, 'PHOTO', $arPhoto);

        //echo '<pre style="display: none">';
        //print_r($arAnswer);
        //echo "</pre>";
        //die("wasq");

        if (trim($MESSAGE)) {
            $info .= 'Комментарий: '.$MESSAGE. "\n";
        }



        $ga_parms = array(
            'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
            'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
            'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
            'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
            'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
        );

        $amo->add(array(
            'name' => $NAME,
            'phone' => $PHONE,
            'email' => $EMAIL,
            'other' => $info,
            'ga' => $ga_parms
        ), 944889);





    }

    else {


        if ($WEB_FORM_ID == 1)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "CITY", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }






            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $CITY = $arAnswer["CITY"]["0"]["USER_TEXT"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            $info = 'Заявка с сайта  (купон)'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            $info .= 'Город: '.$CITY. "\n";



            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";



            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }
        elseif ($WEB_FORM_ID == 18)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("naam", "telefoon", "MODEL_IDENT", "recall_time", "recall_comment"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }






            $NAME = $arAnswer["naam"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["telefoon"]["0"]["USER_TEXT"];
            $MODEL_IDENT = $arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"];
            $RECALL_TIME = $arAnswer["recall_time"]["0"]["USER_TEXT"];
            $MESSAGE = $arAnswer["recall_comment"]["0"]["USER_TEXT"];
            $info = 'Заявка с сайта  (купон)'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Модель: '.$MODEL_IDENT. "\n";
            $info .= 'Желаемая дата и время звонка: '.$RECALL_TIME. "\n";



            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";



//            $ga_parms = array(
//                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
//                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
//                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
//                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
//                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
//            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
//                'email' => $EMAIL,
                'other' => $info,
//                'ga' => $ga_parms
            ));





        }
        elseif ($WEB_FORM_ID == 6)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 2, 'ID' => $arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                }

            }





            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $URLS = 'http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            $info = 'Заявка с формы «Заказать расчет» в карточке товара'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));







        }
        elseif ($WEB_FORM_ID == 2)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 2, 'ID' => $arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                }

            }





            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $URLS = 'http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            $info = 'Заявка с сайта'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }

        elseif ($WEB_FORM_ID == 8)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 2, 'ID' => $arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                }

            }





            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $URLS = 'http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) $info = 'Заявка с формы «Оформить заказ» в карточке товара'. "\n";
            else $info = 'Заявка с формы «Оформить заказ» на странице ИМ'. "\n";

            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }
        elseif ($WEB_FORM_ID == 9)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 2, 'ID' => $arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                }

            }





            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $URLS = 'http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) $info = 'Заявка с формы «Вызвать менеджера» в карточке товара'. "\n";
            else $info = 'Заявка с формы «Вызвать менеджера» на странице ИМ'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }
        elseif ($WEB_FORM_ID == 10) //////////////////// форма посещение шоу-рума
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }

            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $URLS = 'http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];
            $info = 'Заявка с формы «Посетить интернет-магазин» на странице ИМ'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }

            if($arAnswer["MODEL_IDENT"]["0"]["USER_TEXT"]>0) $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }

        elseif ($WEB_FORM_ID == 3)
        {
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "SALE_MODEL_IDENT"),  // вопрос "Какие области знаний вас интересуют?"
                $arResult,
                $arAnswer2);


            /* Авторизация аккаунта в AMOCRM */
            $user=array(
                'USER_LOGIN'=>'anderssen-mag@yandex.ru', #Ваш логин (электронная почта)
                'USER_HASH'=>'70d1a092760e0ae71370c0d44315e2cc' #Хэш для доступа к API (смотрите в профиле пользователя)
            );

            $subdomain='anderssen'; #Наш аккаунт - поддомен
            #Формируем ссылку для запроса
            $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($user));
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
            curl_close($curl); #Завершаем сеанс cURL
            $code=(int)$code;
            $errors=array(
                301=>'Moved permanently',
                400=>'Bad request',
                401=>'Unauthorized',
                403=>'Forbidden',
                404=>'Not found',
                500=>'Internal server error',
                502=>'Bad gateway',
                503=>'Service unavailable'
            );
            try
            {
                #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
                if($code!=200 && $code!=204)
                    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
            }
            catch(Exception $E)
            {
                die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
            }
            /**
             * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
             * нам придётся перевести ответ в формат, понятный PHP
             */
            $Response=json_decode($out,true);
            $Response=$Response['response'];


            $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; #$subdomain уже объявляли выше
            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
            curl_close($curl);
            $code=(int)$code;
            $errors=array(
                301=>'Moved permanently',
                400=>'Bad request',
                401=>'Unauthorized',
                403=>'Forbidden',
                404=>'Not found',
                500=>'Internal server error',
                502=>'Bad gateway',
                503=>'Service unavailable'
            );
            try
            {
                #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
                if($code!=200 && $code!=204)
                    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
            }
            catch(Exception $E)
            {
                die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
            }
            /**
             * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
             * нам придётся перевести ответ в формат, понятный PHP
             */
            $Response=json_decode($out,true);
            $account=$Response['response']['account'];

            foreach($account['pipelines'] as $key => $pipeline_two) {
                if($pipeline_two['name'] == 'Первичный контакт'){
                    $pipelineId = $pipeline_two['id'];
                }
            }

            foreach($account['users'] as $k => $v){
                if($v['name'] == 'Менеджер ИМ'){
                    $responsible_user_id = $v['id'];
                }
            }
            /* Добавляем сделку */
            $need=array_flip(array('Название товара'/*, 'Стоимость оптовая'*/, 'Фабричный номер'));
            if(isset($account['custom_fields'],$account['custom_fields']['leads']))
                do
                {
                    foreach($account['custom_fields']['leads'] as $field)
                        if(is_array($field) && isset($field['id']))
                        {
                            if(isset($field['code']) && isset($need[$field['code']]))
                                $fields[$field['code']]=(int)$field['id'];

                            if(isset($field['name']) && $field['name']=='Название товара')
                                $fields[$field['name']] = (int)$field['id'];

                            /*if(isset($field['name']) && $field['name']=='Стоимость оптовая')
    $fields[$field['name']] = (int)$field['id'];*/

                            if(isset($field['name']) && $field['name']=='Фабричный номер')
                                $fields[$field['name']] = (int)$field['id'];
                        }
                }
                while(false);
            else
                die('Невозможно получить дополнительные поля');

            $custom_fields=isset($fields) ? $fields : false;
//var_dump($custom_fields);die();
            //ДОБАВЛЯЕМ СДЕЛКУ
            $leads['request']['leads']['add']=array(
                array(
                    'name' => 'Заявка с сайта',
                    'status_id' => $pipelineId, //id статуса
                    'price' => $_REQUEST["form_hidden_160"],
                    'responsible_user_id' => $responsible_user_id, //id ответственного по сделке
                    'created_user_id' => $responsible_user_id,//id ответственного по сделке в истории
                    'tags' => 'Бронь с сайта anderssen.ru', #Теги
                    'custom_fields'=>array(
                        array(
                            'id'=>$custom_fields['Название товара'],
                            'values'=>array(
                                array(
                                    'value'=>$_REQUEST["form_hidden_161"]
                                )
                            )
                        ),
                        /*array(
                            'id'=>$custom_fields['Стоимость оптовая'],
                            'values'=>array(
                                array(
                                    'value'=>$_REQUEST["form_hidden_160"]
                                )
                            )
                        ),*/
                        array(
                            'id'=>$custom_fields['Фабричный номер'],
                            'values'=>array(
                                array(
                                    'value'=>$_REQUEST["form_hidden_158"]//фабричный номер
                                )
                            )
                        )
                    )
                )
            );

            $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
            curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
            $Response=json_decode($out,true);

            if(is_array($Response['response']['leads']['add']))
                foreach($Response['response']['leads']['add'] as $lead) {
                    $lead_id = $lead["id"]; //id новой сделки
                };
            //ДОБАВЛЯЕМ СДЕЛКУ - КОНЕЦ

            $need=array_flip(array('POSITION','PHONE','EMAIL', 'Дополнительная информация'));
            if(isset($account['custom_fields'],$account['custom_fields']['contacts']))
                do
                {
                    foreach($account['custom_fields']['contacts'] as $field)
                        if(is_array($field) && isset($field['id']))
                        {
                            if(isset($field['code']) && isset($need[$field['code']]))
                                $fields[$field['code']]=(int)$field['id'];

                            if(isset($field['name']) && $field['name']=='Дополнительная информация')
                                $fields[$field['name']] = (int)$field['id'];

                            $diff=array_diff_key($need,$fields);
                            if(empty($diff))//если совпадений между $need и $fields не найден не найдено то выбрасывает из цикла
                                break 2;
                        }
                    if(isset($diff))
                        die('В amoCRM отсутствуют следующие поля'.': '.join(', ',$diff));
                    else
                        die('Невозможно получить дополнительные поля');
                }
                while(false);
            else
                die('Невозможно получить дополнительные поля');
            $custom_fields=isset($fields) ? $fields : false;

            /* Добавление контакта */
            $contact=array(
                'name'=>$_REQUEST["form_text_11"],

                'linked_leads_id' => array(
                    $lead_id
                ),
                'responsible_user_id' => $responsible_user_id, //id ответственного, попробывать написать вместо id имя ответственного
                'created_user_id' => $responsible_user_id,
                'custom_fields'=>array(
                    array(
                        'id'=>$custom_fields['EMAIL'],
                        'values'=>array(
                            array(
                                'value'=>$_REQUEST["form_text_13"],
                                'enum'=>'WORK'
                            )
                        )
                    ),
                    array(
                        'id'=>$custom_fields['PHONE'],
                        'values'=>array(
                            array(
                                'value'=>$_REQUEST["form_text_12"],
                                'enum'=>'WORK'
                            )
                        )
                    ),
                    array(
                        'id'=>$custom_fields['Дополнительная информация'],
                        'values'=>array(
                            array(
                                'value'=>$_REQUEST["form_textarea_14"]
                            )
                        )
                    ),
                )
            );

            $set['request']['contacts']['add'][]=$contact;

            #Формируем ссылку для запроса
            $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
            curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
            $code=(int)$code;

            /**
             * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
             * нам придётся перевести ответ в формат, понятный PHP
             */
            $Response=json_decode($out,true);
            $Response=$Response['response']['contacts']['add'];

            $output='';
            foreach($Response as $v)
                if (is_array($v))
                    $output .= $v['id'] . PHP_EOL;

            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["SALE_MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 6, 'ID' => $arAnswer["SALE_MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE", "PROPERTY_EMAIL", "PROPERTY_SALON_ID.NAME"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALE_MODEL_CODE', $arINFO["CODE"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                    CFormResult::SetField($RESULT_ID, 'SALON_EMAIL', $arINFO["PROPERTY_EMAIL_VALUE"]);
                    CFormResult::SetField($RESULT_ID, 'SALON_INFO', $arINFO["PROPERTY_SALON_ID__NAME"]);

                }

            }








        }

        elseif ($WEB_FORM_ID == 4)
        {
            //
            $arAnswer = CFormResult::GetDataByID(
                $RESULT_ID,
                array("NAME", "PHONE", "EMAIL", "COMMENT", "SALE_MODEL_IDENT", "ga_cid", "ga_source", "ga_campaign", "ga_term", "ga_medium"),  //
                $arResult,
                $arAnswer2);


            $arFilter = array(
                "GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
            );

            // получим список записей
            $rs = CSession::GetList(
                ($by = "s_id"),
                ($order = "desc"),
                $arFilter,
                $is_filtered
            );
            $arSTAT = array();
            $arINFO = Array();
            // выведем все записи
            while ($ar = $rs->Fetch())
            {
                $arSTAT = $ar;
                CFormResult::SetField($RESULT_ID, 'STAT_ID', $ar["ID"]);
                CFormResult::SetField($RESULT_ID, 'REFERER', $ar["REFERER1"]);
                CFormResult::SetField($RESULT_ID, 'ADV_ID', $ar["ADV_ID"]);
            }



            if($arAnswer["SALE_MODEL_IDENT"]["0"]["USER_TEXT"]>0) {
                $arFilter = array('IBLOCK_ID' => 6, 'ID' => $arAnswer["SALE_MODEL_IDENT"]["0"]["USER_TEXT"]);
                $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, Array("IBLOCK_ID", "ID", "DETAIL_PAGE_URL", "NAME", "PROPERTY_PRICE", "PROPERTY_MODEL_ID_NAME"));
                if($arElement = $rsElements->GetNext()) {
                    $arINFO = $arElement;
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_URL', $arINFO["DETAIL_PAGE_URL"]);
                    CFormResult::SetField($RESULT_ID, 'SALE_MODEL_CODE', $arINFO["CODE"]);
                    CFormResult::SetField($RESULT_ID, 'SALEMODEL_NAME', $arINFO["NAME"]);
                }

            }





            $NAME = $arAnswer["NAME"]["0"]["USER_TEXT"];
            $PHONE = $arAnswer["PHONE"]["0"]["USER_TEXT"];
            $EMAIL = $arAnswer["EMAIL"]["0"]["USER_TEXT"];
            $MESSAGE = $arAnswer["COMMENT"]["0"]["USER_TEXT"];

            $info = 'Заявка с сайта'. "\n";
            $info .= 'Имя: '. $NAME. "\n";
            $info .= 'Телефон: '. $PHONE. "\n";
            $info .= 'Email: '.$EMAIL. "\n";
            if (trim($MESSAGE)) {
                $info .= 'Комментарий: '.$MESSAGE. "\n";
            }
            $info .= 'Страница модели: http://www.anderssen.ru'.$arINFO["DETAIL_PAGE_URL"]. "\n";
            $info .= 'Данные статистики: http://www.anderssen.ru/bitrix/admin/session_detail.php?lang=ru&ID='.$arSTAT["ID"]. "\n";


            $ga_parms = array(
                'cid' => $arAnswer["ga_cid"]["0"]["USER_TEXT"],
                'medium' => $arAnswer["ga_medium"]["0"]["USER_TEXT"],
                'source' => $arAnswer["ga_source"]["0"]["USER_TEXT"],
                'term' => $arAnswer["ga_term"]["0"]["USER_TEXT"],
                'campaign' => $arAnswer["ga_campaign"]["0"]["USER_TEXT"]
            );

            $amo->add(array(
                'name' => $NAME,
                'phone' => $PHONE,
                'price' => $arINFO["PROPERTY_PRICE_VALUE"],
                'email' => $EMAIL,
                'other' => $info,
                'ga' => $ga_parms
            ));





        }
    }
}
AddEventHandler('form', 'onAfterResultAdd', 'my_onAfterResultAddUpdate');
?>
