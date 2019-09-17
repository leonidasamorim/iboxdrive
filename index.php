<?php
include("config.php");
?>
<html>
<head>
     <title>iBox - Storage Drive Free</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
</head>
<body onload='setFocusToTextBox()'>
<div class="jumbotron text-center">
    <h1>iBox</h1>
  <h2>Storage Drive Free</h2>
    <p>Put your url file and get new cloud storage link</p>
</div>
<div class="container">
    <form action="put.php" method="post">
    <div class="form-group">
        <label for="usr">URL:</label>
        <input name="url" type="text" class="form-control" id="url" autocomplete="new-password" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button
    <br><br><br><br>
        <div class="form-group">
            <label for="usr">Or get direct link - Example:</label>
            <p><?=$PROTOCOL?>://<?=$_SERVER['SERVER_NAME']?>/put/http://yourlinkhere</p>
        </div>
</form>
</div>
<div class="footer navbar-fixed-bottom">

<!-- Footer -->
<footer class="page-footer font-small blue">

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© <?=date('Y')?> Copyright:
    <a href="."> IboxDrive</a> - v1.3
  </div>
  <!-- Copyright -->

</footer>
<!-- Footer -->
</div>
</body>
</html>
