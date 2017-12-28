<?php

class SeoUtils {
	
	public static function isValidIP($ip) {
		$ip = trim($ip);

		if (!$ip)
			return false;

		return preg_match('~^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$~', $ip)?true:false;
	}
	
	function validDomain($domain_name){
		return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
	}
	
    public static function normalizeHost($domain) {
        $a = parse_url($domain);

        if (!$a['scheme']) {
            $a['scheme'] = 'http';
            list($a['host'], $a['path']) = explode('/', $a['path'], 2);
        }

        return $a['scheme'] . '://' . $a['host'];
    }

    public static function normalizeDomain($domain, $http = true) {
        $domain = basename($domain);
        $domain = str_replace("www.", "", $domain);
        if ($http)
            return "http://" . $domain;
        else
            return $domain;
    }

    public static function getMirrorDomain($domain) {
        $domain = basename($domain);
        if (strpos($domain, "www.") === 0)
            return substr($domain, 3);
        else
            return "www." . $domain;
    }

    /**
     * Проверить домен является зеркалом или нет
     * @param string $domain
     */
    public static function checkMirrorYandex($domain) {
        $content = CUtils::getContent("http://webmaster.yandex.ru/check.xml?hostname=$domain");
        return strpos($content, "Сайт является зеркалом");
    }

    /**
     * Проверить url существует или нет
     * @param string $domain
     */
    public static function testUrl($url, $bool = false) {
        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false
                )
            )
        );

        $code = 0;

        @file_get_contents($url, false, $context);

        if ($http_response_header and is_array($http_response_header)) {
	        foreach ($http_response_header as $h) {
	            $m = array();

	            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
	                $code = intval($m[1]);
	                break;
	            }
	        }
        }

        if ($bool) {
            return ($code == 200);
        }
        else {
            return $code;
        }

    }

    /**
     * Поличить ТиЦ яндекса
     * @param string $domain
     * @return int
     */
    public static function getTIC($domain) {
        $url = self::normalizeDomain($domain);
        $ci_url = "http://bar-navig.yandex.ru/u?ver=2&show=32&url=$url";
        $ci_data = implode("", file("$ci_url"));
        preg_match("/value=\"(.\d*)\"/", $ci_data, $ci);
        if ($ci[1] == "")
            return 0;
        else
            return $ci[1];
    }

    function getYac($domain) {
        
        $codepage = 'utf-8';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://yaca.yandex.ru/yca?text=http%3A%2F%2F" . $domain . "&yaca=1");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/html; charset=UTF-8"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.0 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $source = curl_exec($curl);
        curl_close($curl);
        $source = mb_convert_encoding($source, 'HTML-ENTITIES', $codepage);
        $source = str_replace("\r", "", $source);
        $source = str_replace("\n", "", $source);
        if ($source) {
            preg_match('/<div class=\"z\-counter\">.*?(\d{1,5})<\/div>/', $source, $out);
            if (trim($out[1]) > 0)
                return 1;
        }
        return 0;
    }

    /**
     * Поличить количество страниц в индексе яндекса
     * @param string $domain
     */
    public static function getPagesInYandexIndex($domain) {
        $content = CUtils::getContent("http://webmaster.yandex.ru/check.xml?hostname=$domain");
        if (strpos($content, "Сайт является зеркалом")) {
            $content = CUtils::getContent("http://webmaster.yandex.ru/check.xml?hostname=" . self::getMirrorDomain($domain));
        }
        preg_match('/<div class="header g-line">.*?<\/div>/', $content, $match);
        return trim(str_replace("Страницы:", "", strip_tags($match[0])));
    }

    public static function getWhois($domain) {
        $content = CUtils::getContent("https://www.nic.ru/whois/?query=" . $domain);
        $content = str_replace("\n", "", $content);
        $content = str_replace("\r", "", $content);
        $content = str_replace("\t", "", $content);
        $content = preg_replace("/<script.*?<\/script>/", "", $content);
        preg_match('/<div class="b-whois-info__info">.*?<\/div>/', $content, $match);
        $whois = $match[0];
        $result = array();
        $result["ns"] = array();
        if ($whois) {
            $whois = str_replace("<br>", "<br/>", $whois);
            $whois = str_replace("&nbsp;", " ", $whois);
            $rows = explode("<br/>", $whois);

            foreach ($rows as $row) {
                $row = trim($row);
                if (strpos(trim($row), "Domain name:") === 0) {
                    $result["domain"] = preg_replace('/[^\w\.\-\_\d]+/', "", str_replace("Domain name:", "", $row));
                }
                if (strpos(trim($row), "domain:") === 0) {
                    $result["domain"] = preg_replace('/[^\w\.\-\_\d]+/', "", str_replace("domain:", "", $row));
                }
                if (strpos(trim($row), "Name Server:") === 0) {
                    $result["ns"][] = preg_replace('/[^\w\.\-\_\d]+/', "", str_replace("Name Server:", "", $row));
                }
                if (strpos(trim($row), "nserver:") === 0) {
                    $result["ns"][] = preg_replace('/[^\w\.\-\_\d]+/', "", str_replace("nserver:", "", $row));
                }
                if (strpos(trim($row), "nserver:") === 0) {
                    $result["ns"][] = preg_replace('/[^\w\.\-\_\d]+/', "", str_replace("nserver:", "", $row));
                }
                if (strpos(trim($row), "Creation Date:") === 0) {
                    $result["created"] = self::normalizeDbDate(str_replace("Creation Date:", "", $row));
                }

                if (strpos(trim($row), "Creation Date:") === 0) {
                    $result["created"] = self::normalizeDbDate(str_replace("Creation Date:", "", $row));
                }
                if (strpos(trim($row), "created:") === 0) {

                    $result["created"] = self::normalizeDbDate(str_replace("created:", "", $row));
                }
                if (strpos(trim($row), "Domain Registration Date:") === 0) {

                    $result["created"] = self::normalizeDbDate(str_replace("Domain Registration Date:", "", $row));
                }

                if (strpos(trim($row), "Created On:") === 0) {

                    $result["created"] = self::normalizeDbDate(str_replace("Created On:", "", $row));
                }


                if (strpos(trim($row), "paid-till:") === 0) {
                    $result["updated"] = self::normalizeDbDate(str_replace("paid-till:", "", $row));
                }

                if (strpos(trim($row), "Updated Date:") === 0) {
                    $result["updated"] = self::normalizeDbDate(str_replace("Updated Date:", "", $row));
                }

                if (strpos(trim($row), "Domain Last Updated Date:") === 0) {
                    $result["updated"] = self::normalizeDbDate(str_replace("Domain Last Updated Date:", "", $row));
                }

                if (strpos(trim($row), "Last Updated On:") === 0) {
                    $result["updated"] = self::normalizeDbDate(str_replace("Last Updated On:", "", $row));
                }



                if (strpos(trim($row), "Expiration Date:") === 0) {
                    $result["expiration"] = self::normalizeDbDate(str_replace("Expiration Date:", "", $row));
                }
                if (strpos(trim($row), "free-date:") === 0) {
                    $result["expiration"] = self::normalizeDbDate(str_replace("free-date:", "", $row));
                }

                if (strpos(trim($row), "Domain Expiration Date:") === 0) {
                    $result["expiration"] = self::normalizeDbDate(str_replace("Domain Expiration Date:", "", $row));
                }




                if (strpos(trim($row), "Registrar:") === 0) {
                    $result["registrar"] = strip_tags(str_replace("Registrar:", "", $row));
                }
                if (strpos(trim($row), "registrar:") === 0) {
                    $result["registrar"] = strip_tags(str_replace("registrar:", "", $row));
                }
                if (strpos(trim($row), "Registrant Name:") === 0) {
                    $result["registrar"] = strip_tags(str_replace("Registrant Name:", "", $row));
                }
            }
        }
        $result["ns"] = implode(",", $result["ns"]);
        foreach ($result as $code => $value) {
            $result[$code] = trim($value);
        }
        return $result;
    }

    /**
     * Форматирование даты
     * @param string $date
     * @return string
     */
    public static function normalizeDate($date) {
        $date = preg_replace('/[^\d\.]+/', "", $date);
        $arDate = explode(".", $date);
        if (strlen(trim($arDate[0])) == 4 && count($arDate) == 3) {
            return implode(".", array_reverse($arDate));
        }
        return $date;
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

    public static function getMedian($nset) {        
        sort($nset);
        $count = count($nset);
        if ($count == 0)
            return false;
        if ($count % 2) {
            return $nset[ceil($count / 2)];
        } else {
            $count--;
            return ($nset[floor($count / 2)] + $nset[ceil($count / 2)]) / 2;
        }
    }

    public static function getWordStat($keyword, $regions) {
        $codes = implode(",", array_keys($regions));
        $names = implode(",+", $regions);
        $url = "http://wordstat.yandex.ru/?cmd=words&page=1&t=" . urlencode($keyword) . "&geo=" . urlencode($codes) . "&text_geo=" . urlencode($names);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        preg_match_all('/(\d{1,10})\&nbsp\;показов в\&nbsp\;месяц\./', $html, $return);
        if (isset($return[1][0]))
            return $return[1][0];
        else
            return '0';
    }

    public static function getTop50($domain) {
        $url = "http://solomono.ru/tools/Default.aspx?t=ps&i=$domain";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        preg_match('/<tr><td>c 1 по 50<\/td><td>(.*?)<\/td><\/tr>/', $html, $match);
        return strip_tags($match[1]);
    }

    public static function getCitationTrustFlow($domain) {
        $url = "http://ru.majesticseo.com/reports/site-explorer/summary/$domain?IndexDataSource=F";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        $html = str_replace("\n", "", $html);
        $html = str_replace("\r", "", $html);
        $html = str_replace("\t", "", $html);
        preg_match('/<p>.*?Поток <br>цитирования \(Citation Flow\).*?<\/p><p.*?><b>(.*?)<\/b><\/p>/', $html, $match);
        $citation = $match[1];
        preg_match('/<p>.*?Поток <br>доверия \(Trust flow\).*?<\/p><p.*?><b>(.*?)<\/b><\/p>/', $html, $match);
        $trust = $match[1];
        return array($citation, $trust);
    }

}

