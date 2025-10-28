#!/usr/bin/env bash

set -euo pipefail

if (( "$#" != 1 )); then
    echo "Usage: bin/release.sh <version>"
    exit 1
fi

RELEASE_BRANCH="main"
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
VERSION="$1"

[[ "$VERSION" == v* ]] || VERSION="v$VERSION"

if [[ "$RELEASE_BRANCH" != "$CURRENT_BRANCH" ]]; then
    echo "Release branch ($RELEASE_BRANCH) does not match current branch ($CURRENT_BRANCH)."
    exit 1
fi

if [[ -n "$(git status --porcelain)" ]]; then
    echo "Working tree is dirty. Please commit or stash changes before releasing."
    exit 1
fi

git fetch origin

if [[ "$(git rev-parse HEAD)" != "$(git rev-parse origin/$RELEASE_BRANCH)" ]]; then
    echo "Local branch is out of sync with origin/$RELEASE_BRANCH."
    exit 1
fi

git tag "$VERSION"
git push origin "$VERSION"

for REMOTE in foundation publisher listener; do
    echo ""
    echo "Tagging $REMOTE..."

    TMP_DIR=$(mktemp -d)
    REMOTE_URL="git@github.com:alazzi-az/laravel-dapr-$REMOTE.git"

    (
        cd "$TMP_DIR"
        git clone "$REMOTE_URL" .
        git checkout "$RELEASE_BRANCH"
        git tag "$VERSION"
        git push origin "$VERSION"
    )

    rm -rf "$TMP_DIR"
done
