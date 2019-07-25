    <div class="order-form">
        <form action="" class="request_form" id="request_form">
            <div class="form-heading">
                Форма заказа
            </div>
            <div class="clear">
            </div>
            <div class="form-inner">
                <div class="ok_message">
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="select_cs">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:catalog.section",
                                "price_select",
                                Array(
                                    "ACTION_VARIABLE" => "action",
                                    "ADD_PROPERTIES_TO_BASKET" => "N",
                                    "ADD_SECTIONS_CHAIN" => "N",
                                    "AJAX_MODE" => "N",
                                    "AJAX_OPTION_ADDITIONAL" => "",
                                    "AJAX_OPTION_HISTORY" => "N",
                                    "AJAX_OPTION_JUMP" => "N",
                                    "AJAX_OPTION_STYLE" => "N",
                                    "BACKGROUND_IMAGE" => "-",
                                    "BASKET_URL" => "/personal/basket.php",
                                    "BROWSER_TITLE" => "-",
                                    "CACHE_FILTER" => "N",
                                    "CACHE_GROUPS" => "Y",
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_TYPE" => "A",
                                    "COMPONENT_TEMPLATE" => "price_table",
                                    "DETAIL_URL" => "",
                                    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                                    "DISPLAY_BOTTOM_PAGER" => "N",
                                    "DISPLAY_TOP_PAGER" => "N",
                                    "ELEMENT_SORT_FIELD" => "sort",
                                    "ELEMENT_SORT_FIELD2" => "id",
                                    "ELEMENT_SORT_ORDER" => "asc",
                                    "ELEMENT_SORT_ORDER2" => "desc",
                                    "FILTER_NAME" => "arrFilter",
                                    "IBLOCK_ID" => "1",
                                    "IBLOCK_TYPE" => "price",
                                    "INCLUDE_SUBSECTIONS" => "Y",
                                    "LINE_ELEMENT_COUNT" => "1",
                                    "MESSAGE_404" => "",
                                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                                    "MESS_BTN_BUY" => "Купить",
                                    "MESS_BTN_DETAIL" => "Подробнее",
                                    "MESS_BTN_SUBSCRIBE" => "Подписаться",
                                    "MESS_NOT_AVAILABLE" => "Нет в наличии",
                                    "META_DESCRIPTION" => "-",
                                    "META_KEYWORDS" => "-",
                                    "OFFERS_LIMIT" => "5",
                                    "PAGER_BASE_LINK_ENABLE" => "N",
                                    "PAGER_DESC_NUMBERING" => "N",
                                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                    "PAGER_SHOW_ALL" => "N",
                                    "PAGER_SHOW_ALWAYS" => "N",
                                    "PAGER_TEMPLATE" => ".default",
                                    "PAGER_TITLE" => "Товары",
                                    "PAGE_ELEMENT_COUNT" => "1000",
                                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                                    "PRICE_CODE" => array(),
                                    "PRICE_VAT_INCLUDE" => "Y",
                                    "PRODUCT_ID_VARIABLE" => "id",
                                    "PRODUCT_PROPERTIES" => array(),
                                    "PRODUCT_PROPS_VARIABLE" => "prop",
                                    "PRODUCT_QUANTITY_VARIABLE" => "",
                                    "PROPERTY_CODE" => array(0=>"HEIGHT",1=>"TYPE",2=>"PAYLOAD",3=>"PRICE_MKAD",4=>"PRICE_1_H",5=>"PRICE_FULL_DAY",6=>"",7=>"",8=>"",9=>"",10=>"",11=>"",),
                                    "SECTION_CODE" => "",
                                    "SECTION_ID" => $_REQUEST["SECTION_ID"],
                                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                                    "SECTION_URL" => "",
                                    "SECTION_USER_FIELDS" => array(0=>"",1=>"",),
                                    "SEF_MODE" => "N",
                                    "SET_BROWSER_TITLE" => "N",
                                    "SET_LAST_MODIFIED" => "N",
                                    "SET_META_DESCRIPTION" => "N",
                                    "SET_META_KEYWORDS" => "N",
                                    "SET_STATUS_404" => "N",
                                    "SET_TITLE" => "N",
                                    "SHOW_404" => "N",
                                    "SHOW_ALL_WO_SECTION" => "N",
                                    "SHOW_PRICE_COUNT" => "1",
                                    "TEMPLATE_THEME" => "blue",
                                    "USE_MAIN_ELEMENT_SECTION" => "N",
                                    "USE_PRICE_COUNT" => "N",
                                    "USE_PRODUCT_QUANTITY" => "N"
                                )
                            );?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ваше имя">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="tel" name="tel" placeholder="Ваш телефон">
                    </div>
                </div>
                <div class="row">
                    <div id="address_wrp" class="col-sm-4">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Адрес">
                    </div>
                    <div id="date_wrp" class="col-sm-4">
                        <input type="text" class="form-control" id="date" name="date" placeholder="Дата">
                    </div>
                    <div id="time_wrp" class="col-sm-4">
                        <input type="text" class="form-control" id="time" name="time" placeholder="Время">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="select_cs">
                            <select class="form-control" id="payment" name="payment">
                                <option value="">Вид оплаты</option>
                                <option value="cash">Наличный расчет</option>
                                <option value="non-cash">Безналичный расчет (для юр. лиц)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="select_cs">
                            <select class="form-control" name="hours" id="hours">
                                <option value="">Количество смен</option>
                                <option value="1s">1 смена</option>
                                <option value="2s">2 смены</option>
                                <option value="3s">3 смены</option>
                                <option value="4s">4 смены</option>
                                <option value="5s">5 смен</option>
                                <option value="10s">10 смен и более</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input type="submit" name="" class="submit" id="request-button" value="Отправить заявку" data-yagoal="formsend_main">
                        <div class="request-button-bot">Оправляя заявку, я принимаю <a href="#">условия</a> передачи информации</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="order-form-popup" class="order-form-popup">
        <div class="order-form-popup-inner">
            <div class="ofpp-select order-form-popup-address">
                <div id="order-form-popup-map">
                </div>
            </div>
            <div class="ofpp-select order-form-popup-datetime">
                <div class="date-picker-full">
                    <div class="order-form-popup-title _date">
                        Выберите дату начала работ
                    </div>
                    <div class="order-form-popup-title _time">
                        Выберите примерное время начала работ
                    </div>
                    <div id="datetimepicker12">
                    </div>
                </div>
            </div>
            <div class="order-form-popup-close">
            </div>
            <div class="order-form-popup-submit">
                Выбрать
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                var now = new Date();
                //console.log(moment(now.getFullYear()+'-'+now.getMonth()+'-'+now.getDate()));
                $('#datetimepicker12').datetimepicker({
                    inline: true,
                    sideBySide: true,
                    minDate: moment().subtract(0, 'days').millisecond(0).second(0).minute(0).hour(0),
                    locale: 'ru'
                });
            });

            $(".date-line .button").click(function () {
                $('.date-picker-full').toggle();
            });
            $(".date-line .date-select").click(function () {
                $('.date-picker-full').toggle();
            });
            $(".date-picker-full .f-close").click(function () {
                $('.date-picker-full').hide();
            });
            $(".date-picker-full .f-done").click(function () {
                $('.date-picker-full').hide();
            });
            $('#datetimepicker12').on('dp.change', function (e) {
                if ($('#order-form-popup .ofpp-select').hasClass('_date'))
                    $('#date').val(e.date.format('DD.MM.YY'));
                if ($('#order-form-popup .ofpp-select').hasClass('_time'))
                    $('#time').val(e.date.format('HH:mm'));
            });
            $('#order-form-popup .order-form-popup-submit').click(function () {

                if ($('#order-form-popup .ofpp-select').hasClass('_date')){
                    var d = $('#datetimepicker12').data("DateTimePicker").date().format('DD.MM.YY');
                    $('#date').val(d);
                }
                if ($('#order-form-popup .ofpp-select').hasClass('_time')){
                    var d = $('#datetimepicker12').data("DateTimePicker").date().format('HH:mm');
                    $('#time').val(d);
                }

                $('#order-form-popup').removeClass('opened');
            });
            $(document).ready(function ($) {
                $(function ($) {
                    $('#request_form [name="tel"]').mask('+7 (000) 000-00-00');
                });
            });
        </script>
    </div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script> <script>
    ymaps.ready(init);

    function init() {
        //order-form-popup-map
        var myPlacemark2,
            myMap2 = new ymaps.Map('order-form-popup-map', {
                center: [55.753994, 37.622093],
                zoom: 9
            }, {
                searchControlProvider: 'yandex#search'
            });

        // Слушаем клик на карте.
        myMap2.events.add('click', function (e) {
            var coords = e.get('coords');

            // Если метка уже создана – просто передвигаем ее.
            if (myPlacemark2) {
                myPlacemark2.geometry.setCoordinates(coords);
            }
            // Если нет – создаем.
            else {
                myPlacemark2 = createPlacemark(coords);
                myMap2.geoObjects.add(myPlacemark2);
                // Слушаем событие окончания перетаскивания на метке.
                myPlacemark2.events.add('dragend', function () {
                    getAddress(myPlacemark2.geometry.getCoordinates());
                });
            }
            getAddress(coords);
        });

        // Создание метки.
        function createPlacemark(coords) {
            return new ymaps.Placemark(coords, {
                iconCaption: 'поиск...'
            }, {
                preset: 'islands#violetDotIconWithCaption',
                draggable: true
            });
        }

        // Определяем адрес по координатам (обратное геокодирование).
        function getAddress(coords) {
            myPlacemark2.properties.set('iconCaption', 'поиск...');
            ymaps.geocode(coords).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);

                $('#address').val(firstGeoObject.getAddressLine());

                myPlacemark2.properties
                    .set({
                        // Формируем строку с данными об объекте.
                        iconCaption: [
                            // Название населенного пункта или вышестоящее административно-территориальное образование.
                            firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                            // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                            firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                        ].filter(Boolean).join(', '),
                        // В качестве контента балуна задаем строку с адресом объекта.
                        balloonContent: firstGeoObject.getAddressLine()
                    });
            });
        }
    }
</script>
