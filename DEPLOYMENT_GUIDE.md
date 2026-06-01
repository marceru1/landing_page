# 🚀 Guia de Deploy — Landing Page + Odoo (Windows Server 2022)

## Arquitetura Atual

```
Internet → Nginx (porta 80/443) → Landing Page (Apache container)
Internet → Nginx (porta 80/443) → Odoo (host Windows, porta 8069)
```

| Serviço | Container | Domínio |
|---------|-----------|---------|
| Landing Page | `grupo20mais.com.br` | https://grupo20mais.com.br |
| Nginx Proxy | `proxy_nginx` | — |
| Certbot SSL | `proxy_certbot` | — |
| Odoo ERP | Roda no host Windows | https://odoo.grupo20mais.com.br |

---

## ⚠️ REGRAS DE OURO (LEIA ANTES DE QUALQUER COISA)

### 1. NUNCA rode `docker compose down` sem necessidade
- O `docker compose down` **destrói** os containers e pode apagar os volumes dos certificados SSL.
- Para reiniciar serviços, use `docker compose restart web` ou `docker compose up -d --build web`.

### 2. Rebuilde APENAS o serviço `web`
- Quando fizer mudanças no código, rode **APENAS**:
  ```bash
  docker compose up -d --build web
  ```
- Isso reconstrói **só** o container da landing page sem tocar no Nginx nem nos certificados.
- **NUNCA** rode `docker compose up -d --build` (sem especificar `web`), pois ele pode recriar o Nginx e perder a config ativa.

### 3. Mudanças no Nginx: apenas reload
- Se editar o `docker/nginx/nginx.conf`, **NÃO** rebuilde o Nginx.
- Rode apenas:
  ```bash
  docker compose exec nginx nginx -s reload
  ```

### 4. Certificados SSL vivem em volumes Docker
- Os certificados estão em volumes nomeados (`certbot-www` e `certbot-conf`), **NÃO** em pastas do Windows.
- Isso é proposital: o Windows corrompe symlinks do Linux. Nunca mude isso para bind mounts.

### 5. Certbot: cuidado com o entrypoint
- Para gerar/renovar certificados manualmente, use `--entrypoint ""`:
  ```bash
  docker compose run --rm --entrypoint "" certbot certbot certonly --webroot -w /var/www/certbot -d DOMINIO -m EMAIL --agree-tos
  ```
- Sem o `--entrypoint ""`, o Docker ignora seu comando e roda o loop de renovação automática.

---

## 🔁 Fluxo de Trabalho Diário

### Mudanças de Código (PHP, Blade, CSS, JS)

```
[Mac] Edita o código
   ↓
[Mac] npm run build          ← SE mexeu em CSS/JS
   ↓
[Mac] git add . && git commit -m "mensagem" && git push
   ↓
[Servidor] git pull
   ↓
[Servidor] docker compose up -d --build web
```

### Tabela Rápida: Tipo de Mudança vs. Comando

| Mudança | Precisa `npm run build`? | Comando no Servidor |
|---------|--------------------------|---------------------|
| PHP / Blade (textos, lógica) | Não | `docker compose up -d --build web` |
| CSS / JS (estilos, animações) | **Sim** | `docker compose up -d --build web` |
| Nginx (rotas, headers, subdomínios) | Não | `docker compose exec nginx nginx -s reload` |
| Variáveis de ambiente | Não | `docker compose up -d --force-recreate web` |
| Banco de dados / Migrations | Não | `docker compose up -d --build web` (entrypoint roda migrate automaticamente) |

---

## 🔒 Configuração SSL (Já Feita)

### Certificados Ativos
- `grupo20mais.com.br` + `www.grupo20mais.com.br` → expira 28/06/2026
- `odoo.grupo20mais.com.br` → expira 28/06/2026

### Renovação Automática
O container `proxy_certbot` roda `certbot renew` a cada 12h automaticamente.

### Renovação Manual (se necessário)
```bash
docker compose run --rm --entrypoint "" certbot certbot renew
docker compose exec nginx nginx -s reload
```

---

## 📋 Nginx — Fases do Arquivo `docker/nginx/nginx.conf`

| Fase | Status | Função |
|------|--------|--------|
| FASE 1 | **Comentada** | HTTP puro (só para gerar certificados iniciais) |
| FASE 2 | **Ativa** | Landing Page com HTTPS + bloqueio admin |
| FASE 3 | **Ativa** | Odoo ERP com HTTPS via proxy reverso |

### Para reativar a Fase 1 (emergência / novo certificado)
1. Descomente a Fase 1 e comente as Fases 2 e 3
2. `docker compose exec nginx nginx -s reload`
3. Gere o certificado necessário
4. Reverta: comente Fase 1, descomente Fases 2 e 3
5. `docker compose exec nginx nginx -s reload`

---

## 🛡️ Segurança

### Rotas Bloqueadas ao Acesso Externo
As rotas `/login`, `/register`, `/dashboard` só são acessíveis de dentro da rede local (192.168.x.x, 10.x.x.x, 172.16.x.x).

### Registro de Usuários
Desabilitado via código. Rotas de `/register` retornam 404. Para reativar, descomente as linhas em `routes/auth.php`.

### Variáveis Sensíveis
- `APP_KEY` está definida no `docker-compose.yml` (environment)
- `.env` **NÃO** vai para o container (está no `.dockerignore`)

---

## 🆘 Troubleshooting

| Problema | Causa Provável | Solução |
|----------|---------------|---------|
| 500 Server Error | Falta `APP_KEY` ou Vite manifest | Checar `docker compose logs web --tail=20` |
| CSS/JS não carregam | Mixed content (HTTP/HTTPS) | Verificar se `trustProxies` está ativo em `bootstrap/app.php` |
| Certificado não encontrado | Volumes corrompidos ou inexistentes | Regerar com `--entrypoint ""` |
| `No renewals were attempted` | Certbot usando entrypoint errado | Usar `--entrypoint ""` no comando |
| Nginx não recarrega | Erro de sintaxe no conf | `docker compose exec nginx nginx -t` para testar config |
| Odoo não abre via subdomínio | Porta 8069 fechada no Windows Firewall | Verificar firewall do host |
