<?php
include "header.php";
$table = $prefix . 'pages-layolt';
$query = mysqli_query($connect, "SELECT * FROM `$table` WHERE page='Blocked_OS'");
$row   = mysqli_fetch_array($query);
?>
	  <div class="page-header">
        <div class="row">
          <div class="col-lg-12">
            <div class="bs-example">
              <div class="jumbotron">
                <center>
				<div class="well" style="background-color: #d9534f; color: white;">
                    <h2><?php
echo $row['text'];
?></h2>
                </div>
                    <p><img src="<?php
echo $row['image'];
?>" width="200px" height="200px" /></p>
                <p>Please contact with the webmaster of the website if you think something is wrong.</p>
				</center>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php
include "footer.php";
?>