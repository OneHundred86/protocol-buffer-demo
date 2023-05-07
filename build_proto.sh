#/bin/sh
rm -rf proto_php/*
protoc --php_out=./proto_php --proto_path=./proto/ ./proto/*.proto
