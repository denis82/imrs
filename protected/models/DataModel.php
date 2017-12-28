<?php

class DataModel
{
	private $project;

	public function __construct( $project ) {
		if (is_numeric($project)) {
			$this->project = Project::model()->findByPk( $project );
		}
		elseif ($project instanceof Project) {
			$this->project = $project;
		}
	}

	public function usabilityFonts() {
		$model = $this->project;

        $opts = array('http' =>
            array(
                'method'  		=> 'GET',
                'user_agent' 	=> $_SERVER['HTTP_USER_AGENT'],
                'timeout' 		=> 60,
            )
        );

        $context  = stream_context_create($opts);

        $css_list = array();

        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_CSS)) as $css) {
            $css_list[] = $css->url;
        }

        $css_data = array();
        for ($j = 0; $j < count($css_list); $j++) {
            $url = $css_list[$j];

            if (substr($url, 0, 2) == '//') {
                $url = $page_url['scheme'] . ':' . $url;
            }

            $p = parse_url($url);

            $external_css = false;

            if ($p['host']) {
                $url = $url;

                if ($p['host'] != $page_url['host']) {
                    $external_css = true;
                }
            }
            else {
                $url = $base_host . ( ($p['path'][0] == '/') ? $p['path'] : $base_dir . $p['path']);
            }

            $css = @file_get_contents($url, false, $context);

            if ($css) {
                $css_data[$url] = $css;

                if (preg_match_all('/\@import(.*?);/si', $css, $matches)) {

                    foreach ($matches[0] as $a) {
                        $href = '';

                        if (preg_match('/url\("(.*?)"\)/si', $a, $m)) {
                            $href = $m[1];
                        }
                        elseif (preg_match('/url\(\'(.*?)\'\)/si', $a, $m)) {
                            $href = $m[1];
                        }
                        elseif (preg_match('/url\(([^\)]+)\)/si', $a, $m)) {
                            $href = $m[1];

                            if ($href[0] == '"' or $href[0] == "'") {
                                $href = substr($href, 1, -1);
                            }
                        }

                        if ($href) {
                            $css_list[] = $href;
                        }
                    }

                }

            }

        }

        $fonts = array();
        $fonts_source = array();

        foreach ($css_data as $name => $text) {

            if (preg_match_all('/@font-face\s*{(.*?)}/si', $text, $matches)) {
                foreach ($matches[1] as $fftext) {
                    $fontface = new FontFace( $fftext );

                    if (!$fonts[$fontface->family]) {
                        $fonts[$fontface->family] = new Font( $fontface->family );
                    }

                    $fonts[$fontface->family]->addFontFace( $fontface );

                    $fonts_source[$fontface->family][ $name ] = true;
                }
            }

        }

        return 
	        array(
	        	'fonts' => $fonts,
	        	'source' => $fonts_source,
	        );
	}

}
