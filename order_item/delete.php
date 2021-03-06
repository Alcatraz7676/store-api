<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/database.php';
    include_once '../objects/order_item.php';
    include_once '../common/result.php';

    $database = new Database();
    $db = $database->getConnection();

    $order_item = new OrderItem($db);

    $data = json_decode(file_get_contents("php://input"));

    $order_item->order_item_id = $data->order_item_id;

    $result = $order_item->delete();
    http_response_code($result->getCode());
    echo json_encode(array("message" => $result->getMessage()));
?>
