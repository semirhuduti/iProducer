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
        'home' => [
            'text' => '<i class="fa fa-music"></i> Home',
            'url' => '',
            'title' => 'Home'
        ],
        'myprofile' => [
            'text' => '<i class="fa fa-user"></i> Profile',
            'url' => 'users/id/' . $_SESSION['authenticated']['user']->id,
            'title' => 'My own profile'
        ],
        'discussion' => [
            'text' => '<i class="fa fa-comments"></i> Discussion',
            'url' => 'comment/view-questions',
            'title' => 'Discussion',
            'submenu' => [
                'items' => [

                    'questions' => [
                        'text' => '<i class="fa fa-question-circle"></i> Questions',
                        'url' => 'comment/view-questions',
                        'title' => 'Questions'
                    ],
                    'addquestion' => [
                        'text' => '<i class="fa fa-plus-square"></i> New topic',
                        'url' => 'comment/add',
                        'title' => 'Questions'
                    ],
                    'tags' => [
                        'text' => '<i class="fa fa-tag"></i> Tags',
                        'url' => 'comment/tags',
                        'title' => 'tags'
                    ],
                ],
            ],
        ],
        'users' => [
            'text' => '<i class="fa fa-users"></i> Users',
            'url' => 'users/list',
            'title' => 'users'
        ],
        'about' => [
            'text' => '<i class="fa fa-newspaper-o"></i> About Us',
            'url' => 'about',
            'title' => 'about'
        ],
        'logout' => [
            'text' => '<i class="fa fa-sign-out"></i> Logout',
            'url' => 'users/logout',
            'title' => 'logout'
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
