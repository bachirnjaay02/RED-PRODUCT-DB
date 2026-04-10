# Configuration Railway

Ce guide explique comment configurer votre application Laravel sur Railway.

## Variables d'Environnement à Configurer

Sur Railway, allez dans **Variables** et configurez les éléments suivants :

### Base de Données

Si vous utilisez Railway Database:
- `DB_CONNECTION=mysql`
- `DB_HOST=${{ MYSQL_HOST }}`
- `DB_PORT=${{ MYSQL_PORT }}`
- `DB_DATABASE=${{ MYSQL_DATABASE }}`
- `DB_USERNAME=${{ MYSQL_USER }}`
- `DB_PASSWORD=${{ MYSQL_PASSWORD }}`

Ou pour PostgreSQL:
- `DB_CONNECTION=pgsql`
- `DB_HOST=${{ DATABASE_URL_PARSED_HOST }}`
- `DB_PORT=${{ DATABASE_URL_PARSED_PORT }}`
- `DB_DATABASE=${{ DATABASE_URL_PARSED_DATABASE }}`
- `DB_USERNAME=${{ DATABASE_URL_PARSED_USER }}`
- `DB_PASSWORD=${{ DATABASE_URL_PARSED_PASSWORD }}`

### Application
- `APP_NAME=RedProduct`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://votre-url-railway.railway.app`

### Génération de clés
- `APP_KEY` sera généré automatiquement par le Dockerfile

### CORS
- `CORS_ALLOWED_ORIGINS=https://votre-vercel-url.vercel.app`

## Étapes de Déploiement

1. Connectez votre repository GitHub à Railway
2. Railway détectera automatiquement le Dockerfile
3. Les variables d'environnement seront appliquées
4. Les migrations et seeders s'exécuteront automatiquement au démarrage
5. L'application sera disponible sur votre URL Railway

## Dépannage

Si les migrations ne fonctionnent pas :
1. Allez dans les logs de Railway
2. Vérifiez que la base de données est correctement configurée
3. Exécutez manuellement les migrations via le Railway CLI
