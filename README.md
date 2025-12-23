# Events Hub

Full-stack event marketplace built with Laravel 12, React, and Lighthouse GraphQL. Customers can register, browse upcoming events, book tickets, and manage their bookings. Admins review and approve events, manage types/locations, and oversee ticketing from a dashboard.

## Features
- Event catalog with search, type/location filters, and monthly views.
- Customer registration/login (web and JWT), ticket booking/cancelation, and QR ticket viewer.
- Admin dashboard for creating, approving/rejecting, and canceling events; manage event types/locations.
- GraphQL API (Lighthouse) powering the React frontend with queries for popular, upcoming, paginated events.
- Email notifications for approvals and cancellations; queue-ready jobs for outbound mail.

## Tech Stack
- Backend: Laravel 12 (Sanctum auth), Lighthouse GraphQL, Eloquent ORM.
- Frontend: React 19 + Vite + Apollo Client.
- Styling: TailwindCSS + Blade layouts for dashboard/auth flows.
- Database: MySQL/PostgreSQL/SQLite (via Eloquent and migrations).

## Screenshots
Homepage showcasing featured events and quick access to the catalog.  
![Homepage](screenshots/homepage.png)

Event listing with filters to browse upcoming and popular events.  
![Event List](screenshots/event-list.png)

Event detail page with description, schedule, and booking actions.  
![Event Detail](screenshots/event-detail.png)

Latest additions highlighted in the new events showcase.  
![New Events](screenshots/new-events.png)

Personal calendar view summarizing a user's booked events.  
![My Event Calendar](screenshots/my-event-calendar.png)

Digital ticket view with scannable QR code for entry.  
![QR Ticket](screenshots/qr-ticket.png)

On-site scanner interface for validating QR codes at check-in.  
![Scan QR Code](screenshots/scan-qr-code.png)

Internal messaging panel for staff communications.  
![Internal Message](screenshots/internal-message.png)

Login form for customer and admin access.  
![Login](screenshots/login.png)

Admin dashboard overview of events, approvals, and metrics.  
![Admin Dashboard](screenshots/admin-dashboard.png)

Admin event list with status indicators and management actions.  
![Admin Event List](screenshots/admin-event-list.png)

Admin form for creating a new event with required details.  
![Admin Create New Event](screenshots/admin-create-new-event.png)

Admin configuration view for managing event types and locations.  
![Admin Config](screenshots/admin-config.png)

## Getting Started
Prerequisites: PHP 8.2+, Composer, Node 20+, npm, and a database (SQLite works for local).

1) Install dependencies  
```bash
composer install
npm install
```

2) Configure environment  
```bash
cp .env.example .env
php artisan key:generate
```
Update DB credentials in `.env`. For SQLite, create `database/database.sqlite` and set `DB_CONNECTION=sqlite`.

3) Run migrations  
```bash
php artisan migrate
```

4) Run the app (backend + Vite)  
```bash
php artisan serve
npm run dev
# or run everything together:
composer run dev
```

## GraphQL
- Endpoint: `/graphql`
- Schema and resolvers: `graphql/` and `app/GraphQL/*`
- Example queries: `EventQuery` (popular, paginated, upcoming), `CustomerEventQuery`, `TicketQuery`.
Use any GraphQL client or Apollo devtools; the React app mounts at `/` and `/event/ticket/...`.

## Testing
```bash
php artisan test
```

## Useful Commands
- Clear/refresh config: `php artisan config:clear`
- Queue worker (for mail jobs): `php artisan queue:listen`
- Build assets for production: `npm run build`
