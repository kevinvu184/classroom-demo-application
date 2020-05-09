<?php
require '../vendor/autoload.php';
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();

$dayErr='';
$timeErr='';
$partnerIDErr='';
$registerPartnerSuccess=false;
$registerIndividual = true;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['slots'])){
        // route to available slots page
    }else if(isset($_POST['register'])){
        if(!empty($_POST['ID'])){
            $registerIndividual=false;
            $partnerID=$_POST['ID'];
            $keyPartner=$datastore->key('user',$partnerID);
            $userPartner = $datastore->lookup($keyPartner);
            
        }

        if($_POST['hour']=='Choose hour...'||$_POST['day']=='Choose Date...'||$_POST['ID']==$_COOKIE['auth']||$registerIndividual == false && empty($userPartner)||($userPartner['registerDemo']==true)){
            $dayErr= ($_POST['day']=='Choose Date...') ? '<small class="form-text text-danger">Please choose date</small>':'';
            $timeErr=($_POST['hour']=='Choose hour...') ? '<small class="form-text text-danger">Please choose hour</small>':'';
            
            if(!empty($_POST['ID'])){
                $partnerIDErr =($_POST['ID']==$_COOKIE['auth']) ? '<small class="form-text text-danger">You cannot type in your own ID</small>' :'';
                if($partnerIDErr==''){
                    $partnerIDErr=(empty($userPartner))? '<small class="form-text text-danger">Your partner has not registered yet</small>':'';
                }
                if($partnerIDErr == ''){
                    $partnerIDErr = ($userPartner['registerDemo']==true)? '<small class="form-text text-danger">This user has already registered</small>':'';
                }
            }
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
                $teamID = $_COOKIE['auth'].$_POST['ID'];
                $id = $_COOKIE['auth'];
                $key = $datastore->key('user', $id);
                $user = $datastore->lookup($key);

                if($registerIndividual==false){
                    $userPartner = $datastore->lookup($keyPartner);
                    $teamName=$user['name'].' and '.$userPartner['name'];
                }else{
                    $teamName=$user['name'];
                }
            
                $keyTeam=$datastore->key('team',$teamID);
                $entity=$datastore->entity($keyTeam,['teamName'=>$teamName]);
                $entity['numberOfVotes']=0;
                $entity['totalScore']=0;

                $transaction=$datastore->transaction();
                $transaction->insert($entity);
                $transaction->commit();

               
                $user['teamID']=$keyTeam;
                $user['registerDemo']=true;
                $datastore->update($user);

                if($registerIndividual == false){
                    $userPartner['teamID']=$keyTeam;
                    $userPartner['registerDemo']=true;
                    $datastore->update($userPartner);
                }
               
                $slots['Status']='Taken';
                $slots['Team']=$keyTeam;
                $datastore->update($slots);

                $registerSuccess=true;
             }
        }
         if($registerSuccess==true){
            //direct back to log in page with congrats message for successful registration
            header("Location: ./main.php");
        }else{
            $dayErr='<small class="form-text text-danger">Slot has already been taken or not added yet</small>';
            $timeErr='<small class="form-text text-danger">Slot has already been taken or not added yet</small>';
        }
    }       
    }
}

$querySlot = $datastore->query();
$querySlot->kind('slot');
$querySlot->order('DateAndTime');
$slots = $datastore->runQuery($querySlot);


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
    <div class="container-sm py-4 my-5 bg-dark text-white rounded-lg">
        <div id="News" class="tabcontent rounded border border-white my-3">
            <h3 class="text-center">SLOT STATUS</h3>
            <table class="table table-dark table-hover">
                <thead>
                    <tr class="bg-info">
                        <th scope="col">#</th>
                        <th scope="col">Team name</th>
                        <th scope="col">Demo Date</th>
                        <th scope="col">Demo Time</th>
                        <th scope="col">Slot Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $slotCounter = 1; ?>
                    <?php foreach($slots as $slot): ?>
                    <tr class="bg-light text-dark">
                        <th scope="row"> <?php echo $slotCounter++; ?> </th>
                        <td> <?php echo ($slot['Status'] == "Available") ? "" : $slot['TeamName']; ?> </td>
                        <td> <?php echo $slot['DateAndTime']->format('Y-m-d'); ?> </td>
                        <td> <?php echo $slot['DateAndTime']->format('H:i:s'); ?> </td>
                        <td> <?php echo $slot['Status']; ?> </td>
                    </tr>
                <?php endforeach; ?>
            </table>  
        </div>
        <form action="#" method="POST" >
            <div class="form-group">
                <label for="partnerID">Your partner ID</label>
                <input id="partnerID" type="text" class="form-control" placeholder="Leave blank if you demo individually" name="ID">
            </div>
            <?php echo $partnerIDErr ?>                    
            <div class="form-group">
                <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Day</label>
                <select name="day" class="custom-select my-1 mr-sm-2" id="daySelect">
                <script>seedDate()</script>
                </select>
                <?php echo $dayErr ?>                    
            </div>
            <div class="form-group">
                <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Hour</label>
                <select name="hour" class="custom-select my-1 mr-sm-2" id="hourSelect">
                    <option selected>Choose hour...</option>
                    <option>09:00</option>
                    <option>10:00</option>
                    <option>11:00</option>
                    <option>14:00</option>
                    <option>15:00</option>
                    <option>16:00</option>
                    <option>17:00</option>
                </select>
                <?php echo $timeErr ?>          
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-lg btn-block">Register</button>  
        </form>
    </div>
</body>

</html>
