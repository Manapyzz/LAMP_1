<?php
require_once("dbconf.php");
session_start();

global $config;
$pdo = new PDO($config['host'], $config['user'], $config['password']);

if(!isset($_SESSION['user'])){
    header("Location:login.php");
    exit;
}

if(isset($_POST['reset_best'])){
    unset($_SESSION['best_score']);
    myBestScore($pdo);
}

if(empty($_SESSION['choice']) || isset($_POST['reset'])){
    $choice  =  rand(0,100);
    $_SESSION['score'] = 0;
    $_SESSION['choice'] = $choice;
    saveMyGame($pdo);
}else{
    $choice = $_SESSION['choice'];
    
}


$response = null;

if( !isset($_POST['guess'])
    || empty($_POST['guess'])){
    $response = "Pas de nombre";
}else{
    $guess = $_POST['guess'];
    $_SESSION['score']++;
    if($guess > $choice) {
        $response = "C'est moins";
    }elseif($guess < $choice){
        $response = "C'est plus";
    }else{
        $response = "C'est gagné";
        if( !isset($_SESSION['best_score'])
            || $_SESSION['best_score'] > $_SESSION['score']){
            $_SESSION['best_score'] = $_SESSION['score'];
            myBestScore($pdo);
        }

        unset($_SESSION['choice']);
    }
    saveMyGame($pdo);

}

function myBestScore($pdo){

    $re = $pdo->prepare("UPDATE myGameId SET best_score = :best_score
                          WHERE users = :users"
    );
    $re->bindParam("best_score",$_SESSION['best_score']);
    $re->bindParam("users",$_SESSION['user']);
    $re->execute();
    $result = $re->fetch();
}

function saveMyGame($pdo){

    $saveMe = $pdo->prepare("UPDATE myGameId SET strokes = :score, plusOrLess = :response, answer = :choice
                          WHERE users = :users"
    );
    $saveMe->bindParam("score",$_SESSION['score']);
    $saveMe->bindParam("response",$_SESSION['response']);
    $saveMe->bindParam("choice",$_SESSION['choice']);
    $saveMe->bindParam("users",$_SESSION['user']);
    $saveMe->execute();
    $result = $saveMe->fetch();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Des papiers dans un bol </title>
</head>
<body>

<?php echo $response;?> <br>
Nombre de coup : <?php echo $_SESSION['score']; ?><br>
<em>[Meilleur score :
    <?php
    echo !isset($_SESSION['best_score'])
        ? "Pas de meilleur score"
        : $_SESSION['best_score'];
    ?>]</em>
<form method="POST">
    <input type="text" name="guess" autofocus>
    <input type="submit">
    <input type="submit" name="reset" value="reset">
    <input type="submit" name="reset_best" value="reset best">
</form>
<em>(La réponse est <?php echo $choice?>)</em>

<form method="POST" action="login.php">
    <input type="submit" name="logout" value="Logout">
</form>

<?php

    $pdo = new PDO($config['host'], $config['user'], $config['password']);

    $board = $pdo->prepare("SELECT users, best_score
                        FROM myGameId
                        ORDER BY best_score LIMIT 0,100");

    $board->execute();

    echo('<table border="1px">'.'<th>Pseudo</th><th>Score</th>');
    while($result = $board->fetch()){
        echo('<tr>'.'<td>' .$result['users'].'</td>'.'<td>'.$result['best_score'].'</td>'.'</tr>');
    }
    echo('</table>');

?>

</body>
</html>
