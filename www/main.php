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

if ($user['admin'] == true) {
    header("Location: ./networkadmin.php");
}

// only allow available vote if there are teams
if ($user['vote'] == False) {
    $noti = "<hr class='my-4'><p class='font-weight-bold'>Please vote now</p>";
    $disabledMarking = "";
} else {
    $noti = "<hr class='my-4'><p class='font-weight-bold'>Your vote has been recorded. Please come back later.</p>";
    $disabledMarking = "disabled";
}

if($user['registerDemo']==False){
    $disabledRegisterDemo="";
}else{
    $disabledRegisterDemo="disabled";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name'])) {
        header("Location: ./name.php");
    } else if (isset($_POST['pwd'])) {
        header("Location: ./password.php");
    } else if (isset($_POST['back'])) {
        unset($_COOKIE['auth']);
        setcookie('auth', null, -1, '/');
        header("Location: ./login.php");
    } else if (isset($_POST['mark'])) {
        header("Location: ./marking.php");
    }else if(isset($_POST['demoregister'])){
        header("Location: ./demoregister.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" content="Peer-to-Peer marking system thats empower teachers.">
    <title>P2P Marking System</title>
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container-sm py-4 my-5 bg-dark text-white rounded-lg">
        <div class="jumbotron text-dark">
            <h1 class="display-4">Welcome back <?php echo $name ?> !</h1>
            <?php echo $noti ?>
        </div>
        <form action="#" method="POST">
            <input type="submit" name="mark" class="btn btn-info btn-lg btn-block" value="Marking" <?php echo $disabledMarking ?>>
            <input type="submit" name="demoregister" class="btn btn-primary btn-lg btn-block" value="Demo Registration" <?php echo $disabledRegisterDemo ?>>
            <input type="submit" name="name" class="btn btn-warning btn-lg btn-block" value="Change Name">
            <input type="submit" name="pwd" class="btn btn-warning btn-lg btn-block" value="Change Password">
            <input type="submit" name="back" class="btn btn-danger btn-lg btn-block" value="Log out">
        </form>
    </div>
</body>

</html>