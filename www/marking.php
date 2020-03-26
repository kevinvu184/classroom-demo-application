<?php
require '../vendor/autoload.php';
use Google\Cloud\Datastore\DatastoreClient;
$datastore = new DatastoreClient();

$query=$datastore->query();
$query->kind('team');

$result=$datastore->runQuery($query);


if (!empty($_POST)) {
    foreach($_POST as $teamID => $score) {
        $key=$datastore->key('team','T'.strval($teamID));
        $team=$datastore->lookup($key);
        $team['TotalScore']=$team['TotalScore']+$score;
        $team['NumberOfVotes']=$team['NumberOfVotes']+1;
        $datastore->update($team);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Mark Evaluation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" type="text/css" media="screen" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" />
    <script src="main.js"></script>
</head>

<body>
    <div class="container">
        <form action="./marking.php" class="container-sm p-4 mt-5 bg-dark text-white ml-1" method="POST">
            <?php
            $keyUser=$datastore->key('user',$_COOKIE['auth']);
            $user=$datastore->lookup($keyUser);
            $i=0;/* hacky - find a way to query in GCPCloudNoSQL */
            foreach($result as $entity){
                $i++;               
                if($user['TeamID']!='T'.strval($i)){
                    echo '<div class="form-group row">'."\n";
                    echo '<label class="col-sm-2 col-form-label text-center">'.$entity['TeamName'].'</label>'."\n";
                    echo '<div class="col-sm-10">'."\n";
                    echo '<input name='.$i.' type="text" class="form-control" placeholder="Score 1-10"/>'."\n";
                    echo '</div>'."\n";
                    echo '</div>'."\n";
                }
            }
        ?>
                <div class="form-group row ">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Submit your Evaluation
                        </button>
                    </div>
                </div>
        </form>
    </div>
</body>

</html>