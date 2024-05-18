<?php

    class DashboardModel extends Mysql{

        public function __construct()
        {
            parent::__construct();
        }

        //TODO Home--------------------------------------------------------------------------
        public function getOrderByNo(int $orderNo)
        {
            $sql = 'SELECT id, order_no 
                    FROM orders 
                    WHERE order_no = :value0 
                    LIMIT 1';

            $array = array($orderNo);
            return $this->selectOne($sql, $array);
        }

        public function setOrder(int $orderNo, int $idSecCust, int $idType, int $idProduct, int $year, int $week, String $destination, String $remarks, String $visitDay)
        {
            $sql = 'INSERT INTO orders (order_no, id_country, id_sec_cust, id_type, id_product, year, week, destination, remarks, user_upload, visit_day) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7, :value8, :value9, :value10)';

            $array = array($orderNo, $_SESSION["id_country"], $idSecCust, $idType, $idProduct, $year, $week, $destination, $remarks, $_SESSION["id"], $visitDay);
            return $this->insert($sql, $array);
        }

        public function getCustByNo(int $number)
        {
            $sql = 'SELECT id, cust 
                    FROM customers 
                    WHERE cust_no = :value0 
                    LIMIT 1';

            $array = array($number);
            return $this->selectOne($sql, $array);
        }

        public function setCustomer(int $custNo, String $cust, String $logo = "")
        {
            $sql = 'INSERT INTO customers (cust_no, cust, logo) VALUES (:value0, :value1, :value2)';

            $array = array($custNo, $cust, $logo);
            return $this->insert($sql, $array);
        }

        public function getSecCustByNo(int $idCust, int $secCust)
        {
            $sql = 'SELECT id 
                    FROM sec_customers 
                    WHERE id_cust = :value0 AND sec_cust_no = :value1 
                    LIMIT 1';

            $array = array($idCust, $secCust);
            return $this->selectOne($sql, $array);
        }

        
        public function setSecCustomer(int $idCust, int $secCustNo, String $secCust)
        {
            $sql = 'INSERT INTO sec_customers (id_cust, sec_cust_no, sec_cust) VALUES (:value0, :value1, :value2)';

            $array = array($idCust, $secCustNo, $secCust);
            return $this->insert($sql, $array);
        }

        public function getOrderTypeByType(String $orderType)
        {
            $sql = 'SELECT id 
                    FROM orders_types 
                    WHERE type = :value0 
                    LIMIT 1';

            $array = array($orderType);
            return $this->selectOne($sql, $array);
        }

        
        public function setOrderType(String $orderType)
        {
            $sql = 'INSERT INTO orders_types (type) VALUES (:value0)';

            $array = array($orderType);
            return $this->insert($sql, $array);
        }

        public function getProductByProduct(String $product)
        {
            $sql = 'SELECT id 
                    FROM products 
                    WHERE product = :value0 
                    LIMIT 1';

            $array = array($product);
            return $this->selectOne($sql, $array);
        }

        
        public function setProduct(String $product)
        {
            $sql = 'INSERT INTO products (product) VALUES (:value0)';

            $array = array($product);
            return $this->insert($sql, $array);
        }

        public function getCropByNo(int $number)
        {
            $sql = 'SELECT id 
                    FROM crops 
                    WHERE crop_no = :value0 
                    LIMIT 1';

            $array = array($number);
            return $this->selectOne($sql, $array);
        }

        public function setCrop(int $idCropGeneral, int $cropNo, String $crop)
        {
            $sql = 'INSERT INTO crops (id_crop_general, crop_no, crop) VALUES (:value0, :value1, :value2)';

            $array = array($idCropGeneral, $cropNo, $crop);
            return $this->insert($sql, $array);
        }

        public function getVarieties()
        {
            $sql = "SELECT v.*, c.crop 
                    FROM varieties AS v 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE TRIM(variety) != '' AND TRIM(variety) != '..' 
                    ORDER BY variety";

            return $this->select($sql);
        }
        
        public function getVarietyByNo(int $idCrop, int $number)
        {
            $sql = "SELECT id 
                    FROM varieties 
                    WHERE id_crop = :value0 AND 
                        CASE 
                            WHEN :value1 != 0 THEN :value1 
                            ELSE -1 
                        END = variety_code 
                    LIMIT 1";

            $array = array($idCrop, $number);
            return $this->selectOne($sql, $array);
        }

        public function getVarietyByName(string $name)
        {
            $sql = 'SELECT v.id, variety, variety_code, crop 
                    FROM varieties AS v 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE variety = :value0 
                    LIMIT 1';

            $array = array($name);
            return $this->selectOne($sql, $array);
        }

        public function getVarietyByNameAdvance(string $name)
        {
            $sql = "SELECT 
                        v.id, variety, variety_code, crop, 
                        MATCH(variety) AGAINST(:value0) AS relevancia 
                    FROM varieties AS v 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE MATCH(variety) AGAINST(:value0) 
                    ORDER BY relevancia DESC,variety";

            $array = array($name);
            return $this->select($sql, $array);
        }

        public function setVariety(int $idCrop, int $varietyCode, String $variety)
        {
            $sql = 'INSERT INTO varieties (id_crop, variety_code, variety) VALUES (:value0, :value1, :value2)';

            $array = array($idCrop, $varietyCode, $variety);
            return $this->insert($sql, $array);
        }

        public function updateVariety(int $id, String $variety)
        {
            $sql = 'UPDATE varieties SET variety = :value1 WHERE id = :value0';

            $array = array($id, $variety);
            return $this->update($sql, $array);
        }

        public function getOrderDetail(int $idOrder, int $idVatiety)
        {
            $sql = 'SELECT id 
                    FROM orders_details 
                    WHERE id_order = :value0 AND id_variety = :value1 
                    LIMIT 1';

            $array = array($idOrder, $idVatiety);
            return $this->selectOne($sql, $array);
        }

        public function setOrderDetail(int $idOrder, int $idVariety, int $totQuantity, float $totPrice = 0)
        {
            $sql = 'INSERT INTO orders_details (id_order, id_variety, tot_quantity, tot_price) VALUES (:value0, :value1, :value2, :value3)';

            $array = array($idOrder, $idVariety, $totQuantity, $totPrice);
            return $this->insert($sql, $array);
        }

        public function updateOrderDetail(int $id, int $totQuantity, float $totPrice = 0)
        {
            $sql = 'UPDATE orders_details 
                    SET tot_quantity = tot_quantity + :value1, tot_price = tot_price + :value2 
                    WHERE id = :value0 AND state = 0';

            $array = array($id, $totQuantity, $totPrice);
            return $this->update($sql, $array);
        }

        public function updateOrderDetailState()
        {
            $sql = 'UPDATE orders_details SET state = 1 WHERE state = 0';

            return $this->update($sql);
        }

        public function getUploadLogs()
        {
            $sql = "SELECT o.date_upload, 
                        MIN(CONCAT(o.year, '-W', CASE WHEN o.week < 10 THEN CONCAT('0', o.week) ELSE o.week END)) AS week_min, 
                        MAX(CONCAT(o.year, '-W', CASE WHEN o.week < 10 THEN CONCAT('0', o.week) ELSE o.week END)) AS week_max, 
                        COUNT(*) AS orders, 
                        od.rowsData 
                    FROM orders AS o 
                    LEFT JOIN (
                        SELECT date_upload, user_upload, COUNT(*) AS rowsData 
                        FROM orders_details AS od 
                        INNER JOIN orders AS o ON o.id = od.id_order 
                        GROUP BY date_upload, user_upload 
                    ) AS od ON od.date_upload = o.date_upload AND od.user_upload = o.user_upload 
                    WHERE order_no > 0 
                    GROUP BY o.date_upload, o.user_upload 
                    ORDER BY o.date_upload 
                    LIMIT 10";

            return $this->select($sql);
        }


        public function getDataBetweenWeeks(int $year1, int $week1, int $year2, int $week2)
        {
            $sql = 'SELECT op.year, op.week, order_no, destination, cust_no, cust, sec_cust_no, sec_cust, 
                        ot.type AS order_type, product, crop_general, crop, variety_code, variety, p.type, parameter, value, obs 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN customers AS c ON c.id = sc.id_cust 
                    INNER JOIN varieties AS v ON v.id = op.id_variety 
                    INNER JOIN crops AS cr ON cr.id = id_crop 
                    INNER JOIN crops_generals AS crg ON crg.id = cr.id_crop_general 
                    INNER JOIN parameters AS p ON p.id = op.id_parameter 
                    INNER JOIN orders_types AS ot ON ot.id = o.id_type 
                    INNER JOIN products AS pr ON pr.id = o.id_product 
                    WHERE (op.year BETWEEN :value0 AND :value2) AND (op.week BETWEEN :value1 AND :value3) 
                    ORDER BY order_no';

            $array = array($year1, $week1, $year2, $week2);
            return $this->select($sql, $array);
        }

        public function getDataBetweenWeeks2(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT id_user, id_order, id_variety, id_parameter, value, obs 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    WHERE YEARWEEK(op.date) BETWEEN :value0 AND :value1 
                    ORDER BY order_no';

            $array = array($weekFrom, $weekTo);
            return $this->select($sql, $array);
        }

        public function getOrdersParametersBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT op.id_user, name, last_name, 
                        id_order, id_variety, id_order, op.date, op.year, op.week, order_no, destination, cust_no, cust, sec_cust_no, sec_cust, 
                        ot.type AS order_type, product, crop_general, crop, variety_code, variety 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN customers AS c ON c.id = sc.id_cust 
                    INNER JOIN varieties AS v ON v.id = op.id_variety 
                    INNER JOIN crops AS cr ON cr.id = id_crop 
                    INNER JOIN crops_generals AS crg ON crg.id = cr.id_crop_general 
                    INNER JOIN orders_types AS ot ON ot.id = o.id_type 
                    INNER JOIN products AS pr ON pr.id = o.id_product 
                    INNER JOIN users_details AS ud ON ud.id_user = op.id_user 
                    WHERE YEARWEEK(op.date) BETWEEN :value0 AND :value1 
                    GROUP BY id_order, op.id_user, v.id 
                    ORDER BY order_no';

            $array = array($weekFrom, $weekTo);
            return $this->select($sql, $array);
        }

        public function getDataCountBetweenWeeksGroupCust(string $weekFrom, string $weekTo, string $filterUploads, string $filterCompleted)
        {
            $sql = "SELECT 
                        id_cust AS id, cust AS categorie, SUM(completed) AS count, SUM(goal) AS goal 
                    FROM (
                        SELECT c.id AS id_cust, c.cust, 0 AS completed, COUNT(*) AS goal 
                        FROM orders AS o 
                        INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                        INNER JOIN customers AS c ON c.id = sc.id_cust 
                        $filterUploads 
                        WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 
                        GROUP BY c.id 

                        UNION ALL 

                        SELECT id_cust, cust, COUNT(*) AS completed, 0 AS goal 
                        FROM (
                            SELECT DISTINCT oc.id_order, c.id AS id_cust, c.cust 
                            FROM orders_closed AS oc 
                            INNER JOIN orders AS o ON o.id = oc.id_order 
                            INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                            INNER JOIN customers AS c ON c.id = sc.id_cust 
                            $filterCompleted 
                            WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                        ) AS d2 
                        GROUP BY id_cust 
                    ) AS d 
                    GROUP BY id_cust 
                    ORDER BY SUM(completed) DESC, cust";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getDataCountBetweenWeeksGroupUser(string $weekFrom, string $weekTo, string $filterCompleted)
        {
            $sql = "SELECT 
                        id_user AS id, u.user AS categorie,
                        COUNT(*) AS count 
                    FROM orders_closed AS oc 
                    INNER JOIN orders AS o ON o.id = oc.id_order 
                    INNER JOIN users AS u ON u.id = id_user 
                    $filterCompleted 
                    WHERE o.id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    GROUP BY id_user 
                    ORDER BY COUNT(*) DESC, u.user";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getDataCountBetweenWeeksGroupCrop(string $weekFrom, string $weekTo, string $filterUploads = "", string $filterCompleted = "")
        {
            $sql = "SELECT 
                        id_crop AS id, crop AS categorie, SUM(completed) AS count, SUM(goal) AS goal 
                    FROM (
                        SELECT id_crop, crop, 0 AS completed, COUNT(*) AS goal 
                        FROM (
                            SELECT DISTINCT v.id_crop, o.id, c.crop 
                            FROM orders_details AS od 
                            INNER JOIN orders AS o ON o.id = od.id_order 
                            INNER JOIN varieties AS v ON v.id = od.id_variety 
                            INNER JOIN crops AS c ON c.id = v.id_crop 
                            $filterUploads 
                            WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 
                        ) AS d1 
                        GROUP BY d1.id_crop 

                        UNION ALL 

                        SELECT id_crop, crop, COUNT(*) AS completed, 0 AS goal 
                        FROM (
                            SELECT DISTINCT v.id_crop, o.id, c.crop 
                            FROM orders_parameters  AS op 
                            INNER JOIN orders AS o ON o.id = op.id_order 
                            INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order 
                            INNER JOIN varieties AS v ON v.id = op.id_variety 
                            INNER JOIN crops AS c ON c.id = v.id_crop 
                            $filterCompleted 
                            WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                        ) AS d2 
                        GROUP BY d2.id_crop 
                    ) AS d 
                    GROUP BY id_crop 
                    ORDER BY SUM(completed) DESC, crop";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getDataCountBetweenWeeksGroupVariety(string $weekFrom, string $weekTo, string $filterUploads = "", string $filterCompleted = "")
        {
            $sql = "SELECT 
                        id_variety AS id, variety AS categorie, SUM(completed) AS count, SUM(goal) AS goal 
                    FROM (
                        SELECT id_variety, variety, 0 AS completed, COUNT(*) AS goal 
                        FROM (
                            SELECT DISTINCT id_variety, o.id, v.variety 
                            FROM orders_details AS od 
                            INNER JOIN orders AS o ON o.id = od.id_order 
                            INNER JOIN varieties AS v ON v.id = od.id_variety 
                            $filterUploads 
                            WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 
                                AND TRIM(variety) != '' 
                        ) AS d1 
                        GROUP BY d1.id_variety 

                        UNION ALL 

                        SELECT id_variety, variety, COUNT(*) AS completed, 0 AS goal 
                        FROM (
                            SELECT DISTINCT id_variety, o.id, v.variety 
                            FROM orders_parameters  AS op 
                            INNER JOIN orders AS o ON o.id = op.id_order 
                            INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order 
                            INNER JOIN varieties AS v ON v.id = op.id_variety 
                            $filterCompleted 
                            WHERE 
                            id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                                AND TRIM(variety) != '' 
                        ) AS d2 
                        GROUP BY d2.id_variety 
                    ) AS d 
                    GROUP BY id_variety 
                    ORDER BY SUM(completed) DESC, variety";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getParametersBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT DISTINCT p.id, p.parameter, p.type, p.category 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    INNER JOIN parameters AS p ON p.id = op.id_parameter 
                    INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order AND oc.id_user = op.id_user 
                    WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ORDER BY p.category, p.parameter';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getUsersBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT id_user AS id, u.user 
                    FROM (
                        SELECT DISTINCT id_user 
                        FROM orders_closed AS oc 
                        INNER JOIN orders AS o ON oc.id_order = o.id 
                        WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ) AS d 
                    INNER JOIN users AS u ON u.id = id_user 
                    GROUP BY id_user 
                    ORDER BY u.user';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getCustomersBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT c.id, c.cust 
                    FROM (
                        SELECT DISTINCT id_sec_cust 
                        FROM orders 
                        WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 

                        UNION ALL

                        SELECT DISTINCT id_sec_cust 
                        FROM orders_closed AS oc 
                        INNER JOIN orders AS o ON oc.id_order = o.id 
                        WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ) AS d 
                    INNER JOIN sec_customers AS sc ON sc.id = id_sec_cust 
                    INNER JOIN customers AS c ON c.id = sc.id_cust 
                    GROUP BY c.id 
                    ORDER BY c.cust';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getDestinationsBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT destination 
                    FROM (
                        SELECT DISTINCT destination 
                        FROM orders 
                        WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 

                        UNION ALL

                        SELECT DISTINCT destination 
                        FROM orders_closed AS oc 
                        INNER JOIN orders AS o ON oc.id_order = o.id 
                        WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ) AS d 
                    GROUP BY destination';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getOrderTypesBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT id_type AS id, ot.type AS categorie 
                    FROM (
                        SELECT DISTINCT id_type 
                        FROM orders 
                        WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 

                        UNION ALL

                        SELECT DISTINCT id_type 
                        FROM orders_closed AS oc 
                        INNER JOIN orders AS o ON oc.id_order = o.id 
                        WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ) AS d 
                    INNER JOIN orders_types AS ot ON ot.id = id_type 
                    GROUP BY id_type 
                    ORDER BY ot.type';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getProductsBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = 'SELECT id_product AS id, p.product AS categorie 
                    FROM (
                        SELECT DISTINCT id_product 
                        FROM orders 
                        WHERE id_country = :value2 AND YEARWEEK(visit_day) BETWEEN :value0 AND :value1 

                        UNION ALL

                        SELECT DISTINCT id_product 
                        FROM orders_closed AS oc 
                        INNER JOIN orders AS o ON oc.id_order = o.id 
                        WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ) AS d 
                    INNER JOIN products AS p ON p.id = id_product 
                    GROUP BY id_product 
                    ORDER BY p.product';

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getVarietiesBetweenWeeks(string $weekFrom, string $weekTo)
        {
            $sql = "SELECT DISTINCT v.id_crop, c.crop, v.id, v.variety 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    INNER JOIN varieties AS v ON v.id = op.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order AND oc.id_user = op.id_user 
                    WHERE id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                    ORDER BY v.id_crop, v.variety";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getVarietiesBetweenWeeksDistinctOrders(string $weekFrom, string $weekTo, string $varieties, string $filterCompleted)
        {
            $sql = "SELECT
                        u.user, 
                        sc.id_cust, sc.sec_cust, 
                        o.order_no, 
                        op.date, op.id_order, op.id_user, op.id_variety, op.year, op.week, 
                        c.crop, 
                        v.id_crop, v.variety 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order AND oc.id_user = op.id_user 
                    INNER JOIN varieties AS v ON v.id = op.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = op.id_user 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    $filterCompleted 
                    WHERE o.id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                        AND id_variety IN ($varieties) 
                    GROUP BY o.id, op.id_user, v.id 
                    ORDER BY v.id_crop, v.variety";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getDataCompareBetweenWeeksOrders(string $weekFrom, string $weekTo, string $varieties, string $parameters)
        {
            $sql = "SELECT 
                        op.id_user, op.id_order, op.id_variety, op.id_parameter, op.value, op.obs 
                    FROM orders_parameters AS op 
                    INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order AND oc.id_user = op.id_user 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    WHERE 
                        id_country = :value2 AND YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                        AND op.id_variety IN ($varieties) AND op.id_parameter IN ($parameters)";

            $array = array($weekFrom, $weekTo, $_SESSION["id_country"]);
            return $this->select($sql, $array);
        }
        //TODO Home--------------------------------------------------------------------------


        //TODO Events--------------------------------------------------------------------------
        public function getEvent(int $id)
        {
            $sql = 'SELECT * 
                    FROM events 
                    WHERE id = :value0 
                    LIMIT 1';

            $array = array($id);
            return $this->selectOne($sql, $array);
        }

        public function getEvents()
        {
            $sql = 'SELECT * FROM events WHERE id_country = :value0';

            $array = array($_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getEventByNameAndWeek(string $name, int $start, int $end, int $id = 0)
        {
            $sql = 'SELECT id 
                    FROM events 
                    WHERE name = :value0 AND start_week = :value1 AND end_week = :value2 AND id != :value3 
                    LIMIT 1';

            $array = array($name, $start, $end, $id);
            return $this->selectOne($sql, $array);
        }

        public function setEvent(string $name, int $start, int $end, String $description, string $image)
        {
            $sql = 'INSERT INTO events (id_country, name, start_week, end_week, description, image) VALUES (:value0, :value1, :value2, :value3, :value4, :value5)';

            $array = array($_SESSION["id_country"], $name, $start, $end, $description, $image);
            return $this->insert($sql, $array);
        }

        public function setEventYear(int $idEvent, int $year)
        {
            $sql = 'INSERT INTO events_header (id_event, year) VALUES (:value0, :value1)';

            $array = array($idEvent, $year);
            return $this->insert($sql, $array);
        }

        public function setEventMap(int $idEvent, int $year, int $idVariety, string $greenhouse, string $position)
        {
            $sql = 'INSERT INTO events_maps (id_event, year, id_variety, greenhouse, position) VALUES (:value0, :value1, :value2, :value3, :value4)';

            $array = array($idEvent, $year, $idVariety, $greenhouse, $position);
            return $this->insert($sql, $array);
        }

        public function updateVarietyMap(int $id, int $idVariety)
        {
            $sql = 'UPDATE events_maps SET id_variety = :value1 WHERE id = :value0';

            $array = array($id, $idVariety);
            return $this->update($sql, $array);
        }

        public function updateEvent(int $id, String $name, int $start, int $end, String $description, string $image = null, int $state)
        {
            $sql = 'UPDATE events SET name = :value1, start_week = :value2, end_week = :value3, description = :value4, image = :value5, state = :value6 WHERE id = :value0';

            $array = array($id, $name, $start, $end, $description, $image, $state);
            return $this->update($sql, $array);
        }

        public function getEventYears(int $idEvent)
        {
            $sql = "SELECT eh.year, IFNULL(o.orders, 0) AS orders, IFNULL(em.map_data, 0) AS map_data 
                    FROM events_header AS eh 
                    LEFT JOIN (
                        SELECT YEAR(e.date) AS year, COUNT(e.id) AS orders 
                        FROM events_sec_customers_orders AS e 
                        WHERE e.id_event = :value0 
                        GROUP BY YEAR(e.date) 
                    ) AS o ON o.year = eh.year 
                    LEFT JOIN (
                        SELECT year, COUNT(*) AS map_data 
                        FROM events_maps 
                        WHERE id_event = :value0 
                        GROUP BY year 
                    ) AS em ON em.year = eh.year 
                    WHERE id_event = :value0";

            $array = array($idEvent);
            return $this->select($sql, $array);
        }

        public function getDataEventByYear(int $year, int $idEvent)
        {
            $sql = "SELECT esc.date AS date_first, d.*, 
                        ud.name AS name_user, last_name, 
                        esc.name, esc.email, esc.email_name, esc.email_name, 
                        e.name AS event, 
                        ot.type AS order_type, 
                        sc.sec_cust_no, sc.sec_cust, 
                        p.product, 
                        v.variety_code, v.variety, 
                        c.crop_no, c.crop, 
                        cust.cust_no, cust.cust, 
                        IFNULL(m.greenhouse, '') AS greenhouse, IFNULL(m.position, '') AS position 
                    FROM events_sec_customers_orders AS d 
                    INNER JOIN events_sec_customers AS esc ON esc.id_event = d.id_event AND esc.id_sec_cust = d.id_sec_cust AND YEAR(esc.date) = YEAR(d.date) AND esc.name = d.name 
                    INNER JOIN events AS e ON e.id = d.id_event 
                    INNER JOIN orders_types AS ot ON ot.id = d.id_type 
                    INNER JOIN sec_customers AS sc ON sc.id = d.id_sec_cust 
                    INNER JOIN customers AS cust ON cust.id = sc.id_cust 
                    INNER JOIN products AS p ON p.id = d.id_product 
                    INNER JOIN varieties AS v ON v.id = d.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users_details AS ud ON ud.id_user = d.id_user 
                    LEFT JOIN events_maps AS m ON m.id = d.id_event_map 
                    WHERE YEAR(d.date) = :value0 AND d.id_event = :value1";

            $array = array($year, $idEvent);
            return $this->select($sql, $array);
        }

        public function getEventMapByYear(int $idEvent, int $year)
        {
            $sql = "SELECT em.*, c.crop, v.variety_code, v.variety 
                    FROM events_maps AS em 
                    INNER JOIN events AS e ON e.id = em.id_event 
                    INNER JOIN varieties AS v ON v.id = em.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    WHERE id_event = :value0 AND year = :value1 
                    ORDER BY greenhouse, position + 0";

            $array = array($idEvent, $year);
            return $this->select($sql, $array);
        }

        public function getIsStartedEvent(int $idEvent, int $year)
        {
            $sql = "SELECT e.* 
                    FROM events_sec_customers AS e 
                    WHERE e.id_event = :value0 AND YEAR(e.date) = :value1 
                    LIMIT 1";

            $array = array($idEvent, $year);
            return $this->selectOne($sql, $array);
        }

        public function deleteEventMapByYear(int $idEvent, int $year)
        {
            $sql = 'DELETE FROM events_maps WHERE id_event = :value0 AND year = :value1 ';

            $array = array($idEvent, $year);
            return $this->delete($sql, $array);
        }
        //TODO Events--------------------------------------------------------------------------


        //TODO Parameters--------------------------------------------------------------------------
        public function getParameter(int $id)
        {
            $sql = 'SELECT p.*, GROUP_CONCAT(DISTINCT pc.id_crop) AS crops, IFNULL(po.options, 0) AS options 
                    FROM parameters AS p 
                    LEFT JOIN parameters_crops AS pc ON pc.id_parameter = p.id 
                    /*LEFT JOIN parameters_options AS po ON po.id_parameter = p.id */
                    LEFT JOIN (
                        SELECT id_parameter, GROUP_CONCAT(id, "^", value, "^", state) AS options 
                        FROM parameters_options 
                        GROUP BY id_parameter 
                    ) AS po ON po.id_parameter = p.id 
                    WHERE p.id = :value0 
                    GROUP BY p.id 
                    LIMIT 1';

            $array = array($id);
            return $this->selectOne($sql, $array);
        }
        
        public function getParameters()
        {
            $sql = 'SELECT * FROM parameters ORDER BY parameter';

            return $this->select($sql);
        }

        public function getCrops()
        {
            $sql = 'SELECT id, crop_no, crop 
                    FROM crops 
                    WHERE id != 17 
                    ORDER BY crop';

            return $this->select($sql);
        }

        public function getParameterByNameAndType(String $name, int $type, int $id = 0)
        {
            $sql = 'SELECT id 
                    FROM parameters 
                    WHERE parameter = :value0 AND type = :value1 AND id != :value2 
                    LIMIT 1';

            $array = array($name, $type, $id);
            return $this->selectOne($sql, $array);
        }

        public function setParameter(String $parameter, int $type, int $category, String $label, int $position, String $remark, int $typeAll, int $required)
        {
            $sql = 'INSERT INTO parameters (parameter, type, category, label, position, remark, type_all, required) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7)';

            $array = array($parameter, $type, $category, $label, $position, $remark, $typeAll, $required);
            return $this->insert($sql, $array);
        }

        public function updateParameter(int $id, String $parameter, int $type, int $category, String $label, int $position, String $remark, int $typeAll, int $required, int $state)
        {
            $sql = 'UPDATE parameters SET parameter = :value1, type = :value2, category = :value3, label = :value4, position = :value5, remark = :value6, type_all = :value7, required = :value8, state = :value9 WHERE id = :value0';

            $array = array($id, $parameter, $type, $category, $label, $position, $remark, $typeAll, $required, $state);
            return $this->update($sql, $array);
        }

        public function deleteParameter(int $idParameter)
        {
            $sql = 'DELETE FROM parameters WHERE id = :value0';

            $array = array($idParameter);
            return $this->delete($sql, $array);
        }

        public function setParameterOption(int $idParameter, String $value)
        {
            $sql = 'INSERT INTO parameters_options (id_parameter, value) VALUES (:value0, :value1)';

            $array = array($idParameter, $value);
            return $this->insert($sql, $array);
        }

        public function updateParameterOption(int $id, String $value, int $state)
        {
            $sql = 'UPDATE parameters_options SET value = :value1, state = :value2 WHERE id = :value0';

            $array = array($id, $value, $state);
            return $this->update($sql, $array);
        }

        public function deleteParametersOptions(int $idParameter)
        {
            $sql = 'DELETE FROM parameters_options WHERE id_parameter = :value0';

            $array = array($idParameter);
            return $this->delete($sql, $array);
        }

        public function setParameterCrop(int $idParameter, int $idCrop)
        {
            $sql = 'INSERT INTO parameters_crops (id_parameter, id_crop) VALUES (:value0, :value1)';

            $array = array($idParameter, $idCrop);
            return $this->insert($sql, $array);
        }

        public function deleteParametersCrops(int $idParameter)
        {
            $sql = 'DELETE FROM parameters_crops WHERE id_parameter = :value0';

            $array = array($idParameter);
            return $this->delete($sql, $array);
        }
        //TODO Parameters--------------------------------------------------------------------------

        //TODO Users--------------------------------------------------------------------------
        public function getUser(int $id)
        {
            $sql = 'SELECT u.id, id_rol, user, pass, name, last_name, state, IFNULL(sc.secCusts, 0) AS secCusts  
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    LEFT JOIN (
                        SELECT id_user, GROUP_CONCAT(id_sec_cust) AS secCusts 
                        FROM users_sec_customers 
                        GROUP BY id_user 
                    ) AS sc ON sc.id_user = u.id 
                    WHERE u.id = :value0 
                    LIMIT 1';

            $array = array($id);
            return $this->selectOne($sql, $array);
        }

        public function getUsers()
        {
            $sql = 'SELECT u.id, user, name, last_name, rol, state 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN roles AS r ON r.id = u.id_rol 
                    WHERE id_country = :value0';

            $array = array($_SESSION["id_country"]);
            return $this->select($sql, $array);
        }

        public function getSecCusts()
        {
            $sql = 'SELECT s.id, id_cust, sec_cust_no, sec_cust, cust_no, cust 
                    FROM sec_customers AS s 
                    INNER JOIN customers AS c ON c.id = s.id_cust 
                    ORDER BY cust, sec_cust_no';

            return $this->select($sql);
        }

        public function getRoles()
        {
            $sql = 'SELECT * FROM roles';

            return $this->select($sql);
        }

        public function getUserByUser(String $user, int $id = 0)
        {
            $sql = 'SELECT id 
                    FROM users 
                    WHERE user = :value0 AND id != :value1 
                    LIMIT 1';

            $array = array($user, $id);
            return $this->selectOne($sql, $array);
        }

        public function setUser(int $idRol, String $user, String $pass)
        {
            $sql = 'INSERT INTO users (id_country, id_rol, id_cust, user, pass) VALUES (:value0, 1, :value1, :value2, :value3)';

            $array = array($_SESSION["id_country"], $idRol, $user, $pass);
            return $this->insert($sql, $array);
        }

        public function updateUser(int $id, int $idRol, String $user, String $pass, int $state)
        {
            $sql = 'UPDATE users SET id_rol = :value1, user = :value2, pass = :value3, state = :value4 WHERE id = :value0';

            $array = array($id, $idRol, $user, $pass, $state);
            return $this->update($sql, $array);
        }

        public function deleteUser(int $idUser)
        {
            $sql = 'DELETE FROM users WHERE id = :value0';

            $array = array($idUser);
            return $this->delete($sql, $array);
        }

        public function setUserDetail(int $iduser, String $name, String $lastName)
        {
            $sql = 'INSERT INTO users_details (id_user, name, last_name) VALUES (:value0, :value1, :value2)';

            $array = array($iduser, $name, $lastName);
            return $this->insert($sql, $array);
        }

        public function updateUserDetail(int $iduser, String $name, String $lastName)
        {
            $sql = 'UPDATE users_details SET name = :value1, last_name = :value2 WHERE id = :value0';

            $array = array($iduser, $name, $lastName);
            return $this->update($sql, $array);
        }

        public function deleteUserDetail(int $idUser)
        {
            $sql = 'DELETE FROM users_details WHERE id_user = :value0';

            $array = array($idUser);
            return $this->delete($sql, $array);
        }

        public function setUserSecCustomer(int $idUser, int $idSecCust)
        {
            $sql = 'INSERT INTO users_sec_customers (id_user, id_sec_cust) VALUES (:value0, :value1)';

            $array = array($idUser, $idSecCust);
            return $this->insert($sql, $array);
        }

        public function deleteUserSecCustomer(int $idUser)
        {
            $sql = 'DELETE FROM users_sec_customers WHERE id_user = :value0';

            $array = array($idUser);
            return $this->delete($sql, $array);
        }
        //TODO Users--------------------------------------------------------------------------

        //TODO Customers--------------------------------------------------------------------------
        public function getCustomer(int $id)
        {
            $sql = 'SELECT c.*, IFNULL(sc.secCusts, "") AS secCusts 
                    FROM customers AS c 
                    LEFT JOIN (
                        SELECT id_cust, GROUP_CONCAT(sec_cust_no, "&", sec_cust) AS secCusts 
                        FROM sec_customers 
                        GROUP BY id_cust 
                    ) AS sc ON sc.id_cust = c.id 
                    WHERE c.id = :value0 
                    LIMIT 1';

            $array = array($id);
            return $this->selectOne($sql, $array);
        }

        public function getCustomerByNumber(int $number, int $id = 0)
        {
            $sql = 'SELECT id 
                    FROM customers 
                    WHERE cust_no = :value0 AND id != :value1 
                    LIMIT 1';

            $array = array($number, $id);
            return $this->selectOne($sql, $array);
        }

        public function getCustomers()
        {
            $sql = 'SELECT * 
                    FROM customers 
                    ORDER BY cust ASC';

            return $this->select($sql);
        }

        public function updateSecCustomer(int $id, String $name, String $logo)
        {
            $sql = 'UPDATE customers SET cust = :value1, logo = :value2 WHERE id = :value0';

            $array = array($id, $name, $logo);
            return $this->update($sql, $array);
        }

        //TODO Customers--------------------------------------------------------------------------


        //TODO Orders--------------------------------------------------------------------------

        public function getOrdersByWeekById(int $year, int $week)
        {
            $sql = "SELECT od.id_order, od.id_variety, tot_quantity, tot_price, remarks, 
                        o.order_no, o.id_sec_cust, sc.sec_cust, id_type, ot.type, o.id_product, o.year, o.week, o.destination, 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN vd.visit_day
                            ELSE o.visit_day
                        END AS visit_day, 
                        IFNULL(vd.notify, 2) AS notify, 
                        product, id_crop, crop, variety, IFNULL(vd.visit_day, 0) AS datetimes, 
                        IFNULL(vts.varieties, '') AS varieties 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN orders_types AS ot ON ot.id = o.id_type 
                    INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                    INNER JOIN products AS p ON p.id = o.id_product 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 
                    INNER JOIN users AS u ON u.id = :value0 
                    LEFT JOIN visit_days aS vd ON vd.id_user = :value0 AND vd.id_order = od.id_order 

                    LEFT JOIN (
                        SELECT od.id_order, GROUP_CONCAT(v.variety) AS varieties 
                        FROM orders_details AS od 
                        INNER JOIN orders AS o ON o.id = od.id_order 
                        INNER JOIN varieties AS v ON v.id = od.id_variety 
                        INNER JOIN users AS u ON u.id = :value0 
                        LEFT JOIN visit_days aS vd ON vd.id_user = :value0 AND vd.id_order = od.id_order 
                        WHERE o.state = 0 AND 
                            CASE 
                                WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = :value0) 
                                ELSE id_sec_cust > 0 
                            END 
                            AND 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN YEAR(vd.visit_day) = :value1 AND WEEK(vd.visit_day) = :value2 
                            ELSE YEAR(o.visit_day) = :value1 AND WEEK(o.visit_day) = :value2
                        END 
                        GROUP BY od.id_order 
                    ) AS vts ON vts.id_order = o.id 

                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END 
                    AND 
                        CASE 
                            WHEN IFNULL(vd.visit_day, 0) != 0 THEN YEAR(vd.visit_day) = :value1 AND WEEK(vd.visit_day) = :value2 
                            ELSE YEAR(o.visit_day) = :value1 AND WEEK(o.visit_day) = :value2
                        END 
                             
                    GROUP BY o.id 
                    ORDER BY visit_day, order_no";

            $array = array($_SESSION["id"], $year, $week);
            return $this->select($sql, $array);
        }

        public function getOrdersTypesByWeekById(int $year, int $week)
        {
            $sql = "SELECT o.id_type, ot.type 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN orders_types AS ot ON ot.id = o.id_type 
                    INNER JOIN users AS u ON u.id = :value0 

                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END 
                        AND YEAR(o.visit_day) = :value1 AND WEEK(o.visit_day) = :value2 
                    GROUP BY o.id_type";

            $array = array($_SESSION["id"], $year, $week);
            return $this->select($sql, $array);
        }

        public function getOrdersDestinationsByWeekById(int $year, int $week)
        {
            $sql = "SELECT o.destination 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN users AS u ON u.id = :value0 

                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END 
                        AND YEAR(o.visit_day) = :value1 AND WEEK(o.visit_day) = :value2 
                    GROUP BY o.destination";

            $array = array($_SESSION["id"], $year, $week);
            return $this->select($sql, $array);
        }

        public function getOrdersCropsByWeekById(int $year, int $week)
        {
            $sql = "SELECT c.id, crop 
                    FROM orders_details AS od 
                    INNER JOIN orders AS o ON o.id = od.id_order 
                    INNER JOIN users AS u ON u.id = :value0 
                    INNER JOIN varieties AS v ON v.id = od.id_variety 
                    INNER JOIN crops AS c ON c.id = v.id_crop 

                    WHERE o.state = 0 AND 
                        CASE 
                            WHEN id_rol != 1 THEN id_sec_cust IN (SELECT id_sec_cust FROM users_sec_customers AS usc WHERE usc.id_user = u.id) 
                            ELSE id_sec_cust > 0 
                        END 
                        AND YEAR(o.visit_day) = :value1 AND WEEK(o.visit_day) = :value2 
                    GROUP BY c.id";

            $array = array($_SESSION["id"], $year, $week);
            return $this->select($sql, $array);
        }

        public function getOrderVisitDay(int $idOrder)
        {
            $sql = 'SELECT id, notify 
                    FROM visit_days 
                    WHERE id_user = :value0 AND id_order = :value1 
                    LIMIT 1';

            $array = array($_SESSION['id'], $idOrder);
            return $this->selectOne($sql, $array);
        }

        public function setOrderVisitDay(int $idOrder, String $datetime, int $notify)
        {
            $sql = 'INSERT INTO visit_days (id_user, id_order, visit_day, notify) VALUES (:value0, :value1, :value2, :value3)';

            $array = array($_SESSION['id'], $idOrder, $datetime, $notify);
            return $this->insert($sql, $array);
        }

        public function updateOrderVisitDay(int $id, String $datetime, $notify)
        {
            $sql = 'UPDATE visit_days SET visit_day = :value1, notify = :value2 WHERE id = :value0';

            $array = array($id, $datetime, $notify);
            return $this->update($sql, $array);
        }

        /* public function getAllOrders()
        {
            $sql = "SELECT id, year, week, destination 
                    FROM orders";

            return $this->select($sql);
        }

        public function updateOrderVisitDay2(int $id, String $datetime)
        {
            $sql = 'UPDATE orders SET visit_day = :value1 WHERE id = :value0';

            $array = array($id, $datetime);
            return $this->update($sql, $array);
        } */

        //TODO Orders--------------------------------------------------------------------------

        //TODO Test optimized images
        public function getEvaluatedImages(int $year1, string $week1, int $year2, string $week2)
        {
            $sql = "SELECT 
                        CONCAT( 
                            YEAR(oc.date), 
                            CASE 
                                WHEN WEEK(oc.date) < 10 
                                    THEN CONCAT('0', WEEK(oc.date)) 
                                ELSE WEEK(oc.date) 
                            END 
                        ) AS weekUnion, 
                        op.value 
                    FROM orders_parameters AS op 
                    INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order AND oc.id_user = op.id_user 
                    INNER JOIN parameters AS p ON p.id = op.id_parameter 
                    WHERE p.type = 4 AND 
                        CONCAT( 
                            YEAR(oc.date), 
                            CASE 
                                WHEN WEEK(oc.date) < 10 
                                    THEN CONCAT('0', WEEK(oc.date)) 
                                ELSE WEEK(oc.date) 
                            END 
                        ) BETWEEN CONCAT(:value0, :value1) AND CONCAT(:value2, :value3) 
                    ORDER BY year, week;";

            $array = array($year1, $week1, $year2, $week2);
            return $this->select($sql, $array);
        }
    }