# Deployment Walkthrough

Foi configurado com sucesso o fluxo do **Nginx Proxy** para a infraestrutura Docker atendendo as regras solicitadas.

## O que foi feito:
1. **Regras de Acesso Exclusivo**: Bloqueio ativo nas URLs `/login`, `/register` e `/dashboard` no arquivo `nginx.conf`. Estas URLs só responderão para requisições vindas de IPs locais (RFC 1918) sendo `192.168.0.0/16`, `10.0.0.0/8`, `172.16.0.0/12` e `127.0.0.1`. Visitantes de fora da rede receberão falha `403 Forbidden`.
   * **Obs:** A regra de IP privado está configurada corretamente para o tráfego do Roteador, assumindo que ele não faz Masking (esconde o IP de origem). Caso você não consiga acessar os painéis depois, nos avise!
2. **Atualização nas Fases 1 e 2**: Modificamos tanto a "Fase 1" (HTTP puro, para viés de Certbot) quanto as linhas comentadas da "Fase 2" (fase definitiva sob HTTPS). Após gerar o certificado com o comando Let's Encrypt, você poderá descomentar e usar com a segurança aplicada integralmente.
3. **Visibilidade de Arquivos (Fotos)**: Nginx continuará fazendo o pass-through de tudo exceto essas 3 rotas específicas. O Entrypoint script do Laravel já lida com o link simbólico `storage:link`, e o volume já está plugado corretamente. Isso significa que assim que subir e inserir itens pelo sistema, o link index os fará visíveis ao público em geral.

## Passos Finais no Servidor 

Agora você precisará rodar os processos na CLI do Server. Certifique-se de estar com a última versão dos arquivos atualizados!

1. Subir a Landing Page:
```bash
cd /Users/marceloC/Desktop/landing_page
docker compose up -d --build
```
2. Subir o Nginx:
```bash
cd /Users/marceloC/Desktop/nginx-proxy
docker compose up -d
```
3. Executar o Certbot para validar Let's Encrypt (com o container proxy no ar, acatando tráfego HTTP porta 80 do mundo externo!):
```bash
docker compose run --rm certbot certonly --webroot -w /var/www/certbot -d grupo20mais.com.br -m seu-email@exemplo.com --agree-tos
```
4. Aplicar SSL - Após Gerar!
- Abra novamente `/Users/marceloC/Desktop/nginx-proxy/nginx.conf`
- Comente as linhas de debaixo da FASE 1
- Descomente as linhas da FASE 2
- Recarregue o Nginx usando o comando: `docker compose exec nginx nginx -s reload`
