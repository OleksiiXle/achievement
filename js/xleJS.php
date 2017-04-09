<script>
    //--------------------------------------------------------------------------
function refreshLinks(newLink) {
    var s;
    document.getElementById('currentPage').value = newLink;
    refreshData();
}
//--------------------------------------------------------------------------
function changeDepartmentsList(newDepartmentId) {
    document.getElementById('id_department').value = newDepartmentId;
    document.getElementById('action').value = 'departments';
    document.getElementById('currentPage').value = 1;
    refreshData();
}
//--------------------------------------------------------------------------
function changePagination(newPagination) {
    document.getElementById('limit').value = newPagination;
    document.getElementById('currentPage').value = 1;
    refreshData();
}
//--------------------------------------------------------------------------
function changeAction(action) {
    document.getElementById('currentPage').value = 1;
    (action == 1) ? document.getElementById('action').value = 'employees':
        document.getElementById('action').value = 'departments';
    refreshData();
}
//--------------------------------------------------------------------------
function refreshData() {
    var s, urlName;
    if (document.getElementById('action').value == 'employees') {
        s = 'mode=query&ajaxQuery=employees/' + document.getElementById('currentPage').value  + '/' + document.getElementById('limit').value;
    } else {
        s = 'mode=query&ajaxQuery=employees/' + document.getElementById('id_department').value +
            '/' + document.getElementById('currentPage').value + '/' + document.getElementById('limit').value;
    }
    urlName = document.getElementById('hostName').value + 'controller.php';
    ajaxXle(urlName, s, 'messageBoxId');
}
//*************************************************************************************
//-------------------------------------------------------
// Выводит таблицу на экран
// - parentClass - класс-предок таблицы
// - tableId - идентификатор таблицы
// - dataArray - массив данных для вывода таблицы

function showTable(parentClass, tableId, dataArray) {
    var parent = document.querySelector('.' + parentClass),
        tr = "",
        th = "",
        td = "",
        text = "";
    var oldTable = document.getElementById(tableId);
    if (oldTable){
        oldTable.remove();
    }
    var table = document.createElement("table");
    table.id = tableId;
    table.setAttribute("border", "2px");
    tr = document.createElement("tr");
    for (var i = 0; i < dataArray[0].length; ++i) {
        th = document.createElement("th");
        text = document.createTextNode(dataArray[0][i]);
        th.appendChild(text);
        tr.appendChild(th);
    }
    table.appendChild(tr);
    for (i = 1; i < dataArray.length; ++i) {
        tr = document.createElement("tr");
        for (var j = 0; j < dataArray[i].length; j++) {
            td = document.createElement("td");
            text = document.createTextNode(dataArray[i][j]);
            td.appendChild(text);
            tr.appendChild(td);
        }
        table.appendChild(tr);
    }
    parent.appendChild(table);
    return true;
}
//----------------------------------------------------------
// панель пагинации
//--  linksCnt -  Количество возможных страниц всего
// -- currentLink - Текущая страница
// -- pagination- страниц на экране
function showLinks(parentClass, linksCnt, currentLink, pagination, maxLinksCount) {
    if (linksCnt ) {
        var btn, i, oldLink,
            startLinkNum, endLinkNum,
            maxLinkDisplay = 15, middleLiknDisplay = 8,
            parentClass2 = 'links';  //----костыль, но нет времени
        //--- определение номера первой кнопки
        startLinkNum =(parseInt(currentLink) );
        if (startLinkNum < middleLiknDisplay + 1) {
            startLinkNum = 1;
        } else {
            startLinkNum = startLinkNum - middleLiknDisplay + 1;
        }
        //--- определение номера последней кнопки
        if ( (
                parseInt(linksCnt) < maxLinkDisplay
            )
            ||
            (
                (parseInt(linksCnt) > maxLinkDisplay)
                &&
                (parseInt(linksCnt) < (startLinkNum + maxLinkDisplay))
            ) )
        {
            endLinkNum = parseInt(linksCnt) ;
        } else {
            endLinkNum = startLinkNum + maxLinkDisplay -1;
        }
        // -----------  удаление старых кнопок пагинации ОЧЕНЬ ДАЛЕКОЕ ОТ СОВЕРШЕНСТВА НО НЕТ ВРЕМЕРИ РАЗБИРАТЬСЯ
        for (i = 1; i <= maxLinksCount; i++) {
            oldLink =  document.getElementById('linkBtnId' + i);
            if (oldLink) oldLink.remove();
        }
        oldLink =  document.getElementById('linkBtnIdStart');
        if (oldLink) oldLink.remove();
        oldLink =  document.getElementById('linkBtnIdPrev');
        if (oldLink) oldLink.remove();
        oldLink =  document.getElementById('linkBtnIdNext');
        if (oldLink) oldLink.remove();
        oldLink =  document.getElementById('linkBtnIdEnd');
        if (oldLink) oldLink.remove();
        //--- кропка "в начало"
        var parent = document.querySelector('.' + parentClass2);
        btn = document.createElement('input');
        btn.id = 'linkBtnIdStart';
        btn.name = 'linkBtnNameStart';
        btn.className = 'gridLink';
        btn.type = 'button';
        btn.value = '<<';
        btn.onclick = function () {
            refreshLinks(1);
        };
        parent.appendChild(btn);
        //--- кропка "на запись назад"
        if (parseInt(currentLink) > 1){
            btn = document.createElement('input');
            btn.id = 'linkBtnIdPrev';
            btn.name = 'linkBtnNamePrev';
            btn.className = 'gridLink';
            btn.type = 'button';
            btn.value = '<';
            btn.onclick = function () {
                refreshLinks(parseInt(currentLink) - 1);
            };
            parent.appendChild(btn);
        }
        //--- кропки с номерами страниц
        for (i = startLinkNum; i <= endLinkNum; i++) {
            btn = document.createElement('input');
            btn.id = 'linkBtnId' + i;
            btn.name = 'linkBtnName' + i;
            btn.className = 'gridLink';
            btn.type = 'button';
            btn.value = i;
            (i == currentLink) ? btn.style.cssText = 'color: red; margin-top: 10px;' :
                btn.style.cssText = 'color: blue; margin-top: 10px;';
            //TODO костыль
            btn.onclick = function () {
                refreshLinks(this.value);
            };
            parent.appendChild(btn);
        }
        //--- кропка "следующая запись"
        if (currentLink != linksCnt){
            btn = document.createElement('input');
            btn.id = 'linkBtnIdNext';
            btn.name = 'linkBtnNameNext';
            btn.className = 'gridLink';
            btn.type = 'button';
            btn.value = '>';
            btn.onclick = function () {
                refreshLinks(parseInt(currentLink) + 1);
            };
            parent.appendChild(btn);
        }
        //--- кропка "в конец"
        btn = document.createElement('input');
        btn.id = 'linkBtnIdEnd';
        btn.name = 'linkBtnNameEnd';
        btn.className = 'gridLink';
        btn.type = 'button';
        btn.value = '>>';
        btn.onclick = function () {
            refreshLinks(linksCnt);
        };
        parent.appendChild(btn);
        return true;
    }
}
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
function ajaxXle(handlerPath, parameters, messageItemId  ) {

    var req, sinc = false;
    var result;
    // Создание объекта XMLHttpRequest
    if (window.XMLHttpRequest) req = new XMLHttpRequest();
    else if (window.ActiveXObject) {
        try {
            req = new ActiveXObject('Msxml2.XMLHTTP');
        } catch (e){}
        try {
            req = new ActiveXObject('Microsoft.XMLHTTP');
        } catch (e){}
    }
    if (req) {
        //*******************************************************
        if (!sinc) {
            req.onloadstart =  function() {
                document.getElementById(messageItemId).value = "Запрос пошел, ждите....";
                return false
            };
            req.onload = req.onerror = function() {
                if (this.status == 200) {
                    // Для статуса "OK"
                    result = JSON.parse(req.responseText);
                    if (result['error'].length == 0){  //----- запрос был обработан на сервере без ошибок
                        document.getElementById(messageItemId).value = "Ответ получен без ошибок";
                        var act1, act2;
                        act1 = refreshAfterAjax(result);
                        act2 = refreshGrid();
                    } else {
                        document.getElementById(messageItemId).value = result['error'];
                    }

                } else {
                    document.getElementById(messageItemId).value = "Не удалось получить данные:\n" + req.statusText;
                }
                return false;
            };
        }
        //******************************************************* onloadstart
        // Отправляем запрос методом POST с обязательным указанием файла обработчика (true - асинхронный режим включен)
        req.open("POST", handlerPath, true);
        // При использовании объекта XMLHttpRequest с методом POST требуется дополнительно отправлять header
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        // Передаем необходимые параметры
        req.send(parameters);
        //***********************************************************************
        //*** синхронный режим - не используется, но может пригодиться
        if (cinc)  {
            // Для статуса "OK"
            if (req.status == 200) {
                // Получаем ответ функции в виде строки
                result = JSON.parse(req.responseText);
                (result['error'].length > 0) ? document.getElementById(messageItemId).value = result['error']:
                    document.getElementById(messageItemId).value = 'О.К. - ответ получен';
            } else {
                document.getElementById(messageItemId).value = "Не удалось получить данные:\n" + req.statusText;
            }
        }
    } else {
        document.getElementById(messageItemId).value = "Браузер не поддерживает AJAX";
    }
    return result;
}
//-------------------------------------------------------------------------------------
function refreshGrid() {
    var linksCnt = document.getElementById('pagesCount').value,
        currentLink = document.getElementById('currentPage').value,
        pagination = document.getElementById('limit').value,
        maxLinksCount = document.getElementById('dbSize').value,
        tabArray = JSON.parse(document.getElementById('dataDB').value);
    showTable('grid', 'myTab', tabArray);
    showLinks('grid', linksCnt, currentLink, pagination, maxLinksCount);

    return ('ok');

}
//-------------------------------------------------------------------------------------
function refreshAfterAjax(params) {
    if (params['action']) document.getElementById('action').value = params['action'];
    if (params['offset'])  document.getElementById('offset').value = params['offset'];
    if (params['limit'])  document.getElementById('limit').value = params['limit'];
    if (params['pagesCount']) document.getElementById('pagesCount').value = params['pagesCount'];
    if (params['currentPage'])  document.getElementById('currentPage').value = params['currentPage'];
    if (typeof (params['data']) == 'object') {
        document.getElementById('dataDB').value = JSON.stringify(params['data']);
    }
    if (params['dbSize']) document.getElementById('dbSize').value = params['dbSize'];
    if (params['paramError']) document.getElementById('paramError').value = params['paramError'];
    return 'ok';
}
//-------------------------------------------------------------------------------------
//*********************************************************************************
//----отладочные функции
function objDump(object) {
    var out = "";
    if(object && typeof(object) == "object"){
        for (var i in object) {
            out += i + ": " + object[i] + "\n";
        }
    } else {
        out = object;
    }
    alert(out);
}
//------------------------------
function objDumpShort(object) {
    var out = "";
    if(object && typeof(object) == "object"){
        for (var i in object) {
            out += i + ": " + object[i] + "\n";
        }
    } else {
        out = object;
    }
    return out;
}

</script>