function createDockerStdoutStderr() {
    # link stdout from docker
    if [[ -n "$LOG_STDOUT" ]]; then
        echo "Log stdout redirected to $LOG_STDOUT"
    else
        LOG_STDOUT="/proc/$$/fd/1"
    fi

    if [[ -n "$LOG_STDERR" ]]; then
        echo "Log stderr redirected to $LOG_STDERR"
    else
        LOG_STDERR="/proc/$$/fd/2"
    fi

    ln -f -s "$LOG_STDOUT" /docker.stdout
    ln -f -s "$LOG_STDERR" /docker.stderr
}

function runEntrypoints() {
  if [[ -d "$1" ]]; then
    for FILE in "$1"/*.sh
    do
      echo "-> Executing ${FILE}"
      # run custom scripts, only once
      #
      # Abort on failure. These scripts template the nginx vhost (DOCUMENT_ROOT,
      # SERVERNAME) before nginx starts, so ignoring their exit status meant a
      # broken entrypoint produced a *running* container serving a config full of
      # unsubstituted placeholders, with nothing in the logs to explain it. Fail
      # the container instead -- a CrashLoopBackOff is diagnosable, that was not.
      sh "$FILE"
      ENTRYPOINT_RC=$?
      if [[ $ENTRYPOINT_RC -ne 0 ]]; then
        echo "!! Entrypoint ${FILE} failed with exit code ${ENTRYPOINT_RC} -- aborting startup" >&2
        exit $ENTRYPOINT_RC
      fi
    done
  fi
}

createDockerStdoutStderr

runEntrypoints /entrypoint.d

exec "$@"
