<?php

$d = '';

if ($_POST['form_id'] == 'upload') {

	$d = date("Ymd-His");

	mkdir('./files/' . $d);

	$f = fopen('./files/' . $d . '/sites.txt', 'w');
	fputs($f, trim($_POST['site']) . "\r\n");
	fputs($f, trim($_POST['sites']) );
	fclose($f);

	foreach ($_FILES as $j => $i) {
		if (is_uploaded_file($i['tmp_name'])) {
			move_uploaded_file($i['tmp_name'], './files/' . $d . '/' . $i['name']);
		}
	}

}

header('Location: result.php?d=' . $d);
