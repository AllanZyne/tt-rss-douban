<?php

function write_log($log)
{
    file_put_contents('/tmp/php_content.log', $log, FILE_APPEND);
}

class Douban extends Plugin {

    private $host;

    function about() {
        return array(1.0,
            "Douban RSS plugin",
            "Allan Zyne",
            true);
    }

    function init($host) {
        $this->host = $host;

        $host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
        // $host->add_hook($host::HOOK_RENDER_ARTICLE, $this);
    }

    function parse_feed($feed) {
        $content = "";

        // entityMap:
        //   "0":
        //     type: "IMAGE"
        //     mutability: "IMMUTABLE"
        //     data:
        //       id: ""
        //       width: 0
        //       height: 0
        //       file_size: 0
        //       thumb: ""
        //       url: ""
        //       file_name: ""
        //       is_animated: false
        //       entityKey: ""
        //       src: ""
        //       caption: ""
        //
        $entityMap = $feed["entityMap"];

        // parse blocks
        // block:
        //   key: ""
        //   text: ""
        //   type: "header-"|"atomic"|"unstyled"
        //   depth: 0
        //   inlineStyleRanges:
        //     - offset: 0
        //       length: 0
        //       style: 'BOLD'
        //   entityRanges:
        //     - offset: 0
        //       length: 0
        //       key: 0
        //   data: {}
        foreach ($feed["blocks"] as $block) {
            if ($block["type"] == "header") {
                $text = nl2br($block["text"]);
                $content = $content . "<h1>" . $text . "</h1>";
            } else if ($block["type"] == "header-one") {
                $text = nl2br($block["text"]);
                $content = $content . "<h1>" . $text . "</h1>";
            } else if ($block["type"] == "header-two") {
                $text = nl2br($block["text"]);
                $content = $content . "<h2>" . $text . "</h2>";
            } else if ($block["type"] == "header-three") {
                $text = nl2br($block["text"]);
                $content = $content . "<h3>" . $text . "</h3>";
            } else if ($block["type"] == "header-four") {
                $text = nl2br($block["text"]);
                $content = $content . "<h4>" . $text . "</h4>";
            } else if ($block["type"] == "header-five") {
                $text = nl2br($block["text"]);
                $content = $content . "<h5>" . $text . "</h5>";
            } else if ($block["type"] == "atomic") {
                // TODO: images
            } else {
                // TODO: inlineStyleRanges
                $text = nl2br($block["text"]);
                $content = $content . "<p>" . $text . "</p>";
            }
        }

        return $content;
    }

    function hook_article_filter($article) {
        $site_url = $article["feed"]["site_url"];
        $parts = parse_url($site_url);

        if ($parts["host"] == "www.douban.com") {
            $content = html_entity_decode(str_replace('"', '\\"', $article["content"]));

            write_log($content . "\n\n");

            $feed = json_decode($content, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $article["content"] = $this->parse_feed($feed);
            }
        }

        return $article;
    }

    // function hook_render_article($article) {
    //     $site_url = $article["site_url"];
    //     $parts = parse_url($site_url);

    //     if ($parts["host"] == "www.douban.com") {
    //         //! delete <xhtml> headers and footers
    //         $content = trim(substr($article["content"], 210, -19));

    //         // write_log($content . "\n\n");

    //         $feed = json_decode($content, true);

    //         if (json_last_error() == JSON_ERROR_NONE) {
    //             $article["content"] = $this->parse_feed($feed);
    //         }/* else { 
    //             $constants = get_defined_constants(true);
    //             $json_errors = array();
    //             foreach ($constants["json"] as $name => $value) {
    //                 if (!strncmp($name, "JSON_ERROR_", 11)) {
    //                     $json_errors[$value] = $name;
    //                 }
    //             }

    //             $article["content"] = $article["content"] . "<p>" . $json_errors[json_last_error()] . "</p>";
    //         }*/
    //     }

    //     return $article;
    // }

    function api_version() {
        return 2;
    }

}
?>
