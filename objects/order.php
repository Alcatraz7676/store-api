<?php
    class Order {

        private $conn;
        private $table_name = "order_";

        public $id;
        public $promocode;
        public $date;
        public $address;

        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $this->log_query("read");

            $query = "SELECT * FROM ".$this->table_name." ORDER BY order_id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        function create() {
            $this->log_query("create");

            if ($this->date == null)
                return new Result(400, "Unable to create order: date is not specified");
            if ($this->address == null)
                return new Result(400, "Unable to create order: address is not specified");

            $query = "INSERT INTO ".$this->table_name." (date, address";
            if ($this->promocode != null)
                $query .= ", promocode";
            $query .= ")";
            $query .= " VALUES (:date, :address";
            if ($this->promocode != null)
                $query .= ", :promocode";
            $query .= ")";

            $stmt = $this->conn->prepare($query);

            $this->date = htmlspecialchars(strip_tags($this->date));
            $this->address = htmlspecialchars(strip_tags($this->address));
            if ($this->promocode != null)
                $this->promocode = htmlspecialchars(strip_tags($this->promocode));

            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":address", $this->address);
            if ($this->promocode != null)
                $stmt->bindParam(":promocode", $this->promocode);

            if($stmt->execute())
                return new Result(201, "Order was created");

            return new Result(503, "Unable to create order");
        }

        function update() {
            $this->log_query("update");

            if ($this->id == null)
                return new Result(400, "Unable to find id");

            $first = true;
            $query = "UPDATE ".$this->table_name." SET ";
            if ($this->promocode != null) {
                $query .= "promocode = :promocode";
                $first = false;
            }
            if ($this->date != null) {
                if (!$first)
                    $query .= ", ";
                $query .= "date = :date";
                $first = false;
            }
            if ($this->address != null) {
                if (!$first)
                    $query .= ", ";
                $query .= "address = :address";
                $first = false;
            }
            // Ничего не пришло на обновление
            if ($first)
                return new Result(400, "Unable to find any new data");
            $query .= " WHERE order_id = :id";

            $stmt = $this->conn->prepare($query);

            $this->id=htmlspecialchars(strip_tags($this->id));
            if ($this->promocode != null)
                $this->promocode=htmlspecialchars(strip_tags($this->promocode));
            if ($this->address != null)
                $this->address=htmlspecialchars(strip_tags($this->address));
            if ($this->date != null)
                $this->date=htmlspecialchars(strip_tags($this->date));

            $stmt->bindParam(':id', $this->id);
            if ($this->promocode != null)
                $stmt->bindParam(':promocode', $this->promocode);
            if ($this->address != null)
                $stmt->bindParam(':address', $this->address);
            if ($this->date != null)
                $stmt->bindParam(':date', $this->date);

            if($stmt->execute())
                return new Result(200, "Order with id=$this->id was updated");

            return new Result(503, "Unable to update order");
        }

        function delete() {
            $this->log_query("delete");

            if ($this->id == null)
                return new Result(400, "Unable to find id");

            $query = "DELETE FROM " . $this->table_name . " WHERE order_id = ?";

            $stmt = $this->conn->prepare($query);

            $this->id=htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            if($stmt->execute()){
                return new Result(200, "Order with id=$this->id was deleted");
            }

            return new Result(503, "Unable to delete order");
        }

        function search($promocode, $address, $date) {
            $this->log_query("search");

            $query = "SELECT * FROM ".$this->table_name." WHERE ";

            $before = false;
            if ($promocode != "") {
                $query .= "promocode='".$promocode."' ";
                $before = true;
            }
            if ($address != "") {
                if ($before) {
                    $query .= "AND ";
                }
                $query .= "address='".$address."' ";
                $before = true;
            }
            if ($date != "") {
                if ($before) {
                    $query .= "AND ";
                }
                $query .= "date='".$date."'";
                $before = true;
            }
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            return $stmt;
        }

        private function log_query($method) {
            $method = "order/".$method;
            $date = date('d.m.Y H:i:s');

            $parameters = "";
            if ($this->id != null)
                $parameters.="id=".$this->id;
            if ($this->promocode != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="promocode=".$this->promocode;
            }
            if ($this->date != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="date=".$this->date;
            }
            if ($this->address != null) {
                if ($parameters != "")
                    $parameters.="&";
                $parameters.="address=".$this->address;
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
