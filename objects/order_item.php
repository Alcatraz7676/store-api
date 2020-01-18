<?php
    class OrderItem {

        private $conn;
        private $table_name = "order_item";

        public $order_item_id;
        public $order_id;
        public $item_id;
        public $quantity;

        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $this->log_query("read");

            $query = "SELECT * FROM ".$this->table_name." ORDER BY order_item_id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        function create() {
            $this->log_query("create");

            if ($this->item_id == null)
                return new Result(400, "Unable to create order item: item_id is not specified");
            if ($this->order_id == null)
                return new Result(400, "Unable to create order item: order_id is not specified");

            $query = "INSERT INTO ".$this->table_name." (item_id, order_id";
            if ($this->quantity != null)
                $query .= ", quantity";
            $query .= ")";
            $query .= " VALUES (:item_id, :order_id";
            if ($this->quantity != null)
                $query .= ", :quantity";
            $query .= ")";

            $stmt = $this->conn->prepare($query);

            $this->item_id = htmlspecialchars(strip_tags($this->item_id));
            $this->order_id = htmlspecialchars(strip_tags($this->order_id));
            if ($this->quantity != null)
                $this->quantity = htmlspecialchars(strip_tags($this->quantity));

            $stmt->bindParam(":item_id", $this->item_id);
            $stmt->bindParam(":order_id", $this->order_id);
            if ($this->quantity != null)
                $stmt->bindParam(":quantity", $this->quantity);

            if($stmt->execute())
                return new Result(201, "Order item was created");

            return new Result(503, "Unable to create order item");
        }

        function update() {
            $this->log_query("update");

            if ($this->order_item_id == null)
                return new Result(400, "Unable to find order_item_id");

            $first = true;
            $query = "UPDATE ".$this->table_name." SET ";
            if ($this->item_id != null) {
                $query .= "item_id = :item_id";
                $first = false;
            }
            if ($this->order_id != null) {
                if (!$first)
                    $query .= ", ";
                $query .= "order_id = :order_id";
                $first = false;
            }
            if ($this->quantity != null) {
                if (!$first)
                    $query .= ", ";
                $query .= "quantity = :quantity";
                $first = false;
            }
            // Ничего не пришло на обновление
            if ($first)
                return new Result(400, "Unable to find any new data");
            $query .= " WHERE order_item_id = :order_item_id";

            $stmt = $this->conn->prepare($query);

            $this->order_item_id=htmlspecialchars(strip_tags($this->order_item_id));
            if ($this->item_id != null)
                $this->item_id=htmlspecialchars(strip_tags($this->item_id));
            if ($this->order_id != null)
                $this->order_id=htmlspecialchars(strip_tags($this->order_id));
            if ($this->quantity != null)
                $this->quantity=htmlspecialchars(strip_tags($this->quantity));

            $stmt->bindParam(':order_item_id', $this->order_item_id);
            if ($this->item_id != null)
                $stmt->bindParam(':item_id', $this->item_id);
            if ($this->order_id != null)
                $stmt->bindParam(':order_id', $this->order_id);
            if ($this->quantity != null)
                $stmt->bindParam(':quantity', $this->quantity);

            if($stmt->execute())
                return new Result(200, "Order item with id=$this->order_item_id was updated");

            return new Result(503, "Unable to update order item");
        }

        function delete() {
            $this->log_query("delete");

            if ($this->order_item_id == null)
                return new Result(400, "Unable to find order_item_id");

            $query = "DELETE FROM ".$this->table_name." WHERE order_item_id = ?";

            $stmt = $this->conn->prepare($query);

            $this->order_item_id=htmlspecialchars(strip_tags($this->order_item_id));
            $stmt->bindParam(1, $this->order_item_id);

            if($stmt->execute())
                return new Result(200, "Order item with id=$this->order_item_id was deleted");

            return new Result(503, "Unable to delete order item");
        }

        function search($order_id) {
            $this->log_query("search");

            $query = "SELECT * FROM item WHERE item_id IN (SELECT item_id FROM ".$this->table_name." WHERE order_id=?)";

            $stmt = $this->conn->prepare($query);

            $order_id = htmlspecialchars(strip_tags($order_id));

            $stmt->bindParam(1, $order_id);

            $stmt->execute();

            return $stmt;
        }

        private function log_query($method) {
            $method = "order_item/".$method;
            $date = date('d.m.Y H:i:s');

            $parameters = "";
            if ($this->order_item_id != null)
                $parameters.="order_item_id=".$this->order_item_id;
            if ($this->order_id != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="order_id=".$this->order_id;
            }
            if ($this->item_id != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="item_id=".$this->item_id;
            }
            if ($this->quantity != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="quantity=".$this->quantity;
            }

            $query = "INSERT INTO logs (method_name, parameters, date) VALUES (:method, :parameters, :date)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":method", $method);
            $stmt->bindParam(":parameters", $parameters);
            $stmt->bindParam(":date", $date);
            $stmt->execute();
        }
    }
?>
