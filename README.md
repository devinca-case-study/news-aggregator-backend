# News Aggregator API

A backend service that aggregates news from multiple providers (NewsAPI, Guardian, NYTimes) and exposes a unified API for filtering, searching, and ranking articles based on user preferences.

---

# Tech Stack

- Framework
  Laravel 12

- Database
  MySQL

- Cache / Queue
  Redis

- Queue Management
  Laravel Horizon

- Containerization
  Docker

- API Documentation
  Swagger (L5-Swagger)

- Architecture
  Controller -> Service -> Repository pattern
  DTO for external API normalization
  Provider abstraction for external APIs

---

# Features

- Fetch news from multiple external providers
- Normalize article structure across providers
- Store articles in database
- Filter articles by:
    - search keyword
    - date
    - categories
    - sources
    - authors
- User preference ranking by:
    - sources
    - categories
    - authors
- Scheduled background news ingestion
- Queue-based job processing using Redis
- Queue monitoring via Laravel Horizon
- Swagger API documentation

---

# System Architecture

The application uses a layered architecture to separate responsibilities.

- Controllers
  Handle HTTP requests and responses.

- Services
  Contain business logic and orchestrate data flow between layers.

- Repositories
  Handle database interactions and persistence logic.

- DTO Layer
  Normalize and transform responses from external news providers into a consistent internal structure.

---

# Request Flow

 Client Request
      │
      ▼
  Controller
      │
      ▼
 Service Layer
      │
      ▼
Repository Layer
      │
      ▼
   Database

---

# News Fetching Strategy

News ingestion is handled via scheduled background jobs.

Each provider has its own fetch rotation strategy designed to stay within the free API rate limits.

Provider Usage Overview

1. NewsAPI
   Schedule: every 30 minutes
   Pages per run: 1
   Requests per day: 48
   Daily API usage: ~48% of free limit (100/day)

2. Guardian
   Schedule: every 15 minutes
   Pages per run: 3
   Requests per day: 288
   Daily API usage: ~57.6% of free limit (500/day)

3. NYTimes
   Schedule: every 30 minutes
   Pages per run: 6
   Requests per day: 288
   Daily API usage: ~57.6% of free limit (500/day)

This configuration intentionally uses only ~50–60% of the available API limits to provide buffer space and avoid rate limiting.

---

# Database Structure

Main tables:

- articles
  stored news articles
- categories
  normalized categories
- authors
  article authors
- sources
  news sources
- category_mappings
  provider category mapping
- user_preferences_categories
  store user preference by categories
- user_preferences_sources
  store user preference by sources
- user_preferences_authors
  store user preference by authors

---

# Installation

1. Clone repository

```
git clone https://github.com/devinca-case-study/news-aggregator-backend/
cd news-aggregator-backend
```

2. Setup environment

```
cp .env.example .env
```

Edit database credentials if necessary.

3. Start Docker

```
docker compose up -d --build
```

4. Install dependencies

```
docker compose exec app composer install
```

5. Generate application key

```
docker compose exec app php artisan key:generate
```

6. Run migration and seed

```
docker compose exec app php artisan migrate --seed
```

# API Documentation

Swagger documentation is available at:

http://localhost:8000/api/documentation

# Scheduler

The scheduler dispatches background jobs responsible for fetching news from external providers.

In the Docker environment, the scheduler runs continuously inside the scheduler container using:

```
php artisan schedule:work
```

This process checks scheduled tasks every second and dispatches jobs when their execution time is reached.

---

# Queue Processing

Background jobs are processed using Redis queues and managed by Laravel Horizon.

Horizon runs inside the horizon container and automatically processes queued jobs.

```
php artisan horizon
```

The Horizon dashboard is available at:

http://localhost:8000/horizon

---

# Job Processing Flow

```
Scheduler (schedule:work)
          │
          ▼
   Dispatch Fetch Jobs
          │
          ▼
     Redis Queue
          │
          ▼
    Horizon Workers
          │
          ▼
Fetch News From Providers
          │
          ▼
Normalize + Store Articles
```

# Design Decisions

- Repository Pattern
  Used to isolate database logic from services and keep business logic independent from persistence.

- DTO Layer
  Used to normalize data fetched from external APIs into a consistent internal structure.

- Provider Abstraction
  Each news provider implements a shared contract, allowing easy addition of new providers.

- Redis Queue
  External API ingestion is handled via background jobs to avoid blocking application requests.

---

# Scalability Considerations

- Provider abstraction
  New providers can be added with minimal changes by implementing AbstractNewsProvider.

- Rate limit awareness
  The system intentionally consumes only ~50–60% of provider API limits to avoid rate limiting.

- Database normalization
  Authors, sources, and categories are stored separately to reduce duplication and improve query performance.

---

# Future Improvements

- Admin management for category mappings
  Category mappings are currently defined via seed data. In a production system, this could be managed through an admin interface to allow dynamic updates without requiring application redeployment.
  
# External API Keys

This project fetches news from several external providers.
To run the ingestion jobs, you must obtain API keys from the following services.

--------------------------------------------------

1. NewsAPI

- Visit the website:
  https://newsapi.org/register
- Create a free account.
- After registration, copy your API key from the dashboard.
- Add it to your .env file:

```
NEWSAPI_BASE_URL=https://newsapi.org/v2
NEWSAPI_KEY=your_api_key_here
```

Free tier limit: 100 requests per day

--------------------------------------------------

2. The Guardian Open Platform

- Visit:
  https://open-platform.theguardian.com/access/
- Click "Register developer key" and register.
- Copy your key and add it to .env:

```
GUARDIAN_BASE_URL=https://content.guardianapis.com
GUARDIAN_KEY=your_api_key_here
```

Free tier limit: 500 requests per day

--------------------------------------------------

3. New York Times Developer API

- Visit:
  https://developer.nytimes.com/get-started
- Create an account and create a new app.
- Copy the generated API key and add it to .env:

```
NYTIMES_BASE_URL=https://api.nytimes.com/svc
NYTIMES_KEY=your_api_key_here
```

Free tier limit: 500 requests per day

## Testing

Feature tests are included to verify core API behavior:

- Authentication endpoints (register, login)
- Article filtering by search, category, and source
- User preference ranking for personalized article results

Run the test suite using:

```
php artisan test
```