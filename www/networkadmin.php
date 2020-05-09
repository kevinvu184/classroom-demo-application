<?php
require '../vendor/autoload.php';

session_start();

if (empty($_COOKIE['auth'])) {
    header("Location: ./login.php");
}

//constructing array from gcp here
use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();

$teamName = array();
$score = array();

$querySlot = $datastore->query();
$querySlot->kind('slot');
$querySlot->order('DateAndTime');
$slots = $datastore->runQuery($querySlot);

$query = $datastore->query();
$query->kind('team');
$queryUser = $datastore->query();
$queryUser->kind('user');

$modal='';

$teams = $datastore->runQuery($query);
foreach ($teams as $team) {
    array_push($teamName, $team['teamName']);
    if ($team['numberOfVotes'] != 0) {
        array_push($score,  round($team['totalScore'] / $team['numberOfVotes'],2));
    }else{
        array_push($score,0);
    }
}

$jsTeamNameArray = json_encode($teamName);
$jsScoreArray = json_encode($score);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['back'])) {
        unset($_COOKIE['auth']);
        setcookie('auth', null, -1, '/');
        header("Location: ./login.php");
    } else if (isset($_POST['reset'])) {
        $teams = $datastore->runQuery($query);
        foreach ($teams as $team) {
            $team['numberOfVotes'] = 0;
            $team['totalScore'] = 0;
            $datastore->update($team);
        }
        $users = $datastore->runQuery($queryUser);
        foreach ($users as $user) {
            $user['vote'] = False;
            $datastore->update($user);
        }
        unset($_COOKIE['auth']);
        setcookie('auth', null, -1, '/');
        $_SESSION['reset'] = true;
        header("Location: ./login.php");
    }else if(isset($_POST['add'])){
             $hourSelect = $_POST['hour'];
             $dateSelect = strtok($_POST['day'],', ');
             $dateTimeSelect =  DateTime::createFromFormat('d-m-Y H:i',$dateSelect." ".$hourSelect);
             $dateTimeSelect->setTimeZone(new DateTimeZone('Australia/Sydney'));
             $idSlot=sha1($hourSelect.$dateSelect);
             
             $checkKey=$datastore->key('slot',$idSlot);
             $checkSlot=$datastore->lookup($checkKey);

             if(empty($checkSlot)){
                $keySlot=$datastore->key('slot',$idSlot);
                $entity=$datastore->entity($keySlot,['Status'=>'Available']);
                $entity['DateAndTime']=$dateTimeSelect;
                $entity['TeamName']='';

                $transaction=$datastore->transaction();
                $transaction->insert($entity);
                $transaction->commit();
                $modal = <<<EOT
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="modal">
        <h4 class="alert-heading text-center">Added Successfully</h4>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
EOT;
            
             }else{
                 $modal = <<<EOT
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="modal">
        <h4 class="alert-heading text-center">Slot already added</h4>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
EOT;
             }
             unset($_POST['day']);
             unset($_POST['hour']);

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src='script.js'></script>
</head>

<body class="bg-light">
    <?php echo $modal ?>
    <div class="container-sm py-4 my-5 bg-dark text-white rounded-lg">
        <div >
            <div class="row">
                <div class="col">
                    <button class="tablink btn btn-primary btn-lg btn-block" style="background-color: green;" onclick="openPage('News', this, 'green')" id="defaultOpen">Slot</button>
                </div>
                <div class="col">
                    <button class="tablink btn btn-primary btn-lg btn-block" onclick="openPage('Home', this, 'green')">Chart</button>
                </div>
            </div>
            
            <div id="Home" class="tabcontent rounded border border-white my-3" style="display: none;">
                <canvas id="myChart" aria-label="Hello ARIA World" role="img"></canvas>
                <script>
                    var teamName= <?php echo $jsTeamNameArray ?>;
                    var teamScore= <?php echo $jsScoreArray ?>;
                    let myChart = document.getElementById('myChart').getContext('2d');
                    Chart.defaults.global.defaultFontSize=18;
                    Chart.defaults.global.defaultFontColor='white';
                    let massPopChart = new Chart(myChart, {
                        type: 'bar',
                        data: {
                            labels: teamName,
                            datasets: [{
                                label:'Mark',
                                data: teamScore,
                                backgroundColor: '#66C7F4',
                                borderWidth: 1,
                                borderColor: '#eee',
                                hoverBorderWidth: 3,
                                barPercentage: 0.8,
                                barThickness: 30,
                                maxBarThickness: 40,
                                minBarLength: 2,
                                hoverBorderColor: '#fff'
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 10,
                                        fontColor: '#fff'
                                    }
                                }]
                            },
                            title: {
                                display: true,
                                text: 'RMIT NETWORK PROGRAMMING DEMO SCORE',
                                fontSize: 25,
                                fontColor: '#fff'
                            }
                        }
                    });
                </script>
            </div>
            
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
        </div>
        
        <div class="mb-2">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#addNewSlotModal">
                Add new slot
            </button>

            <!-- Modal -->
            <div class="modal fade" id="addNewSlotModal" tabindex="-1" role="dialog" aria-labelledby="addNewSlotModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-dark">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">New Slot</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="#" method="POST">
                                <div class="form-group">
                                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Day</label>
                                    <select name="day" class="custom-select my-1 mr-sm-2" id="daySelect">
                                        <script>seedDate()</script>
                                    </select>
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
                                </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                     <button type="submit" name="add" class="btn btn-success">Add</button>
                                </div>
                            </form>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>

        <form action="#" method="POST">
            <input type="submit" name="reset" class="btn btn-warning btn-lg btn-block" value="Reset">
            <input type="submit" name="back" class="btn btn-danger btn-lg btn-block" value="Log out">
        </form>
    </div>
</body>

</html>