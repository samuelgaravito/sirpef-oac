FROM node:18.8.0 AS build
ENV http_proxy "http://10.11.1.20:3128"
ENV https_proxy "http://10.11.1.20:3128"
WORKDIR /app
COPY package.json package.json
#RUN npm install
COPY . .
RUN npm run buildNoCheck

FROM nginx:1.23.1
COPY --from=build /app/dist /opt/site
COPY nginx.conf /etc/nginx/nginx.conf
