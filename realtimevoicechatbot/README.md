Medical Notes Assistant (PHP + HTML/JS)

A simple full-stack app for doctors to upload or record audio, transcribe with Google Cloud Medical STT, and generate structured notes via OpenAI.

Tech Stack
- Backend: PHP 8+, MySQL 5.7+/8
- Frontend: HTML, CSS, Vanilla JS
- APIs: Google Cloud Speech-to-Text (Medical), OpenAI Chat Completions

Project Structure
- `backend/` PHP endpoints (auth, upload, notes) and API clients
- `frontend/` static pages and JS (login, register, dashboard, notes)
- `db/` SQL schema
- `config.php` configuration for API keys, DB, etc.

Prerequisites
- PHP 8+ with cURL extension
- MySQL 5.7+ (JSON column support) or MySQL 8
- A web server (Apache/Nginx) configured so `frontend/` is served and `backend/` is accessible, or use PHP built-in server
- Google Cloud API key or service account with Speech-to-Text enabled
- OpenAI API key

Setup
1. Edit `config.php` and fill in:
   - DB credentials
   - Google STT: API key and options
   - OpenAI API key
2. Create uploads directory:
   - The app creates `uploads/` automatically next to `config.php`. Ensure web server can write to it.
3. Initialize database:
   - Create database `realtimevoicechatbot`
   - Run `db/schema.sql`
4. Serve the app
   - From the project root, run: `php -S localhost:8080 -t frontend`
   - Ensure backend is accessible relative to frontend at `../backend`. If not, adjust `API_BASE` in `frontend/app.js`.

Endpoints
- `backend/register.php` POST { email, password, name? }
- `backend/login.php` POST { email, password }
- `backend/logout.php` POST {}
- `backend/upload_audio.php` POST multipart-form with `audio`
- `backend/fetch_notes.php` GET (auth required)

Notes
- The STT request uses `v1p1beta1` endpoint and medical model. Adjust `encoding` based on uploaded file type.
- `ai_output` is stored as JSON; OpenAI response should be valid JSON content per the system prompt.
- CORS is enabled for all origins by default; refine `allowed_origins` in `config.php` for production.

Optional: TTS
- Integrate TTS by toggling `enable_tts` in `config.php` and adding a TTS client.

Security
- Sessions are used for auth; ensure cookies are `Secure` and `SameSite` via server config in production.
- Validate and limit upload size via web server/PHP `upload_max_filesize` and `post_max_size`.

Troubleshooting
- If frontend cannot reach backend, update `API_BASE` in `frontend/app.js`.
- If Google STT errors about credentials, ensure API key is valid or configure a service account via environment variables and use the proper endpoint.
- If OpenAI returns non-JSON, inspect `backend/openai.php` and prompt in `config.php`.


