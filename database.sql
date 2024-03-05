DROP TABLE IF EXISTS urls;
CREATE TABLE urls
(
    id         bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name       varchar UNIQUE,
    created_at varchar
)

DROP TABLE IF EXISTS url_checks;
CREATE TABLE url_checks
(
    id          bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id      bigint REFERENCES urls (id),
    status_code integer,
    h1          varchar,
    title       text,
    description text,
    created_at  timestamp
)

