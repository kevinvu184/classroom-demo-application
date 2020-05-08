<?php
require '../vendor/autoload.php';

# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();

$pwdErr = '';
$idErr = '';
$nameRegex= "/^[A-Za-z .\-']{1,50}$/";
$nameErr='';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id']) || empty($_POST['pwd'])||empty($_POST['name'])) {
        if (empty($_POST['id'])) {
            $idErr = '<small class="form-text text-danger">ID cannot be empty.</small>';
        }
        if (empty($_POST['pwd'])) {
            $pwdErr = '<small class="form-text text-danger">Password cannot be empty.</small>';
        }
        if (empty($_POST['name'])) {
            $nameErr = '<small class="form-text text-danger">Name cannot be empty.</small>';
        }
    } else {
        $id = $_POST['id'];
        $Name=$_POST['name'];
        if (is_numeric($_POST['pwd'])&&preg_match($nameRegex,$Name)) {
            $pwd = intval($_POST['pwd']);
            $key=$datastore->key('user',$id);

            $entity=$datastore->entity($key,['password'=>$pwd]);
            $entity['name']=$Name;
            $entity['registerDemo']=false;
            $entity['vote']=false;
            $entity['admin']=false;

            $transaction=$datastore->transaction();
            $transaction->insert($entity);
            $transaction->commit();
            $modal = <<<EOT
             <div class="alert alert-success alert-dismissible fade show" role="alert" id="modal">
                <h4 class="alert-heading text-center">Register Successfully</h4>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="closeModal()">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
EOT;
            unset($_POST);
        } else if(!is_numeric($_POST['pwd'])) {
            $pwdErr = '<small class="form-text text-danger">In this software release password is your student number without "s".</small>';
        }else if(!preg_match($nameRegex,$Name)){
            $nameErr='<small class="form-text text-danger">Incorrect name format</small>';
        }
    }
}
?>

<!DOCTYPE html >
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" content="Peer-to-Peer marking system thats empower teachers.">
    <title>P2P Marking System</title>
    <link rel="shortcut icon" href="favicon.svg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src='script.js'></script>
</head>

<body class="bg-light">
    <?php echo $modal ?>
    <form action="#" class="container-sm py-4 my-5 bg-dark text-white rounded-lg" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" type="text" class="form-control" placeholder="Enter your name" name="name">
            <?php echo $nameErr ?>
        </div>
        <div class="form-group">
            <label for="id">ID</label>
            <input id="id" type="text" class="form-control" placeholder="Enter ID" name="id">
            <?php echo $idErr ?>
        </div>
        <div class="form-group">
            <label for="pwd">Password</label>
            <input id="pwd" type="password" class="form-control" placeholder="Enter Password" name="pwd">
            <?php echo $pwdErr ?>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Sign Up</button>  
    </form>
</body>

</html>
