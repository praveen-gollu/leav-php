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
            getEmployee($db, $_GET['id']);
        } else {
            getEmployees($db);
        }
        break;
    case 'POST':
        createEmployee($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateEmployee($db, $_GET['id']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteEmployee($db, $_GET['id']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getEmployees($db) {
    try {
        $query = "SELECT EMPID, EMPNAME, EMPSEX, USERNAME, COMPANY, DEPARTMENT, EMPPOSITION, EMPLOYID, AVELEAVE FROM tblemployee ORDER BY EMPNAME";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $employees = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $row;
        }
        
        echo json_encode($employees);
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function getEmployee($db, $id) {
    try {
        $query = "SELECT EMPID, EMPNAME, EMPSEX, USERNAME, COMPANY, DEPARTMENT, EMPPOSITION, EMPLOYID, AVELEAVE FROM tblemployee WHERE EMPID = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($employee);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Employee not found"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function createEmployee($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->EMPNAME) || !isset($data->USERNAME) || !isset($data->PASSWRD)) {
        http_response_code(400);
        echo json_encode(array("message" => "Required fields missing"));
        return;
    }
    
    try {
        $query = "INSERT INTO tblemployee (EMPNAME, EMPPOSITION, USERNAME, PASSWRD, ACCSTATUS, EMPSEX, COMPANY, DEPARTMENT, EMPLOYID, AVELEAVE) 
                  VALUES (:empname, :empposition, :username, :passwrd, 'YES', :empsex, :company, :department, :employid, 18)";
        
        $stmt = $db->prepare($query);
        
        $hashed_password = sha1($data->PASSWRD);
        
        $stmt->bindParam(":empname", $data->EMPNAME);
        $stmt->bindParam(":empposition", $data->EMPPOSITION);
        $stmt->bindParam(":username", $data->USERNAME);
        $stmt->bindParam(":passwrd", $hashed_password);
        $stmt->bindParam(":empsex", $data->EMPSEX);
        $stmt->bindParam(":company", $data->COMPANY);
        $stmt->bindParam(":department", $data->DEPARTMENT);
        $stmt->bindParam(":employid", $data->EMPLOYID);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Employee created successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create employee"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function updateEmployee($db, $id) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->EMPNAME) || !isset($data->USERNAME)) {
        http_response_code(400);
        echo json_encode(array("message" => "Required fields missing"));
        return;
    }
    
    try {
        $query = "UPDATE tblemployee SET 
                  EMPNAME = :empname, 
                  EMPPOSITION = :empposition, 
                  USERNAME = :username, 
                  EMPSEX = :empsex, 
                  COMPANY = :company, 
                  DEPARTMENT = :department, 
                  EMPLOYID = :employid 
                  WHERE EMPID = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":empname", $data->EMPNAME);
        $stmt->bindParam(":empposition", $data->EMPPOSITION);
        $stmt->bindParam(":username", $data->USERNAME);
        $stmt->bindParam(":empsex", $data->EMPSEX);
        $stmt->bindParam(":company", $data->COMPANY);
        $stmt->bindParam(":department", $data->DEPARTMENT);
        $stmt->bindParam(":employid", $data->EMPLOYID);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Employee updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update employee"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function deleteEmployee($db, $id) {
    try {
        $query = "DELETE FROM tblemployee WHERE EMPID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Employee deleted successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to delete employee"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}
?>