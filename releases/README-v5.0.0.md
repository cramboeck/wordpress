# 🚀 Ramböck IT Service Konfigurator v5.0.0

**Release Date:** 26. Oktober 2025
**Version:** 5.0.0 (MAJOR)
**Status:** Production Ready ✅

---

## 📋 **QUICK OVERVIEW**

Version 5.0.0 ist die umfassendste Aktualisierung des Service Konfigurators:

### **Highlights**

✅ **4-Step Flow** - Erweiterter Konfigurations-Prozess
✅ **KERN-PAKET Integration** - All-Inclusive Managed Service Angebot
✅ **Intelligent Recommendations** - Automatische Paket-Empfehlungen
✅ **Staffelpreise** - Dynamische Preisberechnung (90€ → 70€)
✅ **ROI Calculator** - Business Value Visualisierung
✅ **ADD-ONs System** - Flexible Erweiterungen
✅ **Modern UI** - Komplette visuelle Überarbeitung

---

## 📦 **INSTALLATION**

### **Neue Installation**

1. Download: `ramboeck-service-configurator-v5.0.0.zip`
2. WordPress Admin → Plugins → Add New → Upload Plugin
3. ZIP hochladen und installieren
4. Aktivieren
5. Shortcode einfügen: `[ramboeck_configurator]`

### **Upgrade von v4.2.0**

**Siehe:** [UPGRADE-v5.0.0.md](./UPGRADE-v5.0.0.md)

---

## 🎯 **FEATURES**

### **1. 4-Step Configurator Flow**

**Step 1: Company Profile**
- Industry Selection
- Company Size, Locations
- **NEU:** Device Count (für Staffelpreise)
- **NEU:** User Count (für M365 Lizenzierung)
- **NEU:** Server & Mobile Counts (für ADD-ON Empfehlungen)

**Step 2: Package Selection** ⭐ NEU
- **KERN-PAKET:** All-Inclusive mit Live-Pricing
- **Individual Services:** Einzeln zusammenstellen
- Dynamic Feature Lists
- Onboarding Cost Display

**Step 3: Service Configuration**
- ADD-ONs (immer verfügbar)
- Individual Services (nur bei Individual-Config)
- **NEU:** Recommendation Banner (wenn Paket günstiger)
- **NEU:** "Switch to Package" Button

**Step 4: Contact & Summary**
- Enhanced Summary mit Config Type
- Detailed Pricing Breakdown
- **NEU:** ROI Calculator
- **NEU:** Business Value Metrics

### **2. KERN-PAKET**

**Was ist das KERN-PAKET?**

All-Inclusive Managed Service Paket mit:
- ✅ Microsoft 365 Business Standard (11,70€/User)
- ✅ 24/7 RMM Monitoring
- ✅ Automatisches Patchmanagement
- ✅ Bitdefender Endpoint Security
- ✅ Hornetsecurity E-Mail Security + Archivierung
- ✅ Veeam Backup für M365 (30 Tage)
- ✅ Unbegrenzter Remote-Support
- ✅ Quartals-Vor-Ort-Check
- ✅ Wartungs-Credits (5h pro 10 Geräte/Jahr)

**Staffelpreise:**
```
1-4 Geräte:   90€/Gerät/Monat
5-9 Geräte:   85€/Gerät/Monat (-5,6%)
10-19 Geräte: 80€/Gerät/Monat (-11,1%)
20-49 Geräte: 75€/Gerät/Monat (-16,7%)
50+ Geräte:   70€/Gerät/Monat (-22,2%)
```

**Onboarding-Kosten:**
```
1-3 Geräte:  KOSTENLOS
4-9 Geräte:  99€ pro Gerät (ab 4. Gerät)
10+ Geräte:  KOSTENLOS
```

### **3. ADD-ONs**

Erweiterbare Services für beide Konfigurationen:

- **MDM** (Mobile Device Management): 5€/Gerät
- **Server-Management**: 150€/Server
- **Erweiterte Backup-Retention** (90 Tage): 10€/User
- **Premium-Support** (Extended Hours): 25€/Monat

### **4. Intelligent Recommendations**

Der Konfigurator empfiehlt automatisch das KERN-PAKET wenn:
- ≥3 Core-Services einzeln ausgewählt werden
- Einzelkosten > 110% der KERN-PAKET Kosten

**Zeigt:**
- Absolute Ersparnis in €
- Prozentuale Ersparnis
- "Zum KERN-PAKET wechseln" Button

### **5. ROI Calculator**

Automatische Berechnung:
- **Zeitersparnis:** ~4h/Gerät/Monat (IT-Admin)
- **Ausfallvermeidung:** ~3.5h/Gerät/Monat
- **Business Value:** Basierend auf 50€/h
- **ROI nach 12 Monaten:** Prozentuale Darstellung

---

## 🛠️ **TECHNICAL SPECS**

### **Requirements**

- **WordPress:** 5.0+ (Empfohlen: 6.0+)
- **PHP:** 7.4+ (Empfohlen: 8.0+)
- **MySQL:** 5.7+ (Empfohlen: 8.0+)
- **jQuery:** 3.x (included in WordPress)

### **Database Tables**

```
wp_rsc_services         - Service catalog (11 services)
wp_rsc_leads            - Customer inquiries
wp_rsc_industry_presets - Industry templates (10 industries)
wp_rsc_packages         - Package definitions (1 KERN-PAKET) ⭐ NEW
wp_rsc_pricing_tiers    - Staffed pricing (5 tiers) ⭐ NEW
```

### **File Structure**

```
ramboeck-service-configurator/
├── ramboeck-service-configurator.php  (Main Plugin File)
├── includes/
│   └── class-ramboeck-service-configurator.php  (Core Class)
├── templates/
│   └── configurator.php  (Frontend Template - 4 Steps)
├── assets/
│   ├── css/
│   │   ├── style.css       (1702 lines - Frontend Styles)
│   │   └── admin.css       (Admin Styles)
│   └── js/
│       ├── script.js       (1024 lines - Frontend Logic)
│       └── admin.js        (Admin Scripts)
└── admin/
    ├── leads.php           (Leads Management)
    ├── services.php        (Service Management)
    ├── industries.php      (Industry Management)
    └── settings.php        (Plugin Settings)
```

### **File Sizes**

- **Total Plugin:** ~250 KB (uncompressed)
- **CSS:** ~85 KB
- **JavaScript:** ~45 KB
- **PHP:** ~120 KB

### **AJAX Endpoints**

```php
rsc_get_services              // Load available services
rsc_submit_configuration      // Submit inquiry
rsc_get_package_info          // Load KERN-PAKET with pricing ⭐ NEW
rsc_check_recommendation      // Check if package recommended ⭐ NEW
rsc_calculate_pricing         // Calculate full configuration ⭐ NEW
```

---

## 📸 **SCREENSHOTS**

*(Coming soon - add screenshots of each step)*

---

## 🎨 **CUSTOMIZATION**

### **Colors**

Default Brand Colors:
```php
Primary:   #F27024 (Orange)
Secondary: #36313E (Navy)
Success:   #10b981 (Green)
```

Change via WordPress Admin:
- IT Konfigurator → Einstellungen → Farben

### **Services**

Add/Edit services:
- IT Konfigurator → Services → Add New

Required fields:
- Name, Description, Monthly Price, Service Type

Optional fields:
- Long Description, Features (JSON), Icon, Target Audience

### **Package Customization**

Edit KERN-PAKET:
```sql
UPDATE wp_rsc_packages
SET name = 'Your Custom Package Name',
    tagline = 'Your Custom Tagline'
WHERE package_key = 'kern-paket';
```

### **Pricing Tiers**

Adjust staffed pricing:
```sql
UPDATE wp_rsc_pricing_tiers
SET price_per_unit = 85.00
WHERE service_id = 1 AND min_quantity = 1;
```

---

## 🚀 **USAGE**

### **Basic Shortcode**

```php
[ramboeck_configurator]
```

### **With Custom Attributes**

```php
[ramboeck_configurator
    title="Konfigurator"
    subtitle="Ihre maßgeschneiderte IT-Lösung"]
```

### **In Template Files**

```php
<?php echo do_shortcode('[ramboeck_configurator]'); ?>
```

---

## 📊 **ANALYTICS**

### **Tracked Metrics**

All inquiries are stored in `wp_rsc_leads` with:
- Customer contact info
- Selected industry & company size
- Config type (package or individual)
- Selected services/ADD-ONs
- Device/User counts
- Pricing breakdown
- Timestamp

### **View Leads**

WordPress Admin → IT Konfigurator → Anfragen

---

## 🐛 **KNOWN ISSUES**

None at release.

For bug reports: https://github.com/cramboeck/wordpress/issues

---

## 🔄 **VERSION HISTORY**

| Version | Date | Description |
|---------|------|-------------|
| **5.0.0** | 2025-10-26 | MAJOR: KERN-PAKET integration, 4-step flow, ROI calculator |
| 4.2.0 | 2025-10-26 | Database foundation for v5.0 |
| 4.1.4 | 2025-10-25 | Server-side currency validation |
| 4.1.3 | 2025-10-25 | Critical service loading fix |
| 4.1.2 | 2025-10-24 | Edge browser compatibility |
| 4.1.1 | 2025-10-24 | Industry selection improvements |
| 4.1.0 | 2025-10-23 | Professional plugin foundation |

---

## 📚 **DOCUMENTATION**

- **CHANGELOG:** [CHANGELOG-v5.0.0.md](./CHANGELOG-v5.0.0.md)
- **UPGRADE GUIDE:** [UPGRADE-v5.0.0.md](./UPGRADE-v5.0.0.md)
- **GitHub:** https://github.com/cramboeck/wordpress

---

## 👨‍💻 **DEVELOPMENT**

### **Contributing**

1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

### **Testing**

```bash
# Run WordPress coding standards
phpcs --standard=WordPress includes/

# Run JavaScript tests
npm test

# Manual testing checklist in TESTING.md
```

---

## 📄 **LICENSE**

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

---

## 🙏 **CREDITS**

**Developed with:** Claude Code (Anthropic AI Assistant)
**For:** Ramböck IT GmbH
**Website:** https://ramboeck-it.com
**Developer:** Christian Ramböck

**Technologies:**
- WordPress Plugin API
- Modern PHP (OOP, Singleton Pattern)
- jQuery & Vanilla JavaScript
- Modern CSS3 (Grid, Flexbox, Custom Properties)
- MySQL with dbDelta migrations

---

## 📞 **SUPPORT**

**Email:** support@ramboeck-it.com
**GitHub Issues:** https://github.com/cramboeck/wordpress/issues
**Documentation:** This file + CHANGELOG + UPGRADE guide

---

**Version 5.0.0** - Built with ❤️ for Ramböck IT

*Generated with Claude Code - https://claude.com/claude-code*
