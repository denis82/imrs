<?php

if ($_GET['d']) {

	print "Обработанные файлы: <br>\n";

	scan('./files/' . $_GET['d']);

	print "<br>\n";
	print "Посмотреть результат: <br>\n";

	print "<a href='files/" . $_GET['d'] . "/result.html'>result.html</a><br>\n";
	print "<a href='files/" . $_GET['d'] . "/sites.html'>sites.html</a><br>\n";
	print "<a href='files/" . $_GET['d'] . "/top10.html'>top10.html</a><br>\n";
	print "<a href='files/" . $_GET['d'] . "/competitors.html'>competitors.html</a><br>\n";

}
else {

	print 'Файлы не загружены.';

}


function scan($path) {
	$meta = '<html><head><meta charset="windows-1251"></head><body>' . PHP_EOL;

	$f = fopen($path . '/.htaccess', 'w');
	fputs($f, "adddefaultcharset WINDOWS-1251");
	fclose($f);

	$f = fopen( $path . '/' . 'result.html' , 'w');
	fputs($f, $meta);
	fclose($f);

	$f = fopen( $path . '/' . 'sites.html' , 'w');
	fputs($f, $meta);
	fclose($f);

	$f = fopen( $path . '/' . 'top10.html' , 'w');
	fputs($f, $meta);
	fclose($f);

	$f = fopen( $path . '/' . 'competitors.html' , 'w');
	fputs($f, $meta);
	fclose($f);

	$sites = array();

	if (file_exists($path . '/' . 'sites.txt')) {
		$sites_str = file_get_contents($path . '/' . 'sites.txt');

		$tmp = explode("\n", $sites_str);

		foreach ($tmp as $j => $i) {
			$l = strtolower( trim($i) );

			if (strlen($l)) {
				$sites[] = $l;
			}
		}
	}

	if ($handle = opendir($path)) {
	    while (false !== ($entry = readdir($handle))) {
	        if ($entry != "." && $entry != "..") {

	            if (is_dir($path . '/' . $entry)) {
		            scan($path . '/' . $entry);
	            }
	            elseif (substr($entry, -4) == '.csv') {
		            echo $path . '/' . $entry . " <br>\n";

		            list($a, $b, $c, $d) = prepare( $path . '/' . $entry, $sites );

		            $f = fopen( $path . '/' . 'result.html' , 'a');
		            fputs($f, $a);
		            fclose($f);

		            $f = fopen( $path . '/' . 'sites.html' , 'a');
		            fputs($f, $b);
		            fclose($f);

		            $f = fopen( $path . '/' . 'top10.html' , 'a');
		            fputs($f, $c);
		            fclose($f);

		            $f = fopen( $path . '/' . 'competitors.html' , 'a');
		            fputs($f, $d);
		            fclose($f);

		            // file_put_contents($path . '/' . $entry . '.html', $s);
	            }
	        }
	    }
	    closedir($handle);
	}
}

function prepare($s, $sites) {

	$f = fopen($s, 'r');

	$result = array();

	$words = array();

	while ($data = fgetcsv($f, 102400, ';')) {
		$n = 0;

		foreach ($data as $i) {
			$n += strlen(trim($i));
		}

		if ($n == 0) {
			foreach ($words as $i) {
				if (strlen(trim(implode('', $i)))) {
					$result[] = $i;
				}
			}

			$words = array();
		}
		else {
			foreach ($data as $j => $i) {
				$words[$j][] = $i;
			}
		}
	}

	foreach ($words as $i) {
		if (strlen(trim(implode('', $i)))) {
			$result[] = $i;
		}
	}

	$r = $q = $t = $y = '';

	$wordstat = array();
	$urlstat = array();

	foreach ($result as $i) {
		if (strlen($i[0])) {
			$r .= '<h1>' . $i[0] . '</h1>';

			$r .= '<table border="0">';

			$n = 1;

			foreach ($i as $j => $l) {
				if ($j) {
					$color = '#fff';
					$fontweight = 'normal';

					foreach ($sites as $site) {
						if (strpos($l, '//' . $site . '/') !== false or strpos($l, '.' . $site . '/') !== false) {
							$color = '#cfc';
							$fontweight = 'bold';

							$wordstat[$site][$i[0]][] = array($j, $l);
						}
					}

					$r .= '<tr><td align="right">' . ($n++) . '</td><td style="background: ' . $color . '; font-weight: ' . $fontweight . '; " bgcolor="' . $color . '">' . $l . '</td></tr>';

					$url_hash = md5( trim($l) );

					if (!array_key_exists($url_hash, $urlstat)) {
						$urlstat[ $url_hash ] = array();
						$urlstat[ $url_hash ]['url'] = $l;
						$urlstat[ $url_hash ]['stat'] = array();
					}

					$urlstat[ $url_hash ]['stat'][] = array(
						'n' => $j,
						'w' => $i[0]
					);

				}
			}

			$r .= '</table>';
		}
	}

	usort($urlstat, function ($a, $b) {
		$j = count($a['stat']);
		$i = count($b['stat']);

		if ($j == $i) return 0;

		if ($j < $i) return 1;
		elseif ($j > $i) return -1;
	});	

	foreach ($wordstat as $i => $k) {
		if (strlen($i[0])) {

			$top10 = $top50 = 0;

			foreach ($k as $j => $l) {
				foreach ($l as $o => $p) {
					if ($p[0] <= 10) $top10++;
					if ($p[0] <= 50) $top50++;
				}
			}

			$q .= '<h1>' . $i . ' (top10/top50 = ' . $top10 . '/' . $top50 . ')</h1>';

			$q .= '<table border="0">';

			foreach ($k as $j => $l) {
				foreach ($l as $o => $p) {
					$q .= '<tr><td align="right">' . $p[0] . '</td><td>' . $j . '</td><td>' . $p[1] . '</td></tr>';
				}

			}

			$q .= '</table>';
		}
	}

	foreach ($result as $i) {
		if (strlen($i[0])) {
			$t .= '<h1>' . $i[0] . '</h1>';

			$t .= '<table border="0">';

			foreach ($i as $j => $l) {
				if ($j > 0 and $j <= 10) {
					$color = '';
					$fontweight = 'normal';

					foreach ($sites as $site) {
						if (strpos($l, '//' . $site . '/') !== false or strpos($l, '.' . $site . '/') !== false) {
							$color = '#cfc';
							$fontweight = 'bold';
						}
					}

					if ($color) {
						$t .= '<tr><td align="right">' . $j . '</td><td style="background: ' . $color . '; font-weight: ' . $fontweight . '; " bgcolor="' . $color . '">' . $l . '</td></tr>';
					}

				}
			}

			$t .= '</table>';
		}
	}

	foreach ($urlstat as $j => $i) {
		if ($j > 10) break;

		if (count($i['stat']) > 1) {
			$y .= '<h1>' . count($i['stat']) . ' = ' . $i['url'] . '</h1>';

			$y .= '<table border="0">';

			foreach ($i['stat'] as $l) {
				$y .= '<tr><td align="right">' . $l['n'] . '</td><td>' . $l['w'] . '</td></tr>';
			}

			$y .= '</table>';
		}
	}

	return array($r, $q, $t, $y);

}


