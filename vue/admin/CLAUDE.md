# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Vue Vben Admin is a modern Vue 3 admin template built with a monorepo architecture using pnpm workspaces and Turbo. The project provides multiple UI framework variants (Ant Design Vue, Element Plus, Naive UI, TDesign) sharing a common core architecture.

**Tech Stack:** Vue 3, TypeScript, Vite, pnpm workspaces, Turbo, TailwindCSS

**Node Requirements:** Node >=20.12.0, pnpm >=10.0.0

## Development Commands

### Setup and Installation
```bash
# Install dependencies
pnpm install

# Development - choose specific app
pnpm dev:antd      # Ant Design Vue version
pnpm dev:ele       # Element Plus version
pnpm dev:naive     # Naive UI version
pnpm dev:tdesign   # TDesign version
pnpm dev           # Interactive selection via turbo-run

# Development with backend mock
# The mock server runs automatically when VITE_NITRO_MOCK=true in .env files
```

### Build and Preview
```bash
# Build all apps
pnpm build

# Build specific app
pnpm build:antd
pnpm build:ele
pnpm build:naive
pnpm build:tdesign

# Build with bundle analysis
pnpm build:analyze

# Preview production build
pnpm preview
```

### Testing
```bash
# Run unit tests (Vitest)
pnpm test:unit

# Run e2e tests (Playwright)
pnpm test:e2e

# Type checking
pnpm check:type
```

### Code Quality
```bash
# Lint and format
pnpm lint           # Lint all files
pnpm format         # Format all files

# Comprehensive checks
pnpm check          # Run all checks (circular deps, deps, types, cspell)
pnpm check:circular # Check circular dependencies
pnpm check:dep      # Check dependencies
pnpm check:cspell   # Spell checking
```

### Maintenance
```bash
# Clean build artifacts
pnpm clean

# Reinstall dependencies
pnpm reinstall

# Update dependencies
pnpm update:deps
```

## Monorepo Architecture

### Workspace Structure

The repository is organized into distinct workspace categories:

**apps/** - Application instances (each can run independently)
- `web-antd` - Ant Design Vue implementation
- `web-ele` - Element Plus implementation
- `web-naive` - Naive UI implementation
- `web-tdesign` - TDesign implementation
- `backend-mock` - Nitro-based mock API server

**packages/@core/** - Core framework-agnostic packages (no business logic)
- `base/` - Foundational SDK and primitives
  - `design` - Design tokens and theme system
  - `icons` - Icon system
  - `shared` - Core utilities and helpers
  - `typings` - Shared TypeScript definitions
- `ui-kit/` - UI component abstractions
  - `form-ui` - Form components
  - `layout-ui` - Layout components
  - `menu-ui` - Menu/navigation components
  - `popup-ui` - Modal/drawer components
  - `shadcn-ui` - Shadcn-based components
  - `tabs-ui` - Tab components
- `composables` - Vue composition functions
- `preferences` - Application preferences/settings management

**packages/effects/** - Business logic with light coupling (uses Pinia, routing, component libraries)
- `access` - Permission/access control
- `common-ui` - Business UI components
- `hooks` - Business-specific hooks
- `layouts` - Layout implementations
- `plugins` - Vue plugins (motion, etc.)
- `request` - HTTP request handling with interceptors

**packages/** - Shared packages
- `constants` - Application constants
- `icons` - Icon assets
- `locales` - i18n translations
- `preferences` - Preference implementations
- `stores` - Pinia stores
- `styles` - Global styles and themes
- `types` - TypeScript types
- `utils` - Utility functions

**internal/** - Build tooling and configuration
- `vite-config` - Shared Vite configuration
- `tailwind-config` - Shared Tailwind configuration
- `node-utils` - Node.js utilities
- `lint-configs/` - ESLint, Prettier, Stylelint, Commitlint configs
- `tsconfig` - Shared TypeScript configuration

**scripts/** - Build and deployment scripts
- `vsh` - CLI tool for project operations
- `turbo-run` - Turbo wrapper for interactive task running
- `clean.mjs` - Cleanup script

### Key Architectural Principles

1. **Core vs Effects Separation**:
   - `@core` packages must be framework-agnostic, no business logic, no state management
   - `effects` packages can depend on Pinia, Vue Router, and UI frameworks
   - Apps depend on `effects` packages, which depend on `@core` packages

2. **UI Framework Adapters**: Each app implements adapters to bridge the generic `@core/ui-kit` components with specific UI frameworks (Ant Design, Element Plus, etc.). See `apps/*/src/adapter/` directories.

3. **Workspace Protocol**: All internal dependencies use `workspace:*` in package.json files. This ensures proper monorepo linking.

4. **Catalog System**: The pnpm workspace uses a catalog in `pnpm-workspace.yaml` to centralize external dependency versions.

## Application Structure

Each app in `apps/web-*` follows this structure:

```
src/
├── adapter/          # UI framework adapters (component, form)
├── api/              # API definitions and request client setup
│   ├── core/         # Core APIs (auth, user, menu)
│   └── request.ts    # Configured request client with interceptors
├── router/           # Vue Router configuration
│   ├── routes/       # Route definitions (modules pattern)
│   ├── guard.ts      # Route guards
│   └── access.ts     # Access control integration
├── store/            # Pinia stores (app-specific)
├── views/            # Page components
├── locales/          # i18n locale files
├── layouts/          # Layout components (if custom)
├── bootstrap.ts      # App initialization logic
├── main.ts           # App entry point
├── app.vue           # Root component
└── preferences.ts    # App preferences configuration
```

### Bootstrap Process

Applications initialize in this order (see `apps/*/src/bootstrap.ts`):
1. Initialize component adapters
2. Initialize form adapters
3. Register directives (loading, access)
4. Setup i18n
5. Initialize Pinia stores
6. Register router and guards
7. Initialize plugins (Motion, Tippy)
8. Mount app

### Request Handling

The request client setup (see `apps/*/src/api/request.ts`) includes:
- Base URL configuration from environment
- Authorization header injection
- Token refresh logic with `doRefreshToken()`
- Re-authentication handling with `doReAuthenticate()`
- Error message handling with UI framework's message component
- Response format normalization

## Environment Configuration

Each app has environment files in `apps/*/.env*`:
- `.env` - Base configuration
- `.env.development` - Development overrides
- `.env.production` - Production overrides
- `.env.analyze` - Bundle analysis mode

Key environment variables:
- `VITE_PORT` - Dev server port
- `VITE_BASE` - Base public path
- `VITE_GLOB_API_URL` - API base URL
- `VITE_NITRO_MOCK` - Enable/disable mock server
- `VITE_DEVTOOLS` - Enable/disable Vue devtools
- `VITE_INJECT_APP_LOADING` - Inject global loading indicator
- `VITE_ROUTER_HISTORY` - Router mode (hash/history)

## Routing System

Routes are organized in modules pattern under `apps/*/src/router/routes/modules/`:
- Each feature gets its own route module file
- Core routes (login, error pages) are in `routes/core.ts`
- Routes support meta fields for permissions, i18n titles, icons, etc.
- Dynamic route generation based on backend API is supported via `@vben/access`

## Permission System

Permissions are handled by `@vben/access` package:
- Uses `v-access` directive for UI element control
- Integrates with router guards for page-level access
- Supports role-based and permission-based access control
- Configuration in `apps/*/src/router/access.ts`

## State Management

- Uses Pinia for state management
- Core stores in `packages/stores/` (shared across apps)
- App-specific stores in `apps/*/src/store/`
- Stores initialized in bootstrap with namespace support
- `@vben/stores` exports: `useAccessStore`, `useUserStore`, etc.

## Styling System

- TailwindCSS for utility-first styling
- Theme system in `@vben/design` with CSS variables
- Multiple built-in themes with dark mode support
- Framework-specific styles in `@vben/styles/{framework}`
- Global styles imported in `bootstrap.ts`

## i18n System

- Uses `vue-i18n` with `@intlify/unplugin-vue-i18n`
- Locale files in `packages/locales/` (shared) and `apps/*/src/locales/` (app-specific)
- Lazy loading of locale messages
- Integration with preferences for locale switching
- Use `$t()` helper for translations

## Form System

The form system (`@vben/form-ui` and adapters) provides:
- Schema-based form generation
- Integration with `vee-validate` and `zod`
- Framework-agnostic API with framework-specific adapters
- Support for complex field types, validation, and conditional logic

## Testing

- **Unit tests**: Vitest with happy-dom environment (see `vitest.config.ts`)
- **E2E tests**: Playwright (see `playground/playwright.config.ts`)
- Test files should be co-located with source files or in `__tests__` directories
- Run single test: `pnpm vitest run <test-file-pattern>`

## Git Workflow

### Commit Convention

Follow Angular commit convention:
- `feat`: New feature
- `fix`: Bug fix
- `perf`: Performance improvement
- `refactor`: Code refactoring
- `style`: Code style changes (formatting)
- `test`: Test additions/changes
- `docs`: Documentation changes
- `chore`: Build/tooling changes
- `ci`: CI configuration changes
- `types`: Type definition changes
- `revert`: Revert previous commit

Format: `type(scope): description`

Example: `feat(form): add date range picker component`

### Changesets

For versioning and changelogs:
```bash
pnpm changeset        # Create a changeset
pnpm version          # Apply changesets and update versions
```

## Common Patterns

### Adding a New Page

1. Create page component in `apps/*/src/views/`
2. Add route in `apps/*/src/router/routes/modules/`
3. Add i18n keys in locale files
4. Add API calls in `apps/*/src/api/` if needed

### Creating a New Shared Package

1. Create package directory in appropriate workspace (packages/, packages/@core/, packages/effects/)
2. Add `package.json` with `workspace:*` for internal deps
3. Add to `pnpm-workspace.yaml` if needed
4. Reference as dependency in consuming packages
5. Run `pnpm install` to link workspaces

### Adding UI Components

- **Framework-agnostic**: Add to `packages/@core/ui-kit/`
- **Business components**: Add to `packages/effects/common-ui/`
- **App-specific**: Add to `apps/*/src/components/`

### Working with Forms

Use the VbenForm component from `@vben/common-ui`:
```typescript
import { VbenForm } from '@vben/common-ui';

const [Form, formApi] = VbenForm({
  schema: [
    { component: 'Input', fieldName: 'username', label: 'Username' },
    // ...
  ],
  onSubmit: async (values) => {
    // Handle submission
  },
});
```

### Adding API Endpoints

1. Define API function in `apps/*/src/api/core/` or create new module
2. Use configured `requestClient` from `api/request.ts`
3. TypeScript types should be defined inline or in `packages/types/`

## Troubleshooting

### Build Issues

- Ensure Node version >=20.12.0 and pnpm >=10.0.0
- Try `pnpm clean` then `pnpm install`
- Check for circular dependencies: `pnpm check:circular`

### Type Errors

- Run `pnpm check:type` to see all type errors
- After adding new packages, run `pnpm install` to regenerate types
- Check `tsconfig.json` paths are correctly configured

### Mock Server Not Working

- Verify `VITE_NITRO_MOCK=true` in `.env.development`
- Mock server is in `apps/backend-mock/`
- Mock routes defined in `apps/backend-mock/api/`

### Hot Module Replacement Issues

- Some changes to adapters or bootstrap code require full restart
- If HMR stops working, restart dev server
- Check browser console for HMR errors
