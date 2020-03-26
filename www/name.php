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
    if (empty($_POST['name'])) {
        $err = '<small class="form-text text-muted">Name cannot be empty.</small>';
    } else {
        $key = $datastore->key('user', $id);
        $user = $datastore->lookup($key);
        $user->setProperty('name', $_POST['name']);
        $datastore->update($user);
        header("Location: ./main.php");
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>P2P Marking System</title>
        <link rel="shortcut icon" href="/favicon.svg">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body class="bg-light">
        <form action="./name.php" class="container-sm p-4 mt-5 bg-dark text-white" method="POST">
            <div class="form-group">
                <label for="name">New Name</label>
                <input type="text" class="form-control" placeholder="Enter new Name" name="name">
                <?php echo $err ?>
            </div>  
            <button type="submit" class="btn btn-danger btn-lg btn-block">Change</button>
        </form>
    </body>
</html>