<?php

//Delete previously stored data in table men to renew data
$sql_delete_table_men = "DELETE FROM men";
if ($conn->query($sql_delete_table_men) == FALSE) {
    echo "Failed to delete data from table men:". $conn->error . "<br>";
}

//Delete previously stored data in table women to renew data
$sql_delete_table_men = "DELETE FROM women";
if ($conn->query($sql_delete_table_men) == FALSE) {
    echo "Failed to delete data from table women:". $conn->error . "<br>";
}

//Create table men with preferences as X.X.X...X
$sql_create_table = "CREATE TABLE IF NOT EXISTS men(
    id INT NOT NULL PRIMARY KEY,
    preferences VARCHAR(255)
)";
if ($conn->query($sql_create_table) == FALSE) {
    echo "Failed to create table men:". $conn->error . "<br>";
}


//Create table women with preferences as X.X.X...X
$sql_create_table = "CREATE TABLE IF NOT EXISTS women(
    id INT NOT NULL PRIMARY KEY,
    preferences VARCHAR(255)
)";
if ($conn->query($sql_create_table) == FALSE) {
    echo "Failed to create table women:". $conn->error . "<br>";
}

//If everything is submitted insert data for user and data for non-users
if (!empty($_POST['preferences']) && !empty($_POST['men']) && !empty($_POST['women'])) {
    $id = 1;
    $preferences = $_POST["preferences"];
    $nm = $_POST["men"];
    $nw = $_POST["women"];

    $_SESSION['men'] = $_POST["men"];
    $_SESSION['women'] = $_POST["women"];

    //Set data for user
    $sql_insert = "INSERT INTO men (id, preferences) 
                            VALUES ('$id', '$preferences')";
    if ($conn->query($sql_insert) === FALSE) {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }

    function RandomPreference($num) {
        $preferences = range(1, $num);
        shuffle($preferences);
        return implode('.', $preferences);
    }

    //Set data for non-users men
    for ($i = 2; $i <= $nm; ++$i) {
        $random_preference = RandomPreference($nw); 
        $sql_insert_record = "INSERT INTO men (id, preferences) 
                                        VALUES ('$i', '$random_preference')";
        if ($conn->query($sql_insert_record) == FALSE) {
            echo "Failed to insert prefernces for men $i:". $conn->error . "<br>";
        } 
    }

    //Set data for non-users women
    for ($i = 1; $i <= $nw; ++$i) {
        $random_preference = RandomPreference($nm); 
        $sql_insert_record = "INSERT INTO women (id, preferences) 
                                        VALUES ('$i', '$random_preference')";
        if ($conn->query($sql_insert_record) == FALSE) {
            echo "Failed to insert prefernces for women $i:". $conn->error . "<br>";
        } 
    }
}

?>