-- Table pour enregistrer les logs de connexion
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    password_type ENUM('normal', 'secret') DEFAULT 'normal',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login_date (login_date),
    INDEX idx_nom_prenom (nom, prenom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
