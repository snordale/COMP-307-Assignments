<head>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>
  <?php include 'menu.html';?>
  <?php include 'mysql_connect.php';?>

  <?php
    $check_stmt = $conn->prepare("select * from user where username=?");
    $check_stmt->bind_param("s",
      $username
    );
    
    $username = $_POST['username'];

    $check_stmt->execute();
    $check_results = $check_stmt->get_result();

    if ($check_results->num_rows != 0) {
      print('Username taken, try a different one.'.'<br />');
    }
    
    else if ($username == '' || $_POST['password'] == ''){
      print('Username and Password cannot be empty');
    }

    else {
      $stmt = $conn->prepare("insert into user (username, password) values (?, ?)");

      $stmt->bind_param("ss", 
        $username,
        $password
      );

      $password = password_hash(
        $_POST['password'],PASSWORD_DEFAULT);

      $success = $stmt->execute();

      if (!$success) {
        print('Signup failed: '. $stmt->error);
      }
      else {
        print('Signup successful');
      }
    }
  ?>
</body>