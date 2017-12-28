<?php

/**
 * Модель формы авторизации
 * 
 * @author Alexandr Kirshin <kirshin.as@gmail.com>
 */
class Region extends CFormModel {

    public static function getByPk($pk) {
        $regions = self::getRegions();
		if(!isset($regions[$pk])) return NULL;
        return $regions[$pk];
    }
    
    public static function getRegionsList() {
        $regions = self::getRegions();
        return $regions;
    }

    private static function getRegions() {
        $content = file_get_contents(dirname(__FILE__) . "/../files/regions.txt");
        $rows = explode("\n", $content);
        $result = array();
        foreach ($rows as $row) {
            $r = explode("\t", trim($row), 2);

            if (strlen( trim($r[1]) )) {
                $result[$r[0]] = trim($r[1]);
            }
        }
        return $result;
    }

}

?>
