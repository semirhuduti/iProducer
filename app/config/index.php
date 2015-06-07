<?php

/**
 * This is a Anax pagecontroller.
 *
 */
require __DIR__ . '/config_with_app.php';

$di = new \Anax\DI\CDIFactoryDefault();

// Connect to the database.sqlite
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/config_sqlite.php');
    $db->connect();
    return $db;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('CommentController', function() use ($di) {
    $controller = new \Anax\Comment\CommentDbController();
    $controller->setDI($di);
    return $controller;
});

$di->set('form', '\Mos\HTMLForm\CForm');

$app = new \Anax\Kernel\CAnax($di);

// Start session
$app->session;

// Set at default fallback page-title
$app->theme->setTitle("iProducer");

// Add stylesheet
$app->theme->addStylesheet('//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');


$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Home");
    $content = $app->fileContent->get('home.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('me/page', [
        'content' => $content,
    ]);

    // Get the toplist 
    $app->dispatcher->forward([
        'controller' => 'comment',
        'action' => 'view-top-four',
    ]);
});

$app->router->add('about', function() use ($app) {

    $app->theme->setTitle("About");
    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('me/page', [
        'content' => $content,
    ]);
});

$app->router->add('questions', function() use ($app) {

    $app->theme->setTitle("Home");

    $app->dispatcher->forward([
        'controller' => 'comment',
        'action' => 'view',
    ]);
});

$app->router->add('login', function() use ($app) {

    $app->theme->setTitle("Home");

    $app->dispatcher->forward([
        'controller' => 'comment',
        'action' => 'view',
    ]);
});

/**
 * Add specific theme and add navigation bar if user is online else create a view for non-logged in users.
 */
if (isset($_SESSION['authenticated']['valid'])) {

    $app->router->handle();

    $app->theme->configure(ANAX_APP_PATH . 'config/theme.php');

    $app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');
    
} else {

    $app->theme->configure(ANAX_APP_PATH . 'config/theme_login.php');
    
    $app->navbar->configure(ANAX_APP_PATH . 'config/navbar_login.php');

    $app->dispatcher->forward([
        'controller' => 'users',
        'action' => 'login',
    ]);
}

// Render the response using theme engine.
$app->theme->render();
