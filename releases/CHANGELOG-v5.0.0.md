# 🚀 CHANGELOG v5.0.0 - MAJOR RELEASE

**Release Date:** 26. Oktober 2025
**Version:** 5.0.0 (from 4.2.0)
**Type:** MAJOR - Breaking Changes
**Commits:** 3 major commits (937b874, 9d03918, 5db8fe0)

---

## 🎯 **WHAT'S NEW**

### **Complete Modernization: KERN-PAKET Integration**

Version 5.0.0 ist die größte Aktualisierung des Ramböck IT Service Konfigurators. Der Konfigurator wurde komplett neugestaltet, um das **KERN-PAKET** (All-Inclusive Managed Service Paket) als Hauptangebot zu präsentieren und gleichzeitig individuelle Service-Auswahl zu ermöglichen.

---

## ✨ **MAJOR FEATURES**

### 1. **4-Step Flow (Previously 3 Steps)**

Der Konfigurator verwendet jetzt einen erweiterten 4-Schritt-Prozess:

**Neu:**
- **Step 1:** Company Profile (erweitert mit device/user counts)
- **Step 2:** Package Selection (KERN-PAKET vs Individual) - **NEU!**
- **Step 3:** Service Configuration (ADD-ONs oder Individual Services)
- **Step 4:** Contact & Summary (erweitert mit ROI Calculator)

**Alt (v4.2.0):**
- Step 1: Company Profile
- Step 2: Service Selection
- Step 3: Contact Form

### 2. **KERN-PAKET: All-Inclusive Angebot**

Das KERN-PAKET ist nun das zentrale Angebot:
- ✅ Dynamische Preisberechnung basierend auf Gerätezahl (Staffelpreise)
- ✅ Microsoft 365 Business Standard inkludiert
- ✅ Alle Core-Services enthalten (RMM, Patching, Security, E-Mail, Backup)
- ✅ Live-Pricing mit Tier-Anzeige
- ✅ Feature-Liste direkt aus der Datenbank
- ✅ Garantien-Anzeige
- ✅ Onboarding-Kosten-Berechnung

**Pricing Tiers:**
- 1-4 Geräte: 90€/Gerät
- 5-9 Geräte: 85€/Gerät (-5,6%)
- 10-19 Geräte: 80€/Gerät (-11,1%)
- 20-49 Geräte: 75€/Gerät (-16,7%)
- 50+ Geräte: 70€/Gerät (-22,2%)

**Onboarding Costs:**
- 1-3 Geräte: **Kostenlos**
- 4-9 Geräte: **99€ pro Gerät**
- 10+ Geräte: **Kostenlos**

### 3. **Intelligentes Recommendation System**

Der Konfigurator empfiehlt automatisch das KERN-PAKET, wenn:
- 3 oder mehr Core-Services einzeln ausgewählt werden
- Die Einzelkosten > 110% der KERN-PAKET Kosten sind

**Features:**
- Echtzeit-Berechnung der Ersparnis
- Prominenter "Zum KERN-PAKET wechseln" Button
- Prozentuale und absolute Ersparnis-Anzeige

### 4. **ADD-ONs System**

ADD-ONs können zu beiden Konfigurationen hinzugefügt werden:
- Mobile Device Management (MDM): 5€/Gerät
- Server-Management: 150€/Server
- Erweiterte Backup-Retention (90 Tage): 10€/User
- Premium-Support: 25€/Monat

### 5. **ROI Calculator**

Automatische Berechnung des Return on Investment:
- Zeitersparnis IT-Admin (~4h/Gerät/Monat)
- Vermiedene Ausfallkosten (~3.5h/Gerät/Monat)
- ROI-Prozentsatz nach 12 Monaten
- Business Value Visualization

---

## 🔧 **TECHNICAL CHANGES**

### **Backend (class-ramboeck-service-configurator.php)**

**New Helper Functions:**
```php
calculate_tiered_price($service_id, $quantity)  // Staffelpreis-Berechnung
calculate_onboarding_cost($device_count)         // Onboarding-Kosten
should_recommend_package($service_ids, $count)  // Empfehlungs-Logik
get_package_info($device_count, $user_count)    // KERN-PAKET Info
get_tier_name($device_count)                     // Tier-Beschreibung
```

**New AJAX Handlers:**
```php
ajax_get_package_info()        // Load KERN-PAKET with calculated prices
ajax_check_recommendation()    // Check if package should be recommended
ajax_calculate_pricing()       // Calculate complete configuration pricing
```

**Extended Database Tables:**
- `rsc_services`: New fields (long_description, standalone_price, service_type, features, target_audience)
- `rsc_packages`: NEW table for KERN-PAKET definition
- `rsc_pricing_tiers`: NEW table for staffed pricing

**Real Services Added:**
- 11 echte Ramböck IT Services mit vollständigen Features
- JSON-codierte Features für Skalierbarkeit
- Separate Preise für standalone vs. package-included

### **Frontend Template (configurator.php)**

**File Growth:** 260 → 493 lines (+89%)

**New Step 1 Fields:**
- `device_count` (required) - Für Staffelpreis
- `user_count` (required) - Für M365 Lizenzierung
- `server_count` (optional) - Für Server Management ADD-ON
- `mobile_count` (optional) - Für MDM ADD-ON

**New Step 2: Package Selection**
- KERN-PAKET Card mit Live-Pricing
- Individual Services Card
- "ODER" Divider
- Dynamic feature/guarantee lists
- Onboarding cost display

**New Step 3 Logic:**
- Config type indicator badge
- Conditional service display (ADD-ONs always, services only if individual)
- Recommendation banner (individual config only)
- "Switch to Package" functionality

**New Step 4 Enhancements:**
- Config type summary section
- Detailed pricing breakdown
- ROI calculator component
- Enhanced service list with badges

### **JavaScript (script.js)**

**File Growth:** 555 → 1024 lines (+84%)

**New State Management:**
```javascript
configType: 'package' | 'individual'  // NEW
packageInfo: {...}                      // NEW
selectedAddons: [...]                   // NEW (separate from selectedServices)
deviceCount, userCount, serverCount, mobileCount  // NEW
pricingBreakdown: {...}                 // NEW
```

**New Methods:**
- `loadPackageInfo()` - AJAX load KERN-PAKET
- `renderPackageInfo()` - Display package with pricing
- `selectConfigType(type)` - Handle package/individual selection
- `switchToPackage()` - Switch from individual to package
- `checkRecommendation()` - AJAX check if package recommended
- `calculatePricing()` - AJAX calculate full pricing
- `renderPricingBreakdown()` - Display detailed breakdown
- `renderROI()` - Calculate and display ROI

**New Validations:**
- Step 1: Require device_count & user_count
- Step 2: Require configType selection
- Step 3: Require ≥1 service ONLY if individual config

### **CSS (style.css)**

**File Growth:** 1084 → 1702 lines (+57%)

**618 New Lines of Styles:**

**Step 1:**
- `.rsc-pricing-inputs` - Input section mit Gradient
- `.rsc-form-row` - 2-column grid
- `.rsc-help-icon` - Tooltip icons

**Step 2:**
- `.rsc-package-choice` - 3-column grid layout
- `.rsc-package-card` - Modern cards mit hover effects
- `.rsc-package-recommended` - Orange gradient + glow
- `.rsc-package-badge` - Pulsing "EMPFOHLEN" badge
- `.rsc-package-pricing` - Pricing breakdown
- `.rsc-pricing-total` - Highlighted total
- `.rsc-package-divider` - "ODER" circular divider

**Step 3:**
- `.rsc-config-badge` - Package/Individual badge
- `.rsc-recommendation-banner` - Orange alert box
- `.rsc-service-features` - Feature lists in cards

**Step 4:**
- `.rsc-roi-calculator` - Green ROI box
- `.rsc-pricing-breakdown` - Detailed breakdown
- `.rsc-pricing-subtotal` - Highlighted subtotals

**Animations:**
- `pulse-glow` - Pulsing shadow for badges

**Responsive:**
- Mobile breakpoints for all new components
- Progress steps: 4 col → 2x2 → 1 col
- Stack all flex layouts on mobile

---

## 💾 **DATABASE CHANGES**

### **rsc_services Table (Extended)**
```sql
-- New Columns:
long_description TEXT
standalone_price DECIMAL(10,2)  -- Premium price for individual booking
service_type VARCHAR(50)        -- 'core', 'standalone', 'addon'
package_only TINYINT(1)         -- 1 if only in KERN-PAKET
features TEXT                   -- JSON-encoded features
target_audience TEXT            -- Target customer description
icon VARCHAR(50)                -- Icon identifier
```

### **rsc_packages Table (NEW)**
```sql
CREATE TABLE rsc_packages (
    id MEDIUMINT(9) AUTO_INCREMENT,
    package_key VARCHAR(50) NOT NULL,
    name VARCHAR(200) NOT NULL,
    tagline TEXT,
    description TEXT,
    included_services TEXT,  -- JSON: Service IDs
    features TEXT,           -- JSON: Feature list
    guarantees TEXT,         -- JSON: Guarantee list
    is_active TINYINT(1),
    sort_order INT,
    created_at DATETIME,
    PRIMARY KEY (id),
    UNIQUE KEY package_key (package_key)
);
```

### **rsc_pricing_tiers Table (NEW)**
```sql
CREATE TABLE rsc_pricing_tiers (
    id MEDIUMINT(9) AUTO_INCREMENT,
    service_id MEDIUMINT(9) NOT NULL,
    min_quantity INT NOT NULL,
    max_quantity INT,
    price_per_unit DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2),
    PRIMARY KEY (id)
);
```

**Seed Data:**
- 11 Real Ramböck IT Services
- 1 KERN-PAKET Definition
- 5 Pricing Tiers (90€ → 70€)

---

## ⚠️ **BREAKING CHANGES**

### 1. **Template Changes**
- **Old:** 3-step flow
- **New:** 4-step flow
- **Impact:** Custom templates müssen aktualisiert werden

### 2. **JavaScript API Changes**
- **Removed:** Direct service toggling without config type
- **Added:** Config type selection required before service selection
- **Impact:** Custom JS integrations müssen angepasst werden

### 3. **Database Schema**
- **Added:** 2 new tables (`rsc_packages`, `rsc_pricing_tiers`)
- **Modified:** `rsc_services` table (7 new columns)
- **Impact:** Automatisches Upgrade via `dbDelta()` beim Plugin-Aktivieren

### 4. **CSS Class Changes**
- **Removed:** None (backward compatible)
- **Added:** 50+ new CSS classes
- **Impact:** Keine - alte Klassen bleiben funktional

### 5. **AJAX Endpoints**
- **Added:** 3 new endpoints
- **Modified:** `rsc_get_services` now decodes JSON fields
- **Impact:** Keine - alte Endpoints bleiben kompatibel

---

## 📦 **UPGRADE INSTRUCTIONS**

### **From v4.2.0 → v5.0.0**

1. **Backup Database:**
   ```sql
   mysqldump -u user -p database_name > backup-before-v5.sql
   ```

2. **Deactivate Plugin:**
   - WordPress Admin → Plugins → Deactivate "Ramböck IT Service Konfigurator"

3. **Upload New Files:**
   - Replace all plugin files with v5.0.0 version
   - OR: Update via Git if repository-based

4. **Activate Plugin:**
   - WordPress Admin → Plugins → Activate "Ramböck IT Service Konfigurator"
   - Database migrations run automatically via `dbDelta()`

5. **Verify Installation:**
   - Check plugin version: Should show "5.0.0"
   - Test configurator frontend: All 4 steps should be visible
   - Check database tables: `rsc_packages` and `rsc_pricing_tiers` should exist

### **Migration Notes**

**Automatic:**
- ✅ Database schema updates
- ✅ New tables created
- ✅ Default KERN-PAKET inserted
- ✅ Pricing tiers inserted
- ✅ Real services with features inserted

**Manual:**
- ⚠️ Existing custom services: Review and add new fields manually
- ⚠️ Custom CSS: May need adjustment for new classes
- ⚠️ Custom JS: May need refactoring for 4-step flow

---

## 🐛 **BUG FIXES**

- Fixed: Services not displaying features in frontend
- Fixed: Currency code validation (RangeError from v4.x)
- Fixed: Progress bar calculation for 4 steps
- Improved: Error handling in all AJAX requests
- Improved: Mobile responsiveness across all steps

---

## 🎨 **UI/UX IMPROVEMENTS**

- **Modern Design:** Complete visual overhaul with rounded corners, shadows, gradients
- **Animations:** Smooth transitions, pulsing badges, hover effects
- **Responsive:** Perfect display on all devices (desktop, tablet, mobile)
- **Accessibility:** Better focus states, ARIA labels, keyboard navigation
- **Performance:** Optimized AJAX calls, lazy loading of services
- **User Guidance:** Clear step indicators, inline help text, tooltips

---

## 📊 **PERFORMANCE**

**File Sizes:**
- PHP: +579 lines (Backend logic)
- Template: +233 lines (4-step flow)
- JavaScript: +469 lines (Package logic)
- CSS: +618 lines (New components)

**AJAX Calls:**
- Step 1 → Step 2: +1 call (`rsc_get_package_info`)
- Step 3 (Individual): +1 call (`rsc_check_recommendation`) per selection
- Step 3 → Step 4: +1 call (`rsc_calculate_pricing`)

**Database Queries:**
- Package info: 1 query (cached in JS)
- Recommendation: 2-3 queries (services + pricing tiers)
- Pricing calculation: 3-5 queries (base + addons + onboarding)

---

## 🔮 **FUTURE ENHANCEMENTS**

Planned for v5.1.0:
- Advanced ADD-ON configurator (quantity selection)
- Multi-location support with separate pricing
- Contract term selection (12/24/36 months) with discounts
- Export configuration as PDF
- Email integration for automated quotes
- Admin dashboard for managing packages
- Analytics integration (track popular configurations)

---

## 🙏 **CREDITS**

**Developed by:** Claude Code (Anthropic)
**For:** Ramböck IT (ramboeck-it.com)
**Project:** WordPress Service Configurator v5.0.0

**Technologies:**
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- jQuery 3.x
- Modern CSS3 (Grid, Flexbox, Custom Properties)

---

## 📝 **CHANGELOG SUMMARY**

```
v5.0.0 (2025-10-26)
  - MAJOR: Complete rewrite for KERN-PAKET integration
  - FEATURE: 4-step flow (was 3 steps)
  - FEATURE: Package selection (KERN-PAKET vs Individual)
  - FEATURE: Intelligent recommendation system
  - FEATURE: ROI calculator
  - FEATURE: ADD-ONs system
  - FEATURE: Staffed pricing (90€ → 70€)
  - FEATURE: Onboarding cost calculator
  - DATABASE: 2 new tables (packages, pricing_tiers)
  - DATABASE: Extended services table (7 new columns)
  - DATABASE: 11 real Ramböck IT services
  - UI: Complete visual modernization
  - UI: 618 new lines of CSS
  - JS: Complete rewrite (1024 lines)
  - PHP: 7 new helper functions
  - PHP: 3 new AJAX handlers
  - BREAKING: 4-step flow instead of 3
  - BREAKING: Config type selection required
```

---

**For detailed installation instructions, see:** [UPGRADE-v5.0.0.md](./UPGRADE-v5.0.0.md)

**For support:** GitHub Issues - https://github.com/cramboeck/wordpress/issues
