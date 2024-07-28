<?php
require 'dbconnection.php';

$action = $_POST['action'];

if ($action == 'create') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $status = $_POST['status'];
    $email = $_POST['email'];
    $village_id = $_POST['village_id'];
    $city_id = $_POST['city_id'];
    $province_id = $_POST['province_id'];
    $department_id = $_POST['department_id'];

    $sql = "INSERT INTO employees (name, surname, gender, age, date_of_birth, address, phone_number, status, email, village_id, city_id, province_id, department_id)
            VALUES ('$name', '$surname', '$gender', '$age', '$date_of_birth', '$address', '$phone_number', '$status', '$email', '$village_id', '$city_id', '$province_id', '$department_id')";

    if ($conn->query($sql) === TRUE) {
        echo "New employee created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'update') {
    $employee_id = $_POST['employee_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $status = $_POST['status'];
    $email = $_POST['email'];
    $village_id = $_POST['village_id'];
    $city_id = $_POST['city_id'];
    $province_id = $_POST['province_id'];
    $department_id = $_POST['department_id'];

    $sql = "UPDATE employees SET name='$name', surname='$surname', gender='$gender', age='$age', date_of_birth='$date_of_birth', address='$address', phone_number='$phone_number', status='$status', email='$email', village_id='$village_id', city_id='$city_id', province_id='$province_id', department_id='$department_id'
            WHERE employee_id='$employee_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Employee updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'delete') {
    $employee_id = $_POST['employee_id'];

    $sql = "DELETE FROM employees WHERE employee_id='$employee_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Employee deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();

