<head>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<?php
session_start();
?>
<body>
  <?php include 'menu.html';?>
  <?php include 'mysql_connect.php';?>

  <?php
    $stmt = $conn->prepare("select * from user where username=?");
    
    $stmt->bind_param("s", 
      $username
    );

    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
      print('No account found');
    }
    else if ($username == '' || $password == ''){
      print('Username and Password cannot be empty');
    }
    else {
      $row = $result->fetch_assoc();
      if (password_verify($_POST['password'], $row['password'])) {
        print('Hi '.htmlspecialchars($row['username']).', you are logged in.');
        $_SESSION['username'] = $row['username'];
        $_SESSION['id'] = $row['id'];
        
      }
      else {
        print('Wrong password.');
      }
    }

  ?>
  <p>
    <?php
      
    ?>
  </p>
</body>