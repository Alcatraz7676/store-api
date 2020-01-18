<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/database.php';
    include_once '../objects/order.php';
    include_once '../common/result.php';

    $database = new Database();
    $db = $database->getConnection();

    $order = new Order($db);

    $data = json_decode(file_get_contents("php://input"));

    $order->date = $data->date;
    $order->address = $data->address;
    $order->promocode = $data->promocode;

    $result = $order->create();
    http_response_code($result->getCode());
    echo json_encode(array("message" => $result->getMessage()));
?>
