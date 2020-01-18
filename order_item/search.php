<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/database.php';
    include_once '../objects/order_item.php';

    $database = new Database();
    $db = $database->getConnection();

    $db_order_item = new OrderItem($db);

    $order_id = isset($_GET["order_id"]) ? $_GET["order_id"] : "";

    if ($order_id == "") {

        http_response_code(400);

        echo json_encode(
            array("message" => "Parameter is empty: order_id.")
        );

    } else {

        $stmt = $db_order_item->search($order_id);
        $num = $stmt->rowCount();

        if ($num > 0) {

            $order_items_arr = array();
            $order_items_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $db_order_item = array(
                    "id" => $item_id,
                    "discount" => $discount
                );

                array_push($order_items_arr["records"], $db_order_item);
            }

            http_response_code(200);

            echo json_encode($order_items_arr);
        } else {

            http_response_code(200);

            echo json_encode(
                array("message" => "No order items found.")
            );
        }
    }
?>
