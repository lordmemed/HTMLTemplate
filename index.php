<?php

//show error
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors',1);
error_reporting(-1);

// set internal encoding
mb_internal_encoding("UTF-8");

// set timezone
setlocale(LC_TIME, "id_ID");
date_default_timezone_set("Asia/Jakarta");

include_once 'Router.php';
include_once 'presenter.php';

//url router
use Bramus\Router\Router;

//html presenter
use MangaReader\Presenter;

// In case one is using PHP 5.4's built-in server
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename))
{
    return false;
}

// Include the Router class
// @note: it's recommended to just use the composer autoloader when working with other packages too
//require_once __DIR__ . '/libs/Bramus/Router/Router.php';

// Create a Router
$router = new Router();

//setup php dom query
$pq = new Presenter;

$router->set404(function () use ($pq)
{
    http_response_code(404);
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

    //die('ERROR: URL/ROUTE '.$_SERVER['REQUEST_URI'].' NOT FOUND!');

    //load template to dom
    $pq->load_str_html(@file_get_contents('layout.html'));

    $pq->assign('title', 'Layout - Not Found');

    $pq->assign('.container .page-title', '404 Error');

    $pq->assign('.container .page-content', 'ERROR: URL/ROUTE <b>'.$_SERVER['REQUEST_URI'].'</b> NOT FOUND!');

    die($pq->html(true));
});

// Before Router Middleware
$router->before('GET', '/.*', function ()
{
	header('Content-Type: text/html; charset=utf-8');
	header('X-Powered-By: Bram.US/Router');
});

//Index Route
$router->get('/',  function () use ($pq)
{

    //load template to dom
    $pq->load_str_html(@file_get_contents('layout.html'));

    $pq->assign('title', 'Layout - Index');

    $pq->assign('.container .page-title', 'Everything in moderation');
});

//List Route
$router->get('/list',  function () use ($pq)
{

    //load template to dom
    $pq->load_str_html(@file_get_contents('layout.html'));

    $pq->assign('title', 'Layout - My List');

    $pq->assign('.container .page-title', 'Lists Sample');

    $pq->assign('.container .page-content', 
        $pq->pnode('ul', ['class'=>'item', 'style'=>'list-style: bullet; margin: 0 15px;'])
        ->assign('ul',
            $pq->pnode('li')->assign('li', 'Item 1')
        )
        ->assign('ul',
            $pq->pnode('li')->assign('li', 'Item 2'), true //$params append=true
        )
        ->assign('ul',
            $pq->pnode('li')->assign('li', 'Item 3'), true //$params append=true
        )
    );

});

//Pages/{page_url} Route
$router->get('/pages/([A-Za-z0-9-]+)', function ($url) use ($pq)
{
    $pq->load_str_html(@file_get_contents('layout.html'));

    $pq->assign('title', 'Layout - '.ucfirst($url));

    $pq->assign('.container .page-title', ucfirst($url).' Page');

    $pq->assign('.container .page-content', 'Isi konten dari '.ucfirst($url).' Page');
});


// Run the web!
$router->run(function() use ($pq) {
    $pq->print_html(true);
});