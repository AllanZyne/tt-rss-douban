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
        $parts = parse_url($article["site_url"]);

        if ($parts["host"] == "www.douban.com") {
            $feed = json_decode($article["content"], true);
            if ($feed !== NULL) {
                $blocks = $feed["blocks"];
                $blocks_len = count($blocks);
                $content = "";
                for ($i = 0; $i < $blocks_len; $i++) {
                    $block = $blocks[i];
                    if ($block["type"] == "header-four") {
                        $content = $content . "<h4>" . $block["text"] . "</h4>";
                    } else if ($block["type"] == "atomic") {

                    } else {
                        $content = $content . "<p>" . $block["text"] . "</p>";
                    }
                }
                $article["content"] = $content;
            } else {
                $article["content"] = var_export($article["content"], true);
            }
        }

        return $article;
    }

    function api_version() {
        return 2;
    }

}
?>