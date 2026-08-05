# Changelog

All notable changes to `statamic-yourstoryz` will be documented in this file.

## Release 0.1.5 - 2026-08-05

### What's Changed

* fix: Guard against stories without an author by @jhhazelaar in https://github.com/sharestoryz/statamic-yourstoryz/pull/21

`ProcessStoryJob` threw `Trying to access array offset on null` for any story whose `author` was `null`. Because the job failed before creating the entry, its own duplicate guard never began to apply, so every subsequent sync re-dispatched and re-failed the same story — one author-less story produced an unbounded stream of failed jobs. Sites syncing stories without an author should update.

Also drops the Laravel 11 CI matrix legs, which could no longer install: `statamic/cms ^5.0` now resolves only to versions requiring Laravel `^12.40`. `illuminate/contracts` and `orchestra/testbench` were narrowed to match; the Laravel 11 support they declared was already unsatisfiable, so nothing installable is lost.

**Full Changelog**: https://github.com/sharestoryz/statamic-yourstoryz/compare/0.1.4...0.1.5

## Release 0.1.4 - 2026-04-01

**Full Changelog**: https://github.com/sharestoryz/statamic-yourstoryz/compare/0.1.3...0.1.4
