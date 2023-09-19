<?php

class QrModel extends Mysql
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getData($idEvent)
    {
        $sql = "SELECT e.*, v.variety, cg.crop_general, c.id AS crop FROM events_maps AS e
                INNER JOIN varieties AS v ON v.id = e.id_variety 
                INNER JOIN crops AS c ON c.id = v.id_crop 
                INNER JOIN crops_generals AS cg ON cg.id = c.id_crop_general 
                WHERE id_event = :value0 AND year = 2023 
                ORDER BY cg.id, greenhouse DESC, position + 0";

        $array = array($idEvent);

        return $this->select($sql, $array);
    }

}