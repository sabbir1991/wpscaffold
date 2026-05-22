# wpscaffold

> Instantly scaffold a production-ready WordPress plugin from the command line.

---

## Install

```bash
npm install -g wpscaffold
```

---

## Usage

```bash
# With plugin name as argument (recommended)
wpscaffold create "My Awesome Plugin"

# Or without argument — the tool will ask
wpscaffold create
```

---

## Local testing (before publishing)

```bash
cd wpscaffold
npm link

# now works anywhere on your machine:
wpscaffold create "My Plugin"
```

To unlink after publishing:

```bash
npm unlink -g wpscaffold
npm install -g wpscaffold
```

---

## What it creates

```
my-awesome-plugin/
├── .editorconfig
├── .gitignore
├── .nvmrc
├── .stylelintignore
├── .github/
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── workflows/
│       ├── lint.yml         # ESLint + Stylelint CI
│       ├── phpcs.yml        # PHP CodeSniffer CI
│       └── phpunit.yml      # PHPUnit CI (PHP 8.1–8.4 matrix)
├── includes/                # PHP classes (PSR-4 autoloaded)
│   ├── Admin/Menu.php       # Admin menu
│   ├── Traits/Singleton.php
│   ├── Assets.php
│   ├── Blocks.php           # (block / both types)
│   └── Plugin.php
├── src/
│   ├── block/               # Gutenberg block (block / both types)
│   └── global/              # Admin JS + CSS (admin / both types)
├── tests/
│   ├── bootstrap.php
│   └── Unit/
│       ├── AbstractTestCase.php
│       ├── AssetsTest.php
│       └── MenuTest.php
├── languages/
├── composer.json
├── eslint.config.js
├── lefthook.yml
├── package.json
├── phpcs.xml.dist
├── phpunit.xml.dist
├── my-awesome-plugin.php
└── readme.txt
```

---

## Prompts

| Prompt | Default |
|--------|---------|
| Plugin Name | CLI argument or `My Plugin` |
| Slug / text-domain | kebab-case from name |
| Description | `A WordPress plugin.` |
| Author Name | `git config user.name` |
| Author Email | `git config user.email` |
| Author URI | `https://yoursite.com` |
| Plugin URI | `{authorUri}/{slug}` |
| Namespace Vendor | First word of author name (PascalCase) |
| Namespace Package | PascalCase of slug |
| Plugin Type | `both` |

### Plugin Types

| Type | Includes |
|------|----------|
| `admin` | Admin menu, admin JS/CSS. No blocks. |
| `block` | Gutenberg block(s). No admin menu. |
| `both` | Admin menu + blocks. |

---

## Generated plugin commands

After scaffolding, inside your new plugin directory:

```bash
# Setup
composer install
npm install

# Build
npm run build
npm run start          # Watch mode

# Lint
npm run lint:js
npm run lint:css
npm run lint:php

# Test
npm run test:php       # PHPUnit

# Release
npm run zip            # Build + zip
```

---

## Requirements

| Tool | Version |
|------|---------|
| Node.js | 20+ |
| PHP | 8.1+ (for composer install) |
| Composer | 2+ |

---

## Namespace / Autoloading

All PHP classes follow PSR-4. After scaffolding, your namespace is applied everywhere:

```
Namespace: YourVendor\YourPlugin → includes/
```

---

## License

MIT
