CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email)
);

INSERT INTO users (username, email, password_hash, full_name)
VALUES
('john_doe', 'john@example.com', 'hash123', 'John Doe'),
('jane_smith', 'jane@example.com', 'pass456', 'Jane Smith'),
('mike_williams', 'mike@example.com', 'pwd789', 'Mike Williams'),
('sara_jackson', 'sara@example.com', 'secure123', 'Sara Jackson'),
('alex_carter', 'alex@example.com', 'password456', 'Alex Carter'),
('emily_wilson', 'emily@example.com', 'hashed789', 'Emily Wilson'),
('chris_brown', 'chris@example.com', 'pass123hash', 'Chris Brown'),
('lisa_miller', 'lisa@example.com', 'password789', 'Lisa Miller'),
('ryan_taylor', 'ryan@example.com', 'hashedpass', 'Ryan Taylor'),
('olivia_clark', 'olivia@example.com', 'securepwd', 'Olivia Clark');
