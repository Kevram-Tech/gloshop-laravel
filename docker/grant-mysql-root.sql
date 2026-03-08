-- À exécuter sur le serveur MySQL (après: mysql -u root -p < grant-mysql-root.sql)
-- Remplacez VOTRE_MOT_DE_PASSE par le mot de passe de root.

-- Autoriser root depuis tout hôte (connexions Docker = 172.18.0.x)
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
