<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/database.php';
    include_once '../objects/order.php';

    $database = new Database();
    $db = $database->getConnection();

    $db_order = new Order($db);

    $stmt = $db_order->read();
    $num = $stmt->rowCount();

    http_response_code(200);
    if ($num > 0) {
        $orders_arr = array();
        $orders_arr["orders"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            extract($row);
            $order = array(
                "id" => $order_id,
                "promocode" => $promocode,
                "date" => $date,
                "address" => $address
            );

            array_push($orders_arr["orders"], $order);
        }

        echo json_encode($orders_arr);
        
    } else {

        echo json_encode(
            array("message" => "No orders found.")
        );
    }
?>
