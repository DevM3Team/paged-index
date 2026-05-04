# Paged Index Package

## Purpose
- Package-specific guidance for `m3team/paged-index`.
- This repo is a submodule. Keep changes local to this package unless the task explicitly requires root workspace updates.

## Working Rules
- Namespace and source code live under `src/`.
- Preserve the package's Laravel service provider, config publishing, and artisan command behavior.
- Keep the legacy `DatabasePagedIndex` behavior stable unless the task says otherwise.

## Validation
- Use `./vendor/bin/sail artisan test`, `./vendor/bin/pint`, and `./vendor/bin/pest` when checking package changes from the wrapper project.
- Prefer focused tests around request parsing, filtering, sorting, pagination, and resource transformation.

## Notes
- The package exposes `php artisan make:paged_index`.
- Configuration lives in `config/paged-index.php`.
