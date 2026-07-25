# Autoriser MySQL pour les connexions depuis Docker

Quand Laravel tourne dans Docker et que MySQL est sur l’hôte, MySQL reçoit les connexions depuis l’IP du bridge Docker (ex. `172.18.0.1`) et les refuse tant que l’utilisateur n’est pas autorisé pour cet hôte.

## À faire sur le serveur (où MySQL est installé)

### 1. Se connecter à MySQL

En SSH sur le serveur (46.202.153.86) :

```bash
mysql -u root -p
```

### 2. Autoriser `root` pour les connexions distantes (dont Docker)

Exécuter dans le client MySQL (en adaptant le mot de passe si besoin) :

```sql
-- Autoriser root depuis n'importe quel hôte (dont 172.18.0.1)
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

Si vous préférez limiter à l’IP du bridge Docker uniquement :

```sql
CREATE USER IF NOT EXISTS 'root'@'172.18.0.1' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON gloshop.* TO 'root'@'172.18.0.1';
FLUSH PRIVILEGES;
```

### 3. Vérifier que MySQL écoute sur toutes les interfaces

Fichier de config (souvent `/etc/mysql/mysql.conf.d/mysqld.cnf` ou `/etc/my.cnf`) :

```ini
[mysqld]
bind-address = 0.0.0.0
```

Puis redémarrer MySQL :

```bash
sudo systemctl restart mysql
```

### 4. Firewall

Si un firewall est actif, ouvrir le port 3306 (au moins depuis localhost / 172.18.0.0/16) :

```bash
sudo ufw allow 3306
sudo ufw reload
```

## Résumé

| Où          | Action |
|------------|--------|
| **Serveur MySQL** | Créer `root`@`%` (ou `root`@`172.18.0.1`) et `FLUSH PRIVILEGES` |
| **MySQL**  | `bind-address = 0.0.0.0` puis redémarrer MySQL |
| **Firewall** | Autoriser le port 3306 si besoin |

Après ça, le conteneur Docker (Laravel) peut se connecter à MySQL avec `DB_HOST=46.202.153.86` (ou `host.docker.internal` si Docker et MySQL sont sur la même machine).
