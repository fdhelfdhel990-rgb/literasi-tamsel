#!/bin/sh
set -eu

if [ "${APP_ENV:-}" = "production" ] && [ "${DB_CONNECTION:-}" = "mysql_aiven" ]; then
    ca_file="${AIVEN_MYSQL_ATTR_SSL_CA:-}"

    if [ -z "$ca_file" ]; then
        echo "Startup validation failed: AIVEN_MYSQL_ATTR_SSL_CA is required for mysql_aiven."
        exit 1
    fi

    case "$ca_file" in
        [A-Za-z]:*|*\\*)
            echo "Startup validation failed: AIVEN_MYSQL_ATTR_SSL_CA must be a Linux container path, not a Windows path."
            exit 1
            ;;
    esac

    if [ ! -r "$ca_file" ]; then
        echo "Startup validation failed: Aiven CA file is not readable at configured path."
        echo "Expected Render secret file path: /etc/secrets/aiven-ca.pem"
        exit 1
    fi
fi

rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php

exec "$@"
