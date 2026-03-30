# 🚀 Guia de Implantação e Proxy Reverso (Nginx + Docker)

## 1. Como Subir a Landing Page com HTTPS (Configuração Atual)

Este é o roteiro completo para você rodar no seu servidor Windows Server (onde as portas 80/443 estão liberadas). A nossa infraestrutura atual já suporta o proxy reverso e SSL via Certbot.

### Passo 1: Iniciar os Contêineres (Fase 1 - HTTP)
1. Acesse o seu servidor.
2. Navegue até a pasta da landing page via terminal:
   `cd /caminho-ate-o/landing_page`
3. Baixe/atualize as mudanças do Github (se necessário):
   `git pull`
4. Suba todos os serviços em segundo plano:
   `docker compose up -d --build`

Isso já fará com que o Roteador redirecione o tráfego HTTP porta 80 do mundo e o direcione para a nossa tela (com o bônus de segurança administrativa implementado).

### Passo 2: Gerar Certificado SSL (Let's Encrypt)
Com a Landing Page respondendo na porta 80 externa, execute o Certbot para validar o domínio. **Na mesma pasta**, rode:

`docker compose run --rm certbot certonly --webroot -w /var/www/certbot -d grupo20mais.com.br -m seu-email@dominio.com --agree-tos`

*(O Certbot vai gerar um alerta de Sucesso e guardar as chaves SSL nas pastas corretas de volume que já pré-configuramos).*

### Passo 3: Ativar a Segurança HTTPS (Fase 2)
Após gerar o certificado com sucesso:
1. Abra o código do Nginx em: `docker/nginx/nginx.conf`
2. **Desabilite** o bloco de `server` sob a marcação "Fase 1" (comentando cada linha com `#`).
3. **Habilite** os dois blocos de `server` sob "Fase 2" (removendo os `#` do começo de cada linha).
4. Salve o arquivo Nginx.

### Passo 4: Recarregar o Proxy
Sem derrubar a infraestrutura, informe ao Nginx a nova configuração HTTPS (rode direto do terminal da pasta `landing_page`):

`docker compose exec nginx nginx -s reload`

PRONTO! A Landing Page funcionará exclusivamente em rotas protegidas por Cadeado SSL, com seu Admin Dashboard blindado de invasões externas!

---

## 2. Plano de Implementação Futura: Subdomínio Odoo ERP

> **O cenário do Odoo**: Ele já se encontra funcionando perfeitamente no IP Fixo e na porta **8069**, aberto de forma direta na web. A ideia deste plano é colocá-lo atrás das grades e proteções do nosso Proxy Reverso recém-criado, com HTTPS gratuito!

### Objetivo
Passar a utilizar o subdomínio `odoo.grupo20mais.com.br` sob Certificado SSL (443), acatando o fluxo de dados via Nginx antes de repassar para o Odoo em back-end (8069).

### Checklist de Implementação 
1. **Configuração de DNS**: 
   Acessar o seu provedor (Registro.br / Cloudflare) e associar uma nova entrada "Tipo A" (ou "CNAME") para o ponteiro `odoo`, apontando para o *mesmo IP Fixo* do seu Windows Server.

2. **Reconfiguração do Roteador / Firewall (Recomendado de Segurança)**:
   Bloquear ou despublicar a porta nativa de redirecionamento "8069" do seu provedor para a internet, mantendo o NAT público trabalhando **exclusivamente** nas portas 80 e 443 do docker. *Isso força com que 100% de conexões externas e curiosos precisem passar pela interceptação TLS/SSL do proxy reverso, ocultando infraestrutura e garantindo segurança extrema.*

3. **Inclusão do Subdomínio no Nginx**:
   Embaixo de todo o conteúdo da Fase 2 existente no `docker/nginx/nginx.conf`, abriremos a Fase 3 (Odoo), interceptando requisições com o cabeçalho novo:
   ```nginx
   # FASE 3: REDIRECIONANDO PARA O ODOO INTERNO NO MESMO SERVIDOR
   server {
       listen 80;
       server_name odoo.grupo20mais.com.br;
       
       location /.well-known/acme-challenge/ {
           root /var/www/certbot;
       }

       location / {
           return 301 https://$host$request_uri;
       }
   }

   server {
       listen 443 ssl;
       server_name odoo.grupo20mais.com.br;

       # (Certificados com a URL do odoo)
       ssl_certificate     /etc/letsencrypt/live/odoo.grupo20mais.com.br/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/odoo.grupo20mais.com.br/privkey.pem;

       # Reuso de ssl_protocols...
       
       # Em tese, como o Nginx no Docker alcança diretamente as portas expostas da Maquina Física Windows (localhost),
       # Apontaremos para o próprio Server Windows resolvendo a porta nativa do Odoo:
       location / {
           proxy_pass http://host.docker.internal:8069;
           
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }
   ```

4. **Gerar Segundo Certificado SSL**:
   Assim que o DNS do subdomínio odoo já responder pro seu IP, voltaremos ao servidor na pasta do projeto e rodaremos o Certbot de novo, apenas mudando o nome do domínio solicitado `-d`:
   `docker compose run --rm certbot certonly --webroot -w /var/www/certbot -d odoo.grupo20mais.com.br -m seu-email@exemplo.com --agree-tos`

5. **Recarregar o Nginx novamente**:
   `docker compose exec nginx nginx -s reload`
