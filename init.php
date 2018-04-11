<?php
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

        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
    }

    function get_prefs_js() {
        return file_get_contents(dirname(__FILE__) . "/init.js");
    }

    function hook_article_filter($article) {
        $parts = parse_url($article["feed"]["site_url"]);

        echo 'article: ', $article["content"];

        var_dump($article["content"]);

        if ($parts["host"] == "www.douban.com") {

            $constants = get_defined_constants(true);
            $json_errors = array();
            foreach ($constants["json"] as $name => $value) {
                if (!strncmp($name, "JSON_ERROR_", 11)) {
                    $json_errors[$value] = $name;
                }
            }

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
                echo 'Last error: ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
                $article["content"] = "Content: " . $article["content"];
            }
        }
        // else {
        //     $article["content"] = "Content: " . $article["feed"]["site_url"];
        // }

        return $article;
    }

    function api_version() {
        return 2;
    }

}
?>