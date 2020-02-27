<?php
// https://www.w3schools.com/php/php_sessions.asp
// session_start must be run before any other output
session_start();
unset($_SESSION['username']);
unset($_SESSION['id']);
?>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <?php include 'menu.html';?>
  <p>
    You are now logged out.
  </p>
</body>