<?php
// models/IceCream.php
class IceCream {
    public $id;
    public $flavor;
    public $size;
    public $stock;

    public function __construct($id, $flavor, $size, $stock) {
        $this->id = $id;
        $this->flavor = $flavor;
        $this->size = $size;
        $this->stock = $stock;
    }

    // Méthodes pour interagir avec la base de données (CRUD)
}
?>
