<?php
require '../vendor/autoload.php';
if (empty($_COOKIE['auth'])) {
    header("Location: ./login.php");
}

# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;
$datastore = new DatastoreClient();

$err = '';
$id = $_COOKIE['auth'];

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
<!DOCTYPE html lang="en">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="Description" content="Peer-to-Peer marking system thats empower teachers.">
        <title>P2P Marking System</title>
        <link rel="shortcut icon" href="/favicon.svg">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body class="bg-secondary">
        <form action="./password.php" class="container-sm p-4 mt-5 bg-dark text-white rounded-lg" method="POST">
            <div class="form-group">
                <label for="opwd">Old Password</label>
                <input id="opwd" type="text" class="form-control" placeholder="Enter old Password" name="opwd">
                <?php echo $err ?>   
            </div>
            <div class="form-group">
                <label for="pwd">New Password</label>
                <input id="pwd" type="text" class="form-control" placeholder="Enter new Password" name="pwd">
                <?php echo $err ?>  
            </div>
            <button type="submit" class="btn btn-danger btn-lg btn-block">Change</button>
        </form>
    </body>
</html>