<?php

class CountersCheck {
    private $_host = '';
    private $_page = '';

    public static $items = array(
        'yandex' => 'Яндекс.Метрика',
        'google' => 'Google Analytics',
        'rambler' => 'Рамблер ТОП100',
        'mailru' => 'Mail.ru',
        'li' => 'LiveInternet',
        'hotlog' => 'Hotlog',
    );

    public function __construct( $url ) {
        $this->_host = $url;
        $this->_page = @file_get_contents($url);
    }

    public function checkAll() {
        $result = array();

        foreach (self::$items as $j => $i) {
            $action = 'check' . $j;
            $result[$j] = $this->$action();
        }

        return $result;
    }

    public function check( $name ) {
        if (self::$items[$name]) {
            $action = 'check' . $name;
            return $this->$action();
        }

        return false;
    }

    public function checkYandex() {
        $m = array();
        preg_match_all('/new Ya\.Metrika\(\s*{\s*id:([0-9]+),/si', $this->_page, $m);

        if (count($m[1]) > 1) {
            $r = array();

            foreach ($m[1] as $i) {
                if (!in_array($i, $r)) {
                    $r[] = $i;
                }
            }

            return $r;
        }
        else {
            return $m[1][0];
        }
    }

    public function checkGoogle() {
        $result = array();

        $m = array();
        preg_match_all('/ga\(\'create\', \'([A-Za-z0-9\-]+)\', \'auto\'/si', $this->_page, $m);

        if (count($m[1]) > 1) {
            return $m[1];
        }
        elseif (count($m[1])) {
            return $m[1][0];
        }

        $m = array();
        preg_match_all('/<script[^>]+>(|.+?)<\/script>/si', $this->_page, $m);

        if ($m[1] and is_array($m[1])) {
            foreach ($m[1] as $i) {
                if (strpos($i, 'google-analytics.com') !== false) {
                    $n = array();
                    preg_match_all('/\(\[\'_setAccount\', \'([A-Za-z0-9\-]+)\'\]\)/si', $i, $n);

                    if (is_array($n[1])) {
                        foreach ($n[1] as $l) {
                            $result[] = $l;
                        }
                    }
                }
            }
        }

        if (count($result) > 1) return $result;
        else return $result[0];
    }

    public function checkRambler() {
        $m = array();

        preg_match_all('/\/\/counter\.rambler\.ru\/top100\.(jcn|cnt)\?pid=([0-9]+)/si', $this->_page, $m);

        if (count($m[1]) > 1) {
            $r = array();

            foreach ($m[1] as $i) {
                if (!in_array($i, $r)) {
                    $r[] = $i;
                }
            }

            return $r;
        }
        else {
            return $m[1][0];
        }

    }

    public function checkMailru() {
        $m = array();
        preg_match_all('/mail\.ru\/counter\?id=([0-9]+)/si', $this->_page, $m);

        if (count($m[1]) > 1) {
            $r = array();

            foreach ($m[1] as $i) {
                if (!in_array($i, $r)) {
                    $r[] = $i;
                }
            }

            return $r;
        }
        else {
            return $m[1][0];
        }
    }

    public function checkLi() {
        $m = array();
        preg_match_all('/\/\/counter\.yadro\.ru\/hit/si', $this->_page, $m);

        if (count($m[0]) > 0) {
            return '1';
        }
    }

    public function checkHotlog() {
        $m = array();
        preg_match_all('/\/\/js\.hotlog\.ru\/dcounter\/([0-9]+)/si', $this->_page, $m);

        if (count($m[1]) > 1) {
            $r = array();

            foreach ($m[1] as $i) {
                if (!in_array($i, $r)) {
                    $r[] = $i;
                }
            }

            return $r;
        }
        else {
            return $m[1][0];
        }
    }

}
