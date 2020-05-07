<?php
require '../vendor/autoload.php';
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();

$dayErr='';
$timeErr='';
$partnerIDErr='';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['slots'])){
        // route to available slots page
    }else if(isset($_POST['register'])){
        if($_POST['hour']=='Choose hour...'||$_POST['day']=='Choose date...'){
            $dayErr= ($_POST['hour']=='Choose hour...') ? '<small class="form-text text-danger">Please choose hour</small>':'';
            $timerErr=($_POST['day']=='Choose date...') ? '<small class="form-text text-danger">Please choose date</small>':'';
        }else{
        $hourSelect = $_POST['hour'];
        $dateSelect = strtok($_POST['day'],', ');
        $dateTimeSelect =  DateTime::createFromFormat('d-m-Y H:i',$dateSelect." ".$hourSelect);


        $query = $datastore->query();
        $query->kind('slot');
        $query->filter('Status', '=', 'Available');
        $res = $datastore->runQuery($query);
        $registerSuccess=false;
        foreach($res as $slots){
            $datetimeServer = $slots['DateAndTime'];
             if($datetimeServer==$dateTimeSelect){
                //build team => how to create a new team with ID ? and add to database
                $keyTeam=$datastore->key('team','T3');
                $entity=$datastore->entity($keyTeam,['teamName'=>'Tuan']);
                $entity['numberOfVotes']=0;
                $entity['totalScore']=0;

                $transaction=$datastore->transaction();
                $transaction->insert($entity);
                $transaction->commit();


                // TODO update teamID in "user" database


                //update slot status
                $slots['Status']='Taken';
                $slots['Team']='T3';
                $datastore->update($slots);

                $registerSuccess=true;
             }
        }
         if($registerSuccess==true){
            //direct back to log in page with congrats message on successful registration
        }else{
            $dayErr='<small class="form-text text-danger">Slot has already been taken</small>';
            $timeErr='<small class="form-text text-danger">Slot has already been taken</small>';
        }
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
    <form action="#" class="container-sm py-4 my-5 bg-dark text-white rounded-lg" method="POST" onsubmit="return validateRegistration();">
        <div class="form-group">
            <label for="partnerID">Your partner ID</label>
            <input id="partnerID" type="text" class="form-control" placeholder="Leave blank if you demo individually" name="ID">
        </div>
        <?php echo $partnerIDErr ?>                    
        <div class="form-group">
            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Day</label>
            <select name="day" class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">
                <option selected>Choose date...</option>
                <option>13-05-2020, Wednesday</option>
                <option>14-05-2020, Thursday</option>
                <option>15-05-2020, Friday</option>
            </select>
            <?php echo $dayErr ?>                    
        </div>
        <div class="form-group">
            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Hour</label>
            <select name="hour" class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">
                <option selected>Choose hour...</option>
                <option>09:00</option>
                <option>10:00</option>
                <option>11:00</option>
                <option>14:00</option>
                <option>15:00</option>
                <option>16:00</option>
            </select>
            <?php echo $timeErr ?>          
        </div>
        <button type="submit" name="slots" class="btn btn-info btn-lg btn-block">See Available Slot</button>  
        <button type="submit" name="register" class="btn btn-primary btn-lg btn-block">Register</button>  
    </form>
</body>

</html>
