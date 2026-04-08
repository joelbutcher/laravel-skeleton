#!/usr/bin/env bash
set -euo pipefail

# Read stdin JSON from Claude Code
INPUT=$(cat)

# Prevent infinite loops: if we already blocked once and Claude
# couldn't fix it, let it stop rather than looping forever.
STOP_HOOK_ACTIVE=$(echo "$INPUT" | jq -r '.stop_hook_active // false')
if [ "$STOP_HOOK_ACTIVE" = "true" ]; then
    exit 0
fi

CWD=$(echo "$INPUT" | jq -r '.cwd // empty')
if [ -z "$CWD" ]; then
    exit 0
fi

cd "$CWD"

OUTPUT=$(composer before-stopping 2>&1) || {
    # Quality gate failed — block the stop and feed errors back to Claude
    echo "$OUTPUT" | jq -Rs '{
        "decision": "block",
        "reason": ("Quality gate failed. Fix these issues before claiming done:\n\n" + .)
    }'
    exit 0
}

# Quality gate passed — allow stop (no JSON = allow)
exit 0
