<?php

class ApiModel extends Mysql
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUser(string $user, string $pass)
    {
        $sql = "SELECT u.id, u.id_rol, rol, u.id_cust, cust_no, cust, user, ud.name, last_name, coun.img country_img 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN customers AS c ON c.id = u.id_cust 
                    INNER JOIN roles AS r ON r.id = u.id_rol 
                    INNER JOIN countries AS coun ON u.id_country = coun.id 
                    WHERE user = :value0 AND pass = :value1 AND state = 1";

        $array = array($user, $pass);

        return $this->selectOne($sql, $array);
    }

    public function getUsersDetails()
    {
        $sql = "SELECT id_user, name, last_name FROM users_details";

        return $this->select($sql);
    }
    
    public function getCrops()
    {
        $sql = "SELECT * FROM crops";

        return $this->select($sql);
    }

    public function getVarieties()
    {
        $sql = "SELECT id, id_crop, variety_code, variety, img FROM varieties";

        return $this->select($sql);
    }

    public function getVarieties2()
    {
        $sql = "SELECT * FROM varieties";

        return $this->select($sql);
    }

    public function getProducts()
    {
        $sql = "SELECT * FROM products";

        return $this->select($sql);
    }

    public function getCustomers()
    {
        $sql = "SELECT * FROM customers";

        return $this->select($sql);
    }
    
    public function getSecCustomers()
    {
        $sql = "SELECT id, id_cust, sec_cust_no, UPPER(s.sec_cust) AS sec_cust FROM sec_customers AS s";

        return $this->select($sql);
    }

    /* public function getOrders()
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, remarks, 
                        o.order_no, o.id_sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE o.state = 0 
                    ORDER BY id_order";

        return $this->select($sql);
    }

    public function getOrders2()
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE o.state = 0 
                    ORDER BY id_order";

        return $this->select($sql);
    }

    public function getOrders3(int $id)
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = :value0 
                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers WHERE id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END
                    ORDER BY id_order";

        $array = array($id);
        return $this->select($sql, $array);
    } */

    public function getOrders4(int $id)
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety, 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN vd.visit_day
                            ELSE o.visit_day
                        END AS visit_day 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = :value0 
                    LEFT JOIN visit_days aS vd ON vd.id_user = :value0 AND vd.id_order = od.id_order 
                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END
                    ORDER BY od.id_order";

        $array = array($id);
        return $this->select($sql, $array);
    }

    public function getOrders5(int $id)
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, '' remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety, 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN vd.visit_day 
                            ELSE o.visit_day 
                        END AS visit_day 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = :value0 
                    LEFT JOIN visit_days aS vd ON vd.id_user = :value0 AND vd.id_order = od.id_order 
                    WHERE o.id NOT IN (SELECT id_order FROM orders_closed WHERE id_user = :value0) 
                        AND 
                        o.state = 0 
                        AND o.id_country = u.id_country 
                        /*AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END*/
                    ORDER BY od.id_order";

        $array = array($id);
        return $this->select($sql, $array);
    }

    public function getOrders6(int $id, string $dateUpload)
    {
        $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, '' remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, o.id_product, o.year, o.week, o.destination, 
                        product, crop, variety, 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN vd.visit_day 
                            ELSE o.visit_day 
                        END AS visit_day 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = :value0 
                    LEFT JOIN visit_days aS vd ON vd.id_user = :value0 AND vd.id_order = od.id_order 
                    WHERE o.id NOT IN (SELECT id_order FROM orders_closed WHERE id_user = :value0) 
                        AND 
                        o.state = 0 
                        AND o.id_country = u.id_country 
                        AND date_upload > :value1 
                        /*AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END*/
                    ORDER BY od.id_order";

        $array = array($id, $dateUpload);
        return $this->select($sql, $array);
    }

    public function getOrdersParameters($idUser)
    {
        $path = BASE_URL."/uploads/";
        $sql = "SELECT id_user, id_order, id_variety, id_parameter, 
                        CASE p.type 
                            WHEN 4 THEN CONCAT('$path', value) 
                            ELSE value 
                        END AS value, 
                        obs 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = id_order 
                    INNER JOIN parameters AS p ON p.id = id_parameter 
                    WHERE o.state = 0 AND op.id_user = :value0";

        $array = array($idUser);
        return $this->select($sql, $array);
    }

    public function getOrdersTypes()
    {
        $sql = "SELECT * FROM orders_types";

        return $this->select($sql);
    }

    public function getParameters()
    {
        $sql = "SELECT id, parameter, type, category, label, position, remark FROM parameters WHERE state = 1";

        return $this->select($sql);
    }

    public function getParameters2()
    {
        $sql = "SELECT id, parameter, type, category, label, position, remark, type_all FROM parameters WHERE state = 1";

        return $this->select($sql);
    }

    public function getParameters3()
    {
        $sql = "SELECT id, parameter, type, category, label, position, remark, type_all, required FROM parameters WHERE state = 1";

        return $this->select($sql);
    }

    public function getParametersOptions()
    {
        $sql = "SELECT * FROM parameters_options WHERE state = 1";

        return $this->select($sql);
    }

    public function getParametersCrops()
    {
        $sql = "SELECT * FROM parameters_crops";

        return $this->select($sql);
    }

    public function getOrderParameter(int $idUser, int $idOder, int $idVariety, int $idParameter)
    {
        $sql = "SELECT id FROM orders_parameters WHERE id_user = :value0 AND id_order = :value1 AND id_variety = :value2 AND id_parameter = :value3";
        $array = array($idUser, $idOder, $idVariety, $idParameter);

        return $this->selectOne($sql, $array);
    }

    public function setDataSync(int $idUser, int $idOder, int $idVariety, int $idParameter, String $value, String $obs, int $year, int $week)
    {
        $sql = "INSERT INTO orders_parameters (year, week, id_user, id_order, id_variety, id_parameter, value, obs) VALUES (:value6, :value7, :value0, :value1, :value2, :value3, :value4, :value5)";
        $array = array($idUser, $idOder, $idVariety, $idParameter, $value, $obs, $year, $week);

        return $this->insert($sql, $array);
    }

    public function updateDataSync(int $id, String $value, String $obs)
    {
        $sql = "UPDATE orders_parameters SET value = :value1, obs = :value2 WHERE id = :value0";
        $array = array($id, $value, $obs);

        return $this->update($sql, $array);
    }

    public function updateOrderState(int $id)
    {
        $sql = "UPDATE orders SET state = 1 WHERE id = :value0";
        $array = array($id);

        return $this->update($sql, $array);
    }

    public function setOrderClosed(int $idOder, int $idUser)
    {
        $sql = "INSERT INTO orders_closed (id_order, id_user) VALUES (:value0, :value1)";
        $array = array($idOder, $idUser);

        return $this->insert($sql, $array);
    }

    public function deleteOrderParameter(int $idUser, int $idOder, int $idVariety, int $idParameter)
    {
        $sql = "DELETE FROM orders_parameters WHERE id_user = :value0 AND id_order = :value1 AND id_variety = :value2 AND id_parameter = :value3";
        $array = array($idUser, $idOder, $idVariety, $idParameter);

        return $this->delete($sql, $array);
    }

    public function getIdAddOrder()
        {
            $sql = 'SELECT (order_no - 1) AS order_no 
                    FROM orders 
                    ORDER BY order_no 
                    LIMIT 1';

            return $this->selectOne($sql);
        }

    public function setOrder(int $orderNo, int $idSecCust, int $idType, int $idProduct, int $year, int $week, String $destination, String $visitDay)
    {
        $sql = "INSERT INTO orders (order_no, id_sec_cust, id_type, id_product, year, week, destination, visit_day) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7)";
        $array = array($orderNo, $idSecCust, $idType, $idProduct, $year, $week, $destination, $visitDay);

        return $this->insert($sql, $array);
    }

    public function setOrderDetail(int $idOrder, int $idVariety, int $totQuantity = 0, float $totPrice = 0)
    {
        $sql = 'INSERT INTO orders_details (id_order, id_variety, tot_quantity, tot_price) VALUES (:value0, :value1, :value2, :value3)';

        $array = array($idOrder, $idVariety, $totQuantity, $totPrice);
        return $this->insert($sql, $array);
    }

    public function deleteOrder(int $idOrder)
    {
        $sql = 'DELETE FROM orders WHERE id = :value0';

        $array = array($idOrder);
        return $this->delete($sql, $array);
    }

    public function deleteOrdersDetails(int $idOrder)
    {
        $sql = 'DELETE FROM orders_details WHERE id_order = :value0';

        $array = array($idOrder);
        return $this->delete($sql, $array);
    }
    
    public function getOrderVisitDay(int $id, int $idOrder)
    {
        $sql = 'SELECT id 
                FROM visit_days 
                WHERE id_user = :value0 AND id_order = :value1 
                LIMIT 1';

        $array = array($id, $idOrder);
        return $this->selectOne($sql, $array);
    }

    public function setOrderVisitDay(int $id, int $idOrder, String $datetime)
    {
        $sql = 'INSERT INTO visit_days (id_user, id_order, visit_day) VALUES (:value0, :value1, :value2)';

        $array = array($id, $idOrder, $datetime);
        return $this->insert($sql, $array);
    }

    public function updateOrderVisitDay(int $id, String $datetime)
    {
        $sql = 'UPDATE visit_days SET visit_day = :value1 WHERE id = :value0';

        $array = array($id, $datetime);
        return $this->update($sql, $array);
    }

    public function getVisitDaysByUser(int $user)
    {
        $sql = "SELECT id_order, visit_day, notify 
                    FROM visit_days 
                    WHERE id_user = :value0";

        $array = array($user);

        return $this->select($sql, $array);
    }

    //*Events-------------------------------------------------------------
    public function getEvents(int $user)
    {
        $sql = 'SELECT e.id, e.name, start_week, end_week, description, image, e.state 
                FROM events AS e 
                INNER JOIN users AS u ON u.id = :value0 
                WHERE e.id_country = u.id_country';

        $array = array($user);

        return $this->select($sql, $array);
    }

    public function getEventsCustomers(int $user)
    {
        $sql = 'SELECT esc.*, 1 state 
                FROM events_sec_customers esc 
                JOIN events e ON esc.id_event = e.id 
                JOIN users u ON u.id = :value0 
                WHERE e.id_country = u.id_country';

        $array = array($user);

        return $this->select($sql, $array);
    }

    public function getEventsCustomersOrders(int $user)
    {
        $sql = "SELECT *, 1 state 
                FROM events_sec_customers_orders 
                WHERE /*id_user = :value0 AND*/ YEAR(date) = YEAR(NOW())";

        //$array = array($user);

    return $this->select($sql/*, $array*/);
    }

    public function getEventSecCustByEventBySecCust(int $idUser, int $idEvent, int $idSecCust, String $name, String $table)
    {
        $sql = "SELECT id FROM $table WHERE id_user = :value0 AND id_event = :value1 AND id_sec_cust = :value2 AND YEAR(date) = YEAR(NOW()) AND name = :value3 LIMIT 1";
        $array = array($idUser, $idEvent, $idSecCust, $name);

        return $this->selectOne($sql, $array);
    }

    public function setDataEventSecCustSync(int $idUser, int $idEvent, int $idSecCust, String $name, int $numberPhone, String $email, String $emailName, String $date, String $table)
    {
        $sql = "INSERT INTO $table (id_user, id_event, id_sec_cust, name, number_phone, email, email_name, date) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7)";
        $array = array($idUser, $idEvent, $idSecCust, $name, $numberPhone, $email, $emailName, $date);

        return $this->insert($sql, $array);
    }

    public function updateDataEventSecCustSync(int $id, String $name, int $numberPhone, String $email, String $emailName, String $table)
    {
        $sql = "UPDATE $table SET name = :value1, number_phone = :value2, email = :value3, email_name = :value4 WHERE id = :value0";
        $array = array($id, $name, $numberPhone, $email, $emailName);

        return $this->update($sql, $array);
    }

    //*Varieties
    public function getEventVarietyByEventBySecCustByVariety(int $idUser, int $idEvent, int $idSecCust, int $idVariety, String $name, int $idEventMap = null, String $table)
    {
        $sql = "SELECT id FROM $table WHERE id_user = :value0 AND id_event = :value1 AND id_sec_cust = :value2 AND id_variety = :value3 AND name = :value4 AND 
                CASE 
                    WHEN  :value5 IS NULL THEN id_event_map IS NULL 
                    ELSE id_event_map = :value5 
                END 
                LIMIT 1";
        $array = array($idUser, $idEvent, $idSecCust, $idVariety, $name, $idEventMap);

        return $this->selectOne($sql, $array);
    }

    public function setDataEventVarietySync(int $idUser, int $idEvent, int $idSecCust, String $name, int $idType, int $idProduct, int $idVariety, int $idEventMap = null, int $year, int $week, int $totQuantity, int $replicas, String $remark, String $date, String $table)
    {
        $sql = "INSERT INTO $table (id_user, id_event, id_sec_cust, name, id_type, id_product, id_variety, id_event_map, year, week, tot_quantity, replicas, remark, date) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7, :value8, :value9, :value10, :value11, :value12, :value13)";
        $array = array($idUser, $idEvent, $idSecCust, $name, $idType, $idProduct, $idVariety, $idEventMap, $year, $week, $totQuantity, $replicas, $remark, $date);

        return $this->insert($sql, $array);
    }

    public function updateDataEventVarietySync(int $id, int $year, int $week, int $totQuantity, int $replicas, String $remark, String $table)
    {
        $sql = "UPDATE $table SET year = :value1, week = :value2, tot_quantity = :value3, replicas = :value4, remark = :value5 WHERE id = :value0";
        $array = array($id, $year, $week, $totQuantity, $replicas, $remark);

        return $this->update($sql, $array);
    }

    public function setDataEventVarietySync2(int $idUser, int $idEvent, int $idSecCust, String $name, int $idType, int $idProduct, int $idVariety, int $idEventMap = null, int $year, int $week, int $totQuantity, int $replicas, String $remark, int $confirm, String $date, String $table)
    {
        $sql = "INSERT INTO $table (id_user, id_event, id_sec_cust, name, id_type, id_product, id_variety, id_event_map, year, week, tot_quantity, replicas, remark, confirm, date) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7, :value8, :value9, :value10, :value11, :value12, :value13, :value14)";
        $array = array($idUser, $idEvent, $idSecCust, $name, $idType, $idProduct, $idVariety, $idEventMap, $year, $week, $totQuantity, $replicas, $remark, $confirm, $date);

        return $this->insert($sql, $array);
    }

    public function updateDataEventVarietySync2(int $id, int $year, int $week, int $totQuantity, int $replicas, String $remark, int $confirm, String $table)
    {
        $sql = "UPDATE $table SET year = :value1, week = :value2, tot_quantity = :value3, replicas = :value4, remark = :value5, confirm = :value6 WHERE id = :value0";
        $array = array($id, $year, $week, $totQuantity, $replicas, $remark, $confirm);

        return $this->update($sql, $array);
    }

    public function deleteEventVarietyByEventBySecCustByVariety(int $idUser, int $idEvent, int $idSecCust, int $idVariety, String $name, int $idEventMap = null, String $table)
    {

        $sql = "DELETE FROM $table WHERE id_user = :value0 AND id_event = :value1 AND id_sec_cust = :value2 AND id_variety = :value3 AND name = :value4 AND 
                CASE 
                    WHEN  :value5 IS NULL THEN id_event_map IS NULL 
                    ELSE id_event_map = :value5 
                END 
                LIMIT 1";
        $array = array($idUser, $idEvent, $idSecCust, $idVariety, $name, $idEventMap);

        return $this->delete($sql, $array);
    }
}