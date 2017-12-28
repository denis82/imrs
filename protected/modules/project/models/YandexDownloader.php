<?php

class SitemapDownloader {
    private $queue = array();

    public $names = array();
    public $error_code = 0;

    public function __construct( $url ) {
        if (is_array($url)) {
            $this->queue = $url;
        }
        else {
            $this->queue[] = $url;
        }
    }

    public function load() {
        $exists = false;
        $this->names = array();

        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false
                )
            )
        );

        for ($j = 0; $j < count($this->queue); $j++) {
        	$url = $this->queue[$j];

            $data = @file_get_contents($url, false, $context);

            if ($data) {

				if (strpos($data, '<?xml') === false) {
					$ungz = @gzdecode($data);

					if ($ungz) {
						$data = $ungz;
					}
				}

                $exists = true;

                $a = array();
                $index = array();

                $p = xml_parser_create();
                xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
                $xml_result = xml_parse_into_struct($p, $data, $a, $index);
                xml_parser_free($p);

                if ($xml_result) {
                	if ($index['SITEMAPINDEX']) {
	                	foreach ($index['LOC'] as $i) {
	                		$this->queue[] = $a[$i]['value'];
	                	}
                	}
                	else {
	                	foreach ($index['LOC'] as $i) {
                            $this->names[] = $a[$i]['value'];
	                	}
                	}
                }
            }
        }

        if (!$exists) {
        	if (is_array($http_response_header)) {
	            foreach ($http_response_header as $h) {
	                $m = array();

	                if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
	                    $status = intval($m[1]);
	                    break;
	                }
	            }
        	}

            if ($status and $status != 200) {
                $this->error_code = $status;
            }
        }

        return (count($this->names) or $exists);
    }

    public function save( $domain_id = 0 ) {
        if (count($this->names) == 0) {
            return false;
        }

        $sql = "insert ignore into tbl_sitemap (domain_id, url, hash) values ";

        $parameters = array(
            'did' => $domain_id,
        );

        foreach ($this->names as $i => $j) {
            if ($i > 0) $sql .= ', ';

            $sql.= '(:did, :url' . $i . ', :hash' . $i . ')';

            $parameters['url' . $i] = $j;
            $parameters['hash' . $i] = md5( $domain_id . $j );
        }

        Yii::app()->db->createCommand($sql)->execute($parameters);
    }

}
