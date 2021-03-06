<?php

/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
    // Here comes the menu strcture
    'items' => [

        'about' => [
            'text' => '<i class="fa fa-newspaper-o"></i> About Us',
            'url' => 'about',
            'title' => 'about'
        ],
    ],
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function($url) {
if ($url == $this->di->get('request')->getRoute()) {
    return true;
}
},
    // Callback to create the urls
    'create_url' => function($url) {
return $this->di->get('url')->create($url);
},
];
