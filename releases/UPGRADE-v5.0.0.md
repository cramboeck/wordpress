# 📦 Upgrade Guide: v5.0.0

**Version:** 5.0.0
**From:** 4.2.0
**Type:** MAJOR UPGRADE
**Release Date:** 26. Oktober 2025

---

## ⚠️ WICHTIG: Vor dem Upgrade

### **Breaking Changes**

Diese Version enthält **Breaking Changes**:
- ✅ 4-Step Flow (war 3 Steps)
- ✅ Neue Datenbank-Tabellen
- ✅ Erweiterte Services-Tabelle
- ✅ Neue AJAX Endpoints
- ✅ Template-Änderungen

**Empfehlung:** Testen Sie das Upgrade zuerst in einer Staging-Umgebung!

---

## 🔄 **UPGRADE-PROZESS**

### **Option A: Automatisches Upgrade (Empfohlen)**

1. **Backup erstellen:**
   ```bash
   # Database Backup
   cd /path/to/wordpress
   wp db export backup-before-v5.0.0.sql

   # Files Backup
   cd wp-content/plugins
   cp -r ramboeck-service-configurator ramboeck-service-configurator-backup-v4.2.0
   ```

2. **Plugin deaktivieren:**
   - WordPress Admin → Plugins
   - "Ramböck IT Service Konfigurator" → Deaktivieren

3. **Neue Version hochladen:**
   ```bash
   cd wp-content/plugins
   rm -rf ramboeck-service-configurator
   unzip /path/to/ramboeck-service-configurator-v5.0.0.zip
   ```

4. **Plugin aktivieren:**
   - WordPress Admin → Plugins
   - "Ramböck IT Service Konfigurator" → Aktivieren
   - **Database Migration läuft automatisch!**

5. **Verifizierung:**
   - Version check: Sollte "5.0.0" anzeigen
   - Frontend test: [domain]/konfigurator/ aufrufen
   - Alle 4 Steps sollten sichtbar sein

### **Option B: Git Upgrade (für Entwickler)**

```bash
cd wp-content/plugins/ramboeck-service-configurator

# Backup current version
git stash save "Backup before v5.0.0 upgrade"

# Pull new version
git fetch origin
git checkout claude/service-configurator-update-011CUSfeYruvF1WVbE2VoYbP
git pull origin claude/service-configurator-update-011CUSfeYruvF1WVbE2VoYbP

# Deactivate/Reactivate via WordPress Admin
# Database migration runs on reactivation
```

---

## 🗄️ **DATABASE MIGRATION**

### **Was passiert automatisch?**

Bei der Plugin-Aktivierung führt `RamboeckServiceConfigurator::activate()` folgende Schritte aus:

1. **Tabelle `rsc_services` erweitern:**
   ```sql
   ALTER TABLE wp_rsc_services
   ADD COLUMN long_description TEXT,
   ADD COLUMN standalone_price DECIMAL(10,2) DEFAULT NULL,
   ADD COLUMN service_type VARCHAR(50) DEFAULT 'standalone',
   ADD COLUMN package_only TINYINT(1) DEFAULT 0,
   ADD COLUMN features TEXT,
   ADD COLUMN target_audience TEXT,
   ADD COLUMN icon VARCHAR(50);
   ```

2. **Neue Tabelle `rsc_packages` erstellen:**
   ```sql
   CREATE TABLE IF NOT EXISTS wp_rsc_packages (
       id mediumint(9) NOT NULL AUTO_INCREMENT,
       package_key varchar(50) NOT NULL,
       name varchar(200) NOT NULL,
       tagline text,
       description text,
       included_services text,
       features text,
       guarantees text,
       is_active tinyint(1) DEFAULT 1,
       sort_order int NOT NULL DEFAULT 0,
       created_at datetime DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (id),
       UNIQUE KEY package_key (package_key)
   );
   ```

3. **Neue Tabelle `rsc_pricing_tiers` erstellen:**
   ```sql
   CREATE TABLE IF NOT EXISTS wp_rsc_pricing_tiers (
       id mediumint(9) NOT NULL AUTO_INCREMENT,
       service_id mediumint(9) NOT NULL,
       min_quantity int NOT NULL,
       max_quantity int,
       price_per_unit decimal(10,2) NOT NULL,
       discount_percent decimal(5,2) DEFAULT 0.00,
       PRIMARY KEY (id)
   );
   ```

4. **Default-Daten einfügen:**
   - 11 echte Ramböck IT Services (wenn Tabelle leer)
   - 1 KERN-PAKET Definition
   - 5 Pricing Tiers (90€ → 70€)

### **Manuelle Überprüfung nach Migration**

```sql
-- Check new tables exist
SHOW TABLES LIKE 'wp_rsc_%';
-- Should show: rsc_services, rsc_leads, rsc_industry_presets, rsc_packages, rsc_pricing_tiers

-- Check KERN-PAKET inserted
SELECT * FROM wp_rsc_packages WHERE package_key = 'kern-paket';

-- Check pricing tiers inserted
SELECT * FROM wp_rsc_pricing_tiers WHERE service_id = 1;
-- Should show 5 rows (90€, 85€, 80€, 75€, 70€)

-- Check services have new columns
DESCRIBE wp_rsc_services;
-- Should include: long_description, standalone_price, service_type, features, etc.
```

---

## 🔧 **KOMPATIBILITÄT**

### **WordPress Version**
- **Minimum:** WordPress 5.0
- **Empfohlen:** WordPress 6.0+
- **Getestet bis:** WordPress 6.4

### **PHP Version**
- **Minimum:** PHP 7.4
- **Empfohlen:** PHP 8.0+
- **Getestet bis:** PHP 8.2

### **MySQL Version**
- **Minimum:** MySQL 5.7 oder MariaDB 10.2
- **Empfohlen:** MySQL 8.0+ oder MariaDB 10.6+

### **Browser Support**
- Chrome 90+ ✅
- Firefox 88+ ✅
- Safari 14+ ✅
- Edge 90+ ✅
- Mobile browsers ✅

---

## 🐛 **TROUBLESHOOTING**

### **Problem: Services werden nicht angezeigt**

**Lösung:**
```sql
-- Check if services table has data
SELECT COUNT(*) FROM wp_rsc_services WHERE is_active = 1;

-- If empty, manually re-run seed:
-- 1. Deactivate plugin
-- 2. Truncate table: TRUNCATE TABLE wp_rsc_services;
-- 3. Reactivate plugin (triggers seed)
```

### **Problem: KERN-PAKET lädt nicht**

**Lösung:**
```sql
-- Check if package exists
SELECT * FROM wp_rsc_packages;

-- If empty, insert manually:
INSERT INTO wp_rsc_packages (package_key, name, tagline, description, included_services, is_active)
VALUES ('kern-paket', 'KERN-PAKET: Rundum-Sorglos-Betreuung',
        'Ihre IT - komplett betreut, keine Überraschungen',
        'Alles aus einer Hand: Microsoft 365, komplettes Monitoring, Security, Backup und unbegrenzter Support',
        '1,2', 1);
```

### **Problem: Preisberechnung funktioniert nicht**

**Lösung:**
```sql
-- Check if pricing tiers exist
SELECT * FROM wp_rsc_pricing_tiers WHERE service_id = 1;

-- If empty, insert manually:
INSERT INTO wp_rsc_pricing_tiers (service_id, min_quantity, max_quantity, price_per_unit, discount_percent) VALUES
(1, 1, 4, 90.00, 0.00),
(1, 5, 9, 85.00, 5.56),
(1, 10, 19, 80.00, 11.11),
(1, 20, 49, 75.00, 16.67),
(1, 50, NULL, 70.00, 22.22);
```

### **Problem: JavaScript Errors in Console**

**Lösung:**
1. Hard-Refresh browser (Ctrl+Shift+R oder Cmd+Shift+R)
2. Clear browser cache
3. Check if jQuery is loaded:
   ```javascript
   console.log(typeof jQuery); // Should show "function"
   ```
4. Check if rscData is defined:
   ```javascript
   console.log(rscData); // Should show object with ajaxUrl, nonce, etc.
   ```

### **Problem: CSS sieht kaputt aus**

**Lösung:**
1. Check if CSS file is loaded:
   - DevTools → Network → Filter "style.css"
   - Should show 200 OK status
2. Clear WordPress cache (if caching plugin installed)
3. Hard-refresh browser
4. Check file permissions:
   ```bash
   chmod 644 wp-content/plugins/ramboeck-service-configurator/assets/css/style.css
   ```

---

## 🔙 **ROLLBACK (Falls nötig)**

Falls etwas schief geht, können Sie auf v4.2.0 zurückrollen:

### **Option A: Backup wiederherstellen**

```bash
# 1. Plugin deaktivieren (WordPress Admin)

# 2. Dateien wiederherstellen
cd wp-content/plugins
rm -rf ramboeck-service-configurator
mv ramboeck-service-configurator-backup-v4.2.0 ramboeck-service-configurator

# 3. Database wiederherstellen
wp db import backup-before-v5.0.0.sql

# 4. Plugin aktivieren (WordPress Admin)
```

### **Option B: Selektiver Rollback (behält neue Daten)**

```sql
-- Keep new tables but restore old structure
-- NOTE: This will lose v5.0 data!

-- Drop new tables
DROP TABLE IF EXISTS wp_rsc_packages;
DROP TABLE IF EXISTS wp_rsc_pricing_tiers;

-- Remove new columns from services
ALTER TABLE wp_rsc_services
DROP COLUMN long_description,
DROP COLUMN standalone_price,
DROP COLUMN service_type,
DROP COLUMN package_only,
DROP COLUMN features,
DROP COLUMN target_audience,
DROP COLUMN icon;
```

**⚠️ ACHTUNG:** Selektiver Rollback führt zum Datenverlust der v5.0-Features!

---

## ✅ **POST-UPGRADE CHECKLIST**

Nach dem Upgrade prüfen:

- [ ] **Plugin Version:** Sollte "5.0.0" in WordPress Admin anzeigen
- [ ] **Frontend Test:** Konfigurator aufrufen und alle 4 Steps durchgehen
  - [ ] Step 1: Industry Selection funktioniert
  - [ ] Step 1: Device/User Count Inputs sichtbar
  - [ ] Step 2: KERN-PAKET wird angezeigt mit Preisen
  - [ ] Step 2: "Individuelle Services" Button funktioniert
  - [ ] Step 3: ADD-ONs werden angezeigt
  - [ ] Step 3: Recommendation Banner erscheint (bei Individual + 3+ Services)
  - [ ] Step 4: Summary zeigt alle Daten korrekt
  - [ ] Step 4: ROI Calculator zeigt Werte
- [ ] **Database Test:**
  - [ ] `rsc_packages` Tabelle existiert
  - [ ] `rsc_pricing_tiers` Tabelle existiert
  - [ ] Services haben neue Felder (features, standalone_price, etc.)
- [ ] **AJAX Test:**
  - [ ] Browser Console: Keine Errors
  - [ ] Network Tab: Alle AJAX calls erfolgreich (200 OK)
- [ ] **Mobile Test:**
  - [ ] Responsive Design funktioniert auf Smartphone
  - [ ] Alle Buttons sind klickbar
  - [ ] Text ist lesbar (kein Overflow)

---

## 📞 **SUPPORT**

Bei Problemen:

1. **GitHub Issues:** https://github.com/cramboeck/wordpress/issues
2. **Email:** support@ramboeck-it.com
3. **Dokumentation:** README.md im Plugin-Ordner

---

## 📚 **WEITERE RESSOURCEN**

- [CHANGELOG-v5.0.0.md](./CHANGELOG-v5.0.0.md) - Vollständige Änderungsliste
- [README.md](./README.md) - Allgemeine Dokumentation
- [GitHub Repository](https://github.com/cramboeck/wordpress) - Source Code

---

**Happy Upgrading! 🚀**

*Generated with Claude Code - https://claude.com/claude-code*
