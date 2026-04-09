<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Collaborative task board (Laravel Reverb)

This app includes a shared Kanban-style board on `/dashboard` (after login). Card changes are broadcast over **Laravel Reverb** so other connected users see updates immediately.

### Prerequisites

From the project root:

```bash
composer install
npm install
cp .env.example .env   # if you do not already have .env
php artisan key:generate
php artisan migrate
php artisan db:seed    # optional: demo users + default board/columns
```

Ensure PHP has the **PDO SQLite** driver enabled if you use the default `DB_CONNECTION=sqlite` setup. Broadcasting must use Reverb (see `BROADCAST_CONNECTION=reverb` and `REVERB_*` in `.env`).

### Run locally (three terminals)

Use three terminals in the project directory, or run the processes you prefer in the background.

1. **HTTP app**

   ```bash
   php artisan serve
   ```

   Open `http://127.0.0.1:8000` (or the URL Artisan prints).

2. **Reverb WebSocket server**

   ```bash
   php artisan reverb:start
   ```

   Must match `REVERB_HOST`, `REVERB_PORT`, and `REVERB_SCHEME` in `.env` (defaults in `.env.example` use `127.0.0.1:8080` and `http`).

3. **Vite (frontend assets + Echo)**

   ```bash
   npm run dev
   ```

Then register or log in, open **Dashboard** (`/dashboard`), and keep Reverb running while you use the board.

### Two-browser manual realtime checklist

1. **Browser A** — Log in as user A, go to `/dashboard`.
2. **Browser B** (private/incognito or another profile) — Log in as user B, go to `/dashboard`.
3. In **A**, add a card — it should appear in **B** without refresh.
4. In **A**, edit the card title/description — **B** should update.
5. In **A**, move the card (e.g. Move left / Move right) — **B** should show the new column.
6. In **A**, delete the card — it should disappear in **B**.

If updates do not propagate, confirm Reverb is running, `BROADCAST_CONNECTION=reverb`, and browser devtools show no WebSocket connection errors.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
