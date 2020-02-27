<?php
// https://www.w3schools.com/php/php_sessions.asp
// session_start must be run before any other output
session_start();
?>
<body>
  <?php include 'menu.html';?>
  <?php include 'mysql_connect.php';?>
  <p>
  <?php
    // check if $_SESSION['username'] is declared
    if (isset($_SESSION['username'])) {
      
      $stmt = $conn->prepare("insert into post (creator, title, content) values (?, ?, ?)");

      $stmt->bind_param("iss", 
        $creator,
        $title,
        $content
      );
      
      $creator = htmlspecialchars($_SESSION['id']);
      $title = ($_POST['title']);
      $content = ($_POST['content']);

      $success = $stmt->execute();
      if ($success) {
        print("Post completed.");
      }
      else {
        print("Post failed.". $stmt -> error);
        
      }
    }
    else {
      print('You are not logged in.');
    }
  ?>
  </p>
</body>