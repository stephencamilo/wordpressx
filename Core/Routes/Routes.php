<?php

namespace Core\Routes;

class Routes {
    function init_router() {
        $klein = new \Klein\Klein;

        $klein->respond('GET', '/', function () {
            $WPX_index = new \Core\Wordpressx\Index;
            $WPX_index->blog_header();
        });

        $klein->respond('GET', '/admin/setup/config', function () {
            $WPX_setup_config = new \Core\WPAdmin\SetupConfig;
            $WPX_setup_config->render();
        });
        
        $klein->respond('GET', '/hello-world', function () {
            return 'Hello World!';
        });
        
        $klein->dispatch();
    }
}