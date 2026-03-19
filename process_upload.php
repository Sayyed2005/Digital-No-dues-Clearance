<?php
include "config.php";

if(isset($_POST['upload']))
{
    $dept = (int)$_POST['dept'];
    $year = (int)$_POST['year'];
    $division = (int)$_POST['division'];

    $file = $_FILES['file']['tmp_name'];

    $handle = fopen($file, "r");

    // Skip header row
    fgetcsv($handle);

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO students (name, prn, dept_id, year_id, division_id) VALUES (?, ?, ?, ?, ?)");

    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
        // Skip empty rows
        if(empty($data[1]) || empty($data[2])) {
            continue;
        }

        // CSV structure: [0]=id, [1]=name, [2]=prn
        $name = trim($data[1]);
        $prn  = trim($data[2]);

        $stmt->bind_param("ssiii", $name, $prn, $dept, $year, $division);
        $stmt->execute();
    }

    fclose($handle);

    echo "Upload Successful";
}
?>