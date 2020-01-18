<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/database.php';
    include_once '../objects/order.php';

    $database = new Database();
    $db = $database->getConnection();

    $db_order = new Order($db);

    $promocode = isset($_GET["promocode"]) ? $_GET["promocode"] : "";
    $address = isset($_GET["address"]) ? $_GET["address"] : "";
    $date = isset($_GET["date"]) ? $_GET["date"] : "";

    if ($promocode == "" && $address == "" && $date == "") {

        http_response_code(400);

        echo json_encode(
            array("message" => "All parameters are empty: promocode, address, date.")
        );

    } else {

        $stmt = $db_order->search($promocode, $address, $date);
        $num = $stmt->rowCount();

        if ($num > 0) {

            $orders_arr = array();
            $orders_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $db_order = array(
                    "id" => $order_id,
                    "date" => $date,
                    "promocode" => $promocode,
                    "address" => $address
                );

                array_push($orders_arr["records"], $db_order);
            }

            http_response_code(200);

            echo json_encode($orders_arr);
        } else {

            http_response_code(200);

            echo json_encode(
                array("message" => "No orders found.")
            );
        }
    }
?>
