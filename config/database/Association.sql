CREATE TABLE Event (
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    contentful_event_id VARCHAR(255) NOT NULL UNIQUE,
    google_calendar_event_id VARCHAR(255) NOT NULL UNIQUE
);