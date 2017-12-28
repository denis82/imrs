<?php

class CMSCheck {
    private $_host = '';
    private $_page = '';
    private $_robots = '';

    public static $items = array(
        'bitrix' => '1С Битрикс',
        'wordpress' => 'Wordpress',
        'drupal' => 'Drupal',
        'dle' => 'DataLife Engine (DLE)',
        'joomla' => 'Joomla',
        'modx' => 'ModX',
        'woocommerce' => 'WooCommerce',
        'opencart' => 'OpenCart',
        'prestashop' => 'PrestaShop',
        'wix' => 'Wix',
    );

    public function __construct( $url ) {
        $this->_host = $url;
        $this->_page = @file_get_contents($url);
        $this->_robots = @file_get_contents($url . '/robots.txt');
    }

    public function checkAll() {
        $result = array();

        foreach (self::$items as $j => $i) {
            $action = 'check' . $j;

            if ($this->$action()) {
                $result[] = $j;
            }
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

    public function checkBitrix() {
        $p = strpos($this->_page, '/bitrix/');

        if ($p === false) {
            $p = strpos($this->_robots, '/bitrix/');
        }

        return is_numeric($p);
    }

    public function checkWordpress() {
        $p = strpos($this->_page, '/wp-content/');

        if ($p === false) {
            $p = strpos($this->_robots, '/wp-includes/');
        }

        return is_numeric($p);
    }

    public function checkDrupal() {
        if (strpos($this->_page, 'Drupal.settings') !== false) {
            return true;
        }

        $txt = @file_get_contents($this->_host . '/CHANGELOG.txt');
        if ($txt and strpos($txt, 'Drupal ') !== false) {
            return true;
        }

        $txt = @file_get_contents($this->_host . '/misc/drupal.js');
        if ($txt and strpos($txt, 'Drupal ') !== false) {
            return true;
        }

        return false;
    }

    public function checkDLE() {
        if (strpos($this->_page, 'DataLife Engine') !== false) {
            return true;
        }

        if (strpos($this->_page, 'dle_admin') !== false) {
            return true;
        }

        if (strpos($this->_robots, '/engine/go.php') !== false) {
            return true;
        }

        return false;
    }

    public function checkJoomla() {
        if (strpos($this->_robots, 'Joomla ') !== false) {
            return true;
        }

        if (
            strpos($this->_robots, '/administrator/') !== false and
            strpos($this->_robots, '/cli/') !== false
        ) {
            return true;
        }

        return false;
    }

    public function checkModx() {
        if (
            strpos($this->_robots, '/core/') !== false and
            strpos($this->_robots, '/manager/') !== false
        ) {
            return true;
        }

        $txt = @file_get_contents($this->_host . '/manager/assets/modext/core/modx.js');
        if ($txt and strpos($txt, 'MODx') !== false) {
            return true;
        }

        $txt = @file_get_contents($this->_host . '/manager/');
        if ($txt and strpos($txt, 'MODX') !== false) {
            return true;
        }

        return false;
    }

    public function checkWoocommerce() {
        if (
            strpos($this->_page, '/woocommerce') !== false or
            strpos($this->_page, 'woocommerce-') !== false
        ) {
            return true;
        }

        return false;
    }

    public function checkOpencart() {
        $txt = @file_get_contents($this->_host . '/admin/');
        if ($txt and strpos(strtolower($txt), 'opencart') !== false) {
            return true;
        }

        return false;
    }

    public function checkPrestashop() {
        if (
            strpos($this->_page, 'prestashop') !== false or
            strpos($this->_robots, 'PrestaShop') !== false or
            strpos($this->_robots, 'Disallow: /*controller=') !== false
        ) {
            return true;
        }

        return false;
    }

    public function checkWix() {
        if (
            strpos($this->_page, 'wix.com') !== false or
            strpos($this->_page, 'wixstatic.com') !== false or
            strpos($this->_robots, 'Disallow: /wix/')
        ) {
            return true;
        }

        return false;
    }

}
