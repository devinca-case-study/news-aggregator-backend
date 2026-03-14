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
- Queue-based job processing
- Swagger API documentation

---

# System Architecture

The application uses a layered architecture to separate responsibilities.

- Controllers
  Handle HTTP requests and responses.

- Services
  Contain business logic.

- Repositories
  Handle database access.

- DTO Layer
  Normalize external provider responses.

# News Fetching Strategy

News ingestion is handled via scheduled background jobs.

Each provider has its own fetch rotation strategy designed
to stay within the free API rate limits.

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

# Installation

1. Clone repository
   git clone https://github.com/devincalmt/news-aggregator-backend
   cd news-aggregator-backend

2. Setup environment
   cp .env.example .env
   Edit database credentials if necessary.

3. Start Docker
   docker compose up -d --build

4. Install dependencies
   docker compose exec app composer install

5. Generate application key
   docker compose exec app php artisan key:generate

6. Run migration and seed
   docker compose exec app php artisan migrate --seed

# API Documentation

Swagger documentation is available at:

http://localhost:8000/api/documentation

# Scheduler

The scheduler is responsible for dispatching news fetching jobs.

php artisan schedule:work

# Queue Worker

Background jobs are processed using Redis queues.

php artisan queue:work

# Design Decisions

- Repository Pattern
  Used to isolate database logic from services and keep business logic independent from persistence.

- DTO Layer
  Used to normalize data fetched from external APIs into a consistent internal structure.

- Provider Abstraction
  Each news provider implements a shared contract, allowing easy addition of new providers.

- Redis Queue
  External API ingestion is handled via background jobs to avoid blocking application requests.

# Scalability Considerations

- Provider abstraction
  New providers can be added with minimal changes by implementing AbstractNewsProvider.

- Rate limit awareness
  The system intentionally consumes only ~50–60% of provider API limits to avoid rate limiting.

- Database normalization
  Authors, sources, and categories are stored separately to reduce duplication and improve query performance.
