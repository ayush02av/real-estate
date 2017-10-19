<?php
session_start();

include 'validation.php';
// user can only access form-register via the POST method, not GET (typing directly into the address bar)
if (empty($_POST['submit'])) {
  header('Location: add-property.php');
  die(); 
} 

// set the submit messages
$msg_fail = 'One or more fields have an error.';
$msg_empty = 'Please fill in all required fields.';
$msg_success = 'Property successfully added.';

// set POST values to variables
$img_url = $_POST['img-url'];
$img_upload = $_POST['img-upload'];
$title = $_POST['title'];
$type = $_POST['type'];
$city = $_POST['city'];
$price = $_POST['price'];
$address = $_POST['address'];
$description = $_POST['description'];

// set the placeholders
$_SESSION['placeholder_img_url'] = $img_url;
$_SESSION['placeholder_title'] = $title;
$_SESSION['placeholder_type'] = $type;
$_SESSION['placeholder_city'] = $city;
$_SESSION['placeholder_price'] = $price;
$_SESSION['placeholder_address'] = $address;
$_SESSION['placeholder_description'] = $description;

// if no fields have been filled out
if (empty($img_url) && empty($img_upload) && empty($title) && empty($type) && empty($city) && empty($price) && empty($address) && empty($description)) {
     $_SESSION['alertMessage'] = $msg_empty;
     header("Location: add-property.php");
     die();
}

// set the error messages
if (empty($img_url) || empty($img_upload)) {
   $_SESSION['error_img'] = "Please upload an image or enter a url";
}
if (empty($title)) {
    $_SESSION['error_title'] = "Please enter a title";
}
if (empty($price)) {
    $_SESSION['error_price'] = "Please enter a price (numbers only)";
}
if (empty($address)) {
   $_SESSION['error_address'] = "Please enter an address";
}
if (empty($description)) {
   $_SESSION['error_description'] = "Please enter a description";
}

// if everything is valid then set valid_form to true
$valid_form = !isset($error_img) && !isset($error_title) && !isset($error_price) && !isset($error_address) && !isset($error_description);
  
if ($valid_form) {
    // create the connection
    include('config.php');

    // creates the statement, prepare removes SQL syntax to prevent SQL injection attacks eg someone typing 'DROP table' into a field
    $stmt = $db->prepare("INSERT INTO properties (Image_URL, Title, Type, City, Price, Address, Description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $img_url, $title, $type, $city, $price, $address, $description);

    // running insert statement
    if ($stmt->execute() === TRUE) {
        echo "New record created successfully";
         $_SESSION['alertMessage'] = "Property added successfully";
    } else {
        echo "Error: " . $db->error;
    }

    // close statement
    $stmt->close();
    // close connection
    $db->close();    

    // show a success message
     $_SESSION['alertMessage'] = $msg_success;
    header("Location: add-property.php");
    die();

} else {
    $_SESSION['alertMessage'] = $msg_empty;
     header("Location: add-property.php");
    die();
}