# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/lang/de/).

## [Unreleased]

### Geplant
- Multi-Step-Form Validierung
- PDF-Export
- CRM-Integrationen
- Conditional Logic

## [4.1.0] - 2025-10-24

### Fixed
- 🐛 **CRITICAL**: Services werden jetzt korrekt angezeigt im Frontend
- 🐛 **CRITICAL**: Datenbank-Initialisierung komplett überarbeitet
- 🐛 Backend Service-Editor funktioniert jetzt
- 🐛 AJAX-Handler `ajax_get_services()` implementiert
- 🐛 AJAX-Handler `ajax_get_service()` hinzugefügt

### Added
- ✨ "Datenbank reparieren" Button im Backend
- ✨ Debug-Helper Plugin für Troubleshooting
- ✨ Direktes SQL-Reparatur-Script
- ✨ Umfangreiche Error-Logs
- ✨ Service-Editor Modal im Backend
- 📚 Ausführliche Dokumentation

### Changed
- ♻️ `create_tables()` verwendet jetzt `dbDelta()` korrekt
- ♻️ `insert_default_services()` mit Duplikat-Prüfung
- ♻️ Robustere Aktivierungs-Routine

## [4.0.0] - 2025-10-23

### Added
- ✨ Progress Indicator (3 Schritte)
- ✨ Service-Tooltips mit detaillierten Infos
- ✨ Floating Summary (sticky)
- ✨ Lead-Qualifizierung (8 Felder)
- ✨ Farbkonfigurator im Backend (8 Farben)
- ✨ Danke-Seite mit Success-Animation
- ✨ Calendly-Integration (optional)
- ✨ Disclaimer unter Preiszusammenfassung

### Changed
- 🎨 Komplettes UI-Redesign
- ♻️ Modernisierte Code-Struktur
- 📝 Erweiterte Formular-Felder

## [3.0.0] - 2025-10-20

### Added
- ✨ Corporate Design Integration (Ramböck IT)
- ✨ Orange (#F27024) & Dark Navy (#36313E)
- ✨ Logo-Integration
- ✨ Lead-Management Dashboard
- ✨ E-Mail-Benachrichtigungen

### Changed
- 🎨 Farben an CI angepasst
- ♻️ CSS komplett überarbeitet

## [2.0.0] - 2025-10-15

### Added
- ✨ Service-Konfigurator mit 10 Services
- ✨ Live-Preisberechnung
- ✨ AJAX-basiertes Frontend
- ✨ WordPress-Integration

### Changed
- ♻️ Von Standalone zu WordPress-Plugin

## [1.0.0] - 2025-10-10

### Added
- 🎉 Initial Release
- ✨ Basis-Konfigurator
- ✨ Kontaktformular
- ✨ E-Mail-Versand

---

## Legende

- `Added` - Neue Features
- `Changed` - Änderungen an bestehenden Features
- `Deprecated` - Bald zu entfernende Features
- `Removed` - Entfernte Features
- `Fixed` - Bug-Fixes
- `Security` - Sicherheits-Fixes

## Versionierung

Format: `MAJOR.MINOR.PATCH`

- **MAJOR**: Breaking Changes
- **MINOR**: Neue Features (backwards-compatible)
- **PATCH**: Bug-Fixes (backwards-compatible)

[Unreleased]: https://github.com/ramboeck-it/service-configurator/compare/v4.1.0...HEAD
[4.1.0]: https://github.com/ramboeck-it/service-configurator/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/ramboeck-it/service-configurator/compare/v3.0.0...v4.0.0
[3.0.0]: https://github.com/ramboeck-it/service-configurator/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/ramboeck-it/service-configurator/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/ramboeck-it/service-configurator/releases/tag/v1.0.0
