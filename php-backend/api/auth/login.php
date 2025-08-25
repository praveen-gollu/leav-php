<?php
header("Content-Type: application/json");
include_once '../../config/cors.php';
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->user_email) || !isset($data->user_pass)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Email and password are required"
    ));
    exit();
}

$email = $data->user_email;
$password = $data->user_pass;

try {
    $query = "SELECT * FROM tblemployee WHERE USERNAME = :email AND PASSWRD = :password AND ACCSTATUS = 'YES' LIMIT 1";
    $stmt = $db->prepare($query);
    
    // Hash the password using SHA1 (matching original PHP logic)
    $hashed_password = sha1($password);
    
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Remove password from response
        unset($user['PASSWRD']);
        
        // Generate a simple token (in production, use JWT)
        $token = base64_encode($user['EMPID'] . ':' . time());
        
        echo json_encode(array(
            "success" => true,
            "message" => "Login successful",
            "user" => $user,
            "token" => $token
        ));
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid credentials or account not active"
        ));
    }
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Database error: " . $exception->getMessage()
    ));
}
?>