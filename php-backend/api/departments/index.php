<?php
header("Content-Type: application/json");
include_once '../../config/cors.php';
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getDepartment($db, $_GET['id']);
        } else {
            getDepartments($db);
        }
        break;
    case 'POST':
        createDepartment($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateDepartment($db, $_GET['id']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteDepartment($db, $_GET['id']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getDepartments($db) {
    try {
        $query = "SELECT * FROM tbldepts ORDER BY DEPTNAME";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $departments = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departments[] = $row;
        }
        
        echo json_encode($departments);
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function getDepartment($db, $id) {
    try {
        $query = "SELECT * FROM tbldepts WHERE DEPTID = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $department = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($department);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Department not found"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function createDepartment($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->DEPTNAME) || !isset($data->DEPTSHORTNAME)) {
        http_response_code(400);
        echo json_encode(array("message" => "Department name and short name are required"));
        return;
    }
    
    try {
        $query = "INSERT INTO tbldepts (DEPTNAME, DEPTSHORTNAME) VALUES (:deptname, :deptshortname)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":deptname", $data->DEPTNAME);
        $stmt->bindParam(":deptshortname", $data->DEPTSHORTNAME);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Department created successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create department"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function updateDepartment($db, $id) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->DEPTNAME) || !isset($data->DEPTSHORTNAME)) {
        http_response_code(400);
        echo json_encode(array("message" => "Department name and short name are required"));
        return;
    }
    
    try {
        $query = "UPDATE tbldepts SET DEPTNAME = :deptname, DEPTSHORTNAME = :deptshortname WHERE DEPTID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":deptname", $data->DEPTNAME);
        $stmt->bindParam(":deptshortname", $data->DEPTSHORTNAME);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Department updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update department"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function deleteDepartment($db, $id) {
    try {
        $query = "DELETE FROM tbldepts WHERE DEPTID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Department deleted successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to delete department"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}
?>