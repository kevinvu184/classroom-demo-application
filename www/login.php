<?php
session_start ();
require '../vendor/autoload.php';

# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;
$datastore = new DatastoreClient();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['pwd']) || empty($_POST['id'])) {
        $err = '<small class="form-text text-muted">ID and Password cannot be empty.</small>';
    } else {
        $id = $_POST['id'];
        $pwd = intval($_POST['pwd']);

        // Construct key
        $key = $datastore->key('user', $id);
        // Query
        $user = $datastore->lookup($key);

        if ($user['password'] == $pwd) {
            $_SESSION['id'] = $id;
            header("Location: ./main.php");
        }

        $err = '<small class="form-text text-muted">User id or password is invalid</small>';
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>GCloud Based Registration</title>
        <link rel="shortcut icon" href="favicon.svg">
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
        <form action="./login.php" class="container-sm p-4 mt-5 bg-dark text-white" method="POST">
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" class="form-control" placeholder="Enter ID with 's'" name="id">
                <?php echo $err ?>
            </div>
        
            <div class="form-group">
                <label for="pwd">Password</label>
                <input type="password" class="form-control" placeholder="Enter Password" name="pwd">
                <?php echo $err ?>
            </div>
        
            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
        </form> 
    </body>
</html>