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
                    WHERE user = :value0 AND pass = :value1";

        $array = array($user, $pass);

        return $this->selectOne($sql, $array);
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
                    WHERE finished = 0 
                    ORDER BY id_order";

        return $this->select($sql);
    }

    public function getOrdersTypes()
    {
        $sql = "SELECT * FROM orders_types";

        return $this->select($sql);
    }

    public function getParameters()
    {
        $sql = "SELECT * FROM parameters";

        return $this->select($sql);
    }

    public function getParametersOptions()
    {
        $sql = "SELECT * FROM parameters_options";

        return $this->select($sql);
    }

    public function getOrderParameter(int $idOder, int $idVariety, int $idParameter)
    {
        $sql = "SELECT id FROM orders_parameters WHERE id_order = :value0 AND id_variety = :value1 AND id_parameter = :value2";
        $array = array($idOder, $idVariety, $idParameter);

        return $this->selectOne($sql, $array);
    }

    public function setDataSync(int $idOder, int $idVariety, int $idParameter, String $value, String $obs, int $year, int $week)
    {
        $sql = "INSERT INTO orders_parameters (year, week, id_order, id_variety, id_parameter, value, obs) VALUES (:value5, :value6, :value0, :value1, :value2, :value3, :value4)";
        $array = array($idOder, $idVariety, $idParameter, $value, $obs, $year, $week);

        return $this->insert($sql, $array);
    }

    public function updateDataSync(int $id, String $value, String $obs)
    {
        $sql = "UPDATE orders_parameters SET value = :value1, obs = :value2 WHERE id = :value0";
        $array = array($id, $value, $obs);

        return $this->update($sql, $array);
    }

}