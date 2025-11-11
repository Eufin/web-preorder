CREATE DATABASE preorder_db;
USE preorder_db;

-- Tabel pengguna (pegawai & owner)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(255),
  role ENUM('staff','owner')
);

INSERT INTO users (username, password, role) VALUES
('pegawai1', '1234', 'staff'),
('admin', 'admin', 'owner');

-- Tabel produk
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk VARCHAR(100),
  harga DECIMAL(10,2)
);

INSERT INTO products (nama_produk, harga) VALUES
('Kopi Susu', 15000),
('Espresso', 12000),
('Latte', 18000);

-- Tabel pesanan
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pemesan VARCHAR(100),
  id_produk INT,
  jumlah INT,
  total DECIMAL(10,2),
  suhu ENUM('Panas','Dingin') DEFAULT 'Dingin',
  gula ENUM('Less Sugar','Normal','Request') DEFAULT 'Normal',
  catatan VARCHAR(255) NULL,
  status ENUM('Pending','Lunas') DEFAULT 'Pending',
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_produk) REFERENCES products(id)
);