<?php

class CurlHandler {
	
	public static function getTextPage($url) {

		if( $curl = curl_init() ) {
			curl_setopt($curl,CURLOPT_URL, $url);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_NOBODY,false);
			curl_setopt($curl,CURLOPT_HEADER,false);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			$out = curl_exec($curl);
			curl_close($curl);
		} 
		return $out;
	}
}