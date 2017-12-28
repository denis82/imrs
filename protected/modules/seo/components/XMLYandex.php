<?php

class XMLYandex {
	
	private static $__instance;
	
	private static $query = 'http://xmlsearch.yandex.ru/xmlsearch?user={USER}&key={KEY}&lr={REGION}&page={PAGE}&query={QUERY}';
	private static $user = 'xsite';
	private static $key = '03.37624:68dcfd904d9cac84ac2e25bf79b104af';
	
	public static function getResult($queryString, $region = 213, $page = 1){
		$query = self::$query;
		
		$query = str_replace('{USER}', self::$user, $query);
		$query = str_replace('{KEY}', self::$key, $query);
		$query = str_replace('{REGION}', $region, $query);
		$query = str_replace('{PAGE}', $page, $query);
		$query = str_replace('{QUERY}', urlencode($queryString), $query);
		
		return file_get_contents($query);
		
	}
	
}