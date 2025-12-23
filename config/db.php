<?php
$conn = new mysqli("localhost", "root", "", "membership");
if ($conn->connect_error) die("DB Connection Failed");
session_start();

?>