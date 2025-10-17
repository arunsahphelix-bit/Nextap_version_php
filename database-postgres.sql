-- PostgreSQL Database Schema for NextTap Builder
-- Drop tables if they exist
DROP TABLE IF EXISTS analytics CASCADE;
DROP TABLE IF EXISTS websites CASCADE;
DROP TABLE IF EXISTS otp_verifications CASCADE;
DROP TABLE IF EXISTS nfc_orders CASCADE;
DROP TABLE IF EXISTS profiles CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    verification_status VARCHAR(20) DEFAULT 'pending',
    is_admin SMALLINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Profiles table
CREATE TABLE profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    profile_name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255),
    about TEXT,
    contact_info TEXT,
    social_links TEXT,
    image VARCHAR(500),
    logo VARCHAR(500),
    theme_id INTEGER DEFAULT 1,
    is_public SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_user_id ON profiles(user_id);
CREATE INDEX idx_slug ON profiles(slug);

-- NFC Orders table
CREATE TABLE nfc_orders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    type VARCHAR(20) NOT NULL,
    selected_profile_id INTEGER,
    uploaded_design VARCHAR(500),
    requirements TEXT,
    business_proof VARCHAR(500),
    status VARCHAR(20) DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_profile_id) REFERENCES profiles(id) ON DELETE SET NULL
);

CREATE INDEX idx_order_user ON nfc_orders(user_id);
CREATE INDEX idx_order_status ON nfc_orders(status);

-- OTP Verifications table
CREATE TABLE otp_verifications (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_email_otp ON otp_verifications(email);

-- Websites table
CREATE TABLE websites (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    site_name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    template_id INTEGER NOT NULL,
    content_json TEXT,
    status VARCHAR(20) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_website_user ON websites(user_id);
CREATE INDEX idx_website_slug ON websites(slug);

-- Analytics table
CREATE TABLE analytics (
    id SERIAL PRIMARY KEY,
    profile_id INTEGER NOT NULL,
    event_type VARCHAR(20) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
);

CREATE INDEX idx_profile ON analytics(profile_id);
