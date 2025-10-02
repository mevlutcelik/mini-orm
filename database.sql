-- Mini ORM Test Database Schema

-- Users tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255),
    age INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Posts tablosu
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User profiles tablosu (HasOne ilişkisi için)
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    bio TEXT,
    avatar VARCHAR(255),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Roles tablosu (Many-to-Many için)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User-Role pivot tablosu (BelongsToMany ilişkisi için)
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    role_id INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
);

-- Test verileri
INSERT INTO users (name, email, age, status) VALUES
('Ali Veli', 'ali@example.com', 25, 'active'),
('Ayşe Fatma', 'ayse@example.com', 30, 'active'),
('Mehmet Can', 'mehmet@example.com', 22, 'inactive');

INSERT INTO posts (title, content, user_id) VALUES
('İlk Post', 'Bu benim ilk postum!', 1),
('İkinci Post', 'Bu da ikinci postum.', 1),
('Ayşenin Postu', 'Merhaba dünya!', 2);

INSERT INTO roles (name, description) VALUES
('admin', 'Sistem yöneticisi'),
('user', 'Normal kullanıcı'),
('moderator', 'Moderatör');

INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1), -- Ali admin
(1, 3), -- Ali moderator
(2, 2), -- Ayşe user
(3, 2); -- Mehmet user