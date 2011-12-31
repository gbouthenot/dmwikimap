#!/bin/sh

if [ \( ! -f "$1" \) -o \( -z "$1" \) ]; then
    echo "usage: $0 (file to compress)"
    exit 1
fi;

OUTFILE="$1"

# compress this file
gzip -c -9 "${OUTFILE}" >"${OUTFILE}gz"