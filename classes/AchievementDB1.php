<?php

/**
 * Class AchievementDB1
 */
class AchievementDB1 {
    private $host;
    private $user;
    private $database;
    private $password;
    private $db;
    //--------------------------------------------------------------
    /**
     * AchievementDB1 constructor.
     * @param $config
     */
    public function __construct($config){
        $this->host =       $config['host'];
        $this->database =   $config['database'];
        $this->user =       $config['user'];
        $this->password =   $config['password'];
        //---------------------------------------------
        $dsn = "mysql:host=$this->host;dbname=$this->database;charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->db = new PDO($dsn, $this->user, $this->password, $opt);
        return $this->db;
    }

    /**
     *
     */
    public function disconnect(){
        $this->db = null;
    }

    /**
     *
     */
    public function __destruct(){
        $this->disconnect();
    }

    //--------------------------------------------------------------
    /**
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     * @return PDO
     */
    public function __constructCOPY($host = 'localhost',
                                    $database = 'achievement',
                                    $user = 'oleksii',
                                    $password = "140269"){
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        //---------------------------------------------
        $dsn = "mysql:host=$this->host;dbname=$this->database;charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->db = new PDO($dsn, $this->user, $this->password, $opt);
        return $this->db;

    }
    //---------------------------------------------------------------
    /**
     * @param $limit - пагинация
     * @param $offset - смещение
     * @param string $year - год
     * @param string $month - месяц
     * @return mixed
     *
     */
    public function getPaymentsByPeriodEmployees($limit, $offset, $year = '2017', $month = '03'){
        $strSql = '
        SELECT  employees.id_employee AS ID, employees.f1 AS Фамилия, employees.f2 AS Имя, employees.f3 AS Отчество, employees.birthday AS "Дата рождения", 
        departnemts.name AS Отдел,
        positions.name AS Должность, (IF (employees.hourly_payment, "почасово", "ставка")) AS "Вид оплаты", employees.salary  AS Ставка, 
        (IF (employees.hourly_payment, salary.working_hours, "-"))  AS "отработано часов", salary.period_year  AS год,
        salary.period_month AS месяц, salary.payment  AS  Начислено
        FROM ((
              (employees INNER JOIN departnemts ON employees.id_department = departnemts.id_department)
               		     INNER JOIN positions ON employees.id_position = positions.id_position)
                         INNER JOIN salary ON employees.id_employee = salary.id_employee)
                         WHERE salary.period_year=:y AND salary.period_month=:m ORDER BY employees.id_employee LIMIT ' . $limit . ' OFFSET ' . $offset ;
        $sth = $this->db->prepare($strSql);
        $sth->bindParam(':y', $year,PDO::PARAM_STR,4);
        $sth->bindParam(':m', $month,PDO::PARAM_STR,2);
        $sth->execute();
        $result = $sth->fetchAll();
        $resultArray [0] = [
                             'ID emp', 'Фамилия', 'Имя', 'Отчество','Дата рождения','Отдел','Должность',
                             'Вид занятости','Ставка','Отработано часов','Месяц','Год','Зарплата'
                            ];
        for ($i=0; $i<count($result); $i++){
            $j = 0;
            foreach ($result[$i] as $key=>$value){
                $resultArray[$i+1][$j++] = $value;
            }
        }
        return $resultArray;
    }
    //----------------------------------------------------
    /**
     * @param $id_departnemt - идентификатор отдела
     * @param $limit - пагинация
     * @param $offset - смещение
     * @param string $year - год
     * @param string $month - месяц
     * @return mixed
     */
    public function getPaymentsByPeriodDepartments($id_departnemt, $limit, $offset, $year = '2017', $month = '03'){
        $strSql = '
        SELECT  departnemts.name AS Отдел, employees.id_employee AS IDE, positions.name AS Должность, employees.f1 AS Фамилия, employees.f2 AS Имя, employees.f3 AS Отчество, 
                employees.birthday AS "Дата рождения", (IF (employees.hourly_payment, "почасово", "ставка")) AS "Вид оплаты", employees.salary  AS Ставка, 
                (IF (employees.hourly_payment, salary.working_hours, "-"))  AS "отработано часов", salary.period_year  AS год,
                salary.period_month AS месяц, salary.payment  AS  Начислено
         FROM ((
              (employees INNER JOIN departnemts ON employees.id_department = departnemts.id_department)
               		     INNER JOIN positions ON employees.id_position = positions.id_position)
                         INNER JOIN salary ON employees.id_employee = salary.id_employee)
         WHERE salary.period_year=:y AND salary.period_month=:m AND departnemts.id_department=:d ORDER BY positions.id_position, employees.f1 
         LIMIT ' . $limit . ' OFFSET ' . $offset ;
        $sth = $this->db->prepare($strSql);
        $sth->bindParam(':y', $year,PDO::PARAM_STR,4);
        $sth->bindParam(':m', $month,PDO::PARAM_STR,2);
        $sth->bindParam(':d', $id_departnemt,PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll();
        $resultArray [0] = [
            'Отдел', 'ID', 'Должность', 'Фамилия', 'Имя', 'Отчество','Дата рождения',
            'Вид занятости','Ставка','Отработано часов','Месяц','Год','Зарплата'
        ];
        for ($i=0; $i<count($result); $i++){
            $j = 0;
            foreach ($result[$i] as $key=>$value){
                $resultArray[$i+1][$j++] = $value;
            }
        }
        return $resultArray;
    }
    //--------------------
    /**
     * @param $config - данные из файла конфигурации для подключения к БД
     * @param bool $ajax - если установлен - значит запрос был послан из функции JS, без перезагрузки, если нет-
     * ---------------- обработка ЧПУ из адресной строки
     * @param string $ajaxQuery
     * @return string
     */
    public static function getSEFdata($config, $ajax = false, $ajaxQuery = ''){
        //------------------------------- определение параметра запроса из ЧПУ
        ($ajax) ? $request_uri = $ajaxQuery:
                  $request_uri = $_SERVER['REQUEST_URI'];
        $params = [
            'uri'        => $request_uri,
            'action'        => 'employees',
            'id_department' => '',
            'offset'        => '0',
            'limit'         => '20',
            'pagesCount'     => '',
            'currentPage'     => '1',
            'data'          => [],
            'departments'   => [],
            'dbSize'        => '0',
            'error'         => '',
         ]; // Массив параметров из URI запроса.
        $num1 = $num2 =$num3 =null;
        if ($request_uri != '/') {
            try {
                $url_path = parse_url($request_uri, PHP_URL_PATH);
                $uri_parts = explode('/', trim($url_path, ' /'));
                if ($uri_parts[0] == 'employees') {
                } else {
                    throw new Exception('неверный URL, позиция 1 - ' . $uri_parts[0]);
                }
                if (isset($uri_parts[1]))
                    if (preg_match("/^[0-9]{1,3}$/ui", $uri_parts[1])  && ((int) $uri_parts[1] > 0) ) {
                        $num1 = $uri_parts[1];
                    } else {
                        throw new Exception('неверный URL, позиция 2 - ' . $uri_parts[1]);
                    }
                if (isset($uri_parts[2]) )
                    if (preg_match("/^[0-9]{1,3}$/ui", $uri_parts[2]) && ((int) $uri_parts[2] > 0)) {
                        $num2 = $uri_parts[2];
                    } else {
                        throw new Exception('неверный URL, позиция 3 - ' . $uri_parts[2]);
                    }
                if (isset($uri_parts[3]) )
                    if (preg_match("/^[0-9]{1,3}$/ui", $uri_parts[3]) && ($uri_parts[3] != '0')) {
                        $num3 = $uri_parts[3];
                    } else {
                        throw new Exception('неверный URL, позиция 4 - ' . $uri_parts[3]);
                    }

                if (isset($uri_parts[4])) throw new Exception('неверный URL - много параметров');
                if ($num1 && $num2 && $num3){ // employes/2/3/20
                    $params['action'] = 'departments';  //n2 -1 страница   n3-5 - на странице
                    $params['id_department'] = $num1;
                    $params['offset'] = ((int) $num2 > 1) ? ($num2) * $num3  - $num3 : 0;
                    $params['limit'] = $num3;
                    $params['currentPage'] = $num2;
                } elseif ($num1 && $num2) {    // employes/5/20
                    $params['action'] = 'employees';
                    $params['offset'] = ((int) $num1 > 1) ? ($num1) * $num2  - $num2 : 0;
                    $params['limit'] = $num2;
                    $params['currentPage'] = $num1;
                } elseif ($num1){               // employes/20
                    $params['action'] = 'employees';
                    $params['limit'] = 20;
                    $params['offset'] = $num1 * $params['limit'] - $params['limit'];
                    $params['currentPage'] = $num1;
                }
            }
            catch (Exception $e) {
                $params['error'] = '404 - ' . $e->getMessage();
            }

        } else {
        }
//--------------------------  возврат результата обработки запроса в зависиности от его типа -----------------------
        if ($params['error'] == '') {
            try{
                $db = new self($config);
                ($params['action'] == 'employees') ? $s = $db->querySelect("SELECT COUNT(*) as count FROM employees"):
                    $s = $db->querySelect('SELECT COUNT(*) as count FROM employees WHERE id_department=' . $params['id_department'] );
                $params['dbSize'] = $s[0]['count'];
                $s = $db->querySelect('SELECT departnemts.* FROM departnemts ORDER BY name');
                for ($i = 0; $i <count($s); $i++){
                    $key = $s[$i]['id_department'];
                    $value = $s[$i]['name'];
                    $params['departments'][$key] = $value;
                }
                switch ($params['action']){
                    case 'employees':
                        $s = $db->getPaymentsByPeriodEmployees($params['limit'], $params['offset']);
                        break;
                    case 'departments':
                        $s = $db->getPaymentsByPeriodDepartments($params['id_department'], $params['limit'], $params['offset']);
                        break;
                }
                if (count($s) > 1)
                    $params['data'] = $s;
                else
                    $params['error'] = 'Данные не найдены';
                $s = $db->querySelect("SELECT min(departnemts.id_department) AS min_id FROM departnemts");
                $params['id_department'] = $s[0]['min_id'];
                unset($db);
            } catch (PDOException $e){
                $params['error'] = ' Ошибка БД - ' . $e->getMessage();
            }
        } else {
            try{
                $db = new self($config);
                ($params['action'] == 'employees') ? $s = $db->querySelect("SELECT COUNT(*) as count FROM employees"):
                    $s = $db->querySelect('SELECT COUNT(*) as count FROM employees WHERE id_department=' . $params['id_department'] );
                $params['dbSize'] = $s[0]['count'];
                $s = $db->querySelect("SELECT min(departnemts.id_department) AS min_id FROM departnemts");
                $params['id_department'] = $s[0]['min_id'];
                $s = $db->querySelect('SELECT departnemts.* FROM departnemts ORDER BY name');
                for ($i = 0; $i <count($s); $i++){
                    $key = $s[$i]['id_department'];
                    $value = $s[$i]['name'];
                    $params['departments'][$key] = $value;
                }
                unset($db);
            } catch (PDOException $e){
                $params['error'] = $params['error'] . ' и Ошибка БД - ' . $e->getMessage();
            }
        }
        (($params['dbSize'] % $params['limit']) == 0) ? $params['pagesCount'] = (int) $params['dbSize']/$params['limit']:
                                                        $params['pagesCount'] = (int) $params['dbSize']/$params['limit'] +1;
        //*********************************************************************************************************************
        //---------  возврат результата
        return json_encode($params);
    }
    //********************************
    /**
     * @param $config
     * @return string
     */
    public static function dropDB($config){
        $params['success'] = 'Данные БД успешно удалены';
        $params['error'] = '';
        try{
            $dbh = new self($config);
            $count = $dbh->db->exec("DELETE  FROM employees");
            $count = $dbh->db->exec("DELETE  FROM departnemts");
            $count = $dbh->db->exec("DELETE  FROM positions");
            $count = $dbh->db->exec("DELETE  FROM salary");
            $s = $dbh->querySelect("SELECT COUNT(*) as c FROM employees");
            $params['dbSize'] = $s[0]['c'];
            unset($db);
        } catch (PDOException $e){
            $params['error'] = ' Ошибка БД - ' . $e->getMessage();
            $params['success'] = '';
        }
        return json_encode($params);
    }
    //********************************
    /**
     * @param $config
     * @return string
     */
    public static function getMinDepartmentId($config){
        $params['success'] = 'ok';
        $params['error'] = '';
        try{
            $dbh = new self($config);
            $strSql = 'SELECT min(departnemts.id_department) AS min_id FROM departnemts';
            $dbRes = $dbh->db->query($strSql);
            $result = $dbRes->fetchAll();
            $params['id_department'] = $result[0]['min_id'];
            unset($db);
        } catch (PDOException $e){
            $params['error'] = ' Ошибка БД - ' . $e->getMessage();
            $params['success'] = '';
        }
        return json_encode($params);
    }
    //********************************
    /**
     * @param $config
     * @param int $countE
     * @param int $countD
     * @return string
     */
    public static function makeTestData($config){
        $params['success'] = 'Тестовый набор успешно создан';
        $params['error'] = '';
        try{
            $dbh = new self($config);
            $count = $dbh->db->exec("DELETE  FROM employees");
            $count = $dbh->db->exec("DELETE  FROM departnemts");
            $count = $dbh->db->exec("DELETE  FROM positions");
            $count = $dbh->db->exec("DELETE  FROM salary");
            $res = $dbh->db->exec('INSERT INTO positions SET name="Начальник"');
            $idPos1 = $dbh->db->lastInsertId ();
            $res = $dbh->db->exec('INSERT INTO positions SET name="Зам. начальника"');
            $idPos2 = $dbh->db->lastInsertId ();
            $res = $dbh->db->exec('INSERT INTO positions SET name="Junior PHP Developer"');
            $idPos3 = $dbh->db->lastInsertId ();
            $res = $dbh->db->exec('INSERT INTO positions SET name="Middle PHP Developer"');
            $idPos4 = $dbh->db->lastInsertId ();
            $iName=1;
            for ($i=1; $i <= 5; $i++){
                $res = $dbh->db->exec('INSERT INTO departnemts SET name="Отдел ' . $i . '"');
                $depId = $dbh->db->lastInsertId ();
                $res = $dbh->db->exec(
                    "INSERT INTO employees (id_department, f1, f2, f3, birthday, id_position, hourly_payment, salary) 
                                          VALUES ($depId, 'Фамилия-$iName', 'Имя-$iName', 'Отчество-$iName', '1998-01-11', '$idPos1', '0', '1500')");
                $lastId = $dbh->db->lastInsertId ();
                $iName++;
                $res = $dbh->db->exec(
                    "INSERT INTO salary ( id_employee, period_year, period_month, working_hours, payment)
                                          VALUES ( $lastId, '2017', '03', 0, NULL)");
                $res = $dbh->db->exec(
                    "INSERT INTO employees (id_department, f1, f2, f3, birthday, id_position, hourly_payment, salary) 
                                          VALUES ($depId, 'Фамилия-$iName', 'Имя-$iName', 'Отчество-$iName', '1998-01-11', '$idPos2', '0', '1200')");
                $lastId = $dbh->db->lastInsertId ();
                $iName++;

                $res = $dbh->db->exec(
                    "INSERT INTO salary ( id_employee, period_year, period_month, working_hours, payment)
                                          VALUES ( $lastId, '2017', '03', 0, NULL)");
                for ($j=3; $j <= 15; $j++){
                    $res = $dbh->db->exec(
                        "INSERT INTO employees (id_department, f1, f2, f3, birthday, id_position, hourly_payment, salary) 
                                          VALUES ($depId, 'Фамилия-$iName', 'Имя-$iName', 'Отчество-$iName', '1998-01-11', '$idPos3', '1', '25')");
                    $lastId = $dbh->db->lastInsertId ();
                    $iName++;
                    $res = $dbh->db->exec(
                        "INSERT INTO salary ( id_employee, period_year, period_month, working_hours, payment)
                                          VALUES ( $lastId, '2017', '03', 300, NULL)");
                }
                for ($j=16; $j <= 26; $j++){
                    $res = $dbh->db->exec(
                        "INSERT INTO employees (id_department, f1, f2, f3, birthday, id_position, hourly_payment, salary) 
                                          VALUES ($depId, 'Фамилия-$iName', 'Имя-$iName', 'Отчество-$iName', '1998-01-11', '$idPos4', '1', '50')");
                    $lastId = $dbh->db->lastInsertId ();
                    $iName++;
                    $res = $dbh->db->exec(
                        "INSERT INTO salary ( id_employee, period_year, period_month, working_hours, payment)
                                          VALUES ( $lastId, '2017', '03', 350, NULL)");
                }

            }
            $res = $dbh->db->exec("
                    UPDATE salary, employees
                            SET payment =
                            (SELECT
                            	CASE WHEN hourly_payment THEN salary*working_hours
		                             ELSE salary
	                            END as employee_payment
                            )
                            WHERE employees.id_employee = salary.id_employee;
                ");
            $s = $dbh->querySelect("SELECT COUNT(*) as count FROM employees");
            $params['dbSize'] = $s[0]['count'];
            unset($dbh);
        } catch (PDOException $e){
            $params['error'] = ' Ошибка БД - ' . $e->getMessage();
            $params['success'] = '';
        }
        return json_encode($params);
    }
    //-----------------------------------------------------------------
    public function querySelect($query){
        $dbRes = $this->db->query($query);
        $result = $dbRes->fetchAll();
        return $result;
    }


}
