<?php
session_start ();
require '../vendor/autoload.php';
if (empty($_SESSION['id'])) {
    header("Location: ./login.php");
}

# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;
$datastore = new DatastoreClient();

$err = '';
$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['pwd']) || empty($_POST['opwd'])) {
        $err = '<small class="form-text text-muted">New Password and Old Password cannot be empty.</small>';
    } else {
        $key = $datastore->key('user', $id);
        $pwd = intval($_POST['opwd']);
        $user = $datastore->lookup($key);
        if ($user['password'] == $pwd) {
            $user->setProperty('password', $_POST['pwd']);
            $datastore->update($user);
            header("Location: ./main.php");
        } else {
            $err = '<small class="form-text text-muted">Old Password is wrong.</small>';
        }
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
        <form action="./password.php" class="container-sm p-4 mt-5 bg-dark text-white" method="POST">
            <div class="form-group">
                <label for="pwd">New Password</label>
                <input type="text" class="form-control" placeholder="Enter new Password" name="pwd">
                <?php echo $err ?>  
            </div>

            <div class="form-group">
                <label for="opwd">Old Password</label>
                <input type="text" class="form-control" placeholder="Enter old Password" name="opwd">
                <?php echo $err ?>   
            </div>
        
            <button type="submit" class="btn btn-danger btn-lg btn-block">Change</button>
        </form>
    </body>
</html>