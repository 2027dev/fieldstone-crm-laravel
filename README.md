# Fieldstone CRM

A sales CRM built with Laravel 13, Blade, Tailwind CSS 4 and Alpine.js. It covers the full
pipeline workflow: capture a lead, qualify it, convert it into a deal, work the deal through
the board, and report on the result.

## Features

| Area | What it does |
| --- | --- |
| **Setup guide** | Onboarding checklist with live progress. Tasks auto-complete as you use the product, and can be skipped manually. |
| **Contacts** | People and organizations with linked deals, activities, notes and email. Includes a month-by-month contacts timeline and a duplicate merge tool. |
| **Activities** | Calls, meetings, tasks, deadlines, emails and lunches. Filter by type and by period (to-do, overdue, today, this week…), toggle completion inline, or switch to a weekly calendar. |
| **Deals** | Drag-and-drop pipeline board across five stages with per-stage totals and weighted values, plus a list view, won/lost tracking with structured lost reasons, notes and activities. |
| **Leads** | A Leads Inbox for unqualified opportunities, with archive and one-click conversion into a deal. Includes a public LeadBooster web form that creates a contact, organization and lead. |
| **Insights** | Scorecards (open pipeline, won revenue, win rate, activities due) plus stage funnel, deal-outcome donut, monthly revenue columns, top accounts and activity mix. |
| **Sales Inbox** | Threaded conversations tied to a contact and deal, with replies, starring and archiving. |
| **Search** | One query across people, organizations, deals, leads and activities. |

## Local development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build       # or: npm run dev
php artisan serve
```

The seeder creates a demo workspace:

- **Email:** `demo@fieldstonecrm.test`
- **Password:** `password`

Registering a new account instead gives you a clean workspace pre-loaded with the small
`[Sample]` dataset the setup guide refers to. "Remove sample data" clears it in one click.

## Tests

```bash
php artisan test      # 15 feature tests
./vendor/bin/pint     # code style
```

## Deploying to Laravel Cloud

The app runs on the standard Laravel Cloud build. Set the deploy command to:

```bash
php artisan migrate --force
```

The default sales pipeline and its five stages are created by a migration, so a freshly
provisioned database is usable immediately. To also load the demo dataset, append
`&& php artisan db:seed --force` — the seeder is idempotent and is a no-op once real
deals exist.

Required environment variables beyond the Laravel Cloud defaults: none. `APP_NAME`
defaults to `Fieldstone CRM`.
