<?php
    class Item {

        private $conn;
        private $table_name = "item";

        public $id;
        public $discount;

        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $this->log_query("read");

            $query = "SELECT * FROM ".$this->table_name." ORDER BY item_id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        function create() {
            $this->log_query("create");

            if ($this->id == null)
                return new Result(400, "Unable create an item: id is not specified");

            $query = "INSERT INTO ".$this->table_name." (item_id";
            if ($this->discount != null) {
                if ($this->discount > 100) {
                    return new Result(400, "Unable create an item: discount should be in range from 0 to 100");
                }
                $query .= ", discount";
            }
            $query .= ")";
            $query .= " VALUES (:id";
            if ($this->discount != null)
                $query .= ", :discount";
            $query .= ")";

            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            if ($this->discount != null)
                $this->discount = htmlspecialchars(strip_tags($this->discount));

            $stmt->bindParam(":id", $this->id);
            if ($this->discount != null)
                $stmt->bindParam(":discount", $this->discount);

            if($stmt->execute())
                return new Result(201, "Item was created");

            return new Result(503, "Unable create an item");
        }

        function update() {
            $this->log_query("update");
            if ($this->id == null)
                return new Result(400, "Unable to find id");
            if ($this->discount == null)
                return new Result(400, "Unable to find discount");

            $query = "SELECT * FROM ".$this->table_name." WHERE item_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0)
                return new Result(200, "There is no item with id=$this->id");

            $query = "UPDATE ".$this->table_name." SET discount = :discount WHERE item_id = :id";

            $stmt = $this->conn->prepare($query);

            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->discount=htmlspecialchars(strip_tags($this->discount));

            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':discount', $this->discount);

            if($stmt->execute())
                return new Result(200, "Item with id=$this->id was updated");

            return new Result(503, "Unable to update item");
        }

        function delete() {
            $this->log_query("delete");
            if ($this->id == null)
                return new Result(400, "Unable to find id");

            $query = "SELECT * FROM ".$this->table_name." WHERE item_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0)
                return new Result(200, "There is no item with id=$this->id");

            $query = "DELETE FROM ".$this->table_name." WHERE item_id = ?";

            $stmt = $this->conn->prepare($query);

            $this->id=htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            if($stmt->execute()){
                return new Result(200, "Item with id=$this->id was deleted");
            }

            return new Result(503, "Unable to delete item");
        }

        private function log_query($method) {
            $method = "item/".$method;
            $date = date('d.m.Y H:i:s');

            $parameters = "";
            if ($this->id != null)
                $parameters.="id=".$this->id;
            if ($this->discount != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="discount=".$this->discount;
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
