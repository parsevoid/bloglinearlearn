# LinearLearn Blog — API Documentation

> Complete guide to creating, reading, updating, and deleting blog posts programmatically.

---

## Base URL

```
http://localhost:8080/api/
```

Replace `localhost:8080` with your actual domain in production.

---

## Authentication

Every API request requires authentication via **one** of these methods:

| Method | Header / Parameter | Example |
|---|---|---|
| **API Key Header** (recommended) | `X-API-Key: <key>` | `X-API-Key: linearlearn_secret_key_2026` |
| **Bearer Token** | `Authorization: Bearer <key>` | `Authorization: Bearer linearlearn_secret_key_2026` |
| **Basic Auth** | Standard HTTP Basic Auth | Username + password of an admin user |
| **Query Parameter** | `?api_key=<key>` | `?api_key=linearlearn_secret_key_2026` |

Your default API key is configured in `config/database.php` as the `API_KEY` constant, or via the `API_KEY` environment variable.

### Unauthorized Response

```json
{
  "success": false,
  "error": "Unauthorized",
  "message": "Valid API Key or Admin credentials required. Provide via header \"X-API-Key: <key>\", \"Authorization: Bearer <key>\", or Basic Auth."
}
```

**HTTP Status:** `401 Unauthorized`

---

## Endpoints

### 1. Create a Post

**`POST /api/posts.php`**

Creates a new blog post. Supports both JSON body and multipart form data (for file uploads).

#### Request Headers

```
Content-Type: application/json
X-API-Key: <your-api-key>
```

#### JSON Body Parameters

| Field | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | **Yes** | — | Post title |
| `slug` | string | No | Auto-generated from title | URL-friendly slug |
| `content` | string | No | Auto-built from sections | Full HTML content |
| `excerpt` | string | No | First 140 chars of section text | Short summary |
| `author` | string | No | `"LinearLearn Editorial"` | Author name |
| `category_id` | int | No | `null` | Category ID (1–8) |
| `category` | string | No | — | Category name (alternative to `category_id`) |
| `featured_image` | string | No | First section image | Path to featured image |
| `read_time` | int | No | Auto-calculated | Estimated reading time in minutes |
| `is_featured` | bool | No | `false` | Whether post is the featured/hero post |
| `status` | string | No | `"published"` | `"published"` or `"draft"` |
| `sections` | array | No | `[]` | Array of narrative section objects |

#### Section Object

Each section represents an image + text block in the article:

| Field | Type | Description |
|---|---|---|
| `image` | string | URL or relative path to the section image |
| `caption` | string | Image caption (e.g. "Figure 1 — Neural pathways") |
| `title` | string | Section heading |
| `text` | string | Section body text (plain text or HTML) |

#### Available Categories

| ID | Name | Slug |
|---|---|---|
| 1 | Brain Health | `brain-health` |
| 2 | Memory | `memory` |
| 3 | Focus | `focus` |
| 4 | Learning | `learning` |
| 5 | Sleep | `sleep` |
| 6 | Mindfulness | `mindfulness` |
| 7 | Habits | `habits` |
| 8 | Neuroscience | `neuroscience` |

#### Example: Create Post with JSON

```bash
curl -X POST http://localhost:8080/api/posts.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Mapping Cognitive Reserve",
    "excerpt": "A step-by-step visual exploration of neuroplasticity.",
    "author": "Dr. Elena Vance",
    "category": "Brain Health",
    "status": "published",
    "is_featured": false,
    "sections": [
      {
        "image": "uploads/my-uploaded-image.jpg",
        "caption": "Figure 1 — Synaptic reorganization during learning",
        "title": "Step 1: Synaptic Reorganization",
        "text": "Every new skill reshapes dendritic spines in the cortex, establishing resilient collateral networks."
      },
      {
        "image": "uploads/another-image.jpg",
        "caption": "Figure 2 — Prefrontal attentional gating",
        "title": "Step 2: Deep Attentional Immersion",
        "text": "Sustained single-task focus modulates acetylcholine and noradrenaline to prevent synaptic noise."
      }
    ]
  }'
```

#### Example: Create Post with Direct HTML Content

```bash
curl -X POST http://localhost:8080/api/posts.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Understanding Sleep Cycles",
    "content": "<h2>The Architecture of Sleep</h2><p>Sleep consists of multiple 90-minute cycles, each containing distinct stages...</p>",
    "excerpt": "How sleep cycles shape memory consolidation and brain recovery.",
    "author": "Dr. Sarah Mitchell",
    "category_id": 5,
    "status": "published"
  }'
```

#### Example: Create Post with Image Upload (Multipart)

```bash
curl -X POST http://localhost:8080/api/posts.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -F "title=Sleep Architecture Study" \
  -F "excerpt=Visual guide to sleep stages" \
  -F "author=Dr. Sarah Mitchell" \
  -F "category_id=5" \
  -F "status=published" \
  -F "featured_image=@/path/to/cover-photo.jpg" \
  -F 'sections=[{"title":"Stage 1","text":"Light sleep begins..."},{"title":"Stage 2","text":"Deeper consolidation..."}]' \
  -F "section_image_0=@/path/to/stage1-diagram.jpg" \
  -F "section_image_1=@/path/to/stage2-diagram.jpg"
```

> **Note:** Section images are uploaded as `section_image_0`, `section_image_1`, etc. matching the index of each section in the `sections` array.

#### Success Response — `201 Created`

```json
{
  "success": true,
  "message": "Post created successfully in [Image -> Text] narrative format",
  "post_id": 18,
  "title": "Mapping Cognitive Reserve",
  "slug": "mapping-cognitive-reserve",
  "url": "http://localhost:8080/post.php?slug=mapping-cognitive-reserve",
  "featured_image": "uploads/my-uploaded-image.jpg",
  "sections_count": 2,
  "status": "published"
}
```

#### Error Responses

| Status | Condition | Body |
|---|---|---|
| `400` | Missing title | `{"success": false, "error": "Title is required"}` |
| `500` | Database failure | `{"success": false, "error": "Failed to create post in database"}` |

---

### 2. List Published Posts

**`GET /api/posts.php`**

Returns all published posts, ordered by most recent.

#### Query Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `limit` | int | `20` | Max posts to return (1–100) |

#### Example

```bash
curl -H "X-API-Key: linearlearn_secret_key_2026" \
  "http://localhost:8080/api/posts.php?limit=10"
```

#### Response — `200 OK`

```json
{
  "success": true,
  "count": 4,
  "posts": [
    {
      "id": 1,
      "title": "A Healthier Brain for a Brighter You",
      "slug": "a-healthier-brain-for-a-brighter-you",
      "excerpt": "Practical science-backed ways to improve memory...",
      "author": "Dr. Elena Vance",
      "category_name": "Brain Health",
      "featured_image": "assets/images/featured-brain-art.jpg",
      "read_time": 7,
      "views": 2450,
      "status": "published",
      "sections": [],
      "created_at": "2026-10-01 12:00:00"
    }
  ]
}
```

---

### 3. Get a Single Post

**`GET /api/posts.php?slug=<slug>`** or **`GET /api/posts.php?id=<id>`**

#### Example by Slug

```bash
curl -H "X-API-Key: linearlearn_secret_key_2026" \
  "http://localhost:8080/api/posts.php?slug=a-healthier-brain-for-a-brighter-you"
```

#### Example by ID

```bash
curl -H "X-API-Key: linearlearn_secret_key_2026" \
  "http://localhost:8080/api/posts.php?id=1"
```

#### Response — `200 OK`

```json
{
  "success": true,
  "post": {
    "id": 1,
    "title": "A Healthier Brain for a Brighter You",
    "slug": "a-healthier-brain-for-a-brighter-you",
    "content": "<p>The human brain is remarkably adaptable...</p>",
    "excerpt": "Practical science-backed ways...",
    "sections": [
      {
        "image": "uploads/diagram.jpg",
        "caption": "Figure 1",
        "title": "Section Title",
        "text": "Section body text"
      }
    ]
  }
}
```

#### Error — `404 Not Found`

```json
{
  "success": false,
  "error": "Post not found"
}
```

---

### 4. Update a Post

**`PUT /api/posts.php`** or **`PATCH /api/posts.php`**

Updates an existing post. Only the fields you include in the request body will be changed; all others retain their existing values.

#### JSON Body Parameters

Same as **Create a Post**, plus:

| Field | Type | Required | Description |
|---|---|---|---|
| `id` | int | **Yes** | The post ID to update (can also be passed as `?id=<id>`) |

#### Example

```bash
curl -X PUT http://localhost:8080/api/posts.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "title": "Updated: A Healthier Brain for a Brighter You",
    "status": "published",
    "excerpt": "Updated excerpt with new insights on neuroplasticity."
  }'
```

#### Success Response — `200 OK`

```json
{
  "success": true,
  "message": "Post updated successfully",
  "post_id": 1,
  "title": "Updated: A Healthier Brain for a Brighter You",
  "slug": "updated-a-healthier-brain-for-a-brighter-you",
  "url": "http://localhost:8080/post.php?slug=updated-a-healthier-brain-for-a-brighter-you",
  "status": "published"
}
```

---

### 5. Delete a Post

**`DELETE /api/posts.php?id=<id>`**

Permanently deletes a post.

#### Example

```bash
curl -X DELETE \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  "http://localhost:8080/api/posts.php?id=18"
```

#### Success Response — `200 OK`

```json
{
  "success": true,
  "message": "Post deleted successfully",
  "post_id": 18,
  "title": "Mapping Cognitive Reserve"
}
```

---

### 6. Upload an Image

**`POST /api/upload.php`**

Uploads an image file and returns its URL for use in post content or featured images.

#### Allowed File Types

`image/jpeg`, `image/png`, `image/webp`, `image/gif`, `image/svg+xml`

#### Automatic Image Compression & Optimization

Every uploaded image is automatically processed by `compressAndSaveImage()`:
- **Auto-Resize:** Images exceeding 1600×1600px are scaled down proportionally to preserve bandwidth and performance.
- **JPEG Optimization:** Auto-rotates orientation using EXIF data and re-encodes at 82% quality (yielding ~60–80% size savings).
- **PNG Optimization:** Full alpha channel transparency is preserved with high-level compression (level 8).
- **WebP Optimization:** Re-encodes with lossless alpha transparency preservation at 82% quality.
- **GIF / SVG:** Bypasses rasterization to preserve animation and vector scalability.
- **Configurable via `.env`:** You can customize `IMAGE_MAX_WIDTH`, `IMAGE_MAX_HEIGHT`, and `IMAGE_QUALITY` directly in `.env`.

#### Example

```bash
curl -X POST http://localhost:8080/api/upload.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -F "image=@/path/to/my-diagram.jpg"
```

> **Note:** The file field accepts either `image` or `file` as the field name.

#### Success Response — `200 OK`

```json
{
  "success": true,
  "url": "uploads/my-diagram-1726857600-412.jpg",
  "full_url": "http://localhost:8080/uploads/my-diagram-1726857600-412.jpg",
  "filename": "my-diagram-1726857600-412.jpg",
  "size": 125430
}
```

---

## Complete Workflow: Upload Image → Create Post

A two-step workflow to create a post with a custom uploaded image:

### Step 1: Upload the image

```bash
IMAGE_RESPONSE=$(curl -s -X POST http://localhost:8080/api/upload.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -F "image=@/path/to/brain-scan.jpg")

IMAGE_URL=$(echo "$IMAGE_RESPONSE" | python3 -c "import sys,json; print(json.load(sys.stdin)['url'])")
echo "Uploaded: $IMAGE_URL"
```

### Step 2: Create the post using the uploaded image URL

```bash
curl -X POST http://localhost:8080/api/posts.php \
  -H "X-API-Key: linearlearn_secret_key_2026" \
  -H "Content-Type: application/json" \
  -d "{
    \"title\": \"Brain Scan Analysis Results\",
    \"excerpt\": \"Visual breakdown of fMRI data.\",
    \"author\": \"Dr. Elena Vance\",
    \"category\": \"Neuroscience\",
    \"status\": \"published\",
    \"featured_image\": \"$IMAGE_URL\",
    \"sections\": [
      {
        \"image\": \"$IMAGE_URL\",
        \"caption\": \"fMRI scan showing prefrontal activation\",
        \"title\": \"Prefrontal Cortex Activity\",
        \"text\": \"The scan reveals heightened activation in the dorsolateral prefrontal cortex during focused attention tasks.\"
      }
    ]
  }"
```

---

## Python Example

```python
import requests

API_URL = "http://localhost:8080/api/posts.php"
UPLOAD_URL = "http://localhost:8080/api/upload.php"
API_KEY = "linearlearn_secret_key_2026"

headers = {
    "X-API-Key": API_KEY,
    "Content-Type": "application/json"
}


# --- Create a post ---
payload = {
    "title": "Automated Neural Study",
    "excerpt": "Visual summary generated by analysis pipeline.",
    "author": "Dr. Elena Vance",
    "category": "Brain Health",
    "status": "published",
    "sections": [
        {
            "image": "assets/images/thumb-memory.jpg",
            "caption": "Step 1: Stimulus Encoding",
            "title": "Encoding Phase",
            "text": "First paragraph explaining the data visualization shown above."
        },
        {
            "image": "assets/images/thumb-focus.jpg",
            "caption": "Step 2: Circuit Modulation",
            "title": "Modulation Phase",
            "text": "Second paragraph explaining the attentional modulation."
        }
    ]
}

response = requests.post(API_URL, json=payload, headers=headers)
result = response.json()
print(f"Created Post #{result['post_id']}: {result['title']}")
print(f"View at: {result['url']}")


# --- Upload an image first, then create post ---
with open("/path/to/image.jpg", "rb") as f:
    upload_resp = requests.post(
        UPLOAD_URL,
        headers={"X-API-Key": API_KEY},
        files={"image": f}
    )

image_url = upload_resp.json()["url"]

payload_with_upload = {
    "title": "Post With Custom Image",
    "featured_image": image_url,
    "content": "<p>Article content here...</p>",
    "status": "published"
}

response = requests.post(API_URL, json=payload_with_upload, headers=headers)
print("Created:", response.json())


# --- Update a post ---
update_payload = {
    "id": result["post_id"],
    "title": "Updated: Automated Neural Study",
    "excerpt": "Revised summary with latest data."
}

response = requests.put(API_URL, json=update_payload, headers=headers)
print("Updated:", response.json())


# --- Delete a post ---
response = requests.delete(
    f"{API_URL}?id={result['post_id']}",
    headers={"X-API-Key": API_KEY}
)
print("Deleted:", response.json())
```

---

## JavaScript (Node.js) Example

```javascript
const API_URL = "http://localhost:8080/api/posts.php";
const API_KEY = "linearlearn_secret_key_2026";

// Create a post
const response = await fetch(API_URL, {
  method: "POST",
  headers: {
    "X-API-Key": API_KEY,
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    title: "Focus and Flow States",
    excerpt: "Deep dive into achieving flow.",
    author: "Julian Hayes",
    category: "Focus",
    status: "published",
    sections: [
      {
        title: "What is Flow?",
        text: "Flow is a state of complete immersion in a task...",
      },
    ],
  }),
});

const result = await response.json();
console.log("Created:", result);
```

---

## Error Reference

| HTTP Status | Meaning |
|---|---|
| `200` | Success |
| `201` | Post created successfully |
| `400` | Bad request (missing required fields) |
| `401` | Unauthorized (invalid or missing API key) |
| `404` | Post not found |
| `405` | HTTP method not allowed |
| `500` | Server error (database failure) |

---

## Rate Limits

No rate limits are enforced by default. For production deployments, consider adding rate limiting at the web server (nginx/Apache) level.

---

## Notes

- **Slug auto-generation:** If no `slug` is provided, one is automatically generated from the title (lowercased, spaces to hyphens, special characters removed).
- **Read time:** If no `read_time` is provided, it's auto-calculated at ~200 words per minute from the content.
- **Content vs Sections:** You can provide either `content` (raw HTML) or `sections` (structured data). If only sections are provided, HTML content is auto-generated from them in a narrative image→text layout.
- **Image paths:** Image paths should be relative to the site root (e.g. `uploads/my-image.jpg`). Upload images first via `/api/upload.php` to get valid paths.
