<?php
require '../vendor/autoload.php';



# Create connection to gcloud datastore (NoSQL db) 
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();


$pwdErr = '';
$nameErr = '';
$nameRegex= "/^[A-Za-z .\-']{1,50}$/";
$firstNameErr='';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id']) || empty($_POST['pwd'])||empty($_POST['firstname'])) {
        if (empty($_POST['id'])) {
            $nameErr = '<small class="form-text text-danger">ID cannot be empty.</small>';
        }
        if (empty($_POST['pwd'])) {
            $pwdErr = '<small class="form-text text-danger">Password cannot be empty.</small>';
        }
        if (empty($_POST['firstname'])) {
            $firstNameErr = '<small class="form-text text-danger">Name cannot be empty.</small>';
        }
    } else {
        $id = $_POST['id'];
        $firstName=$_POST['firstname'];
        if (is_numeric($_POST['pwd'])&&preg_match($nameRegex,$firstName)) {
            $pwd = intval($_POST['pwd']);
            $key=$datastore->key('user',$id);

            $entity=$datastore->entity($key,['password'=>$pwd]);
            $entity['name']=$firstName;
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
        }else if(!preg_match($nameRegex,$firstName)){
            $firstNameErr='<small class="form-text text-danger">Incorrect name format</small>';
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
    <script src='script.js'></script>
</head>

<body class="bg-secondary">
    <?php echo $modal ?>
    <form action="#" class="container-sm py-4 my-5 bg-dark text-white rounded-lg" method="POST">
        <div class="form-group">
            <label for="id">Name</label>
            <input id="id" type="text" class="form-control" placeholder="Enter your name" name="firstname">
            <?php echo $firstNameErr ?>
        </div>
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
        <button type="submit" class="btn btn-primary btn-lg btn-block">Sign Up Now</button>  
    </form>
</body>

</html>
