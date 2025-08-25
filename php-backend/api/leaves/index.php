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
            getLeave($db, $_GET['id']);
        } else {
            getLeaves($db);
        }
        break;
    case 'POST':
        createLeave($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateLeave($db, $_GET['id']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteLeave($db, $_GET['id']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getLeaves($db) {
    try {
        $status = isset($_GET['status']) ? $_GET['status'] : 'PENDING';
        $employeeId = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;
        
        $query = "SELECT l.*, e.EMPNAME FROM tblleave l 
                  JOIN tblemployee e ON l.EMPLOYID = e.EMPLOYID 
                  WHERE l.LEAVESTATUS = :status";
        
        if ($employeeId) {
            $query .= " AND l.EMPLOYID = :employee_id";
        }
        
        $query .= " ORDER BY l.DATEPOSTED DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $status);
        
        if ($employeeId) {
            $stmt->bindParam(":employee_id", $employeeId);
        }
        
        $stmt->execute();
        
        $leaves = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leaves[] = $row;
        }
        
        echo json_encode($leaves);
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function getLeave($db, $id) {
    try {
        $query = "SELECT l.*, e.EMPNAME FROM tblleave l 
                  JOIN tblemployee e ON l.EMPLOYID = e.EMPLOYID 
                  WHERE l.LEAVEID = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($leave);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Leave not found"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function createLeave($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->EMPLOYID) || !isset($data->DATESTART) || !isset($data->DATEEND)) {
        http_response_code(400);
        echo json_encode(array("message" => "Required fields missing"));
        return;
    }
    
    try {
        // Calculate number of days
        $dateStart = new DateTime($data->DATESTART);
        $dateEnd = new DateTime($data->DATEEND);
        $diff = $dateStart->diff($dateEnd);
        
        $nodays = 0;
        if ($data->SHIFTTIME == 'AM' || $data->SHIFTTIME == 'PM') {
            $nodays = (1 + $diff->days) / 2;
        } else {
            $nodays = 1 + $diff->days;
        }
        
        $query = "INSERT INTO tblleave (EMPLOYID, DATESTART, DATEEND, NODAYS, SHIFTTIME, TYPEOFLEAVE, REASON, LEAVESTATUS, ADMINREMARKS, DATEPOSTED) 
                  VALUES (:employid, :datestart, :dateend, :nodays, :shifttime, :typeofleave, :reason, 'PENDING', 'N/A', :dateposted)";
        
        $stmt = $db->prepare($query);
        
        $datePosted = date('Y-m-d');
        
        $stmt->bindParam(":employid", $data->EMPLOYID);
        $stmt->bindParam(":datestart", $data->DATESTART);
        $stmt->bindParam(":dateend", $data->DATEEND);
        $stmt->bindParam(":nodays", $nodays);
        $stmt->bindParam(":shifttime", $data->SHIFTTIME);
        $stmt->bindParam(":typeofleave", $data->TYPEOFLEAVE);
        $stmt->bindParam(":reason", $data->REASON);
        $stmt->bindParam(":dateposted", $datePosted);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Leave application created successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create leave application"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function updateLeave($db, $id) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->LEAVESTATUS)) {
        http_response_code(400);
        echo json_encode(array("message" => "Leave status is required"));
        return;
    }
    
    try {
        $query = "UPDATE tblleave SET 
                  LEAVESTATUS = :leavestatus, 
                  ADMINREMARKS = :adminremarks 
                  WHERE LEAVEID = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":leavestatus", $data->LEAVESTATUS);
        $stmt->bindParam(":adminremarks", $data->ADMINREMARKS);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            // Update employee available leave if approved
            if ($data->LEAVESTATUS == 'APPROVED') {
                updateEmployeeLeave($db, $data->EMPLOYID, $data->NODAYS);
            }
            
            echo json_encode(array("message" => "Leave updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update leave"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}

function updateEmployeeLeave($db, $employeeId, $leaveDays) {
    try {
        $query = "UPDATE tblemployee SET AVELEAVE = AVELEAVE - :leavedays WHERE EMPLOYID = :employid";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":leavedays", $leaveDays);
        $stmt->bindParam(":employid", $employeeId);
        $stmt->execute();
    } catch(PDOException $exception) {
        // Log error but don't fail the main operation
        error_log("Error updating employee leave: " . $exception->getMessage());
    }
}

function deleteLeave($db, $id) {
    try {
        $query = "DELETE FROM tblleave WHERE LEAVEID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Leave deleted successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to delete leave"));
        }
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $exception->getMessage()));
    }
}
?>