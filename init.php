<?php
function debug_to_console($article, $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    $article['content'] = $article['content'] . "<script>console.log( 'debug:" . $output . "' );</script>";
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

    function hook_render_article($article) {
        // 
        

        // $article["feed"]["site_url"]
        $site_url = $article["site_url"];
        $parts = parse_url($site_url);

        debug_to_console($article, $site_url);

        if ($parts["host"] == "www.douban.com") {
            $content = substr($article["content"], 210, -19);

            debug_to_console($article, $content);

            // $constants = get_defined_constants(true);
            // $json_errors = array();
            // foreach ($constants["json"] as $name => $value) {
            //     if (!strncmp($name, "JSON_ERROR_", 11)) {
            //         $json_errors[$value] = $name;
            //     }
            // }

        //     debug_to_console($article["content"]);

            // echo 'article ', $content;

            // $feed = json_decode($content, true);

            // echo 'Last error: ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
        
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