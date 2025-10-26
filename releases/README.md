# 📦 Ramböck IT Service Konfigurator - Releases

Hier finden Sie fertige ZIP-Dateien zum direkten Upload in WordPress.

## 🚀 Installation

### Option 1: Über WordPress Admin (Empfohlen)

1. **Laden Sie die neueste Version herunter:**
   - Siehe unten für die aktuelle Version

2. **In WordPress:**
   ```
   Admin → Plugins → Installieren → Plugin hochladen
   ```

3. **ZIP-Datei auswählen und hochladen**

4. **Plugin aktivieren**

5. **Fertig!** Das Plugin erstellt automatisch:
   - 10 IT-Services
   - 10 Branchen-Presets
   - Alle notwendigen Datenbank-Tabellen

### Option 2: Per FTP/SFTP

1. **ZIP entpacken**

2. **Ordner `ramboeck-service-configurator` hochladen nach:**
   ```
   /wp-content/plugins/ramboeck-service-configurator/
   ```

3. **In WordPress Plugin aktivieren**

---

## 📋 Aktuelle Version

**Version:** 4.1.3
**Datum:** 2025-10-26
**Download:** `ramboeck-service-configurator-v4.1.3-20251026-1426.zip` (46KB)

### Was ist neu in 4.1.3? ⭐ KRITISCHER FIX

✅ **Services Loading Fix:**
- Fixed: RangeError "Invalid currency code: €" behoben
- Fixed: Services werden jetzt korrekt angezeigt (kein endloser Spinner mehr)
- Added: Automatische Konvertierung von Währungssymbolen zu ISO-Codes (€ → EUR)
- Added: Validierung der Währungscodes mit Fallback zu EUR
- Improved: Robustere formatPrice() Funktion

**Dieser Fix behebt das Hauptproblem, dass Services nicht geladen werden konnten!**

### Was war neu in 4.1.2?

✅ **Browser-Kompatibilität:**
- Fixed: Branchen-Auswahl funktioniert jetzt auf Desktop Edge Browser
- Removed: Touch-Event-Handling das Edge-Clicks blockiert hat
- Simplified: Event-Handler für bessere Cross-Browser-Kompatibilität
- Improved: Entfernt preventDefault/stopPropagation die mit Edge interferierten

✅ **Mobile:**
- Bestätigt: Mobile/iPhone-Funktionalität bleibt voll erhalten

### Was war neu in 4.1.1?

✅ **Bugfixes:**
- Fixed: Branchen-Auswahl bleibt jetzt selektiert
- Fixed: Services werden korrekt geladen
- Improved: Umfangreiches Error-Handling und Debugging

✅ **Design:**
- Ultra-moderne, runde UI (16-32px border-radius)
- Glassmorphism-Effekte
- Gradient-Buttons und Text-Effekte
- Smooth Animationen (0.3-0.6s transitions)
- Pulse-Animation bei Auswahl
- 8-Level Shadow-System

✅ **UX-Verbesserungen:**
- Größere, besser klickbare Elemente
- Visuelles Feedback bei jeder Interaktion
- Hover-Effekte mit Lift-Animation
- Automatische Button-Aktivierung

✅ **Debugging:**
- Extensive Console-Logs
- Detaillierte Fehlermeldungen
- PHP error_log Unterstützung
- Bessere AJAX-Error-Handling

---

## 🔄 Update-Prozess

Wenn Sie bereits eine ältere Version installiert haben:

1. **Alte Version deaktivieren** (nicht löschen!)
2. **Neue Version hochladen** (überschreibt die alte)
3. **Plugin aktivieren**
4. **Daten bleiben erhalten** (Services, Leads, Einstellungen)

**Wichtig:** Ihre Daten (Services, Anfragen, Einstellungen) bleiben bei Updates erhalten!

---

## 📝 Version History

| Version | Datum | Download | Größe | Highlights |
|---------|-------|----------|-------|------------|
| 4.1.3 ⭐ | 2025-10-26 | ramboeck-service-configurator-v4.1.3-20251026-1426.zip | 46KB | **Services Loading Fix (RangeError)** |
| 4.1.2 | 2025-10-26 | ramboeck-service-configurator-v4.1.2-20251026-1356.zip | 46KB | Edge Browser Kompatibilität |
| 4.1.1 | 2025-10-26 | ramboeck-service-configurator-v4.1.1-20251026-1345.zip | 44KB | Ultra-modern UI + Bugfixes |
| 4.1.0 | 2025-10-24 | - | - | Initial complete implementation |

---

## 🆘 Support

**Bei Problemen:**

1. **Browser-Console prüfen** (F12 → Console)
2. **WordPress Debug-Log prüfen** (`/wp-content/debug.log`)
3. **Plugin deaktivieren & reaktivieren** (behebt 90% der Probleme)

**Häufige Probleme:**

- **"Keine Services gefunden"** → Plugin reaktivieren
- **"Loading forever"** → Cache leeren (Strg + F5)
- **"Branchen nicht klickbar"** → Version 4.1.1+ verwenden

---

## 🔗 Links

- **GitHub Repository:** https://github.com/cramboeck/wordpress
- **Branch:** claude/service-configurator-update-011CUSfeYruvF1WVbE2VoYbP
- **Dokumentation:** Siehe `ramboeck-service-configurator/README.md`

---

## 📦 Was ist im ZIP enthalten?

```
ramboeck-service-configurator/
├── admin/                    # Admin-Seiten (4 Dateien)
│   ├── leads.php            # Lead-Verwaltung
│   ├── services.php         # Service-Management
│   ├── industries.php       # Branchen-Verwaltung
│   └── settings.php         # Einstellungen
├── assets/
│   ├── css/
│   │   ├── style.css       # Frontend CSS (1083 Zeilen)
│   │   └── admin.css       # Admin CSS
│   └── js/
│       ├── script.js       # Frontend JS (460+ Zeilen)
│       └── admin.js        # Admin JS
├── includes/
│   └── class-ramboeck-service-configurator.php  # Hauptklasse
├── templates/
│   └── configurator.php    # Frontend 3-Schritt-Wizard
├── docs/                    # Dokumentation
├── ramboeck-service-configurator.php  # Haupt-Plugin-Datei
├── readme.txt              # WordPress Plugin Readme
├── LICENSE                 # GPL v2 Lizenz
└── ...

Gesamt: 26 Dateien, ~4.500 Zeilen Code
```

---

## ✅ Systemanforderungen

- **WordPress:** 5.0 oder höher
- **PHP:** 7.4 oder höher
- **MySQL:** 5.6 oder höher
- **Browser:** Moderne Browser (Chrome, Firefox, Safari, Edge)

---

**Erstellt mit ❤️ von Claude Code**
