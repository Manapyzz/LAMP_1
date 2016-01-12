<?php

    session_start();

    if(empty($_SESSION['choice'])){
        $choice = rand(1, 40);
        $_SESSION['choice'] = $choice;
    }else{

        $choice = $_SESSION['choice'];
    }

    $highscore = 0;

    $count = 0;

    if(empty($_SESSION['count'])){

        $_SESSION['count'] = 0;
    }

    echo $choice;
    $response = null;

    if(!isset($_POST['guess'])
    || empty($_POST['guess'])){

    $response = "Pas de nombre";

    }else{

        $guess = $_POST['guess'];

        if ($guess > $choice) {

            $response = "C'est moins";
            $_SESSION['count']++;
        } elseif ($guess < $choice) {

            $response = "C'est plus";
            $_SESSION['count']++;
        } else {

            $response = "C'est Gagné !";
            unset($_SESSION['choice']);
            unset($_SESSION['count']);
        }

    }


?>

<html>

<h2>Mon formulaire:</h2>

<form method="post">

    <input type="text" name="guess">
    <input type="submit"><br>
    <?php echo($response.'<br> Nombres de coup: '.($_SESSION['count'])).'<br> Meilleur score:'. ?>

</form>

</html>
