<?
IncludeModuleLangFile(__FILE__);

class CUserTypeStatusBar extends CUserFieldEnum{

    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "listtostatusbar",
            "CLASS_NAME" => "CUserTypeStatusBar",
            "DESCRIPTION" => GetMessage( 'IBLOCK_PROP_STATUSBAR' ),
            "BASE_TYPE" => "list",
        );
    }


    function GetUserPropTypeDescription() {
        return array(
            'PROPERTY_TYPE'        => 'L',
            'USER_TYPE'            => 'listtostatusbar',
            'DESCRIPTION'          => GetMessage( 'IBLOCK_PROP_STATUSBAR' ),
            'GetAdminListViewHTML' => array( __CLASS__ , 'GetAdminListViewHTML' ),
            'GetPropertyFieldHtml' => array( __CLASS__ , 'GetPropertyFieldHtml' )
        );
    }

    public static function GetAdminListViewHTML( $arProperty, $value, $strHTMLControlName ){

        if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
        {
            return $value["VALUE"];
        }
        elseif(strlen($value["VALUE"]["SRC"])>0)
        {
            $return = '<img alt="'.$value["VALUE"]["ALT"].'" title="'.$value["VALUE"]["TITLE"].'" width="100" src="'.$value["VALUE"]["SRC"].'"><br />'.$value["VALUE"]["ALT"].'<br />'.$value["VALUE"]["TITLE"].' <br>';
        }
        else
        {
            $return = '';
        }
        return $return;
    }


    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
        global $APPLICATION;

        /*print_r($arProperty);
        echo "<br>";
        print_r($value);
        echo "<br>";
        print_r($strHTMLControlName);
        echo "<br>";*/


        $size = $arProperty["MULTIPLE_CNT"];  // ' size="5" ';
        $res = array();
        //Получаем значения из списка отсортированные по сортировке или по алфавиту
        $prop_enums = CIBlockProperty::GetPropertyEnum($arProperty['ID'], array("SORT"=>"ASC", "VALUE"=>"ASC"));
        while($ar_enum = $prop_enums->Fetch())
        {
            $res[] = $ar_enum;
        }

        /*if (strLen(trim($strHTMLControlName["FORM_NAME"])) <= 0)
            $strHTMLControlName["FORM_NAME"] = "form_element";
        $name = preg_replace("/[^a-zA-Z0-9_]/i", "x", htmlspecialcharsbx($strHTMLControlName["VALUE"]));*/

        /*if(is_array($value["VALUE"]["VALUE"]))
        {
            $value["VALUE"] = $value["VALUE"]["VALUE"];
            /*$value["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];*//*
        }*/

        if (is_array($value)) {
            foreach ($value as $item) {
                foreach ($res as &$arRes) {
                    if ($item["VALUE"] == $arRes["ID"]) {
                        $arRes["DEF"] = "Y";
                    }
                }
            }
        }

        $option = "";
        foreach ($res as $item) {
            $option .= '<option style="display:table-cell;max-height: fit-content;width: 40px;border: 1px solid #000;" ' . ($item['DEF'] == 'Y' ? 'selected' : '') . ' value="' . $item['ID'] . '" name="' . $item['VALUE'] .'">' . $item['VALUE'] /*'&nbsp;' */ . '</option>';
        }

        $html = '<select multiple  name="' . $strHTMLControlName["VALUE"] . '[]"' .  'size="' . $size . '"style="display:inline-table;max-height:18px;border: none;overflow:hidden;">';
        /*$html = '
        <div>
            <div class="crm-list-stage-bar">
                <table class="crm-list-stage-bar-table">
                    <tbody>
                    <tr>
                    ';*/
        $html .= $option;
        $html .= '</select>';
/*                        <td class="crm-list-stage-bar-part" style="background: rgb(255, 255, 0);">
                            <div class="crm-list-stage-bar-block  crm-stage-new">
                                <div class="crm-list-stage-bar-btn"></div>
                            </div>
                        </td>
                        <td class="crm-list-stage-bar-part" style="background: rgb(255, 255, 0);">
                            <div class="crm-list-stage-bar-block  crm-stage-details">
                                <div class="crm-list-stage-bar-btn"></div>
                            </div>
                        </td>
                        <td class="crm-list-stage-bar-part" style="">
                            <div class="crm-list-stage-bar-block  crm-stage-cannot_contact">
                                <div class="crm-list-stage-bar-btn"></div>
                            </div>
                        </td>
                        <td class="crm-list-stage-bar-part" style="">
                            <div class="crm-list-stage-bar-block  crm-stage-in_process">
                                <div class="crm-list-stage-bar-btn"></div>
                            </div>
                        </td>
                        <td class="crm-list-stage-bar-part">
                            <div class="crm-list-stage-bar-block  crm-stage-converted">
                                <div class="crm-list-stage-bar-btn"></div>
                            </div>
                        </td>
*/
        /*$html .= '
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="crm-list-stage-bar-title">
                Для формирования КП не хватает данных - общаемся
            </div>
        </div>
        ';*/

        /*$html .= "Что-то работает ";
        foreach ($res as $item) {
            $html .= " ". $item["VALUE"]." ";
        }*/


        /*
        if($strHTMLControlName["MODE"]=="FORM_FILL" && CModule::IncludeModule('fileman'))
        {
            $return = '';
            if(!$value["VALUE"]["SRC"])  $return .= '<span>'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_NOPIC' ).'</span>';
            else $return .= '<img style="max-width:200px;  maxheight: 180px;" width="200" src="'.$value["VALUE"]["SRC"].'">';


            $return .= CFileInput::Show($strHTMLControlName["VALUE"]."[SRC]", $value["VALUE"]["SRC"],
                array(
                    "PATH" => "Y",
                    "IMAGE" => "N",
                    "MAX_SIZE" => array(
                        "W" => COption::GetOptionString("iblock", "detail_image_size"),
                        "H" => COption::GetOptionString("iblock", "detail_image_size"),
                    ),
                ), array(
                    'upload' => false,
                    'medialib' => true,
                    'file_dialog' => true,
                    'cloud' => true,
                    'del' => true,
                    'description' => false,
                )
            );

            $return .= '<table><tr><td><span title="'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_ALT_DESC' ).'">'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_ALT' ).':</td><td><input name="'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'[ALT]" value="'.htmlspecialcharsEx($value["VALUE"]["ALT"]).'" size="32" type="text"></span></td></tr>';
            $return .= '<tr><td><span title="'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_TITLE_DESC' ).'">'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_TITLE' ).' :</td><td><input name="'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'[TITLE]" value="'.htmlspecialcharsEx($value["VALUE"]["TITLE"]).'" size="32" type="text"></span></td></tr></table>';


            return $return;
        }
        else
        {
            $return = '<table><tr><td rowspan="3" width="100">';
            if(!$value["VALUE"]["SRC"])  $return .= '<span>'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_NOPIC' ).'</span></td>';
            else $return .= '<img style="max-width:100px;  maxheight: 80px;" width="100" src="'.$value["VALUE"]["SRC"].'"></td>';
            $return .= '<td>  <span title="'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_SRC_DESC' ).'">'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_SRC' ).': <input name="'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'[SRC]" value="'.htmlspecialcharsEx($value["VALUE"]["SRC"]).'" size="32" type="text"></span>';
            $return .= '<br /><span title="'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_ALT_DESC' ).'">'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_ALT' ).':<input name="'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'[ALT]" value="'.htmlspecialcharsEx($value["VALUE"]["ALT"]).'" size="32" type="text"></span>';
            $return .= '<br /><span title="'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_TITLE_DESC' ).'">'.GetMessage( 'IBLOCK_PROP_IMAGESNOTUPLOADDIR_TITLE' ).' :<input name="'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'[TITLE]" value="'.htmlspecialcharsEx($value["VALUE"]["TITLE"]).'" size="32" type="text"></span></table>';

            return $return;
        }*/

        return $html;
    }
}

