#/bin/sh
cd proto
protoc --php_out=../proto_php *.proto
