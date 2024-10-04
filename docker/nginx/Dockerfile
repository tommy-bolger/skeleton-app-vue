FROM nginx:1.21.6-alpine

ARG BACKEND_DOMAIN
ARG FRONTEND_DOMAIN

COPY backend.conf.template /etc/nginx/conf.d/backend.conf
COPY frontend.conf.template /etc/nginx/conf.d/frontend.conf
COPY ssl.conf /etc/nginx/

RUN apk update \
    && sed -i "s/<SERVER_NAME>/${BACKEND_DOMAIN}/g" /etc/nginx/conf.d/backend.conf \
    && sed -i "s/<SERVER_NAME>/${FRONTEND_DOMAIN}/g" /etc/nginx/conf.d/frontend.conf

STOPSIGNAL SIGQUIT

CMD ["nginx", "-g", "daemon off;"]
