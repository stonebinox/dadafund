<?php

ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
      'driver' => 'pdo_mysql',
      'dbname' => 'heroku_3a9490554c5d302',
      'user' => 'bf27271841486e',
      'password' => '3c89adaa',
      'host'=> "us-cdbr-iron-east-05.cleardb.net",
    )
));
$app->register(new Silex\Provider\SessionServiceProvider, array(
    'session.storage.save_path' => dirname(__DIR__) . '/tmp/sessions'
));
$app->before(function(Request $request) use($app){
    $request->getSession()->start();
});
$app->get("/",function() use($app){
    return $app['twig']->render("index.html.twig");
});
$app->get("/createaccount",function() use($app){
    return $app['twig']->render("createaccount.html.twig");
});
$app->post("/login_action",function(Request $request) use($app){
    if(($request->get("email"))&&($request->get("password")))
    {
        require("../classes/adminMaster.php");
        require("../classes/userMaster.php");
        $user=new userMaster;
        $response=$user->authenticateUser($request->get("email"),$request->get("password"));
        echo $response;
        if($response=="USER_AUTHENTICATED")
        {
            // return $app->redirect("/dashboard");
        }
        else
        {
            // return $app->redirect("/?err=".$response);
        }
        return "DONE";
    }
    else
    {
        return $app->redirect("/");
    }
});
$app->get("/dashboard",function() use($app){
    if($app['session']->get("uid"))
    {
        return $app['twig']->render("dashboard.html.twig");
    }
    else
    {
        return $app->redirect("/");
    }
});
$app->post("/create_action",function(Request $request) use($app){
    if(($request->get("email"))&&($request->get("name"))&&($request->get("password"))&&($request->get("rpassword")))
    {
        require("../classes/adminMaster.php");
        require("../classes/userMaster.php");
        $user=new userMaster;
        $response=$user->createAccount($request->get("name"),$request->get("email"),$request->get("password"),$request->get("rpassword"));
        if($response=="ACCOUNT_CREATED")
        {
            return $app->redirect("/?suc=".$response);
        }
        else
        {
            return $app->redirect("/createaccount?err=".$response);
        }
    }
    else
    {
        return $app->redirect("/createaccount");
    }
});
$app->run();
?>