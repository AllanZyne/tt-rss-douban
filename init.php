<?php
class Douban extends Plugin {

	private $host;

	function about() {
		return array(1.0,
			"Example plugin for HOOK_RENDER_ARTICLE",
			"Allan Zyne",
			true);
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_RENDER_ARTICLE, $this);
	}

	function get_prefs_js() {
		return file_get_contents(dirname(__FILE__) . "/init.js");
	}

	function hook_render_article($article) {
		$article["content"] = "Content changed: " . $article["site_url"] . " " . $article["content"];

		return $article;
	}

	function api_version() {
		return 2;
	}

}
?>