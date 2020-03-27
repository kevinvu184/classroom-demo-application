<?php
require '../vendor/autoload.php';
if (isset($_COOKIE['auth'])) {
    header("Location: ./main.php");
}

# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();

$pwdErr = '';
$nameErr = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id']) || empty($_POST['pwd'])) {
        if (empty($_POST['id'])) {
            $nameErr = '<small class="form-text text-muted">Name cannot be empty.</small>';
        }
        if (empty($_POST['pwd'])) {
            $pwdErr = '<small class="form-text text-muted">Password cannot be empty.</small>';
        }
    } else {
        $id = $_POST['id'];
        if (is_numeric($_POST['pwd'])) {
            $pwd = intval($_POST['pwd']);
            // Construct key
            $key = $datastore->key('user', $id);
            // Query
            $user = $datastore->lookup($key);
            if (!empty($user)) {
                if ($user['password'] == $pwd) {
                    setcookie('auth', $id, time() + (86400 * 30), "/");
                    // Redirect for student
                    if (substr($_POST['id'], 0, 1) == 's') {
                        header("Location: ./main.php");
                    }
                    //redirect for network admin
                    else {
                        header("Location: ./networkadmin.php");
                    }
                } else {
                    $pwdErr = '<small class="form-text text-muted">Password is incorrect.</small>';
                }
            } else {
                $nameErr = '<small class="form-text text-muted">User name does not exist</small>';
            }
        } else {
            $pwdErr = '<small class="form-text text-muted">Password must be a number.</small>';
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
    <link rel="shortcut icon" href="favicon.svg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body class="bg-secondary">
    <form action="#" class="container-sm p-4 mt-5 bg-dark text-white rounded-lg" method="POST">
        <div class="form-group">
            <label for="id">ID</label>
            <input id="id" type="text" class="form-control" placeholder="Enter ID with 's'" name="id">
            <?php echo $nameErr ?>
        </div>
        <div class="form-group">
            <label for="pwd">Password</label>
            <input id="pwd" type="password" class="form-control" placeholder="Enter Password" name="pwd">
            <?php echo $pwdErr ?>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
    </form>
</body>

</html>