<?php
  include_once 'dbh.inc.php';
  session_start();
?>
<!DOCTYPE html>
<html>
<head>


  <style>
  ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #333;
    border-top-right-radius: 18px;
    border-top-left-radius: 18px;
  }
  li {
    float: left;
  }
  li a {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
  }
  li a:hover {
    background-color: #111;
  }
  </style>




  <title>Arcadia Transfer Equivalency</title>
  <link rel ="stylesheet" type= "text/css" href="style.css">
</head>
<body>

<ul>
  <li><a class="active" href="https://registrar-capstone.herokuapp.com/">Home</a></li>
  <li><a href="about.html">About</a></li>
  <li><a href="login.php">Registrar Login</a></li>
</ul>


<body>
  <?php
  $sql = "SELECT DISTINCT school FROM data1;";
  $result = mysqli_query($conn, $sql);
  $resultCheck = mysqli_num_rows($result);
?>
<div class ="intro">
<h1>Arcadia Transfer Equivalency</h1>
<br>

</div>
<div class = "school">
<h3>Please select the university you previously attended</h3>
<form action = "" method = "post" name = "school_form">
<select name="school" size ="10">
  <?php
  if ($resultCheck > 0){
    while($row = mysqli_fetch_assoc($result)){
      // inserts all data as array
      echo "<option >". $row['school'] ."</option>";
          }
  }
  ?>
</select>
<br>
<input type ="submit" name = "submit_school" value = "Enter">
</form>
<?php
$selected_school = "";
if(isset($_POST['submit_school'])){
  $selected_school = mysqli_real_escape_string($conn, $_POST['school']);
  $_SESSION['selected_school'] = $selected_school;
  echo "You have selected: " .$selected_school;
}
 ?>
</div>

<div class ="courses">
<h3>Please select the courses you took</h3>

<form action = "" method ="post" name ="course_form">
  <?php
  //create template
  $sql2 = "SELECT transfer_course, transfer_title FROM data1 WHERE school = ? ORDER BY transfer_course ASC";
  //create prepared statement
  $stmt = mysqli_stmt_init($conn);
  //prepare prepared Statement
  //if it doesn't work
  if(!mysqli_stmt_prepare($stmt, $sql2)) {
    echo "SQL statement failed 61";
  } else {
    //bind parameters to the placeholder
    //s is for one single string value
    mysqli_stmt_bind_param($stmt, "s", $selected_school);
    //run parameters inside database
    mysqli_stmt_execute($stmt);
    $result2 = mysqli_stmt_get_result($stmt);

    while($row2 = mysqli_fetch_assoc($result2)){
      // inserts all data as array
    echo "<input type='checkbox' name ='boxes[]' value = '" . $row2['transfer_course'] . "' >" . $row2['transfer_course'] . "<br>";
          }
  }
  ?>
<br>
<input type ="submit" name = "submit_courses" value = "<?php if(isset($selected_school)) echo "Enter"; ?>">
</form>
<br>
<?php
$selected_course = "";
if(isset($_POST['submit_courses'])){//to run PHP script on submit
  if(!empty($_POST['boxes'])){
    // Loop to store and display values of individual checked checkbox.
    foreach($_POST['boxes'] as $selected_course){
      $_SESSION['selected_course'] = $selected_course;
      echo "You have selected: " . $selected_course . "</br>";
  }
}
}
 ?>
</div>


<div class = "output">
<h3>Course Equivalency</h3>
<center>
<table border = "1" bordercolor="black">

 <tr>
   <th>Transfer Course</th>
   <th>Arcadia Course Equivalent</th>
   <th>Arcadia Curricular Requirement</th>
 </tr>
 <tr>
</center>
   <?php
   //$sql3 = "SELECT arcadia_course, curricular_requirement FROM data WHERE school = ? AND transfer_course IN ()";
   $sql3 = "SELECT arcadia_course, transfer_course, curricular_requirement FROM data1 WHERE school = '" . $_SESSION['selected_school'] .
   "' AND transfer_course IN (";
     $loopNum = 0;
     foreach($_POST['boxes'] as $selected_course){
       $sql3 = $sql3 . " '" . $selected_course . "'";
       if(count($_POST['boxes']) - 1 > $loopNum)
       {
          $sql3 = $sql3 . ", ";
       }
       $loopNum++;
     }
     $sql3 = $sql3 . ")";
    if(mysqli_stmt_prepare($stmt, $sql3)){
      //mysqli_stmt_bind_param($stmt, "ss", $_SESSION['selected_school'], $_SESSION['selected_course']);
    }
    else{
      //echo "error";
      }
  //   echo $stmt;
   // echo $sql3;
   //$stmt = $sql3;
   mysqli_stmt_execute($stmt);
   $result3 = mysqli_stmt_get_result($stmt);
   //echo mysqli_num_rows($result3);
   while($row3 = mysqli_fetch_assoc($result3)){
     // inserts all data as array
       echo "<td>" . $row3['transfer_course'] . "</td> <td>" . $row3['arcadia_course'] . "</td> <td>" . $row3['curricular_requirement'] . "</td> </tr>";
      }


    ?>

 </table>

</div>

<!-- <div class ="other">
<h3>If your university or course was not listed, please enter it here</h3>
<textarea name="message" rows="10" cols="50">
</textarea>
</div> -->
</body>

</html>
