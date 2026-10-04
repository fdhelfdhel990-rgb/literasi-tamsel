#!/bin/sh
set -eu

echo "Startup diagnostics: Apache document root is /var/www/html/public."
if [ -f /var/www/html/public/health.txt ]; then
    echo "Startup diagnostics: public health file found."
else
    echo "Startup diagnostics: public health file missing."
fi

if [ "${APP_ENV:-}" = "production" ] && [ "${DB_CONNECTION:-}" = "mysql_aiven" ]; then
    ca_file="${AIVEN_MYSQL_ATTR_SSL_CA:-}"

    if [ -z "$ca_file" ]; then
        echo "Startup diagnostics: AIVEN_MYSQL_ATTR_SSL_CA is not set; database connections will fail until configured."
    else
        case "$ca_file" in
            [A-Za-z]:*|*\\*)
                echo "Startup diagnostics: AIVEN_MYSQL_ATTR_SSL_CA appears to be a Windows path; use /etc/secrets/aiven-ca.pem on Render."
                ;;
            *)
                if [ -r "$ca_file" ]; then
                    echo "Startup diagnostics: Aiven CA file is readable."
                else
                    echo "Startup diagnostics: Aiven CA file is not readable at configured path."
                    echo "Startup diagnostics: expected Render secret file path is /etc/secrets/aiven-ca.pem."
                fi
                ;;
        esac
    fi
fi

rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php

exec "$@"
