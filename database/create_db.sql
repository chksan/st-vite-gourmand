-- VITE & GOURMAND


CREATE DATABASE IF NOT EXISTS `vite_gourmand`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vite_gourmand`;

-- Users + Roles
CREATE TABLE users (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(255) UNIQUE NOT NULL,
                       password VARCHAR(255) NOT NULL,
                       role ENUM('user', 'employe', 'admin') NOT NULL DEFAULT 'user',
                       is_active BOOLEAN NOT NULL DEFAULT TRUE,
                       phone VARCHAR(20) NULL,
                       address TEXT NULL,
                       remember_token VARCHAR(100) NULL,
                       created_at TIMESTAMP NULL,
                       updated_at TIMESTAMP NULL
);

-- Opening hours
CREATE TABLE horaires (
                          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          day ENUM('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche') NOT NULL,
                          opening_time TIME NOT NULL,
                          closing_time TIME NOT NULL,
                          is_closed BOOLEAN DEFAULT FALSE,
                          created_at TIMESTAMP NULL,
                          updated_at TIMESTAMP NULL
);

-- Allergens
CREATE TABLE allergens (
                           id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                           name VARCHAR(100) NOT NULL,
                           created_at TIMESTAMP NULL,
                           updated_at TIMESTAMP NULL
);

-- Dishes
CREATE TABLE plats (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       type ENUM('entree', 'plat', 'dessert') NOT NULL,
                       title VARCHAR(255) NOT NULL,
                       description TEXT NULL,
                       created_at TIMESTAMP NULL,
                       updated_at TIMESTAMP NULL
);

-- Dish <-> Allergen
CREATE TABLE plat_allergen (
                               plat_id BIGINT UNSIGNED NOT NULL,
                               allergen_id BIGINT UNSIGNED NOT NULL,
                               PRIMARY KEY (plat_id, allergen_id),
                               FOREIGN KEY (plat_id) REFERENCES plats(id) ON DELETE CASCADE,
                               FOREIGN KEY (allergen_id) REFERENCES allergens(id) ON DELETE CASCADE
);

-- Menus
CREATE TABLE menus (
                       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       title VARCHAR(255) NOT NULL,
                       description TEXT NOT NULL,
                       theme VARCHAR(50) NOT NULL,
                       regime VARCHAR(50) NOT NULL DEFAULT 'classique',
                       min_personnes INT NOT NULL,
                       price DECIMAL(10,2) NOT NULL,
                       stock INT NOT NULL DEFAULT 10,
                       conditions TEXT NULL,
                       images JSON NULL,
                       created_at TIMESTAMP NULL,
                       updated_at TIMESTAMP NULL
);

-- Many-to-many: Menu <-> Dish
CREATE TABLE menu_plat (
                           menu_id BIGINT UNSIGNED NOT NULL,
                           plat_id BIGINT UNSIGNED NOT NULL,
                           PRIMARY KEY (menu_id, plat_id),
                           FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
                           FOREIGN KEY (plat_id) REFERENCES plats(id) ON DELETE CASCADE
);

-- Orders
CREATE TABLE orders (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        user_id BIGINT UNSIGNED NOT NULL,
                        menu_id BIGINT UNSIGNED NOT NULL,
                        nb_personnes INT NOT NULL,
                        total_price DECIMAL(10,2) NOT NULL,
                        delivery_address TEXT NOT NULL,
                        delivery_date DATE NOT NULL,
                        delivery_time TIME NOT NULL,
                        delivery_fee DECIMAL(8,2) DEFAULT 0.00,
                        status ENUM('pending', 'accepted', 'preparing', 'delivering', 'delivered', 'waiting_material', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
                        cancel_reason TEXT NULL,
                        contact_mode ENUM('gsm', 'email') NULL,
                        cancelled_by BIGINT UNSIGNED NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
                        FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Order status history
CREATE TABLE order_status_history (
                                      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                      order_id BIGINT UNSIGNED NOT NULL,
                                      status ENUM('pending', 'accepted', 'preparing', 'delivering', 'delivered', 'waiting_material', 'completed', 'cancelled') NOT NULL,
                                      comment TEXT NULL,
                                      changed_by BIGINT UNSIGNED NULL,
                                      created_at TIMESTAMP NULL,
                                      FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                                      FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Reviews
-- NULL = pending, TRUE = validated, FALSE = rejected
CREATE TABLE reviews (
                         id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         order_id BIGINT UNSIGNED NOT NULL,
                         user_id BIGINT UNSIGNED NOT NULL,
                         rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
                         comment TEXT NULL,
                         is_validated BOOLEAN NULL DEFAULT NULL,
                         validated_by BIGINT UNSIGNED NULL,
                         validated_at TIMESTAMP NULL,
                         created_at TIMESTAMP NULL,
                         updated_at TIMESTAMP NULL,
                         FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                         FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Contact messages
CREATE TABLE contacts (
                          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(255) NOT NULL,
                          email VARCHAR(255) NOT NULL,
                          subject VARCHAR(255) NOT NULL,
                          message TEXT NOT NULL,
                          created_at TIMESTAMP NULL
);