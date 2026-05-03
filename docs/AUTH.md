Authentication (Sanctum) — usage
===============================

1. Login to obtain token

POST /api/login

Body (json):
{
  "email": "user@example.com",
  "password": "secret"
}

Response contains `token` (plain-text). Use it in `Authorization` header:

Example curl:

```bash
curl -X POST "http://localhost:8000/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"secret"}'
```

Use token when calling protected endpoints:

```bash
curl "http://localhost:8000/api/menus" \
  -H "Authorization: Bearer <token>" \
  -H "Accept: application/json"
```

Protecting routes

- `auth:sanctum` middleware protects authentication; add it to route groups.
- For role-based protection, use the `role` middleware added to `app/Http/Kernel.php`.
  Example: `->middleware(['auth:sanctum','role:admin,owner'])`

Notes
- Ensure `Laravel\Sanctum` is installed and `Sanctum` service provider configured.
- Run migrations after adding the `role` column migration:

```bash
php artisan migrate
```
