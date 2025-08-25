<?php
header("Content-Type: application/json");
include_once '../../config/cors.php';
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getLeaveTypes($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getLeaveTypes($db) {
    try {
        $query = "SELECT * FROM tblleavetype ORDER BY LEAVETYPE";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $leaveTypes = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leaveTypes[] = $row;
        }
        
        echo json_encode($leaveTypes);
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}
?>