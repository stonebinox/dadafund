<?php

ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
function secure($string)
{
    return addslashes(htmlentities($string));
}
function validate($data)
{
    if(($data!="")&&($data!=NULL))
    {
        return true;
    }
    return false;
}
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
    if($app['session']->get("uid"))
    {
        return $app->redirect("/dashboard");
    }
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
        if($response=="AUTHENTICATE_USER")
        {
            return $app->redirect("/dashboard");
        }
        else
        {
            return $app->redirect("/?err=".$response);
        }
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
$app->get("/user/getUser",function() use($app){
    if($app['session']->get("uid")){
        require("../classes/adminMaster.php");
        require("../classes/userMaster.php");
        $user=new userMaster($app['session']->get("uid"));
        $response=$user->getUser();
        if(is_array($response))
        {
            return json_encode($response);
        }
        else
        {
            return $response;
        }
    }
    else
    {
        return "INVALID_PARAMETERS";
    }
});
$app->get("/logout",function() use($app){
    if($app['session']->get("uid"))
    {
        require("../classes/adminMaster.php");
        require("../classes/userMaster,php");
        $user=new userMaster($app['session']->get("uid"));
        $response=$user->logout();
        return $response;
    }
    else
    {
        return "INVALID_PARAMETERS";
    }
});
$app->get("/api/transact",function(Request $request) use($app){
    if(($request->get("amount"))&&($request->get("email"))&&($request->get("partner")))
    {
        require("../classes/adminMaster.php");
        require("../classes/userMaster.php");
        requier("../classes/partnerMaster.php");
        require("../classes/transactionMaster.php");
        $transaction=new transactionMaster;
        $response=$transaction->addTransaction($request->get("email"),$request->get("amount"),$request->get("partner"));
        return $response;
    }
    else
    {
        return "INVALID_PARAMETERS";
    }
});
$app->get("/transaction/getAll",function(Request $request) use($app){
    if($app['session']->get("uid"))
    {
        require("../classes/adminMaster.php");
        require("../classes/userMaster.php");
        require("../classes/partnerMaster.php");
        require("../classes/transactionMaster.php");
        $transaction=new transactionMaster;
        $offset=0;
        if($request->get("offset"))
        {
            $offset=$request->get("offset");
        }
        $rows=$transaction->getTransactions($app['session']->get("uid"),$offset);
        if(is_array($rows))
        {
            return json_encode($rows);
        }
        return $rows;
    }
    else
    {
        return "INVALID_PARAMETERS";
    }
});
$app->run();
?>