# PS Jobboard

[![Version](https://img.shields.io/badge/Version-1.0.5-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.1-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

PS Jobboard verbindet Stellenausschreibungen, Projektvermittlung und Expertenprofile in einer modularen Plattform für WordPress und ClassicPress.

Unternehmen und Mitglieder veröffentlichen Jobs direkt im Frontend. Fachleute präsentieren ihre Erfahrung, Fähigkeiten und Arbeitsproben in eigenen Expertenprofilen. Die Administration steuert Freigaben, Obergrenzen, Vergütung, Laufzeiten, Seiten und optionale Erweiterungen zentral im Backend.

- Jobs für Festanstellungen und Freelance-/Projektarbeit
- Expertenverzeichnis mit Profilen, Skills und Portfolio
- Frontend-Formulare und persönliche Übersichten
- Virtuelle Seiten oder frei zuweisbare Standardseiten
- Optionale Integration mit PS Community, MarketPress und Private Messaging
- Übersetzbar über die Textdomain `psjb`
- GPLv2 oder neuer

## Was PS Jobboard kann

### Jobs und Stellen

Jede Ausschreibung kann als Festanstellung oder Freelance-/Projektarbeit angelegt werden. Abhängig von der Beschäftigungsart passt das Formular die fachlichen Angaben an.

- Jobtitel, Kategorie und Fähigkeits-Tags
- Ausführliche Beschreibung mit optionalem WYSIWYG-Editor
- Pflichtbudget für Projektarbeit
- Optionales Gehalt für Festanstellungen
- Vergütungszeiträume pro Jahr, Monat oder Stunde
- Konkretes Datum, ab sofort, nach Absprache oder eigene Terminangabe
- Kontaktadresse, Firmenwebseite oder externes Bewerbungsformular
- Jobbild, Portfolio und Dateianhänge
- Direkte Veröffentlichung, Prüfung durch die Administration oder Entwurf
- Einstellbare Anzahl eigener Jobs pro Benutzer

### Expertenprofile

Mitglieder können sich als Fachleute präsentieren und ihre Profile selbst verwalten.

- Avatar, Vorname und Nachname
- Biografie und kurze Beschreibung
- Unternehmen, Standort und Kontaktadresse
- Fähigkeiten und soziale Profile
- Portfolio und zusätzliche Dateien
- Eigene Profilübersicht mit Bearbeiten-Funktion
- Direkte Veröffentlichung, Prüfung oder Entwurf
- Einstellbare Anzahl von Profilen pro Benutzer

Ein neues leeres Job- oder Expertenformular wird nur über die jeweilige Erstellen-Schaltfläche geöffnet. Normale Aufrufe laden vorhandene eigene Einträge. Sobald die persönliche Obergrenze erreicht ist, kann kein weiterer Eintrag erzeugt werden.

### Suche und Navigation

PS Jobboard bringt eine gemeinsame Navigation für Jobboard, Jobliste, Expertenboard, neue Ausschreibungen und persönliche Bereiche mit.

- Job- und Expertenarchive
- Suche nach Jobs und Experten
- Kategorien und Fähigkeits-Tags
- Erweiterte Suchfilter als aktivierbares Modul
- Gemeinsame Landingpage für aktuelle Jobs und Experten
- Responsive Frontend-Ausgaben

### Administration

Das zentrale Jobboard-Menü bündelt die tägliche Verwaltung:

- Dashboard mit Schnellzugriffen
- Jobs, Experten und Jobkategorien
- Allgemeine, Job- und Experteneinstellungen
- Veröffentlichungsstatus und Entwurfsoptionen
- Einträge pro Seite und Benutzerobergrenzen
- Währung, Vergütung und Laufzeiten
- Uploadrechte und E-Mail-Texte
- Shortcode-Übersicht
- Erweiterungen und Seitenmanager

Job- und Expertendaten sind in die WordPress-Werkzeuge für den Export und die Löschung personenbezogener Daten eingebunden.

## Seitenmodell

PS Jobboard funktioniert ohne manuell angelegte Seiten. Die `JE_Page_Factory` erzeugt virtuelle Bereiche für:

- Jobboard-Startseite
- Job erstellen und bearbeiten
- Jobliste und Jobkontakt
- Eigene Jobs
- Expertenprofil erstellen und bearbeiten
- Expertenliste und Expertenkontakt
- Eigene Expertenprofile

Mit der Erweiterung **PS-Jobboard Seiten** lassen sich stattdessen normale WordPress-Seiten auswählen oder direkt erzeugen. Das ist praktisch für Page Builder, individuelle Slugs und eigene Seitenlayouts.

Kommentarbereiche und Kommentarhinweise werden auf virtuellen Jobboard-Seiten, normalen Seiten mit Jobboard-Shortcodes sowie Job- und Expertenausgaben unterdrückt.

## Mitgelieferte Erweiterungen

| Erweiterung | Aufgabe |
| --- | --- |
| Erweiterte Suche | Ergänzt Filter für Job- und Expertenlisten |
| Benutzerdefiniertes Layout | Stellt alternative dynamische Listenlayouts bereit |
| Experte Geo Location | Ergänzt Standortangaben für Experten |
| Erweiterter Texteditor | Aktiviert den visuellen Editor für Jobbeschreibungen und Expertenbiografien |
| PS-Jobboard Seiten | Verwendet normale Seiten anstelle virtueller Seiten |
| Experten-Demo-Daten | Erzeugt Beispieldaten für Expertentests |
| Job Demo Daten | Erzeugt Beispieldaten für Jobtests |
| MarketPress Integration | Ergänzt Wallet, Guthabenpakete und Veröffentlichungsregeln |
| PM-System | Verbindet Jobboard-Kontakte mit Private Messaging |

Die Erweiterungen werden im Jobboard-Backend einzeln aktiviert. Module mit externer Abhängigkeit zeigen einen Hinweis, wenn das benötigte Plugin fehlt.

## Zusammenspiel im PSOURCE-Ökosystem

### PS Community

Mit `[jbp-profile-panel]` kann das komplette Jobboard als AJAX-Panel in eine PS-Community-Profilseite eingebettet werden. Das Panel enthält Navigation, Listen, persönliche Bereiche sowie Job- und Expertenformulare.

Wenn die Community-Profilseite konfiguriert ist, führen Speicheraktionen zurück in den passenden Jobboard-Bereich des Mitgliederprofils. Dadurch bleiben Profilverwaltung, Jobs und Expertenauftritt in einer gemeinsamen Oberfläche.

### MarketPress

Die optionale MarketPress Integration lädt die Jobboard-Wallet und Guthabenregeln, sobald MarketPress aktiv ist.

Damit lassen sich:

- Guthabenpakete als MarketPress-Produkte anbieten
- Guthaben eines Mitglieds verwalten
- Kosten für neue Jobs oder Expertenprofile festlegen
- kostenlose Einreichungen und anschließenden Guthabenverbrauch kombinieren
- Wallet- und Regel-Einstellungen direkt im Jobboard verwalten

MarketPress ist nur für die Monetarisierung erforderlich. Das Jobboard selbst funktioniert ohne Shop-System.

### Private Messaging

Das optionale PM-System verbindet PS Jobboard mit dem eigenständigen PSOURCE Private-Messaging-Plugin.

- Kontakt-Schaltflächen starten persönliche Nachrichten
- Ein Postfach-Link wird in die Jobboard-Navigation aufgenommen
- Die Jobboard-Navigation steht auch im Messaging-Layout bereit
- Ohne aktives Messaging-Plugin bleibt der normale Jobboard-Kontaktweg verfügbar

Das benötigte Plugin ist über die [Private-Messaging-Releases](https://github.com/Power-Source/private-messaging/releases) erhältlich.

## Installation

### Voraussetzungen

- WordPress ab 4.9 oder eine kompatible ClassicPress-Installation
- PHP ab 7.4
- MarketPress nur für Guthaben und Monetarisierung
- Private Messaging nur für persönliche Nachrichten
- PS Community nur für die Einbettung in Mitgliederprofile

### Einrichtung

1. Lade PS Jobboard nach `/wp-content/plugins/` oder installiere das Plugin-Paket über die Plugin-Verwaltung.
2. Aktiviere **PS Jobboard**.
3. Öffne **Jobboard > Einstellungen**.
4. Konfiguriere Veröffentlichungsstatus, Obergrenzen, Vergütung, Laufzeiten und Uploadrechte.
5. Aktiviere die benötigten Erweiterungen.
6. Verwende die virtuellen Standardseiten oder richte normale Seiten mit **PS-Jobboard Seiten** ein.
7. Prüfe die Frontend-Formulare mit einem normalen Mitgliedskonto.

## Shortcodes

### Seiten und Inhalte

| Shortcode | Ausgabe |
| --- | --- |
| `[jbp-landing-page]` | Gemeinsame Jobboard-Startseite |
| `[jbp-job-archive-page]` | Jobliste |
| `[jbp-expert-archive-page]` | Expertenliste |
| `[jbp-job-update-page]` | Job erstellen oder bearbeiten |
| `[jbp-expert-update-page]` | Expertenprofil erstellen oder bearbeiten |
| `[jbp-my-job-page]` | Eigene Jobs |
| `[jbp-my-expert-page]` | Eigene Expertenprofile |
| `[jbp-job-contact-page]` | Jobkontakt |
| `[jbp-expert-contact-page]` | Expertenkontakt |
| `[jbp-job-single-page]` | Einzelner Job |
| `[jbp-job-pro-page]` | Einzelnes Expertenprofil |
| `[jbp-profile-panel]` | Vollständiges Profilpanel für PS Community |

### Navigation

- `[jbp-landing-btn]`
- `[jbp-job-browse-btn]`
- `[jbp-expert-browse-btn]`
- `[jbp-job-post-btn]`
- `[jbp-expert-post-btn]`
- `[jbp-my-job-btn]`
- `[jbp-expert-profile-btn]`

Optionale Integrationen ergänzen unter anderem `[jbp-my-wallet]`, `[jbp-my-wallet-btn]` und `[jbp-message-inbox-btn]`.

Die vollständige Parameterübersicht befindet sich im Backend unter **Jobboard > Einstellungen > Shortcodes**.

## Anpassung und Entwicklung

PS Jobboard nutzt eigene Post Types für Jobs und Experten sowie WordPress-Hooks für die Anpassung von Formularen, Listen, URLs und Navigation.

Wichtige Erweiterungspunkte sind unter anderem:

- `je_job_posttype_param`
- `je_experts_posttype_param`
- `jbp_job_search_params`
- `jbp_expert_search_params`
- `je_buttons_on_single_page`
- `jbp_button_url`
- `jbp_setting_menu`
- `je_settings_content_*`

Die zentralen Inhaltstypen sind:

- `jbp_job` für Jobs und Stellen
- `jbp_pro` für Expertenprofile
- `jbp_category` für Jobkategorien
- `jbp_skills_tag` für Fähigkeiten

## Übersetzung

Die Plugin-Texte verwenden die Textdomain `psjb`. Übersetzungen können mit den üblichen WordPress- und ClassicPress-Werkzeugen erstellt werden.

## Support und Mitwirkung

- [Dokumentation](https://cp-psource.github.io/ps-jobboard/)
- [Repository](https://github.com/cp-psource/ps-jobboard)
- [Releases](https://github.com/cp-psource/ps-jobboard/releases)
- [Diskussionen](https://github.com/cp-psource/ps-jobboard/discussions)

Fehlerberichte und Beiträge sind willkommen. Bitte beschreibe bei Problemen die eingesetzte WordPress- oder ClassicPress-Version, PHP-Version, das aktive Theme und die aktivierten Jobboard-Erweiterungen.

## Lizenz

PS Jobboard ist freie Software unter der GNU General Public License, Version 2 oder neuer.
