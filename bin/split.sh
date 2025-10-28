#!/usr/bin/env bash

set -euo pipefail

CURRENT_BRANCH="main"

function split() {
    local prefix=$1
    local remote=$2

    SHA1=$(./bin/splitsh-lite --prefix="$prefix")
    git push "$remote" "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote() {
    local name=$1
    local url=$2

    git remote add "$name" "$url" 2>/dev/null || true
}

git pull origin "$CURRENT_BRANCH"

remote foundation git@github.com:alazzi-az/laravel-dapr-foundation.git
remote publisher git@github.com:alazzi-az/laravel-dapr-publisher.git
remote listener git@github.com:alazzi-az/laravel-dapr-listener.git

split 'src/Foundation' foundation
split 'src/Publisher' publisher
split 'src/Listener' listener
