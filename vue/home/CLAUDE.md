# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Vue 3 + Vite gaming platform with mobile-first responsive design. The project features:
- **Mobile-First Sidebar Layout**: Collapsible sidebar that pushes content to the right on desktop, overlays on mobile
- **Multi-Theme Support**: Dark gold and dark purple themes with smooth transitions
- **Modern Vue 3 Features**: Composition API with `<script setup>` syntax
- **Complete Game Functionality**: Number betting, results tracking, game rules, and user management
- **Responsive Design**: Fully responsive layout using Tailwind CSS

## Development Commands

```bash
# Start development server with hot reload
npm run dev

# Build for production
npm run build

# Build for development (with source maps)
npm run build:dev

# Preview production build
npm run preview

# Preview production build in production mode
npm run preview:prod
```

## Architecture

### Layout System
- **MainLayout.vue** - Primary layout with mobile-first sidebar and dynamic content panels
- **LoginLayout.vue** - Authentication layout for login/register flows
- **Layout Components** (`src/components/Layout/`):
  - **Sidebar.vue** - Mobile-first collapsible navigation with theme integration
  - **Header.vue** - Fixed header with search, notifications, and theme switcher

### Layout Behavior Pattern
The MainLayout implements a unique **transform-based layout system**:
- **Desktop**: Both header and main content transform (`translate-x-64`) when sidebar opens
- **Mobile**: Same transform behavior for consistent mobile experience
- **Dynamic Content Panels**: Search, notifications, settings, and theme selector overlay the router-view
- **Click-to-Reset**: Clicking main content returns to default router-view

### Pages (Views) - `src/views/`
- **Home.vue** - Welcome page with features and navigation
- **Betting.vue** - Number betting interface with 0-99 number grid
- **BetStatus.vue** - Bet status tracking with filtering and pagination
- **Results.vue** - Game results display with statistics and countdown
- **Rules.vue** - Comprehensive game rules and FAQ
- **ChangePassword.vue** - Secure password change with validation
- **Users.vue** - User management demonstration
- **About.vue** - About page with project information

### Core Services
- **Router** (`src/router/index.js`) - Vue Router with lazy loading and auth guards
- **API Service** (`src/services/api.js`) - Axios client with interceptors and environment integration
- **Environment Configs** - `.env.development` and `.env.production` with mode-specific settings
- **Build Config** - Single `vite.config.js` with environment detection and optimization

### Theme System Implementation
- **Dynamic Theme Classes**: `theme-gold` and `theme-purple` classes on body element
- **Gradient Backgrounds**: Applied via JavaScript to sidebar elements for smooth transitions
- **localStorage Integration**: Theme preference persistence and cross-tab synchronization
- **Event-Driven Updates**: `themeChanged` custom events for component coordination
- **Color Schemes**:
  - **Gold**: Linear gradient from #f59e0b → #d97706 → #b45309
  - **Purple**: Linear gradient from #9333ea → #7c3aed → #6b21a8

## Key Technologies

- Vue 3.5.24 with Composition API and `<script setup>` syntax throughout
- Vue Router 4.6.3 for client-side routing with lazy loading and authentication guards
- Axios 1.13.2 for HTTP requests with request/response interceptors
- Tailwind CSS 3.x for utility-first styling with custom color extensions
- Vite (rolldown-vite 7.2.5) for lightning-fast development and optimized builds
- PostCSS with Autoprefixer for CSS processing and vendor prefixing
- Terser for production minification with console statement removal

## Development Environment Variables

Key environment variables that can be configured:

- `VITE_API_BASE_URL` - Base URL for API requests (default: http://localhost:8080)
- `VITE_API_TIMEOUT` - Request timeout in milliseconds (default: 10000)
- `VITE_PORT` - Development server port (default: 3000)
- `VITE_HOST` - Development server host (default: localhost)
- `VITE_ENABLE_AXIOS_LOGGING` - Enable detailed API request/response logging in development
- `VITE_APP_TITLE` - Application title
- `VITE_LOG_LEVEL` - Logging level (debug, info, error)

## API Usage

The project includes a configured Axios instance with comprehensive features:

```javascript
import { userService, api } from '@/services/api'

// Get all users
const users = await userService.getUsers()

// Get user by ID
const user = await userService.getUserById(1)

// Generic API calls
const response = await api.get('/endpoint')
const data = await api.post('/endpoint', payload)
```

**API Features:**
- Request/response interceptors for logging and error handling
- Environment variable integration for base URLs and timeouts
- Development-only detailed logging with `VITE_ENABLE_AXIOS_LOGGING`
- Token-based authentication with localStorage integration
- Comprehensive error handling with user-friendly messages

## Vite Configuration Details

The `vite.config.js` implements environment-aware configuration:

### Development Mode
- Source maps enabled (`sourcemap: true`)
- esbuild minification for faster builds
- API proxy configuration (`/api` → backend)
- Development server auto-open (`open: true`)
- CORS enabled

### Production Mode
- Terser minification with console/debugger removal
- Manual chunk splitting for optimal caching:
  - `vendor` chunk for Vue core
  - `router` chunk for Vue Router
  - `http` chunk for Axios
- Manifest generation for asset hashing
- No source maps for security

### Path Aliases
- `@` resolves to `src/` directory for clean imports

## Layout & Responsive Design Architecture

### Transform-Based Layout System
**Unique Implementation**: Instead of traditional margin/push approaches, this layout uses CSS transforms:

```vue
<!-- Both header and content transform together -->
<div :class="{ 'translate-x-64': sidebarOpen, 'translate-x-0': !sidebarOpen }">
```

**Benefits:**
- Consistent behavior across desktop and mobile
- Smooth GPU-accelerated transitions
- No layout reflow during sidebar toggle
- Fixed positioning without z-index conflicts

### Mobile-First Breakpoints
- **Mobile (< 1024px)**: Sidebar closed by default, overlay behavior with backdrop
- **Desktop (≥ 1024px)**: Sidebar can remain open, content transforms alongside header
- **Transitions**: Uniform 300ms ease-in-out timing for all layout changes

### Dynamic Content Panels
MainLayout implements a state-based content system:
- `currentView` controls whether to show router-view or overlay panels
- Available panels: search-results, notifications-panel, settings-panel, theme-selector
- Click-outside behavior automatically returns to router-view

## Development Patterns

### State Management Architecture
- **Local State**: Vue 3 Composition API with reactive refs
- **Global State**: Theme and layout state via localStorage events
- **Cross-Tab Sync**: `storage` event listeners for theme consistency
- **Custom Events**: `themeChanged` events for component coordination

### Component Patterns
- **Composition API**: All components use `<script setup>` syntax
- **Event-Driven**: Parent-child communication via custom events
- **Responsive Props**: Components adapt behavior based on screen size
- **Theme Integration**: All components support dynamic theming

### Development Workflow
- Use `npm run dev` for hot reload development (port 3000)
- Use `npm run build:dev` for development builds with source maps
- Use `npm run build` for production optimization
- Use `npm run preview` to test production builds locally
- No testing framework currently configured
- No linting/formatting tools set up
- Environment-specific configuration via `.env.development` and `.env.production`