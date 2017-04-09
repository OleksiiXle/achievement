<?php
ini_set('display_errors', 'on');
include "init.php";
require(__DIR__ . '/config.php'); //------------- чтение параметров подключения к БД из файла
include ("./css/xleCSS.php"); //----------------- с используемой конфигурацией .htaccess не получается подключить js и css как положено
include ("./js/xleJS.php");
$params = json_decode(AchievementDB1::getSEFdata($config), true);
?>

<body>

    <div class="admin" align="center">
            <select id="pagination" size="1" onchange="changePagination(this.value);">
                <option value="10" >Пагинация 10</option>
                <option value="20" selected>Пагинация 20</option>
                <option value="40">Пагинация 40</option>
                <option value="50">Пагинация 50</option>
                <option value="100">Пагинация 100</option>
            </select><br>

            <input hidden id="id_department" size 40 value="<?=$params['id_department'];?>"><br>
            <select id="departmentsList" size="5" onchange="changeDepartmentsList(this.value);" ">
            <?php foreach($params['departments'] as $key => $value) : ?>
                <option value="<?= $key;?>"><?= $value;?></option>
            <?php endforeach;?>
            </select><br><br>

            <form name="formAdminName" id="formAdminId" action="<?='http://'. $_SERVER['HTTP_HOST'] . '/controller.php'?>">
              <button type="submit" name="mode" value="insert"
                        onclick="document.getElementById('waitBox').style.display ='block';">Добавить тестовый набор в БД</button><br><br>
              <button type="submit" name="mode" value="delete"
                        onclick="document.getElementById('waitBox').style.display ='block';">Очистить БД</button><br>
            </form>
            <!-- параметры от которых зависит содержимое страницы -->
            <input hidden id="hostName" size 40 value="<?= 'http://'. $_SERVER['HTTP_HOST'] . '/';?>"><br>
            <input hidden id="refreshTable" size 40 value="employees/5/5" ><br>
            <input hidden id="dbSize" size 40 value="<?=$params['dbSize'];?>"><br>
            <input hidden id="action" size 40 value="<?=$params['action'];?>"><br>
            <input hidden id="pagesCount" size 40 value="<?=$params['pagesCount'];?>"><br>
            <input hidden id="limit" size 40 value="<?=$params['limit'];?>"><br>
            <input hidden id="currentPage" size 40 value="<?=$params['currentPage'];?>"><br>
            <input hidden id="prevPage" size 40 value="1"><br>
            <input hidden id="offset" size 40 value="<?=$params['offset'];?>"><br>
            <input hidden id="paramError" size 50 value="<?=$params['error'];?>"><br>
            <textarea hidden id="dataDB" rows="20" cols="70"><?=json_encode($params['data']);?></textarea><br>
    </div>
    <!-- блок для отображения таблицы -->
    <div class="grid" id="gridId">
        <input type = 'radio' name="mode" id = 'employeesMode'  value="1" onchange="changeAction(this.value);">Сотрудники
        <input type = 'radio' name="mode" id = 'departmentsMode' value="2" onchange="changeAction(this.value);">Отделы
    </div>
    <!-- блок для отображения кнопок пагинации -->
    <div class="links" id="linksId">
    </div>
    <!-- результаты выполнения операций -->
    <div class="messageBox">
        <input id="messageBoxIdConf" size="80" disabled
               value="<?= (is_array($config)) ? 'файл конфигурации прочитан' : 'неверный формат файла конфигурации';?>"><br>
        <textarea id="messageBoxId" rows="2" cols="80" disabled></textarea><br>
    </div>
    <!-- окошко, которое выводится при запуске обновления БД (очистка или добавление тестового набора-->
    <div class="waitBox" id="waitBox" >
        <b>Ожидайте, данные обновляются...</b>
    </div>
</body>
<script>
    window.onload = function () {
        var  hasError = document.getElementById('paramError').value;
        if (hasError.length > 0) {
            document.getElementById('messageBoxId').value = hasError;
        } else {
            document.getElementById('messageBoxId').value = 'O.K.';
            refreshGrid();
        }
    };
</script>

