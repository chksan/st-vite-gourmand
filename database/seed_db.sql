-- =============================================
-- VITE & GOURMAND - SEED DATA
-- =============================================

USE `vite_gourmand`;

-- 1. Users (3 roles)
INSERT INTO users (name, email, password, role, phone, address, created_at) VALUES
                                                                                ('Julie Martin', 'julie@vitegourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0612345678', '12 Rue des Vignes, 33000 Bordeaux', NOW()),
                                                                                ('José Dupont', 'jose@vitegourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employe', '0612345679', '12 Rue des Vignes, 33000 Bordeaux', NOW()),
                                                                                ('Client Test', 'client@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '0612345680', '45 Avenue de la République, 33100 Bordeaux', NOW());

-- 2. Opening hours
INSERT INTO horaires (day, opening_time, closing_time, is_closed) VALUES
                                                                      ('Lundi', '09:00:00', '19:00:00', 0),
                                                                      ('Mardi', '09:00:00', '19:00:00', 0),
                                                                      ('Mercredi', '09:00:00', '19:00:00', 0),
                                                                      ('Jeudi', '09:00:00', '19:00:00', 0),
                                                                      ('Vendredi', '09:00:00', '19:00:00', 0),
                                                                      ('Samedi', '09:00:00', '19:00:00', 0),
                                                                      ('Dimanche', '09:00:00', '19:00:00', 0);

-- 3. Allergens
INSERT INTO allergens (name) VALUES
                                 ('Gluten'), ('Lait'), ('Oeuf'), ('Arachide'), ('Fruits à coque'), ('Moutarde'), ('Poisson'), ('Crustacé');

-- 4. Plats
INSERT INTO plats (type, title, description) VALUES
                                                 ('entree', 'Foie gras maison', 'Foie gras de canard mi-cuit au torchon'),
                                                 ('entree', 'Velouté de potiron', 'Velouté de potiron aux châtaignes'),
                                                 ('plat', 'Magret de canard rôti', 'Magret de canard sauce aux cèpes'),
                                                 ('plat', 'Filet de bœuf Rossini', 'Filet de bœuf avec foie gras poêlé'),
                                                 ('dessert', 'Bûche chocolat-noisette', 'Bûche traditionnelle revisitée'),
                                                 ('dessert', 'Tarte aux pommes caramélisées', 'Tarte fine aux pommes et caramel au beurre salé');

-- 5. Menus
INSERT INTO menus (title, description, theme, regime, min_personnes, price, stock, conditions, images) VALUES
                                                                                                           ('Menu Noël Prestige', 'Menu festif pour les fêtes de fin d\'année', 'Noel', 'classique', 4, 89.90, 8, 'Commande obligatoire 7 jours à l\'avance', '["menu-noel-1.jpg"]'),
                                                                                                           ('Menu Pâques Gourmand', 'Agneau de printemps et chocolat', 'Paques', 'classique', 6, 79.90, 12, 'Commande obligatoire 5 jours à l\'avance', '["menu-paques-1.jpg"]'),
                                                                                                           ('Menu Classique', 'Menu traditionnel toute l\'année', 'Classique', 'classique', 2, 45.00, 20, 'Aucune condition particulière', '["menu-classique.jpg"]'),
                                                                                                           ('Menu Végétarien Printemps', 'Menu 100% végétarien', 'Evenement', 'vegetarien', 4, 55.00, 10, 'Commande 3 jours à l\'avance', '["menu-vegetarien.jpg"]'),
                                                                                                           ('Menu Prestige Vegan', 'Menu entièrement vegan', 'Evenement', 'vegan', 6, 65.00, 6, 'Commande 5 jours à l\'avance', '["menu-vegan.jpg"]');

-- 6. Link Plats to Menus
INSERT INTO menu_plat (menu_id, plat_id) VALUES
                                             (1,1), (1,3), (1,5),        -- Noël
                                             (2,2), (2,4), (2,6),        -- Pâques
                                             (3,1), (3,3), (3,6),        -- Classique
                                             (4,2), (4,3), (4,6),        -- Végétarien
                                             (5,2), (5,4), (5,6);        -- Vegan

-- 7. Link some allergens to plats
INSERT INTO plat_allergen (plat_id, allergen_id) VALUES
                                                     (1,1), (1,2),               -- Foie gras
                                                     (3,1),                      -- Magret
                                                     (5,2);                      -- Bûche
