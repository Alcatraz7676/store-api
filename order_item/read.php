<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/database.php';
    include_once '../objects/order_item.php';

    $database = new Database();
    $db = $database->getConnection();

    $db_order_item = new OrderItem($db);

    $stmt = $db_order_item->read();
    $num = $stmt->rowCount();

    http_response_code(200);
    if ($num > 0) {
        $order_items_arr = array();
        $order_items_arr["order_items"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            extract($row);
            $order = array(
                "order_item_id" => $order_item_id,
                "order_id" => $order_id,
                "item_id" => $item_id,
                "quantity" => $quantity
            );

            array_push($order_items_arr["order_items"], $order);
        }


        echo json_encode($order_items_arr);

    } else {

        echo json_encode(
            array("message" => "No order items found.")
        );
    }
?>
