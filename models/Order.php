<?php
// models/Order.php
class Order {
    public $id;
    public $client_id;
    public $status;
    public $created_at;

    public function __construct($id, $client_id, $status, $created_at) {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    // Méthodes pour interagir avec la base de données (CRUD)
}
?>
