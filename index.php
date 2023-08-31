<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

$userInfo = $_SESSION["user"];
$email = $userInfo; // Вземете имейла от сесията

$sql = "SELECT gender, birth_day, birth_month, birth_year FROM users WHERE email = '{$email}'"; // Включване на birth_day, birth_month и birth_year
$result = mysqli_query($conn, $sql);

$gender = "Unknown"; // Дефинирайте стойност по подразбиране
$birthDate = "Unknown"; // Дефинирайте стойност по подразбиране

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $gender = $row["gender"];
    
    // Създаване на пълната дата на раждане
    $birthDay = $row["birth_day"];
    $birthMonth = $row["birth_month"];
    $birthYear = $row["birth_year"];
    $birthDate = "{$birthDay}-{$birthMonth}-{$birthYear}";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
</head>
<body>
<div class="container">
        <h1>Welcome to dashboard</h1>
        <p>Your selected gender: <?php echo $gender; ?></p>
        <p>Your birthdate: <?php echo $birthDate; ?></p>
        <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>
</body>
</html>