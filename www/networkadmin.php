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

$query = $datastore->query();
$query->kind('team');
$queryUser = $datastore->query();
$queryUser->kind('user');

$teams = $datastore->runQuery($query);
foreach ($teams as $team) {
    array_push($teamName, $team['teamName']);
    if ($team['numberOfVotes'] != 0) {
        array_push($score,  round($team['totalScore'] / $team['numberOfVotes'],2));
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
</head>

<body class="bg-light">
    <div class="container-sm py-4 my-5 bg-dark text-white rounded-lg">
        <canvas id="myChart" aria-label="Hello ARIA World" role="img"></canvas>
        <script>
            var teamName= <?php echo $jsTeamNameArray ?>;
            var teamScore= <?php echo $jsScoreArray ?>;
            let myChart = document.getElementById('myChart').getContext('2d');
            let massPopChart = new Chart(myChart, {
                type: 'bar', //type of chart
                data: {
                    labels: teamName,
                    datasets: [{
                        label: 'Mark',
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
        <form action="#" method="POST">
            <input type="submit" name="reset" class="btn btn-warning btn-lg btn-block" value="Reset">
            <input type="submit" name="back" class="btn btn-danger btn-lg btn-block" value="Log out">
        </form>
    </div>
</body>

</html>
