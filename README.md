<div align="center">

# 🧱 wpscaffold

**Instantly scaffold a production-ready WordPress plugin from the command line.**

[![npm version](https://img.shields.io/npm/v/wpscaffold?style=flat-square&color=cb3837&logo=npm)](https://www.npmjs.com/package/wpscaffold)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue?style=flat-square)](LICENSE)
[![Node.js](https://img.shields.io/badge/node-%3E%3D20.0.0-brightgreen?style=flat-square&logo=nodedotjs)](https://nodejs.org)
[![WordPress](https://img.shields.io/badge/WordPress-compatible-21759b?style=flat-square&logo=wordpress)](https://wordpress.org)

</div>

---

## ✨ What is wpscaffold?

`wpscaffold` is a zero-config CLI that generates a **fully wired WordPress plugin** in seconds — complete with PHP class structure, Gutenberg block support, admin UI, GitHub Actions CI, PHPUnit tests, ESLint, Stylelint, PHPCS, and more. Stop copy-pasting boilerplate. Start building.

---

## 🚀 Quick Start

```bash
# Install globally
npm install -g wpscaffold

# Scaffold a plugin
wpscaffold create "My Awesome Plugin"
```

That's it. Follow the prompts, and your plugin directory is ready.

---

## 📦 Installation

### Global (recommended)

```bash
npm install -g wpscaffold
```

### Local development / testing

```bash
git clone https://github.com/wprigel/wpscaffold.git
cd wpscaffold
npm link

# Now available anywhere on your machine:
wpscaffold create "My Plugin"

# Unlink after publishing:
npm unlink -g wpscaffold
npm install -g wpscaffold
```

---

## 🛠️ Usage

```bash
# Pass plugin name directly (recommended)
wpscaffold create "My Awesome Plugin"

# Or let the tool prompt you
wpscaffold create
```

After running, you'll see an interactive prompt session:

```
  ╔══════════════════════════════╗
  ║  Create WordPress Scaffold   ║
  ╚══════════════════════════════╝

  Press Enter to accept default values in brackets.

  Plugin Name [My Plugin]:
  Slug / text-domain [my-plugin]:
  Description [A WordPress plugin.]:
  Author Name [Your Name]:
  Author Email:
  Author URI [https://yoursite.com]:
  Plugin URI [https://yoursite.com/my-plugin]:
  Namespace Vendor [Your]:
  Namespace Package [MyPlugin]:
  Plugin Type  [admin / block / both]:
```

---

## 🎛️ Prompts Reference

| Prompt | Default | Description |
|--------|---------|-------------|
| **Plugin Name** | CLI argument or `My Plugin` | Human-readable plugin name |
| **Slug / text-domain** | kebab-case from name | Used for directories, function prefixes, text-domain |
| **Description** | `A WordPress plugin.` | Short plugin description |
| **Author Name** | `git config user.name` | Auto-detected from Git |
| **Author Email** | `git config user.email` | Auto-detected from Git |
| **Author URI** | `https://yoursite.com` | Your personal/company URL |
| **Plugin URI** | `{authorUri}/{slug}` | Plugin homepage URL |
| **Namespace Vendor** | First word of author (PascalCase) | PHP namespace vendor segment |
| **Namespace Package** | PascalCase of slug | PHP namespace package segment |
| **Constant Prefix** | UPPER_SNAKE from slug | PHP constant prefix (e.g. `MY_PLUGIN`) |
| **Plugin Type** | `both` | Controls what files are scaffolded |

---

## 🧩 Plugin Types

Choose the type that matches what you're building:

| Type | 🏗️ Includes | 🗑️ Omits |
|------|------------|---------|
| `admin` | Admin menu, admin JS/CSS assets | Gutenberg block source |
| `block` | Gutenberg block(s), block editor packages | Admin menu, admin JS/CSS |
| `both` | Everything — admin UI + blocks | Nothing |

> **Tip:** Not sure? Pick `both`. You can always delete what you don't need.

---

## 📁 Generated Structure

```
my-awesome-plugin/
│
├── 📄 my-awesome-plugin.php      ← Plugin entry point
├── 📄 readme.txt                 ← WordPress.org readme
├── 📄 composer.json
├── 📄 phpcs.xml.dist
├── 📄 phpunit.xml.dist
├── 📄 eslint.config.js
├── 📄 lefthook.yml               ← Git hooks (lint on commit)
│
├── 🔧 .editorconfig
├── 🔧 .gitignore
├── 🔧 .nvmrc
├── 🔧 .stylelintignore
│
├── 📂 .github/
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── workflows/
│       ├── lint.yml              ← ESLint + Stylelint CI
│       ├── phpcs.yml             ← PHP CodeSniffer CI
│       └── phpunit.yml           ← PHPUnit CI (PHP 8.1–8.4 matrix)
│
├── 📂 includes/                  ← PHP classes (PSR-4 autoloaded)
│   ├── Admin/
│   │   └── Menu.php              ← Admin menu registration
│   ├── Traits/
│   │   └── Singleton.php
│   ├── Assets.php                ← Enqueue scripts/styles
│   ├── Blocks.php                ← Block registration (block/both)
│   └── Plugin.php                ← Core bootstrap
│
├── 📂 src/
│   ├── block/                    ← Gutenberg block (block/both)
│   │   ├── block.json
│   │   ├── edit.js
│   │   ├── index.js
│   │   ├── save.js
│   │   └── style.scss
│   └── global/                   ← Admin assets (admin/both)
│       ├── js/admin.js
│       └── css/admin.scss
│
├── 📂 tests/
│   ├── bootstrap.php
│   └── Unit/
│       ├── AbstractTestCase.php
│       ├── AssetsTest.php
│       └── MenuTest.php
│
└── 📂 languages/
```

---

## ⚙️ Generated Plugin Commands

Once inside your scaffolded plugin directory:

### Setup

```bash
composer install    # Install PHP dependencies (PHPCS, PHPUnit)
npm install         # Install JS dependencies (@wordpress/scripts)
```

### Development

```bash
npm run build       # Production build
npm run start       # Watch mode (blocks)
npm run start:custom  # Watch mode (admin assets)
```

### Linting & Formatting

```bash
npm run lint:js     # ESLint
npm run lint:css    # Stylelint
npm run lint:php    # PHP CodeSniffer
npm run fix:js      # Auto-fix JS
npm run fix:css     # Auto-fix CSS
npm run fix:php     # Auto-fix PHP
npm run format      # wp-scripts format
```

### Testing

```bash
npm run test:php    # Run PHPUnit tests
```

### Internationalization

```bash
npm run makepot     # Generate .pot translation file
```

### Release

```bash
npm run zip         # Build + create installable .zip
```

---

## 🏗️ PHP Architecture

Generated plugins follow **PSR-4 autoloading** with a clean namespace structure:

```
YourVendor\YourPlugin\           → includes/
YourVendor\YourPlugin\Admin\     → includes/Admin/
YourVendor\YourPlugin\Traits\    → includes/Traits/
```

All classes use the **Singleton trait** for consistent instantiation:

```php
use YourVendor\YourPlugin\Traits\Singleton;

class Plugin {
    use Singleton;
}

// Bootstrap
YourVendor\YourPlugin\Plugin::instance();
```

---

## 🔄 CI / CD Out of the Box

Your plugin ships with **three GitHub Actions workflows**:

| Workflow | Trigger | What it checks |
|----------|---------|---------------|
| `lint.yml` | Push / PR | ESLint + Stylelint |
| `phpcs.yml` | Push / PR | PHP CodeSniffer (WordPress coding standards) |
| `phpunit.yml` | Push / PR | PHPUnit on PHP 8.1, 8.2, 8.3, 8.4 matrix |

---

## 📋 Requirements

| Tool | Version | Purpose |
|------|---------|---------|
| **Node.js** | ≥ 20.0.0 | Run wpscaffold |
| **PHP** | ≥ 8.1 | Plugin development |
| **Composer** | ≥ 2.0 | PHP dependency management |
| **WordPress** | ≥ 6.0 | Target environment |

---

## 🔑 How It Works

1. **Collects prompts** — plugin name, slug, namespace, type, author info
2. **Builds replacements** — maps placeholder strings (`plugin-skeleton`, `PluginSkeleton`, `wpRigel`) to your values
3. **Copies template/** recursively — applies type markers and text replacements to every text file
4. **Removes unused code** — strips block or admin sections based on chosen type
5. **Renames entry file** — `plugin-skeleton.php` → `{your-slug}.php`
6. **Writes package.json** — dynamically built with the correct scripts and dependencies for your plugin type

### Type Markers

Template files use marker comments to gate type-specific code:

```php
/* @skeleton-admin */
// This code only appears in admin and both types
/* @skeleton-admin-end */

/* @skeleton-block */
// This code only appears in block and both types
/* @skeleton-block-end */
```

---

## 🤝 Contributing

1. Fork the repo
2. Create your branch: `git checkout -b feature/my-feature`
3. Make changes in `lib/generator.js` or `template/`
4. Test locally: `npm link && wpscaffold create "Test Plugin"`
5. Submit a pull request

> When adding new PHP classes to `template/includes/`, mirror the pattern in `buildReplacements()`. The placeholder namespace is `wpRigel\PluginSkeleton` and the slug placeholder is `plugin-skeleton`.

---

## 📄 License

[MIT](LICENSE) © [wpRigel](https://github.com/wprigel)
