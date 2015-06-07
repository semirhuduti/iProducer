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

    // This is a menu item
        'home'  => [
            'text'  => '<i class="fa fa-home"></i> Hem',
            'url'   => '',
            'title' => 'Hem'
        ],

 
        // This is a menu item
        'regioner'  => [
            'text'  => '<i class="fa fa-newspaper-o"></i> Regioner',
            'url'   => 'theme.php/regioner',
            'title' => 'Regioner',
            ],
            
            
        'typography'  => [
            'text'  => '<i class="fa fa-sort-alpha-asc"></i> Typografi',
            'url'   => 'theme.php/typography',
            'title' => 'Kommentarer',

            /* Here we add the submenu, with some menu items, as part of a existing menu item
            'submenu' => [

                'items' => [

                    // This is a menu item of the submenu
                    'item 1'  => [
                        'text'  => 'Moment 1',
                        'url'   => $this->di->get('url')->createRelative('test.php/about'),
                        'title' => 'Url to specific route on specific frontcontroller'
                    ],

                    // This is a menu item of the submenu
                    'item 2'  => [
                        'text'  => 'Moment 2',
                        'url'   => $this->di->get('url')->asset('/humans.txt'),
                        'title' => 'Url to sitespecific asset',
                        'class' => 'italic'
                    ],

                    // This is a menu item of the submenu
                    'item 3'  => [
                        'text'  => 'Moment 3',
                        'url'   => $this->di->get('url')->asset('humans.txt'),
                        'title' => 'Url to asset relative to frontcontroller',
                    ],
                ],
            ],
            */
        ],
 
        // This is a menu item
        'about' => [
            'text'  =>'<i class="fa fa-font"></i> Font Awesome',
            'url'   => 'theme.php/font-awesome',
            'title' => 'Font Awesome'
        ],
        
        
    ],
 
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getRoute()) {
                return true;
        }
    },

    // Callback to create the urls
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
];
