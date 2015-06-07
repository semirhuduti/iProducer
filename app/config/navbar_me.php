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
            'text'  => 'Hem',
            'url'   => '',
            'title' => 'Hem'
        ],
 
        // This is a menu item
        'report'  => [
            'text'  => 'Redovisningar',
            'url'   => 'redovisning',
            'title' => 'Redovisningar',
            ],
            
         'theme'  => [
            'text'  => 'Mitt Tema',
            'url'   => 'theme.php',
            'title' => 'Mitt Tema',
            ],

            
        'comments'  => [
            'text'  => 'Kommentarer',
            'url'   => 'comments',
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

        'list'  => [
            'text'  => 'Användare',
            'url'   => 'Users/list',
            'title' => 'Lista alla',
            
            'submenu' => [
                'items' => [
                                            
                     'active' => [
			            'text' => 'Aktiva',
			            'url' => 'Users/active',
			            'title' => 'Aktiva användare'
			            ],
			            
			         'deactive' => [
			            'text' => 'Inaktiva',
			            'url' => 'Users/inactive',
			            'title' => 'Inaktiva användare'
			            ],
			            
			         'trash'  => [
			            'text'  => 'Papperskorgen',   
			            'url'   => 'users/trash',   
			            'title' => 'Soft-deleted',
						],
						
					'add' => [
			            'text'  =>'Lägg till', 
			            'url'   =>'users/add',  
			            'title' => 'Lägg till användare'
			            ],
			         'setup' => [
                        'text'      => 'Återställ',
                        'url'       => 'setup',
                        'title'     => 'Setup'
                        ],




                ],
            ],
            
            
        ],
        
                
         // This is a menu item
        'rss' => [
            'text'  =>'Rss',
            'url'   => 'rss',
            'title' => 'RSS'
        ],


 
        // This is a menu item
        'about' => [
            'text'  =>'Source',
            'url'   => 'source',
            'title' => 'Källkod'
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
