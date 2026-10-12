-- Deepkala Hair Salon database schema
CREATE DATABASE IF NOT EXISTS deepkala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE deepkala;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    membership ENUM('none', 'silver', 'gold') NOT NULL DEFAULT 'none',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE barbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(150) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    price INT UNSIGNED NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE KEY uq_service_name (name)
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    barber_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service VARCHAR(150) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time VARCHAR(20) NOT NULL,
    notes TEXT,
    amount INT UNSIGNED NOT NULL,
    payment_method VARCHAR(40) NOT NULL,
    status ENUM('confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_appointments_barber FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_barber_slot (barber_id, appointment_date, appointment_time, status),
    KEY idx_appointments_user_date (user_id, appointment_date)
) ENGINE=InnoDB;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    service VARCHAR(150) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    photo_path VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_id INT NULL,
    plan ENUM('silver', 'gold') NULL,
    amount INT UNSIGNED NOT NULL,
    method VARCHAR(40) NOT NULL,
    provider_reference VARCHAR(100) NULL,
    status ENUM('demo', 'pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'demo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO barbers (name, role) VALUES
    ('Deepesh Parmar', 'Founder & Master Stylist'),
    ('Kalpesh Patel', 'Senior Fade & Colour Specialist'),
    ('Sanjay Kumar', 'Beard Specialist & Spa Therapist'),
    ('Nisha Solanki', 'Bridal & Wedding Makeup Artist'),
    ('Rahul Desai', 'Senior Barber'),
    ('Amit Joshi', 'Beard & Grooming Specialist'),
    ('Priya Shah', 'Hair Colour Specialist'),
    ('Vikram Rathod', 'Stylist');

INSERT INTO services (name, category, price, duration_minutes) VALUES
    ('Buzz Cut', 'Cut & Style', 200, 30),
    ('Crew Cut', 'Cut & Style', 250, 30),
    ('Fade Cut', 'Cut & Style', 350, 45),
    ('Undercut', 'Cut & Style', 400, 45),
    ('Mid Fade', 'Cut & Style', 500, 45),
    ('Quiff', 'Cut & Style', 450, 45),
    ('Side Part', 'Cut & Style', 300, 30),
    ('French Crop', 'Cut & Style', 350, 45),
    ('Texture Crop', 'Cut & Style', 450, 45),
    ('Caesar Cut', 'Cut & Style', 300, 30),
    ('Root Touch-Up', 'Coloring', 3200, 120),
    ('Highlights', 'Coloring', 1700, 120),
    ('Lowlights', 'Coloring', 1500, 120),
    ('Gloss Treatment', 'Coloring', 2000, 90),
    ('Color Correction', 'Coloring', 1600, 120),
    ('Fashion Color', 'Coloring', 1900, 120),
    ('Relaxation Spa', 'Spa & Treatment', 800, 60),
    ('Aroma Treatment', 'Spa & Treatment', 1200, 60),
    ('Head Spa', 'Spa & Treatment', 700, 60),
    ('Deep Tissue Spa', 'Spa & Treatment', 1400, 75),
    ('Scalp Treatment', 'Spa & Treatment', 700, 60),
    ('Hydrating Spa', 'Spa & Treatment', 900, 60),
    ('Goatee Beard', 'Beard Grooming', 500, 30),
    ('French Beard', 'Beard Grooming', 400, 30),
    ('Full Beard Style', 'Beard Grooming', 600, 45),
    ('Italian Beard', 'Beard Grooming', 500, 30),
    ('Stubble Beard', 'Beard Grooming', 250, 20),
    ('Corporate Beard', 'Beard Grooming', 350, 30),
    ('Anchor Beard', 'Beard Grooming', 500, 30),
    ('Boxed Beard', 'Beard Grooming', 400, 30),
    ('Special Bridal Package', 'Bridal & Party', 8000, 240),
    ('Bridal Package', 'Bridal & Party', 5000, 180),
    ('Party Makeup + Hair', 'Bridal & Party', 2500, 120),
    ('Groom Package', 'Bridal & Party', 1500, 90);
