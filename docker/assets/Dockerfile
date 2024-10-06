FROM node:22.9.0-alpine3.20

WORKDIR /var/www/frontend

COPY entrypoint.sh /entrypoint.sh

RUN apk update \
    && apk upgrade \
    && chown node:node /entrypoint.sh \
    && chmod +x /entrypoint.sh

USER node

ENTRYPOINT ["/entrypoint.sh"]
