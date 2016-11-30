<?php
$logout="";
session_start();

if (isset($_SESSION['username'])) {
    $logout="<li><a href=\"login/logout.php\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout</a></li>";
}
?>
  <div class="jumbotron">
  <div class="container text-center">
    <h1>Price Tracker</h1>
    <p>NH Liquor & Wine Outlets</p>
  </div>
</div>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="grid.php">PriceTracker</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="grid.php">Home</a></li>
        <li><a href="table.php">Table</a></li>
        <li><a href="deals.php">Deals</a></li>
        <li><a target="_blank" href="http://liquorandwineoutlets.com">Stores</a></li>
        <li><a target="_blank" href="https://github.com/ailgup/PriceTracker">About</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="user.php"><span class="glyphicon glyphicon-user"></span> Your Account</a></li>
        <?php echo $logout?>
      </ul>
    </div>
  </div>
</nav>

