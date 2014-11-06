#!/bin/sh

openssl genrsa -out CA.key 2048
openssl req -x509 -new -nodes -key CA.key -days 1460 -subj '/CN=Swiftmailer CA/O=Swiftmailer/L=Paris/C=FR' -out CA.crt
openssl x509 -in CA.crt -clrtrust -out CA.crt

openssl genrsa -out sign.key 2048
openssl req -new -key sign.key -subj '/CN=Swiftmailer-User/O=Swiftmailer/L=Paris/C=FR' -out sign.csr
openssl x509 -req -in sign.csr -CA CA.crt -CAkey CA.key -out sign.crt -days 1460 -addtrust emailProtection
openssl x509 -in sign.crt -clrtrust -out sign.crt

rm sign.csr

openssl genrsa -out encrypt.key 2048
openssl req -new -key encrypt.key -subj '/CN=Swiftmailer-User/O=Swiftmailer/L=Paris/C=FR' -out encrypt.csr
openssl x509 -req -in encrypt.csr -CA CA.crt -CAkey CA.key -CAcreateserial -out encrypt.crt -days 1460 -addtrust emailProtection
openssl x509 -in encrypt.crt -clrtrust -out encrypt.crt

rm encrypt.csr

openssl genrsa -out encrypt2.key 2048
openssl req -new -key encrypt2.key -subj '/CN=Swiftmailer-User2/O=Swiftmailer/L=Paris/C=FR' -out encrypt2.csr
openssl x509 -req -in encrypt2.csr -CA CA.crt -CAkey CA.key -CAcreateserial -out encrypt2.crt -days 1460 -addtrust emailProtection
openssl x509 -in encrypt2.crt -clrtrust -out encrypt2.crt

rm encrypt2.csr
