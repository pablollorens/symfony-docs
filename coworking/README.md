# CoWorking Space — Booking System

MVP para gestión de reservas de salas de reuniones. Symfony 7.x + Twig + CSS nativo.

## Instalación

```bash
cd coworking
composer install
```

Copia y configura el entorno:

```bash
cp .env .env.local
# Edita DATABASE_URL en .env.local
```

Crea la base de datos y ejecuta migraciones:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Carga fixtures (datos de prueba):

```bash
php bin/console doctrine:fixtures:load
```

Inicia el servidor:

```bash
symfony server:start
# o: php -S localhost:8000 -t public/
```

Accede en `http://localhost:8000`.

**Credenciales de prueba:**
- Admin: `admin@coworking.com` / `admin123`
- Usuario: `user@coworking.com` / `user123`

## Tests

```bash
php bin/phpunit
```

## Decisiones arquitectónicas

- **Entidades**: `User`, `Room`, `Booking`. Relaciones ManyToOne entre Booking→Room y Booking→User.
- **BookingService**: encapsula toda la lógica de negocio (validación de duración, detección de solapamiento, cálculo de coste).
- **BookingRepository**: consulta SQL de solapamiento (`startAt < :endAt AND endAt > :startAt`) para detectar conflictos de horario.
- **Seguridad**: roles `ROLE_USER` y `ROLE_ADMIN`. El backoffice de salas está protegido con `ROLE_ADMIN`.
- **CSS nativo**: variables CSS, Grid y Flexbox. Sin dependencias de frontend, sin build step.
- **AssetMapper**: no utilizado en este MVP dado que no hay assets JS complejos.
