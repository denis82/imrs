<?php

class LiveInternet {

    public static function getStat($host) {
		$host = trim($host);

        $src = imagecreatefromgif("http://counter.yadro.ru/logo;$host?29.1");

        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 27, 44, 5);
        $mh = self::getValue($dest);        
        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 34, 44, 5);
        $mv = self::getValue($dest);
        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 46, 44, 5);
        $wh = self::getValue($dest);
        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 53, 44, 5);
        $wv = self::getValue($dest);
        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 65, 44, 5);
        $dh = self::getValue($dest);
        $dest = imagecreatetruecolor(44, 5);
        imagecopy($dest, $src, 0, 0, 38, 72, 44, 5);
        $dv = self::getValue($dest);
        return array($mh, $mv, $wh, $wv, $dh, $dv);
    }

    /*
      $dest = imagecreatetruecolor(44, 5);
      imagecopy($dest, $src, 0, 0, 38, 84, 44, 5);
      echo getValue($dest) . "<br/>";
      $dest = imagecreatetruecolor(44, 5);
      imagecopy($dest, $src, 0, 0, 38, 91, 44, 5);

      echo getValue($dest) . "<br/>";

      $dest = imagecreatetruecolor(44, 5);
      imagecopy($dest, $src, 0, 0, 38, 103, 44, 5);

      echo getValue($dest) . "<br/>";
      $dest = imagecreatetruecolor(44, 5);
      imagecopy($dest, $src, 0, 0, 38, 110, 44, 5);
      echo getValue($dest) . "<br/>";
     * 
     */

    static function getValue($dest) {
        $matrix = array();
        for ($x = 0; $x < imagesx($dest); $x++) {
            for ($y = 0; $y < 5; $y++) {
                $color = imagecolorat($dest, $x, $y);
                if ($color == 0) {
                    $matrix[$x][$y] = 1;
                } else
                    $matrix[$x][$y] = 0;
            }
        }
        $digitsMatrix = array(
            1 =>
            array(
                array(0, 1, 0),
                array(1, 1, 0),
                array(0, 1, 0),
                array(0, 1, 0),
                array(1, 1, 1),
            ),
            2 =>
            array(
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(1, 1, 1, 1),
                array(1, 0, 0, 0),
                array(1, 1, 1, 1),
            ),
            3 =>
            array(
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(0, 1, 1, 1),
                array(0, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
            4 =>
            array(
                array(1, 0, 0, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(0, 0, 0, 1),
            ),
            5 =>
            array(
                array(1, 1, 1, 1),
                array(1, 0, 0, 0),
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
            6 =>
            array(
                array(1, 1, 1, 1),
                array(1, 0, 0, 0),
                array(1, 1, 1, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
            7 =>
            array(
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(0, 0, 0, 1),
                array(0, 0, 0, 1),
                array(0, 0, 0, 1),
            ),
            8 =>
            array(
                array(1, 1, 1, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
            9 =>
            array(
                array(1, 1, 1, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
                array(0, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
            0 =>
            array(
                array(1, 1, 1, 1),
                array(1, 0, 0, 1),
                array(1, 0, 0, 1),
                array(1, 0, 0, 1),
                array(1, 1, 1, 1),
            ),
        );
        $result = "";

        $start = 0;
        while ($start < count($matrix)) {
            $dig = self::findDigit($matrix, $start);
            foreach ($digitsMatrix as $d => $m) {
                if (self::array_compare($dig, $m)) {
                    $result .=$d;
                }
            }
        }
        return $result;
    }

    static function findDigit($matrix, &$start) {
        $blok = array();
        $started = false;
        $nx = 0;
        for ($x = $start; $x < count($matrix); $x++) {
            $exist = false;
            for ($y = 0; $y < 5; $y++) {
                if ($matrix[$x][$y] == 1) {
                    $exist = true;
                }
            }
            if ($exist) {
                $started = true;
                for ($y = 0; $y < 5; $y++) {
                    $blok[$y][$nx] = $matrix[$x][$y];
                }
                $nx++;
            } else {
                if ($started) {
                    $start = $x;
                    return $blok;
                }
            }
        }
        $start = count($matrix);
        return $blok;
    }

    public static function array_compare($array1, $array2) {
        for ($x = 0; $x < count($array1); $x++) {
            for ($y = 0; $y < 5; $y++){
				if(!isset($array1[$x][$y])) $array1[$x][$y] = NULL;
				if(!isset($array2[$x][$y])) $array2[$x][$y] = NULL;
                if ($array1[$x][$y] != $array2[$x][$y])
                    return false;
            }
        }
        return true;
    }

}
