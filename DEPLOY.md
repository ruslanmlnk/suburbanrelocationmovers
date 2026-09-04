# Dokploy deployment

1. Push this directory to a Git repository.
2. In Dokploy create a **Compose** application and select that repository.
3. Add the following environment variables:
   - `WORDPRESS_URL=https://your-domain.com`
   - `DB_NAME=suburbanrelocation`
   - `DB_USER=suburbanrelocation`
   - `DB_PASSWORD` with a long random value
   - `DB_ROOT_PASSWORD` with a different long random value
4. Set the WordPress service's internal port to `80` and attach the domain in Dokploy.
5. Deploy. The included database dump is imported only when the database volume is empty.

If Dokploy has its own HTTP health check enabled, use `/wp-includes/images/blank.gif` instead of `/wp-login.php`. The static path verifies Apache without failing while MariaDB is still starting.

Keep both named volumes during updates. Removing `database_data` deletes the WordPress database; removing `wordpress_uploads` deletes uploaded media added after deployment.
