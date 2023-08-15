<?php

class QrModel extends Mysql
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getData()
    {
        $sql = "SELECT e.*, v.variety, cg.crop_general FROM events_maps AS e
                INNER JOIN varieties AS v ON v.id = e.id_variety 
                INNER JOIN crops AS c ON c.id = v.id_crop 
                INNER JOIN crops_generals AS cg ON cg.id = c.id_crop_general 
                ORDER BY greenhouse DESC, position";

        return $this->select($sql);
    }

}