
<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/111/log.txt");
AddMessage2Log("q", "my_module_id");

function my_onBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES){
    global $APPLICATION;
    // действие обработчика распространяется только на форму с ID=6
    if ($WEB_FORM_ID == 7) {
        // в текстовый вопрос с ID=135 должен содержать целое число, большее 5ти.
        $arrVALUES['form_text_135'] = intval($arrVALUES['form_text_135']);
        if ($arrVALUES['form_text_135'] < 5) {
            // если значение не подходит - отправим ошибку.
            $APPLICATION->ThrowException('Значение должно быть больше или равно 5!');
        }
    }
}
AddEventHandler('form', 'onBeforeResultAdd', 'my_onBeforeResultAdd');

$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
$APPLICATION->AddAdditionalJS(SITE_TEMPLATE_PATH."menu.js");
