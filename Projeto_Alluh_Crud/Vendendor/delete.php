<?php
include '../connect&login/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM rent WHERE id='$id'";
    $result = $con->query($sql);

    if ($result === TRUE) {
        echo '<script>alert("Record deleted successfully");</script>';
    } else {
        echo '<script>alert("Error: ' . $sql . '\n' . $con->error . '");</script>';
    }

    // Redirect to a specific page after displaying the alert
    echo '<script>window.location.href = "home_vendedor.php";</script>';
}
?>
