<?php

class ApiModel extends Mysql
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUser(string $user, string $pass)
    {
        $sql = "SELECT u.id, u.id_rol, rol, u.id_cust, cust_no, cust, user, ud.name, last_name 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN customers AS c ON c.id = u.id_cust 
                    INNER JOIN roles AS r ON r.id = u.id_rol 
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
        $sql = "SELECT * FROM varieties";

        return $this->select($sql);
    }

    public function getProducts()
    {
        $sql = "SELECT * FROM products";

        return $this->select($sql);
    }

    public function getSecCustomers()
    {
        $sql = "SELECT id, id_cust, sec_cust_no, UPPER(s.sec_cust) AS sec_cust FROM sec_customers AS s";

        return $this->select($sql);
    }

    public function getOrders()
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
    }

    public function getOrdersParameters($idUser)
    {
        $path = BASE_URL."uploads/";
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
        $sql = "SELECT * FROM parameters WHERE state = 1";

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

    public function deleteOrderParameter(int $idOder, int $idVariety, int $idParameter)
    {
        $sql = "DELETE FROM orders_parameters WHERE id_order = :value0 AND id_variety = :value1 AND id_parameter = :value2";
        $array = array($idOder, $idVariety, $idParameter);

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

    public function setOrder(int $orderNo, int $idSecCust, int $idType, int $idProduct, int $year, int $week, String $destination)
    {
        $sql = "INSERT INTO orders (order_no, id_sec_cust, id_type, id_product, year, week, destination) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6)";
        $array = array($orderNo, $idSecCust, $idType, $idProduct, $year, $week, $destination);

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

}