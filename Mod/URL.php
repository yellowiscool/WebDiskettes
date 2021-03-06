<?php
class URL
{
	// Primary key
	protected $id;

	// Unique columns
	public $url;

	public function __construct($url) {
		$this->url = $url;
	}

	public function getId() {
		if ($this->id === null) {
			CPDO::exec('INSERT IGNORE INTO URLs (url) VALUES (:url)',
					array(':url' => $this->url));
			$this->id = CPDO::execOne('SELECT idURLs FROM URLs WHERE (url = :url) LIMIT 1',
					array(':url' => $this->url))->idURLs;
		}

		return $this->id;
	}
	
	public static function getAll() {
		return CPDO::exec('SELECT idURLs, url FROM URLs');
	}

	public function getCaptures() {

		$hashs = array();
		$cpt = 0;

		$captures = CPDO::exec('SELECT dateID, date, HEX(hash) as hash, statusCode, size, type
FROM Captures, ContentTypes
WHERE URLs_idURLS = ? AND idContentTypes = ContentTypes_idContentTypes', array($this->getId()));

		foreach ($captures as $capture) {
			if (array_key_exists($capture->hash, $hashs)) {
				$capture->version = $hashs[$capture->hash];
			} else {
				$capture->version = ++$cpt;
				$hashs[$capture->hash] = $cpt;
			}
		}

		return $captures;
	}
}
?>
