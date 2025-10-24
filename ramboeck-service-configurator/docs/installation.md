# Installation Guide

## Voraussetzungen

- WordPress 5.0 oder höher
- PHP 7.4 oder höher
- MySQL 5.7+ oder MariaDB 10.2+
- Empfohlen: HTTPS-Verbindung

## Installation

### Methode 1: Via WordPress Admin (Empfohlen)

1. ZIP herunterladen
2. WordPress Admin → Plugins → Installieren → ZIP hochladen
3. Aktivieren
4. Fertig!

### Methode 2: Via FTP

1. ZIP entpacken
2. Ordner via FTP nach `/wp-content/plugins/` hochladen
3. WordPress Admin → Plugins → Aktivieren

### Methode 3: Via WP-CLI

```bash
wp plugin install ramboeck-configurator.zip --activate
```

## Erste Schritte

1. **Services prüfen**: IT Services → Services
2. **Einstellungen**: IT Services → Einstellungen
3. **Shortcode verwenden**: `[ramboeck_configurator]`

## Troubleshooting

Siehe [Troubleshooting Guide](troubleshooting.md)
