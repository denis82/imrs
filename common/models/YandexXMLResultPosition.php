<?php

/**
 * Class YandexXMLResultPosition
 *
 * @property string $domain
 * @property string $url
 * @property string $title
 * @property string $titleHtml
 * @property int $titleIncludes
 * @property int $position
 * @property string $path
 * @property array $passages
 * @property int $passagesIncludes
 * @property string|bool $savedCopyUrl
 */
class YandexXMLResultPosition {
	public $domain;
	public $url;
	public $title;
	public $titleHtml;
	public $titleIncludes = 0;
	public $position;
	public $path = '/';
	public $passages = [];
	public $passagesIncludes = 0;
	public $savedCopyUrl = false;
}

