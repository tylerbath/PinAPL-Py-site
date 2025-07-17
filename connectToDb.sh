#!/bin/bash
source .env

mysql -u ${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}
