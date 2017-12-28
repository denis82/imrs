<?php

/**
 * Class YandexXMLResult
 *
 * @property YandexXMLResultPosition[] $list
 * @property int $foundPhrase
 * @property int $foundStrict
 * @property int $foundAll
 * @property int $foundDocsPhrase
 * @property int $foundDocsStrict
 * @property int $foundDocsAll
 */
class YandexXMLResult {
	
	public $list = [];
	public $response;

	public $foundPhrase = 0;
	public $foundStrict = 0;
	public $foundAll = 0;

	public $foundDocsPhrase = 0;
	public $foundDocsStrict = 0;
	public $foundDocsAll = 0;
	
	protected function __construct($content){

		$this->response = new stdClass;
		/**
		<reqid>1424333294826191-1398379995358134233813037-man1-3558-XML</reqid>
		 * <found priority="phrase">103</found>
		 * <found priority="strict">103</found>
		 * <found priority="all">103</found>
		 * <found-human>Нашлось 103 ответа</found-human>
		 */
		if(preg_match('/<response[^>]+>(.*?)<results>/is', $content, $match)){

			preg_match_all('/<found\spriority="([a-z]+)">([0-9]+?)<\/found>/is', $match[1], $matches);

			if(count($matches[1]) > 0){

				foreach($matches[1] as $k => $priority){

					$fieldKey = sprintf('found%s', ucfirst($priority));

					$this->{$fieldKey} = (int)$matches[2][$k];

				}

			}

		}

		if(preg_match('/<grouping[^>]+>(.*?)<page/is', $content, $match)){

			preg_match_all('/<found\-docs\spriority="([a-z]+)">([0-9]+?)<\/found-docs>/is', $match[1], $matches);

			if(count($matches[1]) > 0){

				foreach($matches[1] as $k => $priority){

					$fieldKey = sprintf('foundDocs%s', ucfirst($priority));

					$this->{$fieldKey} = (int)$matches[2][$k];

				}

			}

		}
		
		$groups = array_filter(explode('<group>', $content));
		
		if(count($groups) <= 1) return false;
		
		$head = array_shift($groups);
		
		if($groups){
			
			foreach ($groups as $k => $group) {
				
				$this->list[$k] = new YandexXMLResultPosition();
				
				$this->list[$k]->position = $k + 1;

				if(preg_match('/<_PassagesType>([0-9]+)<\/_PassagesType>/i', $group, $match)){
					$this->list[$k]->passageType = (int)$match[1];
				}
				
				if(preg_match('/<title>(.+)<\/title>/i', $group, $match)){
					$this->list[$k]->titleHtml = $match[1];
					$this->list[$k]->title = strip_tags($match[1]);
					$this->list[$k]->titleIncludes = substr_count($match[1], '<hlword>');
				}
				
				if(preg_match('/<url>(.+)<\/url>/i', $group, $match)){
					$this->list[$k]->url = strtolower($match[1]);
				}
				
				if(preg_match('/<domain>(.+)<\/domain>/i', $group, $match)){
					$this->list[$k]->domain = strtolower($match[1]);
					
					if(isset($this->list[$k]->url)){
						$parts = explode($this->list[$k]->domain, $this->list[$k]->url);
						$this->list[$k]->path = array_pop($parts);
					}
				}
				
				if(preg_match('/<passages>(.+)<\/passages>/i', $group, $match)){
					$this->list[$k]->passages = [];
					
					$this->list[$k]->passagesIncludes = substr_count($match[1], '<hlword>');
					
					$passages = array_filter(explode('</passage>', str_replace('<passage>', '', $match[1])));
					
					foreach ($passages as $key => $passage) {
						$this->list[$k]->passages[] = strip_tags($passage);
					}
				}
				
				if(preg_match('/<saved-copy-url>(.+)<\/saved-copy-url>/i', $group, $match)){
					$this->list[$k]->savedCopyUrl = htmlspecialchars_decode($match[1]);
				}else{
					$this->list[$k]->savedCopyUrl = false;
				}
				
			}
			
		}
		
		return false;
	}

	public static function parse($content){
		if($content && strpos($content, '<yandexsearch') !== false){
			$result = new self($content);
			
			return $result;
		}

		var_dump('strpos:', strpos($content, '<yandexsearch'));
		
		return false;
	}

	public static function compareDomains($a, $b){
		return (self::preparedDomain($a) == self::preparedDomain($b));
	}

	public static function preparedDomain($domain){
		$domain = preg_replace('/https?:\/\//i', '', trim(mb_strtolower($domain, 'UTF-8')));
		list($domain) = explode('/', $domain);
		if(strpos($domain,'www.')===0){
			$domain = mb_substr($domain, 4);
		}
		return $domain;
	}
	
}

