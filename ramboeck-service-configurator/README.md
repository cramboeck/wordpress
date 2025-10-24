# Ramböck IT Service Konfigurator

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/Version-4.1.0-orange.svg)](CHANGELOG.md)

> Professioneller WordPress-Service-Konfigurator für IT-Dienstleister und MSPs (Managed Service Provider)

Ein interaktiver Service-Konfigurator für WordPress, der IT-Dienstleister dabei unterstützt, qualifizierte Leads zu generieren und Angebote zu erstellen.

![Screenshot](docs/screenshot.png)

## 🚀 Features

### Frontend
- **🎨 3-Schritt-Konfigurator** - Intuitive Benutzerführung
- **💡 Service-Katalog** - 10 vorkonfigurierte IT-Services
- **💰 Live-Preisberechnung** - Setup + monatliche Kosten
- **ℹ️ Service-Tooltips** - Detaillierte Informationen per Hover
- **📊 Floating Summary** - Sticky Preiszusammenfassung
- **✅ Danke-Seite** - Mit Success-Animation
- **📅 Calendly-Integration** - Optionale Terminbuchung

### Backend
- **📋 Lead-Management** - Übersicht aller Anfragen
- **⚙️ Service-Editor** - CRUD für Services
- **🎨 Farbkonfigurator** - 8 anpassbare Farben
- **📧 E-Mail-Benachrichtigungen** - An Kunde & Admin
- **📊 Statistiken** - Umsatzpotential, Status-Tracking
- **🔧 Reparatur-Tool** - "Datenbank reparieren" Funktion

### Technisch
- **✨ AJAX-basiert** - Keine Page-Reloads
- **📱 Responsive** - Mobile-optimiert
- **🔒 Sicherheit** - Nonce-Validierung, Prepared Statements
- **🌐 Mehrsprachig-bereit** - i18n-ready
- **🎨 CSS Variables** - Dynamische Farbanpassung
- **♿ Accessibility** - WCAG 2.1 konform

## 📦 Installation

### Voraussetzungen
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+ oder MariaDB 10.2+

### Schnellinstallation

1. **Plugin herunterladen**
   ```bash
   git clone https://github.com/ramboeck-it/service-configurator.git
   cd service-configurator
   ```

2. **Als ZIP verpacken** (optional)
   ```bash
   zip -r ramboeck-configurator.zip . -x "*.git*" "node_modules/*" ".*"
   ```

3. **In WordPress installieren**
   ```
   WordPress Admin → Plugins → Installieren → ZIP hochladen
   → ramboeck-configurator.zip → Installieren → Aktivieren
   ```

4. **Shortcode verwenden**
   ```
   [ramboeck_configurator]
   ```

### Via Composer (empfohlen für Entwickler)

```bash
composer require ramboeck-it/service-configurator
```

## 🎯 Verwendung

### Shortcode

Füge den Shortcode auf einer beliebigen Seite ein:

```
[ramboeck_configurator]
```

### Einstellungen

```
WordPress Admin → IT Services → Einstellungen
→ Admin E-Mail konfigurieren
→ Währung wählen (EUR/CHF/USD)
→ Optional: Calendly-Link für Terminbuchung
```

### Services anpassen

```
IT Services → Services
→ Service auswählen → "Bearbeiten"
→ Name, Beschreibung, Preise anpassen
→ Speichern
```

### Design anpassen

```
IT Services → Design
→ 8 Farben mit WordPress Color Picker anpassen
→ Live-Vorschau prüfen
→ Speichern
```

## 🛠️ Entwicklung

### Development Setup

```bash
# Repository klonen
git clone https://github.com/ramboeck-it/service-configurator.git
cd service-configurator

# Composer Dependencies (falls verwendet)
composer install

# NPM Dependencies (falls verwendet)
npm install

# Development Server (optional)
npm run dev
```

### Projekt-Struktur

```
ramboeck-service-configurator/
├── admin/                      # Backend-Seiten
│   ├── leads.php              # Lead-Übersicht
│   ├── services.php           # Service-Verwaltung
│   ├── settings.php           # Einstellungen
│   └── design.php             # Farbkonfigurator
├── assets/
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript
│   └── images/                # Bilder
├── includes/                   # PHP-Klassen (optional)
├── languages/                  # Übersetzungen
├── templates/                  # Frontend-Templates
│   └── configurator.php       # Haupttemplate
├── docs/                       # Dokumentation
├── .github/                    # GitHub-Workflows
├── ramboeck-service-configurator.php  # Hauptdatei
├── README.md
├── CHANGELOG.md
└── LICENSE
```

### Coding Standards

- **PHP**: PSR-12
- **JavaScript**: ESLint + Prettier
- **CSS**: BEM-Notation

### Hooks & Filter

```php
// Filter: Service-Daten vor Anzeige
add_filter('rsc_services', function($services) {
    // Deine Anpassungen
    return $services;
});

// Action: Nach Lead-Speicherung
add_action('rsc_after_lead_saved', function($lead_id, $data) {
    // Deine Aktionen
}, 10, 2);
```

### Testing

```bash
# PHPUnit Tests (wenn implementiert)
composer test

# JavaScript Tests
npm test
```

## 📚 Dokumentation

- **[Installation Guide](docs/installation.md)** - Detaillierte Installationsanleitung
- **[User Guide](docs/user-guide.md)** - Benutzerhandbuch
- **[Developer Guide](docs/developer-guide.md)** - Für Entwickler
- **[API Reference](docs/api.md)** - Hooks & Filter
- **[Troubleshooting](docs/troubleshooting.md)** - Problemlösungen
- **[CHANGELOG](CHANGELOG.md)** - Versionshistorie

## 🤝 Contributing

Wir freuen uns über Contributions! Bitte lies [CONTRIBUTING.md](CONTRIBUTING.md) für Details.

### Workflow

1. Fork das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/AmazingFeature`)
3. Commit deine Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Push zum Branch (`git push origin feature/AmazingFeature`)
5. Öffne einen Pull Request

## 🐛 Bug Reports & Feature Requests

Nutze die [GitHub Issues](https://github.com/ramboeck-it/service-configurator/issues) für:
- 🐛 Bug Reports
- 💡 Feature Requests
- 📖 Dokumentation

## 📋 Roadmap

### v4.2 (geplant)
- [ ] Multi-Step-Form mit Validierung
- [ ] PDF-Export der Konfiguration
- [ ] CRM-Integration (Salesforce, HubSpot)
- [ ] Conditional Logic für Services
- [ ] A/B-Testing Framework

### v5.0 (geplant)
- [ ] Multi-Tenant-Fähigkeit
- [ ] REST API
- [ ] React-basiertes Frontend
- [ ] Block-Editor-Integration (Gutenberg)
- [ ] Advanced Analytics Dashboard

## 📝 Changelog

Siehe [CHANGELOG.md](CHANGELOG.md) für Details zu jeder Version.

## 📄 License

Dieses Projekt ist lizenziert unter der GPL v2 oder höher - siehe [LICENSE](LICENSE) für Details.

## 👤 Autor

**Ramböck IT**
- Website: [ramboeck-it.com](https://ramboeck-it.com)
- GitHub: [@ramboeck-it](https://github.com/ramboeck-it)

## 🙏 Acknowledgments

- WordPress Community
- [PSAppDeployToolkit](https://psappdeploytoolkit.com/) für Inspiration
- Alle Contributors

## 📞 Support

- **E-Mail**: support@ramboeck-it.com
- **Issues**: [GitHub Issues](https://github.com/ramboeck-it/service-configurator/issues)
- **Dokumentation**: [docs/](docs/)

---

**Made with ❤️ by Ramböck IT** | IT Consulting & Managed Services
