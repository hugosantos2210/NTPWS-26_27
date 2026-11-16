# PawSafe REST API — Documentation

**Base URL:** `/api/v1`  
**Format:** All requests and responses use `application/json`.

---

## Authentication

Protected endpoints (`POST`, `PATCH`, `DELETE`) require authentication and an authorized role (`editor` or `admin`).

Authentication can be performed via:
1. **Session / Cookie:** Standard authenticated session (when logged in through the web interface or frontend application).
2. **Bearer Token / API Guard:** Via `Authorization: Bearer {token}` header.

If an unauthenticated request is made to a protected endpoint, the API returns `401 Unauthorized`. If an authenticated user lacks permissions (e.g. regular `user`), the API returns `403 Forbidden`.

---

## Endpoints

### 1. `GET /api/v1/news` — List Published News

Returns a paginated list of published news articles. Public endpoint — no authentication required.

**Query Parameters**

| Parameter     | Type    | Required | Description                                |
|---------------|---------|----------|--------------------------------------------|
| `search`      | string  | No       | Full-text search on `title` and `summary`  |
| `category_id` | integer | No       | Filter by category ID                      |
| `per_page`    | integer | No       | Items per page (default: 10, max: 50)      |
| `page`        | integer | No       | Page number (default: 1)                   |

**Permissions:** Public

**Example Request**

```bash
curl -X GET "https://example.com/api/v1/news?search=adoption&category_id=2&per_page=5&page=1" \
  -H "Accept: application/json"
```

**Example Response — 200 OK**

```json
{
  "success": true,
  "data": [
    {
      "id": 12,
      "title": "Adoption Stories from 2025",
      "slug": "adoption-stories-from-2025-abc12",
      "summary": "Heartwarming adoption stories from our shelter.",
      "content": "<p>Full article content...</p>",
      "category_id": 2,
      "author_id": 3,
      "image_path": null,
      "status": "published",
      "published_at": "2025-11-01T10:00:00.000000Z",
      "created_at": "2025-10-28T08:30:00.000000Z",
      "updated_at": "2025-11-01T10:00:00.000000Z",
      "category": { "id": 2, "name": "Adoption Stories", "slug": "adoption-stories" },
      "author": { "id": 3, "name": "Editor Jane" }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 5,
    "total": 42,
    "last_page": 9
  }
}
```

**Possible Errors**

| Status | Description             |
|--------|-------------------------|
| 500    | Internal server error   |

---

### 2. `GET /api/v1/news/{id}` — Get Single News Article

Returns a single published news article by its numeric ID. Draft and withdrawn articles return 404.

**Path Parameters**

| Parameter | Type    | Description      |
|-----------|---------|------------------|
| `id`      | integer | News article ID  |

**Permissions:** Public

**Example Request**

```bash
curl -X GET "https://example.com/api/v1/news/12" \
  -H "Accept: application/json"
```

**Example Response — 200 OK**

```json
{
  "success": true,
  "data": {
    "id": 12,
    "title": "Adoption Stories from 2025",
    "slug": "adoption-stories-from-2025-abc12",
    "summary": "Heartwarming adoption stories from our shelter.",
    "content": "<p>Full article content...</p>",
    "category_id": 2,
    "author_id": 3,
    "image_path": null,
    "status": "published",
    "published_at": "2025-11-01T10:00:00.000000Z",
    "created_at": "2025-10-28T08:30:00.000000Z",
    "updated_at": "2025-11-01T10:00:00.000000Z",
    "category": { "id": 2, "name": "Adoption Stories", "slug": "adoption-stories" },
    "author": { "id": 3, "name": "Editor Jane" }
  }
}
```

**Possible Errors**

| Status | Description                              |
|--------|------------------------------------------|
| 404    | Article not found or not published       |

---

### 3. `POST /api/v1/news` — Create News Article

Creates a new news article. Requires authentication as `editor` or `admin`.

**Permissions:** `editor`, `admin`

**Request Body** (`application/json`)

| Field         | Type    | Required | Description                                        |
|---------------|---------|----------|----------------------------------------------------|
| `title`       | string  | Yes      | Article title (max 255 chars)                      |
| `summary`     | string  | Yes      | Short summary (max 500 chars)                      |
| `content`     | string  | Yes      | Full HTML content                                  |
| `category_id` | integer | Yes      | ID of an existing category                         |
| `status`      | string  | No       | `draft` (default) or `published`                   |

**Example Request**

```bash
curl -X POST "https://example.com/api/v1/news" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "title": "New Shelter Wing Opens",
    "summary": "Our shelter has opened a new wing for cats.",
    "content": "<p>We are excited to announce...</p>",
    "category_id": 1,
    "status": "published"
  }'
```

**Example Response — 201 Created**

```json
{
  "success": true,
  "message": "News article created successfully.",
  "data": {
    "id": 55,
    "title": "New Shelter Wing Opens",
    "slug": "new-shelter-wing-opens-xr4k2",
    "summary": "Our shelter has opened a new wing for cats.",
    "content": "<p>We are excited to announce...</p>",
    "category_id": 1,
    "author_id": 3,
    "status": "published",
    "published_at": "2025-11-16T12:00:00.000000Z",
    "created_at": "2025-11-16T12:00:00.000000Z",
    "updated_at": "2025-11-16T12:00:00.000000Z",
    "category": { "id": 1, "name": "Shelter News", "slug": "shelter-news" },
    "author": { "id": 3, "name": "Editor Jane" }
  }
}
```

**Possible Errors**

| Status | Description                                           |
|--------|-------------------------------------------------------|
| 400    | Validation failed — missing or invalid fields         |
| 401    | Unauthenticated — missing or invalid token            |
| 403    | Forbidden — authenticated user lacks required role    |

---

### 4. `PATCH /api/v1/news/{id}` — Update News Article

Partially updates an existing news article. Editors may only update their own articles; admins can update any.

**Path Parameters**

| Parameter | Type    | Description      |
|-----------|---------|------------------|
| `id`      | integer | News article ID  |

**Permissions:** `editor` (own articles only), `admin`

**Request Body** (`application/json`) — all fields optional

| Field         | Type    | Description                                      |
|---------------|---------|--------------------------------------------------|
| `title`       | string  | New title (max 255 chars)                        |
| `summary`     | string  | New summary (max 500 chars)                      |
| `content`     | string  | New HTML content                                 |
| `category_id` | integer | ID of an existing category                       |
| `status`      | string  | `draft`, `published`, or `withdrawn`             |

**Example Request**

```bash
curl -X PATCH "https://example.com/api/v1/news/55" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "status": "withdrawn"
  }'
```

**Example Response — 200 OK**

```json
{
  "success": true,
  "message": "News article updated successfully.",
  "data": {
    "id": 55,
    "title": "New Shelter Wing Opens",
    "status": "withdrawn",
    "updated_at": "2025-11-17T09:30:00.000000Z"
  }
}
```

**Possible Errors**

| Status | Description                                                       |
|--------|-------------------------------------------------------------------|
| 400    | Validation failed                                                 |
| 401    | Unauthenticated                                                   |
| 403    | Forbidden — editor attempting to edit another editor's article    |
| 404    | Article not found                                                 |

---

### 5. `DELETE /api/v1/news/{id}` — Delete News Article

Permanently deletes a news article. No response body on success.

**Path Parameters**

| Parameter | Type    | Description      |
|-----------|---------|------------------|
| `id`      | integer | News article ID  |

**Permissions:** `editor` (own articles only), `admin`

**Example Request**

```bash
curl -X DELETE "https://example.com/api/v1/news/55" \
  -H "Authorization: Bearer {token}"
```

**Example Response — 204 No Content**

_(empty body)_

**Possible Errors**

| Status | Description                                                       |
|--------|-------------------------------------------------------------------|
| 401    | Unauthenticated                                                   |
| 403    | Forbidden — editor attempting to delete another editor's article  |
| 404    | Article not found                                                 |

---

### 6. `GET /api/v1/categories` — List Categories

Returns all categories with the count of their published articles. Public endpoint.

**Permissions:** Public

**Example Request**

```bash
curl -X GET "https://example.com/api/v1/categories" \
  -H "Accept: application/json"
```

**Example Response — 200 OK**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Adoption Stories",
      "slug": "adoption-stories",
      "description": "Stories about successful adoptions.",
      "news_count": 14,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-11-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Shelter News",
      "slug": "shelter-news",
      "description": "Updates from the shelter.",
      "news_count": 7,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-10-15T00:00:00.000000Z"
    }
  ]
}
```

**Possible Errors**

| Status | Description           |
|--------|-----------------------|
| 500    | Internal server error |

---

### 7. `GET /api/v1/schema` — JSON Schema

Returns the JSON Schema for a PawSafe news article. Useful for validating POST/PATCH request bodies.

**Permissions:** Public

**Example Request**

```bash
curl -X GET "https://example.com/api/v1/schema" \
  -H "Accept: application/schema+json"
```

**Example Response — 200 OK**

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "$id": "https://pawsafe.example.com/schemas/news.json",
  "title": "PawSafe News Article",
  "type": "object",
  "required": ["title", "summary", "content", "category_id"],
  "properties": {
    "title":       { "type": "string", "maxLength": 255 },
    "summary":     { "type": "string", "maxLength": 500 },
    "content":     { "type": "string" },
    "category_id": { "type": "integer" },
    "status":      { "type": "string", "enum": ["draft", "published"] }
  }
}
```

**Possible Errors**

| Status | Description                        |
|--------|------------------------------------|
| 500    | Schema file missing or unreadable  |

---

## Error Response Format

All error responses follow this structure:

```json
{
  "success": false,
  "error": "Human-readable error message.",
  "details": { }
}
```

The `details` field is present on validation errors (400) and contains field-level messages.

---

## Role Reference

| Role     | Public API | POST/PATCH/DELETE own | PATCH/DELETE any |
|----------|------------|----------------------|------------------|
| visitor  | ✅         | ❌                   | ❌               |
| user     | ✅         | ❌                   | ❌               |
| editor   | ✅         | ✅                   | ❌               |
| admin    | ✅         | ✅                   | ✅               |
