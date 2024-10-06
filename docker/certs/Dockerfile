FROM alpine:3.20

ARG BACKEND_DOMAIN
ARG FRONTEND_DOMAIN
ARG COUNTRY
ARG STATE
ARG ORGANIZATION

WORKDIR /etc/wild-alaskan-certs

RUN apk update \
    && apk upgrade \
    && apk add --no-cache openssl \
    && openssl req -x509 -nodes -days 3650 \
		-subj  "/C=${COUNTRY}/ST=${STATE}/O=${ORGANIZATION}/CN=${BACKEND_DOMAIN}" \
		-newkey rsa:4096 -sha256 -keyout "/etc/wild-alaskan-certs/key.pem" \
		-out "/etc/wild-alaskan-certs/cert.pem" \
    	-addext "subjectAltName=DNS:${BACKEND_DOMAIN},DNS:${FRONTEND_DOMAIN}" \
    	-addext "extendedKeyUsage=serverAuth" \
    	-addext "keyUsage=digitalSignature" \
    && openssl dhparam -dsaparam -out "/etc/wild-alaskan-certs/ssl-dhparams.pem" 4096
