# Blog System Design

**Date:** 2026-07-15  
**Status:** Approved for planning  
**Approach:** Fix & harden existing blog scaffold (Approach 1)

## Goal

Deliver a complete, production-ready blog for Degchi with:

- Admin (User) CRUD for categories and posts in the backend dashboard
- Public blog list/detail on the frontend
- Member-only commenting, nested replies, and like/dislike
- Guests can view posts, comments, and reaction counts but cannot interact

## Auth boundaries (non-negotiable)

| Actor | Access | Blog role |
|-------|--------|-----------|
| **User** (admin) | Backend dashboard only | Creates/edits posts; `author_id` defaults to `Auth::id()`; optional author override from `users` |
| **Member** | Frontend only (existing member dashboard unchanged) | Comment, reply, like/dislike |
| **Guest** | Frontend public pages | View published posts, comments, and reaction counts only |

- Do **not** remove or replace the Member system.
- Members must **not** gain admin dashboard access.
- Admin Users commenting on the frontend requires a Member account; User auth alone is insufficient.

## Architecture overview

```
Admin User ──► Backend Controllers ──► posts / blog_categories / comments (moderation)
Member     ──► Frontend BlogController ──► comments + comment_reactions
Guest      ──► Frontend BlogController ──► read-only published posts + comment display
```

Reuse existing project patterns:

- Backend: DataTables + modals (Branch-style), Quill rich text (About-style)
- Frontend: `resources/views/frontend/layout.blade.php` navbar
- Auth: `Auth::user()` for admin; `Auth::guard('member')` for members

## Data model

### Schema strategy

Blog migrations already ran. Per decision: **edit the original create migrations**, then run `migrate:fresh` (local/dev data wipe acceptable).

### Tables

#### `blog_categories`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string(150) | required |
| slug | string(150) unique | auto from name, editable |
| is_active | boolean default true | status |
| timestamps | | |

#### `posts`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| author_id | FK → users, nullable, set null on delete | admin User who authored |
| blog_category_id | FK → blog_categories, nullable, set null on delete | |
| title | string(255) | |
| slug | string(255) unique | |
| content | longText | rich HTML from Quill |
| image | string nullable | path on `public` disk under `posts/` |
| is_active | boolean default true | published (`true`) / draft (`false`) |
| comments_enabled | boolean default true | per-post comment toggle |
| view_count | bigInteger default 0 | |
| timestamps | | |

**Removed from current scaffold:** `member_id` as post author. Authors are Users only.

#### `comments`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| post_id | FK → posts, cascade | |
| member_id | FK → members, cascade | commenter |
| parent_id | FK → comments, nullable, cascade | self-relation; unlimited nesting |
| comment | text | comment body text |
| is_active | boolean default true | admin moderation visibility |
| timestamps | | |

#### `comment_reactions`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| comment_id | FK → comments, cascade | |
| member_id | FK → members, cascade | required (no guest reactions) |
| reaction | enum `like` \| `dislike` | |
| timestamps | | |
| unique | (`comment_id`, `member_id`) | one reaction per member per comment |

**Removed from current scaffold:** `ip_address` uniqueness. Reactions are member-scoped only.

### Eloquent models & relationships

- Rename `BlogCategorie` → `BlogCategory` (model, factory, seeder, views folder references)
- `BlogCategory` hasMany `Post`
- `Post` belongsTo `User` as `author`; belongsTo `BlogCategory`; hasMany top-level `comments` (`whereNull('parent_id')`); hasMany `allComments`
- `Comment` belongsTo `Post`, belongsTo `Member`; belongsTo `parent`; hasMany `replies`; hasMany `reactions`
- `CommentReaction` belongsTo `Comment`, belongsTo `Member`
- `User` hasMany `Post` as author (optional inverse)
- `Member` hasMany `Comment` / `CommentReaction` (optional inverses)

## Backend (admin)

### Sidebar

Blog parent menu:

1. Blog Categories → category index
2. Blog Posts → post index

Comments moderation page under Blog menu (list / hide / delete only — no create form), using existing `CommentController`.

### Blog Categories

- CRUD via AJAX + DataTables + modal (Branch pattern)
- Fields: name, slug, is_active
- Validation: unique name/slug rules on create/update
- Soft expectations: cannot break FK integrity unexpectedly (posts set null on category delete)

### Blog Posts

- CRUD via AJAX + modal/form with Quill editor
- Fields: title, slug, blog_category_id, content, image (optional), is_active, comments_enabled, author_id
- `author_id` defaults to logged-in admin `Auth::id()`; dropdown allows selecting another User
- Image upload/replace/remove on `public` disk
- Slug uniqueness with collision handling
- Toggle comments endpoint

### Comment moderation

- Index DataTable: post title, member name, comment snippet, status, replies/likes counts
- Toggle `is_active`, delete (cascade replies/reactions via FKs)

### Controllers to keep / clean

| Controller | Role |
|------------|------|
| `Backend\BlogCategory\BlogCategoryController` | Category CRUD |
| `Backend\Post\PostController` | Post CRUD + toggle comments |
| `Backend\Comment\CommentController` | Moderation |
| `Frontend\Blog\BlogController` | Public list/show + comment/react |

Remove unused empty `Backend\Blog\BlogController` unless something still references it.

### Routes (admin)

Single clean group under existing admin middleware — **no duplicate route blocks**:

- `admin/blog-categories` — index/store/edit/update/delete
- `admin/posts` — index/store/edit/update/delete + toggle-comments
- `admin/comments` — index/toggle/delete

Permissions: replace incorrect `@can('users-show')` on blog-nav with an open admin check consistent with other CRUD menus (or add `blog-*` abilities if the project already seeds similar permission names). Do not block the feature behind a dead permission.

## Frontend

### Navigation

Add **Blog** to desktop and mobile nav in `resources/views/frontend/layout.blade.php` pointing to `route('frontend.blog.index')`.

### Blog list (`GET /blog`)

- Only `is_active` posts
- Eager load `author`, `blogCategory`
- Paginate (12 per page)
- Show: title, image, category name, author name, created_at

### Blog detail (`GET /blog/{slug}`)

- Published only; 404 otherwise
- Increment `view_count`
- Show full content, category, author, date
- Related posts (same category, limit 3)
- Comments section when `comments_enabled`; otherwise show disabled message

### Comments UX

- Nested replies: **unlimited depth** (recursive partial)
- Guest: see comments + like/dislike counts; CTA to member login for actions
- Member: form for new comment / reply; like/dislike buttons
- Inactive comments (`is_active = false`) hidden from frontend

### Reactions UX

- Member only
- Same reaction again → remove (toggle off)
- Opposite reaction → switch
- Unique constraint enforces one row per member per comment
- Guests cannot POST react (401 + message)

### Frontend routes

Post-scoped routes use **`{slug}` consistently** (no mixing with post ID). Comment-scoped routes use comment `{id}` (comments have no slug).

- `GET /blog` → index
- `GET /blog/{slug}` → show
- `POST /blog/{slug}/comments` → store comment / reply (member auth; resolve post by slug)
- `POST /blog/comments/{comment}/react` → like/dislike (member auth)
- Optional: reaction counts returned in the react JSON response (no separate counts route required)

Route ordering: register `/blog/comments/...` (and any other static `/blog/...` paths) **before** `/blog/{slug}` so they are not captured as slugs.

## Error handling

| Case | Behavior |
|------|----------|
| Unpublished / missing slug | 404 |
| Guest comment/react | 401 JSON or redirect to member login with message |
| Comments disabled on post | 403 JSON / UI message |
| Duplicate reaction uniqueness race | treat as update/toggle safely |
| Validation failures | standard Laravel JSON/HTML errors |

## Cleanup checklist

- Fix migrations (author_id, blog_category_id, member reactions unique)
- Use slug consistently for all post-scoped frontend blog routes
- Rename BlogCategorie → BlogCategory everywhere
- Deduplicate `web.php` blog routes
- Fix models fillable/casts/relationships
- Fix controllers validation and auth guards
- Fix backend blades / DataTables columns / action partials
- Fix frontend blades for author (User) vs member comments
- Fix sidebar blog-nav permissions
- Update factories/seeders to match schema
- Preserve Member module and frontend member dashboard untouched

## Out of scope

- Blog permissions role matrix overhaul beyond making menu work
- SEO microsite / RSS / scheduled publishing
- Comment editing by members after post
- Guest comments
- Replacing Member with User on frontend

## Verification plan

1. `php artisan migrate:fresh --seed` (include blog seeders if useful)
2. Admin login as User → create category → create post (author defaults to self) → edit author override → draft vs published
3. Frontend guest → list/detail visible; cannot comment/react; sees login prompt
4. Member login → comment, nested reply, like/dislike toggle/switch
5. Disable comments on post → member cannot comment
6. Admin hide comment → hidden on frontend
7. Confirm Member dashboard and existing member features still work

## Decisions log

| Topic | Decision |
|-------|----------|
| Post author | Admin `users.id` (`author_id`) |
| Commenter | `members` only |
| Reactions | Logged-in members only; guests view counts |
| Reply depth | Unlimited nesting |
| Schema update method | Edit create migrations + `migrate:fresh` |
| Implementation approach | Fix & harden existing scaffold |
| Member system | Keep fully intact |
| Post-scoped frontend routes | Always `{slug}` (never mix with post ID) |
