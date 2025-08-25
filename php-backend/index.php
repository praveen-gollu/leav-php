<?php
header("Content-Type: application/json");
include_once 'config/cors.php';

echo json_encode(array(
    "message" => "Leave Management System API",
    "version" => "1.0.0",
    "endpoints" => array(
        "auth" => "/api/auth/login",
        "employees" => "/api/employees",
        "companies" => "/api/companies", 
        "departments" => "/api/departments",
        "leaves" => "/api/leaves",
        "leave-types" => "/api/leave-types"
    )
));
?>