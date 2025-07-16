CREATE TABLE optin_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    person VARCHAR(100),
    phone VARCHAR(50),
    usage TEXT NOT NULL,
    usage_type VARCHAR(50),
    area FLOAT,
    structure VARCHAR(50),
    calc_type VARCHAR(50),
    prefecture VARCHAR(50),
    city VARCHAR(100),
    delivery_date DATE,
    is_express BOOLEAN,
    estimate_price INT,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,       -- 例: inquiry_user, inquiry_admin
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO email_templates (name, subject, body) VALUES
('inquiry_user', '見積もり依頼を受け付けました', 
'{email} 様

この度は見積もり依頼をいただきありがとうございます。
以下の内容で受け付けました。

建物用途: {usage}
用途区分: {usage_type}
延床面積: {area} ㎡
構造種別: {structure}
計算種別: {calc_type}
都道府県: {prefecture}
市区町村: {city}
希望納期: {delivery_date}
概算金額: {estimate_price} 円

担当者より折り返しご連絡いたしますので、しばらくお待ちください。
よろしくお願いいたします。'),

('inquiry_admin', '新しい見積もり依頼が届きました',
'新しい見積もり依頼が届きました。

メールアドレス: {email}
建物用途: {usage}
用途区分: {usage_type}
延床面積: {area} ㎡
構造種別: {structure}
計算種別: {calc_type}
都道府県: {prefecture}
市区町村: {city}
希望納期: {delivery_date}
概算金額: {estimate_price} 円
コメント:
{comment}');

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE price_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usage_name VARCHAR(255) NOT NULL,
    unit_price INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE global_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    express_multiplier DECIMAL(5,2) NOT NULL DEFAULT 1.0,
    multi_usage_multiplier DECIMAL(5,2) NOT NULL DEFAULT 1.0
);

INSERT INTO global_settings (express_multiplier, multi_usage_multiplier)
VALUES (1.0, 1.0);

CREATE TABLE structure_multipliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    structure VARCHAR(255) NOT NULL,
    multiplier DECIMAL(5,2) NOT NULL DEFAULT 1.0
);

INSERT INTO structure_multipliers (structure, multiplier) VALUES
('RC', 1.0),
('S', 1.0),
('木造', 1.0),
('混構造', 1.0);