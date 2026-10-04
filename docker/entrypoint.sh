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

# Opt-in, non-blocking TLS diagnostics for Render Free (no shell access).
# Never print credentials, certificate contents, or PHP environment variables.
if [ "${AIVEN_TLS_DIAGNOSTICS:-0}" = "1" ] && [ "${DB_CONNECTION:-}" = "mysql_aiven" ]; then
    echo "Aiven TLS check: diagnostic enabled."
    if ! command -v openssl >/dev/null 2>&1; then
        echo "Aiven TLS check: openssl unavailable."
    elif [ -z "${AIVEN_MYSQL_ATTR_SSL_CA:-}" ] || [ ! -r "${AIVEN_MYSQL_ATTR_SSL_CA}" ]; then
        echo "Aiven TLS check: CA file missing or unreadable."
    elif ! openssl x509 -in "$AIVEN_MYSQL_ATTR_SSL_CA" -noout -checkend 0 >/dev/null 2>&1; then
        echo "Aiven TLS check: CA is invalid, expired, or not a readable PEM certificate."
    else
        echo "Aiven TLS check: PEM parses and is currently valid."
        openssl x509 -in "$AIVEN_MYSQL_ATTR_SSL_CA" -noout -fingerprint -sha256 2>/dev/null || true
        if [ -n "${AIVEN_DB_HOST:-}" ] && [ -n "${AIVEN_DB_PORT:-}" ]; then
            tls_result="ok"
            tls_output=$(timeout 12 openssl s_client -starttls mysql \
                -connect "${AIVEN_DB_HOST}:${AIVEN_DB_PORT}" \
                -servername "$AIVEN_DB_HOST" \
                -CAfile "$AIVEN_MYSQL_ATTR_SSL_CA" \
                -verify_hostname "$AIVEN_DB_HOST" -verify_return_error -brief </dev/null 2>&1) || tls_result="failed"
            echo "Aiven TLS check: OpenSSL MySQL handshake $tls_result."
            printf '%s\n' "$tls_output" | grep -Ei 'Verification|verify|error|certificate|CONNECTION|Protocol|Cipher|no peer|unexpected|handshake|BIO_connect|SSL|TLS|server response|STARTTLS' | head -n 8 || true
        else
            echo "Aiven TLS check: host or port is missing."
        fi
    fi
    php -r 'echo "Aiven TLS check: PHP mysqlnd ".(phpversion("mysqlnd") ?: "unavailable")."; ".OPENSSL_VERSION_TEXT.PHP_EOL;' 2>/dev/null || true
fi

rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php

exec "$@"
