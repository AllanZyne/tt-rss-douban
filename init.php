<?php

function write_log($log)
{
    file_put_contents('/tmp/php.log', $log, FILE_APPEND);
}

class Douban extends Plugin {

    private $host;

    function about() {
        return array(1.0,
            "Example plugin for HOOK_ARTICLE_FILTER",
            "Allan Zyne",
            true);
    }

    function init($host) {
        $this->host = $host;

        // $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
        $host->add_hook($host::HOOK_RENDER_ARTICLE, $this);
    }

    // function get_prefs_js() {
    //     return file_get_contents(dirname(__FILE__) . "/init.js");
    // }

    function parse_feed($feed) {
        $blocks = $feed["blocks"];
        $blocks_len = count($blocks);
        $content = "<p>parse feed</p><br>";
        for ($i = 0; $i < $blocks_len; $i++) {
            $block = $blocks[$i];
            if ($block["type"] == "header-four") {
                $text = nl2br($block["text"]);
                $content = $content . "<h4>" . $text . "</h4>";
            } else if ($block["type"] == "atomic") {

            } else {
                $text = nl2br($block["text"]);
                $content = $content . "<p>" . $text . "</p>";
            }
        }
        return $content;
    }

    function hook_render_article($article) {
        $site_url = $article["site_url"];
        $parts = parse_url($site_url);

        if ($parts["host"] == "www.douban.com") {
            //! delete <xhtml> headers and footers
            $content = trim(substr($article["content"], 210, -19));
            
            // write_log($content . "\n\n");
            
            $feed = json_decode($content, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $article["content"] = $this->parse_feed($feed);
            }/* else { 
                $constants = get_defined_constants(true);
                $json_errors = array();
                foreach ($constants["json"] as $name => $value) {
                    if (!strncmp($name, "JSON_ERROR_", 11)) {
                        $json_errors[$value] = $name;
                    }
                }

                $article["content"] = $article["content"] . "<p>" . $json_errors[json_last_error()] . "</p>";
            }*/
        }

        return $article;
    }

    function api_version() {
        return 2;
    }

}
?>
