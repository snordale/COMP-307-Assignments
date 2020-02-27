<?php
session_start();
?>

<head>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>
  <?php include 'menu.html';?>
  <?php include 'mysql_connect.php';?>
  <p>
  <h1>Posts</h1>
  <?php
  
    $stmt = $conn->prepare("select * from post");

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
      print('<div class="no-posts-alert">No posts available.</div>');
    }

    print("<div class='all-posts'>");
    while($row = $result->fetch_assoc()) {
      $stmt2 = $conn->prepare("select * from user where id=?");
      $stmt2->bind_param( 'i',
        $row['creator']
      );
      $stmt2->execute();
      $result2 = $stmt2->get_result();
      $row2 = $result2->fetch_assoc();
      print("
      <div class='post'>
        <div class='label'>
          <div class='title'>Title</div>"
          .htmlspecialchars($row['title']).
        "</div>
        <div class='label'>
          <div class='creator'>By</div>"
          .htmlspecialchars($row2['username']).'
        </div>
        <div class="content">'.htmlspecialchars($row['content']).'</div>
      </div>');
    }
    print("</div>");
    ?>
    
    <?php
    if (isset($_SESSION['username'])) {
      print('<div class="posting-container">');
      print('<h3 class="post-prompt">Create a post as '.htmlspecialchars($_SESSION['username']).'</h3>');
      print('
      <h4>Title</h4>
      <input type="text" name="title" class="title-input"/>
      <h4>Content</h4>
      <textarea type="text" name="content" class="content-input" rows="6"></textarea><br><br>
      <input class="submit-btn" type="submit"/>
      </form>
      ');
      print('</div>');
    }
    else {
      print("You need to log in to make posts.");
    }
  ?>
  </p>
</body>

<script>
  let errorMessage = $('.posting-container').prepend("<h5></h5>").children('h5').addClass('error-message');
  let postPrompt = $('.post-prompt');
  errorMessage.insertAfter(postPrompt);

  let body = $('body');
  let submitBtn = $('.submit-btn');

  let username;
  if ($('.post-prompt').html()) {
    let promptArr = $('.post-prompt').html().split(" ");
    username = promptArr[4];
  }

  submitBtn.on('click', function(e) {
    let titleInput = $('.title-input').val();
    let contentInput = $('.content-input').val();
    if (titleInput == "") {
      $('.error-message').html('Title cannot be empty.');
    }
    else if (contentInput == "") {
      $('.error-message').html('Content cannot be empty.');
    }
    else {
      $('.error-message').html('');
      $('.no-posts-alert').html('').css('display', 'none');
      $.ajax({
        method: "POST",
        url: "post.php",
        data: { title: titleInput, content: contentInput }
      })
      .done(function(response) {
        showSuggestions(response);
      })
      .fail(function(jqXHR) {
        alert("error");
      })
      .always(function() {
        console.log('All done');
      });
      
      var lt = /</g, 
          gt = />/g, 
          ap = /'/g, 
          ic = /"/g;
          titleInput = titleInput.toString().replace(lt, "&lt;").replace(gt, "&gt;").replace(ap, "&#39;").replace(ic, "&#34;");
          contentInput = contentInput.toString().replace(lt, "&lt;").replace(gt, "&gt;").replace(ap, "&#39;").replace(ic, "&#34;");
          
      let newPostEl = "<div class='post'><div class='label'><div class='title'>Title</div>" + titleInput 
        + "</div><div class='label'><div class='creator'>By</div>" + username + '</div><div class="content">' 
        + contentInput + '</div></div>';
      $('.all-posts').append(newPostEl);

      $('.title-input').val('');
      $('.content-input').val('');
    };
  });
</script>