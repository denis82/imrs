<?php

class CUtils {

    public static function getContent($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36");
        return curl_exec($curl);
    }

    /**
     * Форматирование даты
     * @param string $date
     * @return string
     */
    public static function normalizeDbDate($date) {
        if (is_int(strpos($date, "."))) {
            $date = preg_replace('/[^\d\.]+/', "", $date);
            $arDate = explode(".", $date);
            if (strlen(trim($arDate[0])) == 4 && count($arDate) == 3) {
                $date = implode(".", array_reverse($arDate));
            }
        }
        return date("Y-m-d", strtotime($date));
    }

    public static function getMirrorDomain($domain) {
        $domain = basename($domain);
        if (strpos($domain, "www.") === 0)
            return substr($domain, 3);
        else
            return "www." . $domain;
    }

    public static function normalizeDomain($domain, $http = true) {
        $domain = basename($domain);
        $domain = str_replace("www.", "", $domain);
        if ($http)
            return "http://" . $domain;
        else
            return $domain;
    }

    public static function traceValidationErrors($model) {
        $log = "";
        $log .= "------------------attributes----------------\n";
        foreach ($model->attributes as $key => $message) {
            $log .= sprintf('%s:%s\n', $key, is_array($message)? json_encode($message) : $message);
        }
        $log .= "------------------errors--------------------\n";
        foreach ($model->errors as $key => $message) {
            //$log .= "$key:$message\n";
			$log .= sprintf('%s:%s\n', $key, is_array($message)? json_encode($message) : $message);
        }
        //Yii::trace($log);
    }

     public static function getMedian($set) {
        $nset = array();
        foreach ($set as $v) {
            if ($v != "-")
                $nset[] = $v;
        }
        sort($nset);
        $count = count($nset);
        if ($count == 0)
            return false;
        if ($count % 2) {
            return $nset[ceil($count / 2)];
        } else {
            $count--;
            return ceil( ($nset[floor($count / 2)] + $nset[ceil($count / 2)]) / 2);
        }
    }
}

?>
