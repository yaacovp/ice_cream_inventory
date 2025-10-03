<?php
// models/OrderItem.php
class OrderItem {
    public $id;
    public $order_id;
    public $ice_cream_id;
    public $quantity;

    public function __construct($id, $order_id, $ice_cream_id, $quantity) {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->ice_cream_id = $ice_cream_id;
        $this->quantity = $quantity;
    }

    // Méthodes pour interagir avec la base de données (CRUD)
}
?>
