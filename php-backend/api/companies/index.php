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
            getCompany($db, $_GET['id']);
        } else {
            getCompanies($db);
        }
        break;
    case 'POST':
        createCompany($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateCompany($db, $_GET['id']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteCompany($db, $_GET['id']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getCompanies($db) {
    try {
        $query = "SELECT * FROM tblcompany ORDER BY COMPANY";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $companies = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $companies[] = $row;
        }
        
        echo json_encode($companies);
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function getCompany($db, $id) {
    try {
        $query = "SELECT * FROM tblcompany WHERE COMPID = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($company);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Company not found"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function createCompany($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->COMPANY)) {
        http_response_code(400);
        echo json_encode(array("message" => "Company name is required"));
        return;
    }
    
    try {
        $query = "INSERT INTO tblcompany (COMPANY) VALUES (:company)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":company", $data->COMPANY);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Company created successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create company"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function updateCompany($db, $id) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->COMPANY)) {
        http_response_code(400);
        echo json_encode(array("message" => "Company name is required"));
        return;
    }
    
    try {
        $query = "UPDATE tblcompany SET COMPANY = :company WHERE COMPID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":company", $data->COMPANY);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Company updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update company"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function deleteCompany($db, $id) {
    try {
        $query = "DELETE FROM tblcompany WHERE COMPID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Company deleted successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to delete company"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}
?>