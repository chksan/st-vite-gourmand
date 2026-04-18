-- VITE & GOURMAND Seed


USE `vite_gourmand`;

SET FOREIGN_KEY_CHECKS = 0;

-- Clear data
DELETE FROM plat_allergen;
DELETE FROM menu_plat;
DELETE FROM menus;
DELETE FROM plats;
DELETE FROM allergens;
DELETE FROM horaires;
DELETE FROM users;

-- Users
INSERT INTO users (name, email, password, role, phone, address, created_at) VALUES
                                                                                ('Julie Martin', 'julie@vitegourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0612345678', '12 Rue des Vignes, 33000 Bordeaux', NOW()),
                                                                                ('José Dupont', 'jose@vitegourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employe', '0612345679', '12 Rue des Vignes, 33000 Bordeaux', NOW()),
                                                                                ('Client Test', 'client@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '0612345680', '45 Avenue de la République, 33100 Bordeaux', NOW());

-- Horaires
INSERT INTO horaires (day, opening_time, closing_time, is_closed) VALUES
                                                                      ('Lundi', '09:00:00', '19:00:00', 0),
                                                                      ('Mardi', '09:00:00', '19:00:00', 0),
                                                                      ('Mercredi', '09:00:00', '19:00:00', 0),
                                                                      ('Jeudi', '09:00:00', '19:00:00', 0),
                                                                      ('Vendredi', '09:00:00', '19:00:00', 0),
                                                                      ('Samedi', '09:00:00', '19:00:00', 0),
                                                                      ('Dimanche', '09:00:00', '19:00:00', 0);

-- Allergens
INSERT INTO allergens (name) VALUES
                                 ('Gluten'), ('Lait'), ('Oeuf'), ('Arachide'), ('Fruits à coque'), ('Moutarde'), ('Poisson'), ('Crustacé');

-- Plats
INSERT INTO plats (type, title, description) VALUES
                                                 ('entree', 'Foie gras maison', 'Foie gras de canard mi-cuit au torchon'),
                                                 ('plat', 'Magret de canard rôti', 'Magret de canard sauce aux cèpes'),
                                                 ('dessert', 'Bûche chocolat-noisette', 'Bûche traditionnelle revisitée');

-- Menus
INSERT INTO menus (title, description, theme, regime, min_personnes, price, stock, conditions, images) VALUES
                                                                                                           ('Menu Noël Prestige', 'Menu festif pour les fêtes de fin d\'année', 'Noel', 'classique', 4, 89.90, 8, 'Commande obligatoire 7 jours à l\'avance', '["menu-noel-1.jpg"]'),
                                                                                                           ('Menu Pâques Gourmand', 'Agneau de printemps et chocolat', 'Paques', 'classique', 6, 79.90, 12, 'Commande obligatoire 5 jours à l\'avance', '["menu-paques-1.jpg"]'),
                                                                                                           ('Menu Classique', 'Menu traditionnel toute l\'année', 'Classique', 'classique', 2, 45.00, 20, 'Aucune condition particulière', '["menu-classique.jpg"]');

-- Link plats to menus
INSERT INTO menu_plat (menu_id, plat_id) VALUES
                                             (1,1),(1,2),(1,3),
                                             (2,2),(2,3),
                                             (3,1),(3,2),(3,3);

SET FOREIGN_KEY_CHECKS = 1;