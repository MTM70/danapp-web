<?php

    class DashboardModel extends Mysql{

        public function __construct()
        {
            parent::__construct();
        }

        public function getOrderByNo(int $orderNo)
        {
            $sql = 'SELECT id, order_no 
                    FROM orders 
                    WHERE order_no = :value0 
                    LIMIT 1';

            $array = array($orderNo);
            return $this->selectOne($sql, $array);
        }

        public function setOrder(int $orderNo, int $idSecCust, int $idType, int $idProduct, int $year, int $week, String $destination, String $remarks)
        {
            $sql = 'INSERT INTO orders (order_no, id_sec_cust, id_type, id_product, year, week, destination, remarks) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6, :value7)';

            $array = array($orderNo, $idSecCust, $idType, $idProduct, $year, $week, $destination, $remarks);
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

        public function setCustomer(int $custNo, String $cust)
        {
            $sql = 'INSERT INTO customers (cust_no, cust) VALUES (:value0, :value1)';

            $array = array($custNo, $cust);
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
        
        public function getVarietyByNo(int $number)
        {
            $sql = 'SELECT id 
                    FROM varieties 
                    WHERE variety_code = :value0 
                    LIMIT 1';

            $array = array($number);
            return $this->selectOne($sql, $array);
        }

        public function setVariety(int $idCrop, int $varietyCode, String $variety)
        {
            $sql = 'INSERT INTO varieties (id_crop, variety_code, variety) VALUES (:value0, :value1, :value2)';

            $array = array($idCrop, $varietyCode, $variety);
            return $this->insert($sql, $array);
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

        public function getDataBetweenWeeks2(int $year1, int $week1, int $year2, int $week2)
        {
            $sql = 'SELECT id_order, id_variety, id_parameter, value, obs 
                    FROM orders_parameters AS op 
                    INNER JOIN orders AS o ON o.id = op.id_order 
                    WHERE (op.year BETWEEN :value0 AND :value2) AND (op.week BETWEEN :value1 AND :value3) 
                    ORDER BY order_no';

            $array = array($year1, $week1, $year2, $week2);
            return $this->select($sql, $array);
        }

        public function getOrdersParametersBetweenWeeks(int $year1, int $week1, int $year2, int $week2)
        {
            $sql = 'SELECT id_order, id_variety, id_order, op.year, op.week, order_no, destination, cust_no, cust, sec_cust_no, sec_cust, 
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
                    WHERE (op.year BETWEEN :value0 AND :value2) AND (op.week BETWEEN :value1 AND :value3) 
                    GROUP BY id_order, v.id 
                    ORDER BY order_no';

            $array = array($year1, $week1, $year2, $week2);
            return $this->select($sql, $array);
        }

        public function getParameters()
        {
            $sql = 'SELECT id, parameter, type, category, label, position, remark 
                    FROM parameters';

            return $this->select($sql);
        }

        public function getUsers()
        {
            $sql = 'SELECT u.id, user, name, last_name, rol 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN roles AS r ON r.id = u.id_rol';

            return $this->select($sql);
        }

    }