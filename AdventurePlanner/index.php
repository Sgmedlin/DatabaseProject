<?php
	// Initialize the session
	session_start();
?>

<!DOCTYPE html>
<html lang="en">
<html>
	<head>

		<!-- BootStrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<!-- Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

		<title>Home</title>

		<!-- FOR CAROUSAL - make image responsive -->
		<style>
  		/* Make the image fully responsive */
  		.carousel-inner img {
      			width: 100%;
     			height: 700px;
  		}
  		</style>
	</head>

	<body>

		<!-- NavBar must be changed in individual files -->
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="navbar-brand" href="#">Adventure Planner</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav mr-auto">
		      <li class="nav-item">
		        <a class="nav-link" href="index.php">Home</a>
		      </li>
		      <?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                	echo "<li class='nav-item'>
                	<a class='nav-link' href='profile.php'>Profile <span class='sr-only'>(current)</span></a>
                	</li>";
                }
                ?>
		      <li class="nav-item">
		        <a class="nav-link" href="adventures.php">Adventures </a>
		      </li>
					<li class="nav-item">
		        <a class="nav-link" href="trips.php"> Trips </a>
		      </li>

			<li class="nav-item dropdown">
      				<a class="nav-link dropdown-toggle" href="groups.php" id="navbardrop" data-toggle="dropdown">
        				Groups
      				</a>
      			 	<div class="dropdown-menu">
        				<a class="dropdown-item" href="create_group.php">Create a Group</a>
        				<a class="dropdown-item" href="groups.php">List of Groups</a>
      				</div>
   			</li>
		    </ul>
    		<?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                	echo "<span class='navbar-nav'>
                			<a href='logout.php' class='nav-link'> Log Out </a>
            			</span>";
                } else {
                	echo "<span class='navbar-nav'>
		    				<a href='login.php' class='nav-link'> Login </a>
		    			</span>";
                }
                ?>

		  </div>
		</nav>

		<!-- FOR CAROUSAL -->
	
		<div id="demo" class="carousel slide" data-ride="carousel">

  		<!-- Indicators -->
  		<ul class="carousel-indicators">
   			<li data-target="#demo" data-slide-to="0" class="active"></li>
   			<li data-target="#demo" data-slide-to="1"></li>
    			<li data-target="#demo" data-slide-to="2"></li>
 		</ul>
  
  		<!-- The slideshow -->
 		<div class="carousel-inner">
    	 	 <div class="carousel-item active">
      			<img src="ireland.jpg" alt="Los Angeles" width="1100" height="500">
			<div class="carousel-caption">
                    		<h1>Outdoors Adventure Planner</h1>
				<p>Your Adventure Begins Here</p>
				<a href="login.php" class="btn btn-info">Start Now</a>
                        </div><!-- end carousel-caption -->
    		</div>
    		 <div class="carousel-item">
      			<img src="shoes.jpg" alt="Chicago" width="1100" height="500">
			<div class="carousel-caption">
                    		<h1>Outdoors Adventure Planner</h1>
				<p>Your Adventure Begins Here</p>
				<a href="login.php" class="btn btn-info">Start Now</a>
                        </div><!-- end carousel-caption -->

    		</div>
    		 <div class="carousel-item">
      			<img src="iceland.jpg" alt="New York" width="1100" height="500">
			<div class="carousel-caption">
                    		<h1>Outdoors Adventure Planner</h1>
				<p>Your Adventure Begins Here</p>
				<a href="login.php" class="btn btn-info">Start Now</a>
                        </div><!-- end carousel-caption -->
    	 	</div>
  		</div>
  
  	<!-- Left and right controls -->
  	<a class="carousel-control-prev" href="#demo" data-slide="prev">
    		<span class="carousel-control-prev-icon"></span>
  	</a>
  	<a class="carousel-control-next" href="#demo" data-slide="next">
    		<span class="carousel-control-next-icon"></span>
  	</a>
	</div>

	</body>

</html>
