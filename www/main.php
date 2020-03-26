<?php
require '../vendor/autoload.php';
if (empty($_COOKIE['auth'])) {
    header("Location: ./login.php");
}
# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;
$datastore = new DatastoreClient();

$id = $_COOKIE['auth'];
$key = $datastore->key('user', $id);
$user = $datastore->lookup($key);
$name = $user['name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (isset($_POST['name'])) {
        header("Location: ./name.php");
    } else if (isset($_POST['pwd'])) {
        header("Location: ./password.php");
    } else if (isset($_POST['back'])) {
        var_dump($_COOKIE['auth']);
        unset($_COOKIE['auth']);
        setcookie('auth', null, -1, '/');
        var_dump($_COOKIE['auth']);
        header("Location: ./login.php");
    }else if(isset($_POST['mark'])){
       header("Location: ./marking.php");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>GCloud Based Registration</title>
        <link rel="shortcut icon" href="/favicon.svg">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-148874673-5"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-148874673-5');
        </script>
    </head>
    <body class="bg-light">
        <div class="container-sm p-4 mt-5 bg-dark text-white">
            <div class="jumbotron text-dark"><h1 class="display-4">Welcome back <?php echo $name ?> !</h1></div>
            <form action="./main.php" method="POST">
                <input type="submit" name="mark" class="btn btn-warning btn-lg btn-block" value="Marking">
                <input type="submit" name="name" class="btn btn-warning btn-lg btn-block" value="Change Name">
                <input type="submit" name="pwd" class="btn btn-warning btn-lg btn-block" value="Change Password">
                <input type="submit" name="back" class="btn btn-danger btn-lg btn-block" value="Log out">
            </form>
        </div>
    </body>
</html>