CREATE DATABASE IF NOT EXISTS autogo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autogo;

CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(160) NOT NULL UNIQUE,
 phone VARCHAR(30),
 password VARCHAR(255) NOT NULL,
 role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cars (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 brand VARCHAR(100),
 type ENUM('car','motorbike') NOT NULL DEFAULT 'car',
 seats INT NOT NULL DEFAULT 4,
 price_per_day DECIMAL(12,2) NOT NULL,
 image TEXT,
 description TEXT,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 car_id INT NOT NULL,
 start_date DATE NOT NULL,
 end_date DATE NOT NULL,
 days INT NOT NULL,
 total_price DECIMAL(12,2) NOT NULL,
 insurance_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
 deposit_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
 payment_method ENUM('qr','cash') NOT NULL DEFAULT 'cash',
 payment_status ENUM('unpaid','awaiting_confirmation','paid','refunded') NOT NULL DEFAULT 'unpaid',
 payment_reference VARCHAR(100) NULL,
 paid_at DATETIME NULL,
 deposit_status ENUM('not_collected','held','refunded') NOT NULL DEFAULT 'not_collected',
 note TEXT,
 status ENUM('pending','confirmed','ongoing','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_booking_car FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE RESTRICT,
 INDEX idx_car_dates (car_id,start_date,end_date,status),
 INDEX idx_booking_payment_status (payment_status,paid_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS app_settings (
 setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
 setting_value TEXT NOT NULL,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cars(name,brand,type,seats,price_per_day,image,description) VALUES
('Toyota Camry 2.5Q','Toyota','car',5,1500000,'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?auto=format&fit=crop&w=1000&q=85','Sedan rộng rãi, phù hợp công tác và du lịch gia đình.'),
('Mazda CX-5 Premium','Mazda','car',5,1300000,'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1000&q=85','SUV tiện nghi, vận hành linh hoạt trong thành phố và đường dài.'),
('Mercedes-Benz C200','Mercedes-Benz','car',5,2200000,'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1000&q=85','Sedan sang trọng với nội thất cao cấp.'),
('Honda Vision','Honda','motorbike',2,150000,'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1000&q=85','Xe tay ga tiết kiệm, phù hợp di chuyển hằng ngày.'),
('Yamaha Exciter 155','Yamaha','motorbike',2,250000,'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1000&q=85','Xe côn tay thể thao, phù hợp chuyến đi ngắn.');
