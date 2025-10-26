# 🔄 Upgrade-Anleitung: v4.1.x → v4.2.0

## ⚠️ WICHTIG: Datenbank-Update

Version 4.2.0 enthält **wichtige Datenbank-Änderungen** zur Vorbereitung des modernisierten Konfigurators.

---

## 📋 Was ist neu?

### Datenbank-Erweiterungen:

**Neue Felder in `rsc_services`:**
- `long_description` - Ausführliche Beschreibung für expandable Cards
- `standalone_price` - Preis wenn Service einzeln gebucht wird
- `service_type` - Type: core, standalone, addon
- `package_only` - Ob Service nur im Paket verfügbar ist
- `features` - JSON-Array mit detaillierten Features
- `target_audience` - Zielgruppen-Beschreibung
- `icon` - Icon-Identifier

**Neue Tabellen:**
- `rsc_packages` - Definition von Service-Paketen (z.B. KERN-PAKET)
- `rsc_pricing_tiers` - Staffelpreise (z.B. 1-4 Geräte: 90€, 5-9: 85€)

### Neue Service-Daten:

Version 4.2.0 enthält die **echten Ramböck IT Services**:
- Managed Service Pauschale mit Staffelpreisen
- Microsoft 365 Business Standard
- RMM Monitoring, Patchmanagement, Security (einzeln oder im Paket)
- ADD-ONs: MDM, Server-Management, Extended Backup, Premium Support

---

## 🚀 Upgrade-Schritte

### Option 1: WordPress Admin (Empfohlen)

1. **Backup erstellen** (optional aber empfohlen)
   ```
   WordPress → Plugins → Ramböck Configurator → Deaktivieren
   ```

2. **Neue Version hochladen**
   ```
   Plugins → Installieren → Plugin hochladen
   → ramboeck-service-configurator-v4.2.0-*.zip auswählen
   ```

3. **Plugin aktivieren**
   ```
   WordPress → Plugins → Ramböck Configurator → Aktivieren
   ```

4. **Automatische Datenbank-Migration**
   - Beim Aktivieren werden automatisch alle neuen Tabellen und Felder angelegt
   - Ihre bestehenden Daten (Leads, alte Services) bleiben erhalten
   - Neue Services werden zusätzlich eingefügt

### Option 2: FTP/SFTP

1. **Backup des alten Plugins** (optional)
   ```
   /wp-content/plugins/ramboeck-service-configurator/ → sichern
   ```

2. **ZIP entpacken und hochladen**
   ```
   Ordner nach: /wp-content/plugins/ramboeck-service-configurator/
   ```

3. **Plugin in WordPress aktivieren**
   - Die Datenbank wird beim Aktivieren automatisch aktualisiert

---

## ✅ Nach dem Update prüfen:

1. **Services prüfen** (Admin-Bereich)
   ```
   WordPress Admin → Ramböck Configurator → Services
   ```
   - Es sollten jetzt 11 Services zu sehen sein
   - Neue Felder wie "Features", "Standalone Preis" etc.

2. **Packages prüfen** (Datenbank)
   ```sql
   SELECT * FROM wp_rsc_packages;
   ```
   - KERN-PAKET sollte vorhanden sein

3. **Pricing Tiers prüfen** (Datenbank)
   ```sql
   SELECT * FROM wp_rsc_pricing_tiers;
   ```
   - 5 Staffelpreise für Managed Service sollten vorhanden sein

---

## 🔍 Bekannte Änderungen:

### Was funktioniert:
✅ Alte Services bleiben erhalten (aber inaktiv)
✅ Leads bleiben vollständig erhalten
✅ Einstellungen bleiben erhalten
✅ Frontend-Konfigurator funktioniert weiterhin (alte Version)

### Was noch NICHT verfügbar ist:
⏳ Neuer 4-Schritt-Flow (kommt in v5.0.0)
⏳ KERN-PAKET Auswahl im Frontend (kommt in v5.0.0)
⏳ Expandable Service-Details (kommt in v5.0.0)
⏳ Intelligente Paket-Empfehlungen (kommt in v5.0.0)
⏳ Staffelpreis-Berechnung im Frontend (kommt in v5.0.0)

---

## 🐛 Troubleshooting

### Problem: "Database error" nach Update

**Lösung:**
```
1. Plugin deaktivieren
2. Plugin wieder aktivieren
   → Triggert dbDelta erneut
```

### Problem: Alte Services werden angezeigt

**Lösung:**
```
Die alten Demo-Services sind noch aktiv.
In v5.0.0 wird es ein Admin-Interface geben um diese zu deaktivieren.
```

### Problem: KERN-PAKET wird nicht angezeigt

**Lösung:**
```
Das KERN-PAKET ist bereits in der Datenbank,
wird aber erst in v5.0.0 im Frontend nutzbar sein.
Aktuell (v4.2.0) ist es eine Vorbereitung.
```

---

## 📊 Datenbank-Schema-Übersicht

### Erweiterte Tabellen:
```
wp_rsc_services (erweitert)
├─ id
├─ name
├─ description
├─ long_description          [NEU]
├─ tooltip
├─ setup_price
├─ monthly_price
├─ standalone_price          [NEU]
├─ is_active
├─ sort_order
├─ recommended_for
├─ service_type              [NEU]
├─ package_only              [NEU]
├─ features                  [NEU - JSON]
├─ target_audience           [NEU]
├─ icon                      [NEU]
└─ created_at
```

### Neue Tabellen:
```
wp_rsc_packages
├─ id
├─ package_key
├─ name
├─ tagline
├─ description
├─ included_services (JSON)
├─ features (JSON)
├─ guarantees (JSON)
├─ is_active
├─ sort_order
└─ created_at

wp_rsc_pricing_tiers
├─ id
├─ service_id
├─ min_quantity
├─ max_quantity
├─ price_per_unit
└─ discount_percent
```

---

## 🗺️ Roadmap: Was kommt als nächstes?

### v5.0.0 (In Entwicklung)
- 🎯 Komplett neuer 4-Schritt-Konfigurator
- 🌟 KERN-PAKET als Haupt-Option
- 💡 Intelligente Paket-Empfehlungen
- 📊 Staffelpreis-Automatik
- 🎨 Moderne UI mit expandable Cards
- 💰 ROI-Rechner
- 📋 Verbesserte Summary-Seite

---

## 💾 Rollback (Falls nötig)

Wenn Sie zur vorherigen Version zurückkehren möchten:

1. **v4.1.4 erneut installieren**
   ```
   releases/ramboeck-service-configurator-v4.1.4-*.zip
   ```

2. **Datenbank-Bereinigung** (optional)
   ```sql
   -- Neue Felder werden ignoriert, keine Bereinigung nötig
   -- Neue Tabellen können bleiben oder gelöscht werden:
   DROP TABLE IF EXISTS wp_rsc_packages;
   DROP TABLE IF EXISTS wp_rsc_pricing_tiers;
   ```

⚠️ **Hinweis:** Die neuen Felder in `rsc_services` schaden nicht, werden von v4.1.4 einfach ignoriert.

---

## 📞 Support

**Bei Problemen:**
- GitHub Issues: https://github.com/cramboeck/wordpress/issues
- E-Mail: kontakt@ramboeck-it.de

**Logs prüfen:**
- WordPress: `/wp-content/debug.log`
- Browser: F12 → Console

---

**Version:** 4.2.0
**Datum:** 26. Oktober 2024
**Status:** Stable (Vorbereitung für v5.0.0)

*Ramböck IT - Professionelle IT-Betreuung*
