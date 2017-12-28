<?php
class NoCommand extends CConsoleCommand {

    public function actionHi($args) {
    	print_r($args);
    	echo 'hello world = ' . PHP_EOL;

    	$t1 = time();

    	while (true) {
    		$t2 = time();

    		if ($t2 - $t1 > 3600) {
    			break;
    		}
    	}

    	echo 'done' . PHP_EOL;
    }
}