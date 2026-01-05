CREATE DATABASE ClubManagement;
GO
USE ClubManagement;
GO
CREATE TABLE roles (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);
CREATE TABLE users (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    email NVARCHAR(255) NOT NULL UNIQUE,
    email_verified_at DATETIME NULL,
    password NVARCHAR(255) NOT NULL,
    student_code NVARCHAR(50),
    role_id BIGINT NULL,
    status BIT DEFAULT 1,
    class NVARCHAR(50),
    phone NVARCHAR(20),
    gender NVARCHAR(10),
    date_of_birth DATE,
    department NVARCHAR(255),
    bio NVARCHAR(MAX),
    avatar NVARCHAR(255),
    last_activity DATETIME,
    two_factor_enabled BIT DEFAULT 0,
    devices NVARCHAR(MAX),
    email_notifications BIT DEFAULT 1,
    event_notifications BIT DEFAULT 1,
    club_notifications BIT DEFAULT 1,
    language NVARCHAR(10) DEFAULT 'vi',
    dark_mode BIT DEFAULT 0,
    remember_token NVARCHAR(100),
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE users
ADD CONSTRAINT FK_users_roles
FOREIGN KEY (role_id) REFERENCES roles(id)
ON DELETE SET NULL;
CREATE TABLE clubs (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    slug NVARCHAR(255) NOT NULL UNIQUE,
    code NVARCHAR(255) UNIQUE,
    description NVARCHAR(MAX),
    activity_goals NVARCHAR(MAX),
    logo NVARCHAR(255),
    banner NVARCHAR(255),
    owner_id BIGINT NULL,
    status NVARCHAR(20) DEFAULT 'pending',
    field NVARCHAR(255),
    club_type NVARCHAR(255),
    chairman NVARCHAR(255),
    members INT DEFAULT 0,
    activity NVARCHAR(MAX),
    establishment_date DATE,
    email NVARCHAR(255),
    fanpage NVARCHAR(255),
    phone NVARCHAR(20),
    social_links NVARCHAR(MAX),
    meeting_place NVARCHAR(255),
    meeting_schedule NVARCHAR(255),
    approval_mode NVARCHAR(20) DEFAULT 'manual',
    activity_approval_mode NVARCHAR(20) DEFAULT 'school',
    is_public BIT DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE clubs
ADD CONSTRAINT FK_clubs_users
FOREIGN KEY (owner_id) REFERENCES users(id)
ON DELETE SET NULL;
CREATE TABLE club_members (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    club_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    position NVARCHAR(30) DEFAULT 'member',
    status NVARCHAR(30) DEFAULT 'pending',
    joined_date DATE,
    join_count INT DEFAULT 1,
    notes NVARCHAR(MAX),
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    CONSTRAINT UQ_club_user UNIQUE (club_id, user_id)
);

ALTER TABLE club_members
ADD CONSTRAINT FK_cm_clubs FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE;

ALTER TABLE club_members
ADD CONSTRAINT FK_cm_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
CREATE TABLE events (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    club_id BIGINT NULL,
    title NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    start_at DATETIME,
    end_at DATETIME,
    location NVARCHAR(255),
    status NVARCHAR(30) DEFAULT 'upcoming',
    approval_status NVARCHAR(30) DEFAULT 'pending',
    created_by BIGINT NULL,
    activity_type NVARCHAR(100),
    max_participants INT,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
);

ALTER TABLE events
ADD CONSTRAINT FK_events_clubs FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE;

ALTER TABLE events
ADD CONSTRAINT FK_events_users FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
CREATE TABLE event_registrations (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    status NVARCHAR(30) DEFAULT 'pending',
    activity_points INT DEFAULT 0,
    notes NVARCHAR(MAX),
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    CONSTRAINT UQ_event_user UNIQUE (event_id, user_id)
);

ALTER TABLE event_registrations
ADD CONSTRAINT FK_er_events FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE;

ALTER TABLE event_registrations
ADD CONSTRAINT FK_er_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
CREATE TABLE regulations (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    code NVARCHAR(255) NOT NULL UNIQUE,
    title NVARCHAR(255) NOT NULL,
    content NVARCHAR(MAX) NOT NULL,
    scope NVARCHAR(20) DEFAULT 'all_clubs',
    club_id BIGINT NULL,
    severity NVARCHAR(20) DEFAULT 'medium',
    status NVARCHAR(20) DEFAULT 'active',
    issued_date DATE NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
);

ALTER TABLE regulations
ADD CONSTRAINT FK_regulations_clubs
FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL;

ALTER TABLE regulations
ADD CONSTRAINT FK_regulations_created_by
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE regulations
ADD CONSTRAINT FK_regulations_updated_by
FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;
CREATE TABLE violations (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    user_id BIGINT NOT NULL,
    club_id BIGINT NOT NULL,
    regulation_id BIGINT NOT NULL,
    description NVARCHAR(MAX) NOT NULL,
    severity NVARCHAR(20) DEFAULT 'medium',
    violation_date DATETIME NOT NULL,
    recorded_by BIGINT NOT NULL,
    status NVARCHAR(20) DEFAULT 'pending',
    discipline_type NVARCHAR(30),
    discipline_reason NVARCHAR(MAX),
    discipline_period_start DATE,
    discipline_period_end DATE,
    processed_by BIGINT NULL,
    processed_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
);

ALTER TABLE violations
ADD CONSTRAINT FK_violations_user FOREIGN KEY (user_id) REFERENCES users(id);

ALTER TABLE violations
ADD CONSTRAINT FK_violations_club FOREIGN KEY (club_id) REFERENCES clubs(id);

ALTER TABLE violations
ADD CONSTRAINT FK_violations_regulation FOREIGN KEY (regulation_id) REFERENCES regulations(id);

ALTER TABLE violations
ADD CONSTRAINT FK_violations_recorded_by FOREIGN KEY (recorded_by) REFERENCES users(id);

ALTER TABLE violations
ADD CONSTRAINT FK_violations_processed_by FOREIGN KEY (processed_by) REFERENCES users(id)
ON DELETE SET NULL;
CREATE TABLE notifications (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,
    body NVARCHAR(MAX),
    sender_id BIGINT NULL,
    club_id BIGINT NULL,
    type NVARCHAR(100),
    source NVARCHAR(100),
    is_public BIT DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE notifications
ADD CONSTRAINT FK_notifications_sender
FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE notifications
ADD CONSTRAINT FK_notifications_club
FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL;
CREATE TABLE notification_recipients (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    notification_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    club_id BIGINT NULL,
    is_read BIT DEFAULT 0,
    read_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE notification_recipients
ADD CONSTRAINT FK_nr_notification
FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE;

ALTER TABLE notification_recipients
ADD CONSTRAINT FK_nr_user
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE notification_recipients
ADD CONSTRAINT FK_nr_club
FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE;
CREATE TABLE admin_logs (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    admin_id BIGINT NOT NULL,
    action NVARCHAR(255) NOT NULL,
    model_type NVARCHAR(255),
    model_id BIGINT,
    notes NVARCHAR(MAX),
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE admin_logs
ADD CONSTRAINT FK_admin_logs_users
FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE;
CREATE TABLE support_requests (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    user_id BIGINT NOT NULL,
    club_id BIGINT NULL,
    subject NVARCHAR(255) NOT NULL,
    message NVARCHAR(MAX) NOT NULL,
    status NVARCHAR(30) DEFAULT 'pending',
    response NVARCHAR(MAX),
    responded_by BIGINT NULL,
    responded_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

ALTER TABLE support_requests
ADD CONSTRAINT FK_support_user FOREIGN KEY (user_id) REFERENCES users(id);

ALTER TABLE support_requests
ADD CONSTRAINT FK_support_club FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL;

ALTER TABLE support_requests
ADD CONSTRAINT FK_support_responded_by FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL;
CREATE TABLE system_configs (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    [key] NVARCHAR(255) NOT NULL UNIQUE,
    value NVARCHAR(MAX),
    description NVARCHAR(MAX),
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);
