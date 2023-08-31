<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
          animation: waveBackground 11s infinite alternate;
        }
        @keyframes waveBackground {
        0% {
            background-color: #3498db; /* Стартов цвят */
        }
       25% {
        background-color: #f39c12;
        }
        50% {
            background-color: #e74c3c;
        }
        75% {
            background-color: #2ecc71;
        }
        100% {
            background-color: #3498db;
         }
       }
        .container{
          background: #fff;
          max-width: 600px;
          border: 1px solid #ccc;
          border-radius: 4px;
        }
        .hidden {
            display: none;
        }
        .custom-button {
           background-color: #B0E2FF; 
           border: 0.1px solid white;
           border-radius: 50%; 
           padding: 8px 16px; 
           cursor: pointer;
        }

        .custom-button i {
           color: #fff; 
           font-size: 16px; 
        }
        .text-down{
            margin-top: 20px;
            font-size: 16px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
    <?php
$errors = array();

if(isset($_POST["submit"])) {
    $email = $_POST["email"];
    
    require_once "database.php";
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    
    $sql_check_name = "SELECT * FROM users WHERE full_name = ?";
    $stmt_check_name = mysqli_stmt_init($conn);
    
    if(mysqli_stmt_prepare($stmt_check_name, $sql_check_name)) {
        mysqli_stmt_bind_param($stmt_check_name, "s", $_POST["fullname"]);
        mysqli_stmt_execute($stmt_check_name);
        $result_name = mysqli_stmt_get_result($stmt_check_name);
        
        if(mysqli_num_rows($result_name) > 0) {
            $errors[] = "This name is already taken. Please choose another one.";
        }
    }
    
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $errors[] = "Email already exists!";
        } else {
            $fullName = $_POST["fullname"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            $gender = $_POST["gender"];
            $birth_day = $_POST["birth_day"];
            $birth_month = $_POST["birth_month"];
            $birth_year = $_POST["birth_year"];
            
            
            if ($birth_month < 1 || $birth_month > 12) {
                $errors[] = "Invalid birth month";
            }
            
            
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $birth_month, $birth_year);
            if ($birth_day < 1 || $birth_day > $days_in_month) {
                $errors[] = "Invalid birth day";
            }
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            if(empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat) OR empty($birth_day) OR empty($birth_month) OR empty($birth_year)){
                $errors[] = "All fields are required";
            }
            
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = "Email is not valid";
            }
            if(strlen($password) < 8){
                $errors[] = "Password must be at least 8 characters long";
            }
            if($password !== $passwordRepeat){
                $errors[] = "Password does not match";
            }
            
            if(count($errors) > 0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                $birthdate = "$birth_year-$birth_month-$birth_day"; // Формиране на датата на раждане
                
                $sql = "INSERT INTO users (full_name, email, password, gender, birth_day, birth_month, birth_year) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)){
                    mysqli_stmt_bind_param($stmt, "ssssiii", $fullName, $email, $passwordHash, $gender, $birth_day, $birth_month, $birth_year);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                } else {
                    die("Something went wrong");
                }
            }
        }
    } else {
        die("Something went wrong");
    }
}
?>

        <form action="registration.php" method="post" novalidate>
            <div id="step1">
                <div class="form-group">
                    <label for="fullname">Въведете име:</label>
                    <input type="text" class="form-control" name="fullname" id="fullname" required autocomplete="off">
                </div>
                <button type="button" class="custom-button" onclick="nextStep('step1', 'step2')">
                 <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>

            <div id="step2" class="hidden">
             <div class="form-group">
             <label for="email">Email:</label>
             <input type="email" class="form-control" name="email" id="email" required>
            </div>
             <button type="button" class="custom-button" onclick="prevStep('step2', 'step1')">
             <i class="fa-solid fa-angle-left"></i>
            </button>
            <button type="button" class="custom-button" onclick="nextStep('step2', 'step3')">
            <i class="fa-solid fa-angle-right"></i>
            </button>
            </div>
            <div id="step3" class="hidden">
                <div class="form-group">
                    <label for="password">Парола:</label>
                    <input type="password" class="form-control" name="password" id="password" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="repeat_password">Потвърдете паролата:</label>
                    <input type="password" class="form-control" name="repeat_password" id="repeat_password" required>
                </div>
                <div class="form-group">
                    <label for="gender">Пол:</label>
                    <select class="form-control" name="gender" id="gender" required>
                        <option value="male">Мъжки</option>
                        <option value="female">Женски</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birth_day">Ден на раждане:</label>
                    <input type="number" class="form-control" name="birth_day" id="birth_day" min="1" max="31" required>
                </div>
                <div class="form-group">
                    <label for="birth_month">Месец на раждане:</label>
                    <input type="number" class="form-control" name="birth_month" id="birth_month" min="1" max="12" required>
                </div>
                <div class="form-group">
                    <label for="birth_year">Година на раждане:</label>
                    <input type="number" class="form-control" name="birth_year" id="birth_year" min="1900" max="2099" required>
                </div>
                <button type="button" class="custom-button" onclick="prevStep('step2', 'step1')">
                 <i class="fa-solid fa-angle-left"></i>
                </button>
                <input type="submit" class="btn btn-primary" value="Регистрация" name="submit" onclick="redirectToLogin()">
            </div>
        </form>
        <div class="text-down">
            <p>Имате съществуващ акаунт? <a href="login.php">Вход</a></p>
        </div>
    </div>
    <script>
  function nextStep(currentStepId, nextStepId) {
    const currentStep = document.getElementById(currentStepId);
    const nextStep = document.getElementById(nextStepId);

    const invalidInputs = currentStep.querySelectorAll('input:invalid');

    if (invalidInputs.length > 0) {
     
      const errorMessage = "Моля, попълнете всички полета коректно.";
      alert(errorMessage);

      
      invalidInputs.forEach(input => {
        input.setCustomValidity(errorMessage);
      });
    } else {
      
      currentStep.classList.add('hidden');
      nextStep.classList.remove('hidden');
    }
  }

  function prevStep(currentStepId, prevStepId) {
    const currentStep = document.getElementById(currentStepId);
    const prevStep = document.getElementById(prevStepId);

    
    currentStep.classList.add('hidden');
    prevStep.classList.remove('hidden');
  }

  function redirectToLogin() {
    window.location.href = "login.php";
  }
</script>
</body>
</html>