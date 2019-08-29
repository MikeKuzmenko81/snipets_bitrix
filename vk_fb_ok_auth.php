<?header("Pragma: no-cache");// Никогда не используем кэш
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
// переменные для настройки [Начало настроек]
// Для ВКонтакте
$vkIdApp = 6905497;// Id приложения ВКонтакте зарегистрированного для целей авторизации
$vkS = "BNWxufK72GxeXvHBrA33";//Секретный ключ приложения ВКонтакте
$vkAuthUrl = "http://artponline.aspired.ru/local/templates/main/ajax/socialauth.php?action=vk";//URL для возврата после авторизации через VK

// Для Одноклассники
$okIdApp = "1277195520";// Id приложения в Одноклассники
$okS = "B282AA9369EAA7A117FA6A50";// Секретный ключ приложения в Одноклассники
$okP = "CBAGHBCNEBABABABA";// Публичный ключ приложения в Одноклассники
$okAuthUrl = "http://artponline.aspired.ru/local/templates/main/ajax/socialauth.php?action=ok";//URL для возврата после авторизации через Одноклассники

// Для FaceBook
$fbIdApp = "1137321549809847";// Id приложения в FaceBook
$fbS = "128cff26f10b3ceef713c82739158bb2";// Секретный ключ приложения в FaceBook
$fbAuthUrl = "https://artponline.aspired.ru/local/templates/main/ajax/socialauth.php?action=fb";//URL для возврата после авторизации через FaceBook

// [Конец настроек]

// Генерация переменных на случай регистрации нового пользователя
$passwordToReg = randString(10, array("abcdefghijklnmopqrstuvwxyz","ABCDEFGHIJKLNMOPQRSTUVWX­YZ","0123456789","!@#\$%^&*()"));// Генерирует пароль
$loginToReg = randString(1, array("abcdefghijklnmopqrstuvwxyz")) . randString(11, array("abcdefghijklnmopqrstuvwxyz","0123456789"));// Генерирует логин
$emailToReg = randString(1, array("abcdefghijklnmopqrstuvwxyz")) . randString(11, array("abcdefghijklnmopqrstuvwxyz","0123456789")) . "@fake.autoreg.bysocid.com";// Генерирует E-mail

// Вспомогательная функция - получение информации о пользователе битрикс по идентификатору соответствующей социальной сети
function getUserInfoBySocId($socName=false,$id=false) {
    if (($socName !== false) and ($id !== false)) {
        $filter = array();
        if (($socName == "VK") or ($socName == "OK") or ($socName == "FB")) {// В этой строке в дальнейшем необходимо перечислить все возможные социальные сети
            // Создаём фильтр в зависимости от социальной сети
            if ($socName == "VK") {
                $filter = array("UF_VKID"=>$id);
            } elseif ($socName == "OK") {
                $filter = array("UF_OKID"=>$id);
            } elseif ($socName == "FB") {
                $filter = array("UF_FBID"=>$id);
            }

            // Создаём прочие переменные необходимые для запроса информации о пользователях bitrix
            $order = array('sort' => 'asc');
            $tmp = 'sort';

            //Выполняем запрос и обрабатываем результат
            $dbUsers = CUser::GetList($order, $tmp, $filter,array("SELECT"=>array("UF_*")));
            if ($arUser = $dbUsers->Fetch()) {
                return $arUser;
            }
        }
    }
    return false;
}

// Вспомогательная функция - получение информации о авторизированном в настоящий момент пользователе битрикс
function getAuthUserInfo() {
    $filter = array();
    $filter = array("ID"=>CUser::GetID());
    $order = array('sort' => 'asc');
    $tmp = 'sort';

    //Выполняем запрос и обрабатываем результат
    $dbUsers = CUser::GetList($order, $tmp, $filter,array("SELECT"=>array("UF_*")));
    if ($arUser = $dbUsers->Fetch()) {
        return $arUser;
    }
    return false;
}




// Проверяем передан ли action
if (isset($_REQUEST["action"]) and ($_REQUEST["action"] != "")) {
    global $USER;// поскольку потребуется для всех action определяем переменную здесь

    // определяем авторизован ли пользователь битрикс на момент начала выполнения скрипта
    $authnow = false;
    if (CUser::GetID()) {
        $authnow = CUser::GetID();
    }

    $action = $_REQUEST["action"];// Определяем action(содержит информацию о социальной сети с которой сейчас осуществляется взаимодействие)

    if ($action == "vk") {
        // Если авторизация производится через социальную сеть ВКонтакте
        $uid = false;// В переменной при необходимости сохраняется Id пользователя ВКонтакте
        if (isset($_REQUEST["code"]) and ($code = $_REQUEST["code"])) {// Если авторизация на сервере ВКонтакте прошла успешно, пользователь вернётся для подтверждение авторизации на страничку сайта содержащую параметр code/ Проверяем действительно ли code передан сервером ВКонтакте и в случаи корректности получаем access_token и user_id, для чего отправляем запрос серверу ВКонтакте

            // Поскольку при работе с file_get_contents результат от сервера ВКонтакте не удавалось получить используется curlPro
            require_once('curlPro.php');// Подключаем библиотеку curlPro
            $cp = new curlPro;// Экземпляр объекта curlPro
            $cp->newip("oauth.vk.com");// IP адрес для установки соединения
            $cp->newuri("/access_token?client_id=$vkIdApp&client_secret=$vkS&redirect_uri=" . $vkAuthUrl . "&code=$code");// URi запроса
            $cp->https(true);// Требуется именно HTTPS соединение
            $text = $cp->go();// Выполняем запрос к серверу ВКонтакте
            $text = $text->val();// Избавляемся от парсинг объекта, получаем текстовое значение результата

            if ($result = json_decode($text)) {// Сервер ВКонтакте должен вернуть объект JSON
                $updateUserInfoAr = array();// Если не требуется обновить информацию о пользователе в соответствии с информацией в социальной сети массив остаётся пустым, если требуется - данные которые требуется обновить передаются в массив, а именно ключом является поле в ответе ВКонтакте, а значением соответствующее поле в инфоблоке bitrix
                // Проверяем наличие Ид пользователя ВКонтакте в ответе
                if (isset($result->user_id) and $result->user_id) {
                    $uid = $result->user_id;

                    // Проверяем наличие $access_token в ответе
                    $access_token = false;
                    if (isset($result->access_token) and $result->access_token) {
                        $access_token = $result->access_token;
                    }

                    // Получаем информацию есть ли пользователь с таким Id ВКонтакте уже в базе данных
                    $userInfo = getUserInfoBySocId("VK",$uid);

                    // Вычисляем необходимость в изменении информации в пользовательских полях базы данных(а именно имени и фамилии), кроме случая когда пользователь авторизован,
                    // но информация о опрделённом Id ВКонтакте относится к другому пользователю(в этом случаи просто выдаём ошибку)
                    if (isset($userInfo["ID"]) and $authnow and ($userInfo["ID"] != $authnow)) {// Если условие выполняется выводим информацию и завершаем выполнение скрипта
                        echo "<p>Такой Id-ВКонтакте уже закреплён за другим пользователем. Для того чтобы продолжить работу с старым аккаунтом, нажмите ссылку <a href=\"/logout/\">выход</a> в меню сайта, а затем выполните авторизацию через ВКонтакте.</p>\n";
                        echo "<p><a href=\"/\">Перейти на главную страницу сайта</a>. <a href=\"/account/\">Перейти в аккаунт</a>.</p>\n";
                        exit;
                    }
                    if ($userInfo) {//Если Id пользователя в социальной сети ВКонтакте уже закреплён за пользователем bitrix
                        if (!$userInfo["UF_FIRST_NAME"]) {
                            $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                        }
                        if (!$userInfo["UF_LAST_NAME"]) {
                            $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                        }

                    } else {// Если информации о Id в социальной сети ещё нет в базе
                        if ($authnow) {// Если пользователь авторизован в bitrix
                            if (($userInfoAuth = getAuthUserInfo()) and isset($userInfoAuth["ID"]) and ($userInfoAuth > 0)) {// Если удалось получить информацию о авторизованном пользователе bitrix
                                if (!$userInfoAuth["UF_FIRST_NAME"]) {
                                    $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                                }
                                if (!$userInfoAuth["UF_LAST_NAME"]) {
                                    $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                                }
                            } else {//Такой ветки быть не может(но на всякий случай напишу), поскольку если пользователь авторизован в bitrix информация о нём по его текущему Id в bitrix должна подгрузиться
                                $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                            }
                        } else {// Пользователь сейчас не авторизован в bitrix
                            $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                        }
                    }

                    // Если есть необходимость в получении пользовательской информации из соц сети
                    $updatesUserFieldsAr = array();
                    if (count($updateUserInfoAr) and $access_token) {// Проверяем необходимость запроса информации у социальной сети и наличие $access_token(без него запрос нельзя выполнить)
                        $cp->newip("api.vk.com");
                        $cp->newuri("/method/users.get?user_ids=$uid&access_token=$access_token&v=5.92");
                        $text = $cp->go();
                        $text = $text->val();

                        if ($result2 = json_decode($text)) {// Если возвращён ответ JSON ответ от ВКонтакте получен
                            foreach ($updateUserInfoAr as $k => $v) {
                                if (isset($result2->response[0]->$k) and $result2->response[0]->$k) {
                                    $updatesUserFieldsAr[$v] = $result2->response[0]->$k;
                                }
                            }
                            // выполняем запрос на обновление информации о пользователе
                            $USER->Update(CUser::GetID(),$updatesUserFieldsAr);//!!!!!!!!!!!!!!!!!!!!!В этой строчке возможно вместо CUser::GetID требуется использовать переменную
                        }
                    }

                    // Внесение изменений в базу данных пользователя при наличии необходимости
                    if ($authnow) {// Если пользователь авторизован
                        if (isset($userInfo["ID"]) and ($userInfo["ID"] == $authnow)) {// Если у текущего пользователя уже был привязан Id ВКонтакте
                            if (count($updatesUserFieldsAr)) {// Если есть поля для обновления
                                $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                            }
                        } elseif(!isset($userInfo["ID"])) {// Если записи с таким Id ВКонтакте в базе нет
                            $updatesUserFieldsAr["UF_VKID"] = $uid;// Добавляем необходимость обновления Id ВКонтакте в базе данных пользователя
                            $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                        }
                        header("location: /account/",true,301);
                        exit;
                    } else {// Если пользователь не авторизован
                        if (isset($userInfo["ID"])) {// Информация о пользователе есть в базе данных
                            if (count($updatesUserFieldsAr)) {// Проверяем необходимость обновления информации
                                $USER->Update($userInfo["ID"],$updatesUserFieldsAr);// выполняем обновление полей
                            }
                            $USER->Authorize($userInfo["ID"]);// выполняем авторизацию
                        } else {// Информации о пользователе нет в базе данных

                            // Подготавливаем массив для регистрации нового пользователя
                            $arFields = Array(
                                "EMAIL"=>$emailToReg,
                                "LOGIN"=>$loginToReg,
                                "ACTIVE"=>"Y",//Делаем пользователя активным
                                "PASSWORD"=>$passwordToReg,
                                "CONFIRM_PASSWORD"=>$passwordToReg,
                                "UF_VKID"=>$uid
                            );
                            if (count($updatesUserFieldsAr)) {// Если есть поля (имя и фамилия) добавляем их к массиву $arFields
                                $arFields = $arFields + $updatesUserFieldsAr;
                            }
                            $newUserId = $USER->Add($arFields);// Создаём нового пользователя
                            if ($newUserId) {// Если пользователь успешно создан
                                $USER->Authorize($newUserId);// Авторизуем пользователя
                                header("location: /account/",true,301);
                                exit;
                            }
                        }
                    }
                }
            }
        }
    } elseif ($action == "ok") {
        // Если авторизация производится через социальную сеть Одноклассники
        $uid = false;// В переменной при необходимости сохраняется Id пользователя Одноклассники
        if (isset($_REQUEST["code"]) and ($code = $_REQUEST["code"])) {// Если авторизация на сервере Одоклассники прошла успешно, пользователь вернётся для подтверждение авторизации на страничку сайта содержащую параметр code/ Проверяем действительно ли code передан сервером Одноклассники и в случаи корректности получаем access_token, для чего отправляем запрос серверу Одноклассники

            // Используем библиотеку curlPro для отправки запросов на сервер Одноклассники
            require_once('curlPro.php');// Подключаем библиотеку curlPro
            $cp = new curlPro;// Экземпляр объекта curlPro
            $cp->newip("api.ok.ru");// IP адрес для установки соединения
            $cp->newuri("/oauth/token.do");// URi запроса
            $cp->https(true);// Требуется именно HTTPS соединение

            // Формируем массив данных передаваемых серверу Одноклассники методом POST и передаём их объекту curlPro
            $postAr = array(
                "code"=>$_REQUEST["code"],
                "redirect_uri"=>$okAuthUrl,
                "grant_type"=>"authorization_code",
                "client_id"=>$okIdApp,
                "client_secret"=>$okS
            );
            $cp->postactivedata = urldecode(http_build_query($postAr));

            $text = $cp->go();// Выполняем запрос к серверу Одноклассники
            $text = $text->val();// Избавляемся от парсинг объекта, получаем текстовое значение результата

            if (($result = json_decode($text)) and isset($result->access_token)) {// Сервер Одноклассники должен вернуть объект JSON и передать в нём access_token
                $access_token = $result->access_token;
                $sign = md5("application_key=$okP" . "format=jsonmethod=users.getCurrentUser" . md5($access_token . $okS));// Формируем подпись для сервера Одноклассники

                // Подготавливаем массив для запроса информации у сервера Одноклассники
                $postAr = array(
                    'method'=>'users.getCurrentUser',
                    'access_token'=>$access_token,
                    'application_key'=>$okP,
                    'format'=>'json',
                    'sig'=>$sign
                );

                // Перенастраиваем curlPro, устанавливаем новый uri и передаём POST данные, затем выполняем запрос к серверу Одноклассников
                $cp->newuri("/fb.do");
                $cp->postactivedata = urldecode(http_build_query($postAr));// Устанавливаем данные для передачи серверу Одноклассники методом POST
                $text = $cp->go();
                $text = $text->val();
                $result = json_decode($text);
                $updateUserInfoAr = array();// Если не требуется обновить информацию о пользователе в соответствии с информацией в социальной сети массив остаётся пустым, если требуется - данные которые требуется обновить передаются в массив, а именно ключом является поле в ответе Одноклассники, а значением соответствующее поле в инфоблоке bitrix
                // Проверяем наличие Ид пользователя Одноклассники в ответе
                if (isset($result->uid) and $result->uid) {
                    $uid = $result->uid;

                    // Получаем информацию есть ли пользователь с таким Id Одноклассники уже в базе данных
                    $userInfo = getUserInfoBySocId("OK",$uid);

                    // Вычисляем необходимость в изменении информации в пользовательских полях базы данных(а именно имени и фамилии), кроме случая когда пользователь авторизован, но информация о опрделённом Id Одноклассники относится к другому пользователю(в этом случаи просто выдаём ошибку)
                    if (isset($userInfo["ID"]) and $authnow and ($userInfo["ID"] != $authnow)) {// Если условие выполняется выводим информацию и завершаем выполнение скрипта
                        echo "<p>Такой Id пользователя в социальной сети Одноклассники уже закреплён за другим пользователем. Для того чтобы продолжить работу с старым аккаунтом, нажмите ссылку <a href=\"/logout/\">выход</a> в меню сайта, а затем выполните авторизацию через Одноклассники.</p>\n";
                        echo "<p><a href=\"/\">Перейти на главную страницу сайта</a>. <a href=\"/account/\">Перейти в аккаунт</a>.</p>\n";
                        exit;
                    }
                    if ($userInfo) {//Если Id пользователя в социальной сети ВКонтакте уже закреплён за пользователем bitrix
                        if (!$userInfo["UF_FIRST_NAME"]) {
                            $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                        }
                        if (!$userInfo["UF_LAST_NAME"]) {
                            $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                        }

                    } else {// Если информации о Id в социальной сети ещё нет в базе
                        if ($authnow) {// Если пользователь авторизован в bitrix
                            if (($userInfoAuth = getAuthUserInfo()) and isset($userInfoAuth["ID"]) and ($userInfoAuth > 0)) {// Если удалось получить информацию о авторизованном пользователе bitrix
                                if (!$userInfoAuth["UF_FIRST_NAME"]) {
                                    $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                                }
                                if (!$userInfoAuth["UF_LAST_NAME"]) {
                                    $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                                }
                            } else {//Такой ветки быть не может(но на всякий случай напишу), поскольку если пользователь авторизован в bitrix информация о нём по его текущему Id в bitrix должна подгрузиться
                                $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                            }
                        } else {// Пользователь сейчас не авторизован в bitrix
                            $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                        }
                    }

                    // Если есть необходимость в получении пользовательской информации из соц сети
                    $updatesUserFieldsAr = array();
                    if (count($updateUserInfoAr)) {// Проверяем необходимость сохранения информации о пользователе из социальной сети
                        foreach ($updateUserInfoAr as $k => $v) {
                            if (isset($result->$k) and $result->$k) {
                                $updatesUserFieldsAr[$v] = $result->$k;
                            }
                        }
                        // выполняем запрос на обновление информации о пользователе
                        $USER->Update(CUser::GetID(),$updatesUserFieldsAr);//!!!!!!!!!!!!!!!!!!!!!В этой строчке возможно вместо CUser::GetID требуется использовать переменную
                    }

                    // Внесение изменений в базу данных пользователя при наличии необходимости
                    if ($authnow) {// Если пользователь авторизован
                        if (isset($userInfo["ID"]) and ($userInfo["ID"] == $authnow)) {// Если у текущего пользователя уже был привязан Id Одноклассники
                            if (count($updatesUserFieldsAr)) {// Если есть поля для обновления
                                $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                            }
                        } elseif(!isset($userInfo["ID"])) {// Если записи с таким Id Одноклассники в базе нет
                            $updatesUserFieldsAr["UF_OKID"] = $uid;// Добавляем необходимость обновления Id ВКонтакте в базе данных пользователя
                            $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                        }
                        header("location: /account/",true,301);
                        exit;
                    } else {// Если пользователь не авторизован
                        if (isset($userInfo["ID"])) {// Информация о пользователе есть в базе данных
                            if (count($updatesUserFieldsAr)) {// Проверяем необходимость обновления информации
                                $USER->Update($userInfo["ID"],$updatesUserFieldsAr);// выполняем обновление полей
                            }
                            $USER->Authorize($userInfo["ID"]);// выполняем авторизацию
                        } else {// Информации о пользователе нет в базе данных

                            // Подготавливаем массив для регистрации нового пользователя
                            $arFields = Array(
                                "EMAIL"=>$emailToReg,
                                "LOGIN"=>$loginToReg,
                                "ACTIVE"=>"Y",//Делаем пользователя активным
                                "PASSWORD"=>$passwordToReg,
                                "CONFIRM_PASSWORD"=>$passwordToReg,
                                "UF_OKID"=>$uid
                            );
                            if (count($updatesUserFieldsAr)) {// Если есть поля (имя и фамилия) добавляем их к массиву $arFields
                                $arFields = $arFields + $updatesUserFieldsAr;
                            }
                            $newUserId = $USER->Add($arFields);// Создаём нового пользователя
                            if ($newUserId) {// Если пользователь успешно создан
                                $USER->Authorize($newUserId);// Авторизуем пользователя
                                header("location: /account/",true,301);
                                exit;
                            }
                        }
                    }
                }
            }
        }
    } elseif ($action == "fb") {
        // Если авторизация производится через социальную сеть FaceBook
        $uid = false;// В переменной при необходимости сохраняется Id пользователя FaceBook
        if (isset($_REQUEST["code"]) and ($code = $_REQUEST["code"])) {// Если авторизация на сервере FaceBook прошла успешно, пользователь вернётся для подтверждение авторизации на страничку сайта содержащую параметр code/ Проверяем действительно ли code передан сервером FaceBook и в случаи корректности получаем access_token, для чего отправляем запрос серверу FaceBook

            // Используем библиотеку curlPro для отправки запросов на сервер FaceBook
            require_once('curlPro.php');// Подключаем библиотеку curlPro
            $cp = new curlPro;// Экземпляр объекта curlPro
            $cp->newip("graph.facebook.com");// IP адрес для установки соединения

            // Формируем массив данных передаваемых серверу FaceBook методом GET
            $query = array(
                "client_id"=>$fbIdApp,
                "redirect_uri"=>$fbAuthUrl,
                "client_secret"=>$fbS,
                "code"=>$code
            );
            $query = $cp->getStringByArray($query,"");

            $cp->newuri("/v3.2/oauth/access_token?$query");// Устанавливаем URi запроса к серверу FaceBook и передаём сформированные данные для передачи методом GET
            $cp->https(true);// Требуется именно HTTPS соединение

            $text = $cp->go();// Выполняем запрос к серверу FaceBook
            $text = $text->val();// Избавляемся от парсинг объекта, получаем текстовое значение результата

            if (($result = json_decode($text)) and isset($result->access_token)) {// Сервер Одноклассники должен вернуть объект JSON и передать в нём access_token
                $access_token = $result->access_token;

                // Подготавливаем массив для запроса информации у сервера FaceBook
                $access_token = $result->access_token;
                $query = array(
                    "access_token"=>$access_token,
                    "fields"=>"id,first_name,last_name,picture.width(120).height(120)"
                );
                $query = $cp->getStringByArray($query,"");

                // Запрашиваем информацию у сервера FaceBook используя $access_token
                $text = file_get_contents("https://graph.facebook.com/me?$query");
                $result = json_decode($text);

                $updateUserInfoAr = array();// Если не требуется обновить информацию о пользователе в соответствии с информацией в социальной сети массив остаётся пустым, если требуется - данные которые требуется обновить передаются в массив, а именно ключом является поле в ответе Одноклассники, а значением соответствующее поле в инфоблоке bitrix
                // Проверяем наличие Ид пользователя FaceBook в ответе
                if (isset($result->id) and $result->id) {
                    $uid = $result->id;

                    // Получаем информацию есть ли пользователь с таким Id FaceBook уже в базе данных
                    $userInfo = getUserInfoBySocId("FB",$uid);

                    // Вычисляем необходимость в изменении информации в пользовательских полях базы данных(а именно имени и фамилии), кроме случая когда пользователь авторизован, но информация о опрделённом Id FaceBook относится к другому пользователю(в этом случаи просто выдаём ошибку)
                    if (isset($userInfo["ID"]) and $authnow and ($userInfo["ID"] != $authnow)) {// Если условие выполняется выводим информацию и завершаем выполнение скрипта
                        echo "<p>Такой Id пользователя в социальной сети FaceBook уже закреплён за другим пользователем. Для того чтобы продолжить работу с старым аккаунтом, нажмите ссылку <a href=\"/logout/\">выход</a> в меню сайта, а затем выполните авторизацию через FaceBook.</p>\n";
                        echo "<p><a href=\"/\">Перейти на главную страницу сайта</a>. <a href=\"/account/\">Перейти в аккаунт</a>.</p>\n";
                        exit;
                    }
                    if ($userInfo) {//Если Id пользователя в социальной сети FaceBook уже закреплён за пользователем bitrix
                        if (!$userInfo["UF_FIRST_NAME"]) {
                            $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                        }
                        if (!$userInfo["UF_LAST_NAME"]) {
                            $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                        }

                    } else {// Если информации о Id в социальной сети ещё нет в базе
                        if ($authnow) {// Если пользователь авторизован в bitrix
                            if (($userInfoAuth = getAuthUserInfo()) and isset($userInfoAuth["ID"]) and ($userInfoAuth > 0)) {// Если удалось получить информацию о авторизованном пользователе bitrix
                                if (!$userInfoAuth["UF_FIRST_NAME"]) {
                                    $updateUserInfoAr["first_name"] = "UF_FIRST_NAME";
                                }
                                if (!$userInfoAuth["UF_LAST_NAME"]) {
                                    $updateUserInfoAr["last_name"] = "UF_LAST_NAME";
                                }
                            } else {//Такой ветки быть не может(но на всякий случай напишу), поскольку если пользователь авторизован в bitrix информация о нём по его текущему Id в bitrix должна подгрузиться
                                $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                            }
                        } else {// Пользователь сейчас не авторизован в bitrix
                            $updateUserInfoAr = array("first_name"=>"UF_FIRST_NAME","last_name"=>"UF_LAST_NAME");
                        }
                    }

                    // Если есть необходимость в получении пользовательской информации из соц сети
                    $updatesUserFieldsAr = array();
                    if (count($updateUserInfoAr)) {// Проверяем необходимость сохранения информации о пользователе из социальной сети
                        foreach ($updateUserInfoAr as $k => $v) {
                            if (isset($result->$k) and $result->$k) {
                                $updatesUserFieldsAr[$v] = $result->$k;
                            }
                        }
                        // выполняем запрос на обновление информации о пользователе
                        $USER->Update(CUser::GetID(),$updatesUserFieldsAr);//!!!!!!!!!!!!!!!!!!!!!В этой строчке возможно вместо CUser::GetID требуется использовать переменную
                    }

                    // Внесение изменений в базу данных пользователя при наличии необходимости
                    if ($authnow) {// Если пользователь авторизован
                        if (isset($userInfo["ID"]) and ($userInfo["ID"] == $authnow)) {// Если у текущего пользователя уже был привязан Id FaceBook
                            if (count($updatesUserFieldsAr)) {// Если есть поля для обновления
                                $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                            }
                        } elseif(!isset($userInfo["ID"])) {// Если записи с таким Id FaceBook в базе нет
                            $updatesUserFieldsAr["UF_FBID"] = $uid;// Добавляем необходимость обновления Id FaceBook в базе данных пользователя
                            $USER->Update($authnow,$updatesUserFieldsAr);// выполняем обновление полей
                        }
                        header("location: /account/",true,301);
                        exit;
                    } else {// Если пользователь не авторизован
                        if (isset($userInfo["ID"])) {// Информация о пользователе есть в базе данных
                            if (count($updatesUserFieldsAr)) {// Проверяем необходимость обновления информации
                                $USER->Update($userInfo["ID"],$updatesUserFieldsAr);// выполняем обновление полей
                            }
                            $USER->Authorize($userInfo["ID"]);// выполняем авторизацию
                        } else {// Информации о пользователе нет в базе данных

                            // Подготавливаем массив для регистрации нового пользователя
                            $arFields = Array(
                                "EMAIL"=>$emailToReg,
                                "LOGIN"=>$loginToReg,
                                "ACTIVE"=>"Y",//Делаем пользователя активным
                                "PASSWORD"=>$passwordToReg,
                                "CONFIRM_PASSWORD"=>$passwordToReg,
                                "UF_FBID"=>$uid
                            );
                            if (count($updatesUserFieldsAr)) {// Если есть поля (имя и фамилия) добавляем их к массиву $arFields
                                $arFields = $arFields + $updatesUserFieldsAr;
                            }
                            $newUserId = $USER->Add($arFields);// Создаём нового пользователя
                            if ($newUserId) {// Если пользователь успешно создан
                                $USER->Authorize($newUserId);// Авторизуем пользователя
                                header("location: /account/",true,301);
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }
}

header("location: /",true,301);

