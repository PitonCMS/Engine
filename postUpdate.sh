#!/bin/sh
echo "Moving admin public assets..."
rm -rf public/admin/*
cp -r vendor/pitoncms/engine/publicAssets/* public/admin
echo "Complete"
