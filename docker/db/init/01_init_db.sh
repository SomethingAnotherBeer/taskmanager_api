#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER $DB_USER WITH PASSWORD $DB_PASS;
	CREATE DATABASE $DB_NAME;
	GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;
	\connect $DB_NAME $DB_USER

	BEGIN;
		CREATE TABLE IF NOT EXISTS user_rights(
			user_rights_id SERIAL PRIMARY KEY,
			user_rights_name VARCHAR(255) UNIQUE NOT NULL
	
		);




		CREATE TABLE users(
			user_id SERIAL PRIMARY KEY,
			user_rights_id INTEGER NOT NULL,
			user_login VARCHAR(255) UNIQUE NOT NULL,
			user_password VARCHAR(255) NOT NULL,
			user_email VARCHAR(255) UNIQUE,
			user_name VARCHAR(255),
			user_surname VARCHAR(255),
	
			FOREIGN KEY(user_rights_id) REFERENCES user_rights(user_rights_id)

		);


		CREATE TABLE IF NOT EXISTS tokens(
			token_id SERIAL PRIMARY KEY,
			user_id INTEGER NOT NULL,
			token_key VARCHAR(255) UNIQUE NOT NULL,
			token_untill INTEGER NOT NULL,
	
			FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE
		);

		CREATE TABLE IF NOT EXISTS task_statuses(
			task_status_id SERIAL PRIMARY KEY,
			task_status_name VARCHAR(255) UNIQUE NOT NULL
	
		);


		CREATE TABLE IF NOT EXISTS tasks(
			task_id SERIAL PRIMARY KEY,
			formulator_id INTEGER NOT NULL,
			executor_id INTEGER NOT NULL,
			task_status_id INTEGER NOT NULL,
			task_key VARCHAR(255) UNIQUE NOT NULL,
			task_name VARCHAR(255) NOT NULL,
			task_description TEXT,
			task_create_timestamp INTEGER NOT NULL DEFAULT EXTRACT (EPOCH FROM NOW()),
			task_first_processing_timestamp INTEGER DEFAULT 0 CHECK ((task_first_processing_timestamp > task_create_timestamp AND task_first_processing_timestamp != 0) OR  task_first_processing_timestamp = 0),

			task_last_processing_timestamp INTEGER DEFAULT 0 CHECK ((task_last_processing_timestamp > task_first_processing_timestamp AND task_last_processing_timestamp != 0) OR task_last_processing_timestamp = 0),
	
			task_end_timestamp INTEGER DEFAULT 0,

			FOREIGN KEY(formulator_id) REFERENCES users(user_id),
			FOREIGN KEY(executor_id) REFERENCES users(user_id),
			FOREIGN KEY(task_status_id) REFERENCES task_statuses(task_status_id)
		);

		CREATE TABLE IF NOT EXISTS groups(
			group_id SERIAL PRIMARY KEY,
			group_name VARCHAR(255) UNIQUE NOT NULL

		);


		CREATE TABLE IF NOT EXISTS users_associates(
			users_associate_id SERIAL PRIMARY KEY,
			user_one_id INTEGER NOT NULL,
			user_two_id INTEGER NOT NULL,
			associate_status VARCHAR(25) NOT NULL,

			FOREIGN KEY(user_one_id)  REFERENCES users(user_id) ON DELETE CASCADE,
			FOREIGN KEY(user_two_id) REFERENCES users(user_id) ON DELETE CASCADE
		);


		CREATE TABLE IF NOT EXISTS users_per_groups(
			user_per_group_id SERIAL PRIMARY KEY,
			user_id INTEGER NOT NULL,
			group_id INTEGER NOT NULL,
	
			FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
			FOREIGN KEY(group_id) REFERENCES groups(group_id) ON DELETE CASCADE
		);

		INSERT INTO user_rights (user_rights_name) VALUES
			('admin'),('user');

		INSERT INTO users (user_login, user_password, user_rights_id, user_name, user_surname) VALUES ('some111','\$2y\$10\$EDnAYBvkseHM5pTxKw3yDO2ev6UuIFy6qGo2c4dQUCk5JFiFHhJh6',1,'John','Malkovich');




	COMMIT;
EOSQL
