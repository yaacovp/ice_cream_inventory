-- Créer la base de données
CREATE DATABASE ice_cream_inventory;
USE ice_cream_inventory;

-- Table pour les goûts
CREATE TABLE flavors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Table pour les glaces
CREATE TABLE ice_creams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flavor_id INT,
    size ENUM('500ml', '1L') NOT NULL,
    stock INT NOT NULL,
    FOREIGN KEY (flavor_id) REFERENCES flavors(id)
);

-- Table pour les clients
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Table pour les commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    status ENUM('En préparation', 'Terminée') DEFAULT 'En préparation',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Table pour les éléments de commande
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    ice_cream_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (ice_cream_id) REFERENCES ice_creams(id)
);
