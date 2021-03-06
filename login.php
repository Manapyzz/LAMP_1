<?php
require_once("dbconf.php");
session_start();

if(isset($_POST['logout'])){
    unset($_SESSION['user']);
}

if(isset($_SESSION['user'])){
    header("Location: /");
    exit;
}

$errormessage = null;
if(isset($_POST['username'])){

    global $config;
    $pdo = new PDO($config['host'], $config['user'], $config['password']);

    $stmt = $pdo->prepare("SELECT * FROM myGameId
                          WHERE users = :users"
    );
    $stmt->bindParam("users",$_POST['username']);
    $stmt->execute();
    $result = $stmt->fetch();

    if($result === false){
        $errormessage = "Wrong username";
    }elseif (empty($_POST["password"])) {
        $errormessage = "No password";
    }elseif (sha1($_POST["password"]) != $result["password"]) {
        $errormessage = "Wrong password";
    }else{
        $_SESSION['user'] = $result["users"];
        $_SESSION['best_score'] = $result['best_score'];

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
Merci de vous connecter :
<form method="POST">
    Login : <input type="text" name="username"><br>
    Password : <input type="text" name="password"><br>
    <input type="submit" value="Log in">
</form>
<?php echo $errormessage;?>
</body>
</html>