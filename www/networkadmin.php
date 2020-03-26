<?php 
require '../vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My Chart.js chart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.svg">
    <link rel="stylesheet" type="text/css" media="screen" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
</head>
<body>
    <div class="container">
        <canvas id="myChart">

        </canvas>
    </div>
    
    
    <?php
     echo "\n\n<script>\n";
        //constructing array from gcp here
        use Google\Cloud\Datastore\DatastoreClient;
        $datastore = new DatastoreClient();
        $teamName=array();
        $score=array();

        $query=$datastore->query();
        $query->kind('team');
        $result=$datastore->runQuery($query);
        
        foreach($result as $entity){
            array_push($teamName,$entity['TeamName']);
            if($entity['NumberOfVotes']!=0){
                array_push($score,$entity['TotalScore']/$entity['NumberOfVotes']);
            }
        }
        
        $jsTeamNameArray=json_encode($teamName);
        $jsScoreArray=json_encode($score);
        echo "var teamName=". $jsTeamNameArray . ";\n";
        echo "var teamScore=". $jsScoreArray . ";\n";

    echo "\n</script>\n";
    
    ?>

     <script>
        let myChart=document.getElementById('myChart').getContext('2d');
      

        let massPopChart=new Chart(myChart,{
            type:'bar', //type of chart
            data:{
                labels:teamName,
                datasets:[{
                    label:'Score',
                    data:teamScore,
                    backgroundColor:'#66C7F4',
                    borderWidth:1,
                    borderColor:'#777',
                    hoverBorderWidth:3,
                    hoverBorderColor:'#000'
                }]
            },
            options:{
                scales:{
                    yAxes: [{
                        ticks:{
                            suggestedMin:0,
                            suggestedMax:10
                        }
                    }]
                },
                title:{
                    display:true,
                    text:'RMIT NETWORK PROGRAMMING DEMO SCORE',
                    fontSize:25
                }
            }

        });
        
    </script>
</body>
</html>