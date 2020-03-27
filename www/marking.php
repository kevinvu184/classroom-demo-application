<?php
require '../vendor/autoload.php';
session_start();
if (empty($_COOKIE['auth'])) {
    header("Location: ./login.php");
}

use Google\Cloud\Datastore\DatastoreClient;

$datastore = new DatastoreClient();
$query = $datastore->query();
$query->kind('team');
$teams = $datastore->runQuery($query);

$userKey = $datastore->key('user', $_COOKIE['auth']);
$user = $datastore->lookup($userKey);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $teamID => $score) {
        $teamKey = $datastore->key('team', $teamID);
        $currentTeam = $datastore->lookup($teamKey);
        $currentTeam['totalScore'] = $currentTeam['totalScore'] + intval($score);
        $currentTeam['numberOfVotes'] = $currentTeam['numberOfVotes'] + 1;
        $datastore->update($currentTeam);
    }
    unset($_COOKIE['auth']);
    setcookie('auth', null, -1, '/');
    $_SESSION['success'] = true;
    header("Location: ./login.php");
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
    <script src='script.js'></script>
</head>

<body class="bg-secondary">
    <form action="#" class="container-sm p-4 mt-5 bg-dark text-white rounded-lg" method="POST" onsubmit='return markValidation();'>
        <?php
            foreach ($teams as $team) {
                if ($team->key() != $user['teamID']) {
                    echo '<div class="form-group row">' . "\n";
                        echo '<label class="col-md-2 col-form-label text-center">' . $team['teamName'] . '</label>' . "\n";
                        echo '<div class="col-md-10">' . "\n";
                            echo '<input name=' . $team->key()->pathEndIdentifier() . ' type="text" class="form-control" placeholder="Score 1-10"/>' . "\n";
                        echo '</div>' . "\n";
                    echo '</div>' . "\n";
                }
            }
        ?>
        <div class="form-group row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <button type="submit" class="btn btn-success btn-lg btn-block">
                    Submit your Evaluation
                </button>
            </div>
        </div>
    </form>
</body>

</html>