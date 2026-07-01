#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PID_FILE="$APP_DIR/storage/app/central-cron.pid"
LOG_FILE="$APP_DIR/storage/logs/central-cron.log"

mkdir -p "$APP_DIR/storage/app" "$APP_DIR/storage/logs"

is_running() {
    if [[ ! -f "$PID_FILE" ]]; then
        return 1
    fi

    local pid
    pid="$(cat "$PID_FILE")"
    [[ -n "$pid" ]] && kill -0 "$pid" 2>/dev/null
}

case "${1:-start}" in
    start)
        if is_running; then
            echo "Worker central ja esta rodando com PID $(cat "$PID_FILE")."
            exit 0
        fi

        cd "$APP_DIR"
        nohup php artisan system:cron-work --sleep=5 >> "$LOG_FILE" 2>&1 &
        echo "$!" > "$PID_FILE"
        echo "Worker central iniciado com PID $(cat "$PID_FILE")."
        ;;
    stop)
        if ! is_running; then
            echo "Worker central nao esta rodando."
            rm -f "$PID_FILE"
            exit 0
        fi

        kill "$(cat "$PID_FILE")"
        rm -f "$PID_FILE"
        echo "Worker central parado."
        ;;
    restart)
        "$0" stop
        "$0" start
        ;;
    status)
        if is_running; then
            echo "Worker central rodando com PID $(cat "$PID_FILE")."
        else
            echo "Worker central parado."
            exit 1
        fi
        ;;
    *)
        echo "Uso: $0 {start|stop|restart|status}"
        exit 2
        ;;
esac
