# Contributing to Paperdoc Library

Thank you for considering contributing to **paperdoc-lib**! This document covers everything you need to know before opening an issue or submitting a pull request.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Branch Strategy](#branch-strategy)
- [Making Changes](#making-changes)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Submitting a Pull Request](#submitting-a-pull-request)
- [Issue Reporting](#issue-reporting)
- [Security Vulnerabilities](#security-vulnerabilities)

---

## Code of Conduct

This project follows a standard open-source Code of Conduct. Be respectful, inclusive, and constructive in all interactions.

---

## Getting Started

1. **Fork** the repository on GitHub.
2. **Clone** your fork locally:
   ```bash
   git clone https://github.com/<your-username>/paperdoc-lib.git
   cd paperdoc-lib
   ```
3. **Add the upstream remote**:
   ```bash
   git remote add upstream https://github.com/paperdoc-dev/paperdoc-lib.git
   ```

---

## Development Setup

```bash
# Install dependencies
composer install

# Verify the test suite passes
./vendor/bin/phpunit
```

**Requirements:** PHP ^8.2, ext-dom, ext-mbstring, ext-zip, ext-zlib.

---

## Branch Strategy

| Branch | Purpose |
|--------|---------|
| `main` | Stable, production-ready code. Never push directly. |
| `develop` | Integration branch for upcoming releases. Base your PRs here. |
| `feature/<name>` | New features |
| `fix/<name>` | Bug fixes |
| `docs/<name>` | Documentation only |
| `chore/<name>` | Maintenance, dependencies, CI |

> **Base all pull requests on `develop`**, not `main`.

---

## Making Changes

1. Sync with upstream before starting:
   ```bash
   git fetch upstream
   git checkout develop
   git merge upstream/develop
   ```
2. Create a descriptive branch:
   ```bash
   git checkout -b feature/xlsx-table-styling
   ```
3. Make your changes, keeping commits small and focused.
4. Write or update tests covering your change.
5. Run the full test suite before pushing.

### Commit Message Format

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <short description>

[optional body]

[optional footer: Closes #123]
```

**Types:** `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`, `perf`

**Examples:**

```
feat(renderer): add table border styles to XLSX renderer
fix(parser): handle empty paragraphs in DOCX parser
docs(readme): add batch processing example
test(pdf): add unit tests for PdfRenderer page breaks
```

---

## Coding Standards

- **PSR-12** coding style
- **Strict types** — every file must start with `declare(strict_types=1);`
- **Type hints** — use typed properties, parameter types, and return types everywhere
- **No magic numbers** — use named constants or enums
- **Interfaces first** — new features must implement an existing contract or propose a new one in `src/Contracts/`
- **No direct `echo` or `print`** — all output goes through renderers

Run static analysis before pushing (if available in your setup):

```bash
./vendor/bin/phpstan analyse
```

---

## Testing

All changes must include tests.

| Type | Location | When |
|------|----------|------|
| Unit tests | `tests/Unit/` | For isolated classes/methods |
| Integration tests | `tests/Integration/` | For parser↔renderer round-trips |

```bash
# Run all tests
./vendor/bin/phpunit

# Run a specific suite
./vendor/bin/phpunit --testsuite Unit
./vendor/bin/phpunit --testsuite Integration

# Run a single test file
./vendor/bin/phpunit tests/Unit/Parsers/DocxParserTest.php
```

Aim for meaningful coverage, not 100% line coverage. Focus on edge cases and format-specific behaviour.

---

## Submitting a Pull Request

1. Push your branch to your fork:
   ```bash
   git push origin feature/xlsx-table-styling
   ```
2. Open a PR on GitHub against the `develop` branch of `paperdoc-dev/paperdoc-lib`.
3. Fill in the PR template completely:
   - **What** — what does this PR change?
   - **Why** — what problem does it solve?
   - **How** — brief description of the approach
   - **Tests** — what tests were added/updated?
   - **Breaking changes** — yes / no (if yes, describe the impact)
4. Link any related issues: `Closes #42`
5. Request a review from the relevant code owners (see `CODEOWNERS`).

### PR Checklist

- [ ] Branch is based on `develop`
- [ ] `declare(strict_types=1)` present in every new PHP file
- [ ] All existing tests still pass
- [ ] New tests added for new functionality or bug fixes
- [ ] `CHANGELOG.md` updated (add entry under `[Unreleased]`)
- [ ] No debug code, commented-out code, or `var_dump` left in
- [ ] Docblocks updated if public API changed

---

## Issue Reporting

Before opening an issue:
- Search existing issues to avoid duplicates.
- Reproduce the problem on the latest `develop` branch.

When reporting a bug, include:
- PHP version
- Library version (`composer show paperdoc-dev/paperdoc-lib`)
- Minimal reproducible example
- Expected vs. actual behaviour
- Any relevant stack trace

Use GitHub issue labels: `bug`, `enhancement`, `question`, `documentation`, `good first issue`.

---

## Security Vulnerabilities

**Do not open public GitHub issues for security vulnerabilities.**

Please disclose them responsibly by emailing **security@paperdoc.dev**. We aim to acknowledge within 48 hours and release a patch within 14 days.

---

Thank you for helping make Paperdoc better! 🙌
