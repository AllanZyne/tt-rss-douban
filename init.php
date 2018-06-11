<?php
function debug_to_console($article, $data)
{
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);
    $article["content"] = $output ;
}

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

    function get_prefs_js() {
        return file_get_contents(dirname(__FILE__) . "/init.js");
    }

    function parse_feed($feed)
    {
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
	    $article_content = $article["content"];

            $article["content"] = 'Debug: ';

            $content = trim(substr($article_content, 210, -19));

            // $article["content"] = $article["content"] . "<pre>" . $content . "</pre>";

            // write_log($content . "\n\n");

            $feed = json_decode($content, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $article["content"] = $article["content"] . $this->parse_feed($feed);
            } else {
                $constants = get_defined_constants(true);
                $json_errors = array();
                foreach ($constants["json"] as $name => $value) {
                    if (!strncmp($name, "JSON_ERROR_", 11)) {
                        $json_errors[$value] = $name;
                    }
                }

                $article["content"] = $article["content"] . "<p>" . $json_errors[json_last_error()] . "</p>";
            }

            // write_log($json_errors[json_last_error()] . "\n\n");

            // if ($feed !== NULL) {
            //     $blocks = $feed["blocks"];
            //     $blocks_len = count($blocks);
            //     $content = "";
            //     for ($i = 0; $i < $blocks_len; $i++) {
            //         $block = $blocks[i];
            //         if ($block["type"] == "header-four") {
            //             $content = $content . "<h4>" . $block["text"] . "</h4>";
            //         } else if ($block["type"] == "atomic") {

            //         } else {
            //             $content = $content . "<p>" . $block["text"] . "</p>";
            //         }
            //     }
            //     $article["content"] = $content;
            // }

        //     } else {
        //         echo 'Last error: ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
        //         $article["content"] = "Content: " . $article["content"];
        //     }
        } else {
            // $article["content"] = "Content: " . $article["feed"]["site_url"];
        }

        return $article;
    }

    function api_version() {
        return 2;
    }

}
?>
