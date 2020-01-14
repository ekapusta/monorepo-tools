#!/usr/bin/env bash

# Build monorepo from specified remotes
# You must first add the remotes by "git remote add <remote-name> <repository-url>" and fetch from them by "git fetch --all"
# Final monorepo will contain all branches from the first remote and master branches of all remotes will be merged
# If subdirectory is not specified remote name will be used instead
#
# Usage: monorepo_build.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...
#
# Example: monorepo_build.sh main-repository package-alpha:packages/alpha package-beta:packages/beta

# Check provided arguments
if [ "$#" -lt "1" ]; then
    echo 'Please provide at least 1 remote to be merged into a new monorepo'
    echo 'Usage: monorepo_build.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...'
    echo 'Example: monorepo_build.sh package-alpha:packages/alpha package-beta:packages/beta'
    exit
fi
MERGE_INTO_BRANCH=$(git rev-parse --abbrev-ref HEAD)
# Get directory of the other scripts
MONOREPO_SCRIPT_DIR=$(dirname "$0")
# Wipe original refs (possible left-over back-up after rewriting git history)
$MONOREPO_SCRIPT_DIR/original_refs_wipe.sh
for PARAM in $@; do
    # Parse parameters in format <remote-name>[:<subdirectory>]
    PARAM_ARR=(${PARAM//:/ })
    REMOTE=${PARAM_ARR[0]}
    SUBDIRECTORY=${PARAM_ARR[1]}
    if [ "$SUBDIRECTORY" == "" ]; then
        SUBDIRECTORY=$REMOTE
    fi
    # Rewrite all branches from the first remote, only master branches from others
    echo "Building branch 'master' of the remote '$REMOTE'"
    git checkout --detach $REMOTE/master
    $MONOREPO_SCRIPT_DIR/rewrite_history_into.sh $SUBDIRECTORY
    MERGE_REFS="$MERGE_REFS $(git rev-parse HEAD)"
    # Wipe the back-up of original history
    $MONOREPO_SCRIPT_DIR/original_refs_wipe.sh

    START_COMMIT=$(git log --pretty=format:"%h" | tail -1)
    END_COMMIT=$(git rev-parse HEAD)

    git rebase --preserve-merges --onto $MERGE_INTO_BRANCH $START_COMMIT $END_COMMIT
    git checkout -b repo/$REMOTE
    
    MERGE_REFS=$(git rev-parse HEAD)

    git checkout $MERGE_INTO_BRANCH
    git merge --no-ff repo/$REMOTE
    git branch --delete repo/$REMOTE
done
