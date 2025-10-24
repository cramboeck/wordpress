# Contributing to Ramböck Service Konfigurator

Vielen Dank für dein Interesse an diesem Projekt! 🎉

## 🤝 Wie kann ich beitragen?

Es gibt viele Wege, zum Projekt beizutragen:

- 🐛 Bug Reports
- 💡 Feature Requests
- 📖 Dokumentation verbessern
- 💻 Code beitragen
- 🌐 Übersetzungen

## 🐛 Bug Reports

Wenn du einen Bug findest:

1. Prüfe, ob der Bug bereits gemeldet wurde ([Issues](https://github.com/ramboeck-it/service-configurator/issues))
2. Falls nicht, erstelle ein neues Issue mit:
   - Klarer Beschreibung des Problems
   - Schritten zur Reproduktion
   - Erwartetes vs. tatsächliches Verhalten
   - Screenshots (falls hilfreich)
   - WordPress-Version, PHP-Version, Browser

### Bug Report Template

```markdown
**Beschreibung:**
[Kurze Beschreibung des Problems]

**Schritte zur Reproduktion:**
1. Gehe zu...
2. Klicke auf...
3. Scrolle nach...

**Erwartetes Verhalten:**
[Was sollte passieren]

**Tatsächliches Verhalten:**
[Was passiert]

**Environment:**
- WordPress Version: 
- PHP Version:
- Browser:
- Plugin Version:
```

## 💡 Feature Requests

Feature Requests sind willkommen!

1. Prüfe, ob das Feature bereits vorgeschlagen wurde
2. Erstelle ein Issue mit:
   - Klarer Beschreibung des Features
   - Use Case / Problem, das gelöst wird
   - Mögliche Implementierung (optional)

## 💻 Code beitragen

### Setup Development Environment

```bash
# Repository forken & klonen
git clone https://github.com/DEIN-USERNAME/service-configurator.git
cd service-configurator

# Dependencies installieren
composer install  # falls Composer verwendet wird
npm install       # falls Node.js verwendet wird

# Feature-Branch erstellen
git checkout -b feature/dein-feature-name
```

### Coding Standards

#### PHP
- PSR-12 Coding Standard
- WordPress Coding Standards
- Nutze Type Hints (PHP 7.4+)
- Docblocks für Funktionen/Klassen

```php
/**
 * Get all active services
 *
 * @since 4.1.0
 * @return array Array of service objects
 */
public function get_active_services(): array {
    // Code
}
```

#### JavaScript
- ES6+ Syntax
- ESLint-konform
- Kommentare für komplexe Logik

```javascript
/**
 * Load services via AJAX
 * @returns {Promise<Array>} Array of service objects
 */
async function loadServices() {
    // Code
}
```

#### CSS
- BEM-Notation für Klassen
- CSS Variables nutzen
- Mobile-First Approach

```css
/* Block */
.rsc-service-card { }

/* Element */
.rsc-service-card__title { }

/* Modifier */
.rsc-service-card--featured { }
```

### Commit Messages

Nutze [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: Neues Feature
- `fix`: Bug-Fix
- `docs`: Dokumentation
- `style`: Formatierung
- `refactor`: Code-Refactoring
- `test`: Tests
- `chore`: Maintenance

**Beispiele:**
```bash
feat(services): add service duplication feature
fix(ajax): resolve services not loading issue
docs(readme): update installation instructions
```

### Pull Request Process

1. **Branch erstellen**
   ```bash
   git checkout -b feature/mein-feature
   ```

2. **Änderungen committen**
   ```bash
   git add .
   git commit -m "feat: add amazing feature"
   ```

3. **Tests durchführen** (falls vorhanden)
   ```bash
   composer test
   npm test
   ```

4. **Pushen**
   ```bash
   git push origin feature/mein-feature
   ```

5. **Pull Request öffnen**
   - Auf GitHub zum Original-Repository
   - "New Pull Request" klicken
   - Feature-Branch auswählen
   - Template ausfüllen

### Pull Request Template

```markdown
## Beschreibung
[Was macht dieser PR?]

## Type of Change
- [ ] Bug fix (non-breaking)
- [ ] New feature (non-breaking)
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Code getestet
- [ ] Neue Tests hinzugefügt
- [ ] Alle Tests laufen durch

## Checklist
- [ ] Code folgt Style Guide
- [ ] Selbst-Review durchgeführt
- [ ] Kommentare hinzugefügt
- [ ] Dokumentation aktualisiert
- [ ] Keine Warnings generiert
- [ ] CHANGELOG.md aktualisiert
```

## 🌐 Übersetzungen

Übersetzungen sind sehr willkommen!

### Neue Sprache hinzufügen

1. POT-Datei generieren (falls nicht vorhanden)
2. PO-Datei für deine Sprache erstellen
3. Übersetzen mit Poedit oder ähnlich
4. MO-Datei generieren
5. Pull Request öffnen

```
languages/
├── ramboeck-configurator.pot  # Template
├── ramboeck-configurator-de_DE.po
├── ramboeck-configurator-de_DE.mo
└── ramboeck-configurator-fr_FR.po
```

## 📖 Dokumentation

Dokumentation verbessern ist immer hilfreich!

- README.md aktualisieren
- Code-Kommentare hinzufügen
- Wiki-Seiten erstellen
- Tutorials schreiben
- Screenshots aktualisieren

## ⚖️ Code of Conduct

### Unsere Verpflichtung

Wir verpflichten uns, ein freundliches und respektvolles Umfeld zu schaffen.

### Standards

**Positive Verhaltensweisen:**
- Respektvolle Kommunikation
- Konstruktives Feedback
- Andere Perspektiven akzeptieren
- Fokus auf das Projekt

**Unakzeptables Verhalten:**
- Belästigung jeglicher Art
- Persönliche Angriffe
- Diskriminierung
- Unangemessene Kommentare

## 📞 Fragen?

Bei Fragen:
- Öffne ein Issue mit Label "question"
- Kontaktiere: support@ramboeck-it.com

## 🙏 Danke!

Danke, dass du dir die Zeit nimmst, zu diesem Projekt beizutragen! Jeder Beitrag, egal wie klein, ist wertvoll.

---

**Happy Coding! 💻**
