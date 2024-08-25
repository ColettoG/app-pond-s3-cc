<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Ponderada Page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the STUDENTS table exists. */
  VerifyStudentsTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the STUDENTS table. */
  $student_name = htmlentities($_POST['NAME']);
  $student_birthday = htmlentities($_POST['BIRTHDAY']);
  $student_grade = htmlentities($_POST['GRADE']);
  $student_course = htmlentities($_POST['COURSE']);

  if (strlen($student_name) || strlen($student_birthday) || strlen($student_grade) || strlen($student_course)) {
    AddStudent($connection, $student_name, $student_birthday, $student_grade, $student_course);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>BIRTHDAY</td>
      <td>GRADE</td>
      <td>COURSE</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="date" name="BIRTHDAY" />
      </td>
      <td>
        <input type="number" name="GRADE" min="0" max="100" />
      </td>
      <td>
        <input type="text" name="COURSE" maxlength="45" size="30" />
      </td>
      <td>
        <input type="submit" value="Add Student" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>BIRTHDAY</td>
    <td>GRADE</td>
    <td>COURSE</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM STUDENTS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$query_data[4], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add a student to the table. */
function AddStudent($connection, $name, $birthday, $grade, $course) {
   $n = mysqli_real_escape_string($connection, $name);
   $b = mysqli_real_escape_string($connection, $birthday);
   $g = (int)$grade; // Ensure grade is treated as an integer
   $c = mysqli_real_escape_string($connection, $course);

   $query = "INSERT INTO STUDENTS (NAME, BIRTHDAY, GRADE, COURSE) VALUES ('$n', '$b', $g, '$c');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding student data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyStudentsTable($connection, $dbName) {
  if(!TableExists("STUDENTS", $connection, $dbName))
  {
     $query = "CREATE TABLE STUDENTS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         BIRTHDAY DATE,
         GRADE INT,
         COURSE VARCHAR(45)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
