<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/database.php';
    include_once '../objects/item.php';

    $database = new Database();
    $db = $database->getConnection();

    $db_item = new Item($db);

    $stmt = $db_item->read();
    $num = $stmt->rowCount();

    http_response_code(200);
    if ($num > 0) {
        $items_arr = array();
        $items_arr["items"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            extract($row);
            $item = array(
                "id" => $item_id,
                "discount" => $discount
            );

            array_push($items_arr["items"], $item);
        }


        echo json_encode($items_arr);

    } else {

        echo json_encode(
            array("message" => "No items found.")
        );
    }
?>
