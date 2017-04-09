Тестовое задание
Хлестков Алексей


Ссылка на работающий пример http://cn78291.tmweb.ru/employees
Задание выполнено с использованием MySQL.
Дамп пустой базы — script.SQL
Дамп базы с тестовыми данными  - script_data_test.SQL
Конфигурация подключения к БД — config.PHP
В программе реализована возможность удалять все данные из БД и добавлять тестовый набор.
Действия пользователя производятся без перезагрузки страницы
Пагинация по умолчанию — 20.
ЧПУ работает при вводе данных в адресную строку
 примеры:
	- eployees/ - вывод всех сотрудников (пагинация по умолчанию 20)
	- eployees/3 — вывод 3 страницы (пагинация по умолчанию 20)
	- eployees/3/21 — вывод 3 страницы с пагинацыей 21
- eployees/2/3/42 — вывод 2 страницы сотрудников 3 отдела с пагинацией 42

Из адресной строки можно задавать любую пагинацию, из программы — заданый в задании диапазон.

В случае неверного указания данных в адресной строке — выводится сообщение о 404 ошибке


Схема базы данных:

Структура таблицы `employees`
  `id_employee` int(11) NOT NULL COMMENT 'Идентификатор',
  `id_department` int(11) DEFAULT NULL COMMENT 'Идентификатор отдела',
  `f1` varchar(30) NOT NULL COMMENT 'Фамилия',
  `f2` varchar(30) NOT NULL COMMENT 'Имя',
  `f3` varchar(30) NOT NULL COMMENT 'Отчество',
  `birthday` date NOT NULL COMMENT 'Дата рождения',
  `id_position` int(11) NOT NULL COMMENT 'Идентификатор должности',
  `hourly_payment` tinyint(1) DEFAULT NULL COMMENT 'почасовая оплата',
  `salary` float NOT NULL COMMENT 'оклад или ставка'
 Структура таблицы `departnemts`
  `id_department` int(11) NOT NULL,
  `name` varchar(100) NOT NULL

Структура таблицы `employees`
  `id_employee` int(11) NOT NULL COMMENT 'Идентификатор',
  `id_department` int(11) DEFAULT NULL COMMENT 'Идентификатор отдела',
  `f1` varchar(30) NOT NULL COMMENT 'Фамилия',
  `f2` varchar(30) NOT NULL COMMENT 'Имя',
  `f3` varchar(30) NOT NULL COMMENT 'Отчество',
  `birthday` date NOT NULL COMMENT 'Дата рождения',
  `id_position` int(11) NOT NULL COMMENT 'Идентификатор должности',
  `hourly_payment` tinyint(1) DEFAULT NULL COMMENT 'почасовая оплата',
  `salary` float NOT NULL COMMENT 'оклад или ставка'

 Структура таблицы `positions`
  `id_position` int(11) NOT NULL,
  `name` varchar(50) NOT NULL

Структура таблицы `salary`
  `id_salary` int(11) NOT NULL,
  `id_employee` int(11) DEFAULT NULL COMMENT 'идентификатор сотрудника',
  `period_year` varchar(4) DEFAULT '2017' COMMENT 'год',
  `period_month` varchar(2) DEFAULT '03' COMMENT 'месяц',
  `working_hours` int(11) DEFAULT NULL COMMENT 'Рабочие часы',
  `payment` float DEFAULT NULL COMMENT 'зарплата'

