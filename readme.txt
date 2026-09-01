=== PS JobBoard ===
Contributors: DerNerd (PSOURCE)
Tags: jobs, experts, jobbörse, jobboard, vermittlung, freelancer, projekte
Requires at least: 4.9
Tested up to: 7.1
ClassicPress: 2.7.1
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Verbinde Unternehmen, Projekte und Fachleute mit einer flexiblen Job- und Expertenbörse für WordPress und ClassicPress.

== Description ==

**PS Jobboard** verbindet Stellenausschreibungen, Projektvermittlung und Expertenprofile in einem modularen System. Mitglieder können Jobs im Frontend veröffentlichen, eigene Einträge verwalten und sich mit Fähigkeiten, Biografie, Portfolio und Kontaktdaten präsentieren. Du steuerst Freigaben, Obergrenzen, Vergütung, Laufzeiten und Seiten zentral im Backend.

= Jobs veröffentlichen und verwalten =

* Ausschreibungen als Freelance-/Projektarbeit oder Festanstellung
* Beschäftigungsabhängige Vergütung: Budget oder optionales Gehalt pro Jahr, Monat oder Stunde
* Flexible Terminangaben: konkretes Datum, ab sofort, nach Absprache oder eigener Kurztext
* Kategorien, Fähigkeits-Tags, Kontaktadresse, Firmenwebseite oder externes Bewerbungsformular
* Jobbild, Portfolio und Dateianhänge
* Veröffentlichung direkt, nach Prüfung oder als Entwurf
* Persönliche Obergrenzen für Jobs pro Benutzer

= Experten sichtbar machen =

* Expertenprofile mit Avatar, Biografie, Kurzbeschreibung, Unternehmen und Standort
* Fähigkeiten, soziale Profile, Portfolio und zusätzliche Dateien
* Eigene Profilverwaltung im Frontend
* Veröffentlichung direkt, nach Prüfung oder als Entwurf
* Einstellbare Anzahl von Expertenprofilen pro Benutzer
* Eigene Archive, Suche, Einzelansichten und Kontaktwege

= Seiten und Darstellung =

PS Jobboard erstellt die benötigten Bereiche standardmäßig als virtuelle Seiten. Mit der Erweiterung **PS-Jobboard Seiten** kannst Du stattdessen normale WordPress-Seiten zuweisen oder direkt erzeugen. Archive, Formulare, Kontaktseiten, persönliche Übersichten und die gemeinsame Startseite stehen zusätzlich als Shortcodes bereit. Jobboard-Seiten geben keine Kommentarbereiche aus.

= Administration und Datenschutz =

Das zentrale Jobboard-Menü bündelt Dashboard, Jobs, Experten und Einstellungen. Du konfigurierst Status, Einträge pro Seite, Benutzerobergrenzen, Währung, Vergütung, Laufzeiten, Uploads, E-Mail-Texte und Erweiterungen. Persönliche Job- und Expertendaten sind in die WordPress-Werkzeuge für Datenexport und Datenlöschung eingebunden.

== Installation ==

1. Lade den Plugin-Ordner nach `/wp-content/plugins/` oder installiere das Paket über die Plugin-Verwaltung.
2. Aktiviere **PS Jobboard**.
3. Öffne **Jobboard > Einstellungen** und prüfe die allgemeinen, Job- und Expertenoptionen.
4. Aktiviere unter **Erweiterungen** nur die Module, die Du benötigst.
5. Nutze die automatisch angelegten virtuellen Seiten oder weise mit **PS-Jobboard Seiten** normale Seiten zu.
6. Prüfe die Frontend-Formulare und passe Freigaben, Obergrenzen und Uploadrechte an Deine Website an.

== Verwendung ==

Nach der Aktivierung findest Du im Backend ein gemeinsames **Jobboard**-Menü. Dort verwaltest Du:

* Jobs und Jobkategorien
* Expertenprofile
* allgemeine, Job- und Experteneinstellungen
* Shortcodes und Uploadregeln
* aktivierbare Erweiterungen
* bei Bedarf Seitenmanager, Wallet und Guthabenregeln

Mitglieder verwenden die Frontend-Navigation für Jobboard, Expertenboard, neue Ausschreibungen sowie ihre eigenen Jobs und Profile. Ein neues leeres Formular wird nur über die jeweilige Erstellen-Schaltfläche geöffnet. Normale Aufrufe laden vorhandene eigene Einträge; die eingestellten Benutzerobergrenzen gelten für alle Rollen.

== Erweiterungen ==

Die mitgelieferten Module können gezielt aktiviert werden:

* **Erweiterte Suche** – zusätzliche Filter für Job- und Expertenlisten
* **Benutzerdefiniertes Layout** – alternative dynamische Listenlayouts
* **Experte Geo Location** – Standortangaben für Experten
* **Erweiterter Texteditor** – visueller Editor für Jobbeschreibungen und Expertenbiografien
* **PS-Jobboard Seiten** – normale Seiten statt virtueller Seiten
* **Experten-Demo-Daten** und **Job Demo Daten** – Beispieldaten für Tests
* **MarketPress Integration** – Guthabenpakete und Veröffentlichungsregeln; benötigt MarketPress
* **PM-System** – persönliche Nachrichten als Kontaktweg; benötigt das PSOURCE Private-Messaging-Plugin

== Zusammenspiel mit PSOURCE Plugins ==

= PS Community =

Der Shortcode `[jbp-profile-panel]` bettet Navigation, Listen und Formulare als AJAX-Panel in eine Community-Profilseite ein. Nach dem Speichern führen Job- und Expertenformulare zurück in den passenden Profilbereich, wenn eine PS-Community-Profilseite konfiguriert ist.

= MarketPress =

Die MarketPress-Erweiterung ergänzt Wallet, Guthabenpakete und Regeln für kostenpflichtige Veröffentlichungen. Administratoren können Guthabenpläne verwalten und festlegen, wann Jobs oder Expertenprofile Guthaben verbrauchen. MarketPress ist nur für diese Monetarisierungsfunktionen erforderlich.

= Private Messaging =

Das PM-System ersetzt die normalen Kontakt-Schaltflächen durch persönliche Nachrichten, ergänzt einen Postfach-Link und übernimmt die Jobboard-Navigation in das Messaging-Layout. Das eigenständige Private-Messaging-Plugin muss dafür aktiv sein.

== Wichtige Shortcodes ==

* `[jbp-landing-page]` – gemeinsame Jobboard-Startseite
* `[jbp-job-archive-page]` – Jobliste
* `[jbp-expert-archive-page]` – Expertenliste
* `[jbp-job-update-page]` – Job erstellen oder bearbeiten
* `[jbp-expert-update-page]` – Expertenprofil erstellen oder bearbeiten
* `[jbp-my-job-page]` – eigene Jobs
* `[jbp-my-expert-page]` – eigene Expertenprofile
* `[jbp-job-contact-page]` – Jobkontakt
* `[jbp-expert-contact-page]` – Expertenkontakt
* `[jbp-job-single-page]` – einzelne Jobausgabe
* `[jbp-job-pro-page]` – einzelnes Expertenprofil
* `[jbp-profile-panel]` – vollständiges Profilpanel für PS Community

Für Navigationselemente stehen zusätzliche Button-Shortcodes zur Verfügung. Eine vollständige Übersicht findest Du unter **Jobboard > Einstellungen > Shortcodes**.

== Häufig gestellte Fragen ==

= Benötigt PS Jobboard weitere Plugins? =
Nein. Jobs, Expertenprofile, Archive, Formulare und virtuelle Seiten funktionieren eigenständig. MarketPress, Private Messaging und PS Community erweitern bestimmte Arbeitsabläufe, sind aber keine Voraussetzung für den Grundbetrieb.

= Kann ich normale Seiten statt virtueller Seiten verwenden? =
Ja. Aktiviere **PS-Jobboard Seiten** und ordne jedem Jobboard-Bereich eine vorhandene Seite zu oder lasse sie direkt erstellen.

= Kann ich die Felder anpassen? =
Ja. Das Plugin stellt zahlreiche Hooks und Filter für Felder, Validierung, Listenlayouts, URLs und Schaltflächen bereit. Außerdem können normale WordPress-Custom-Fields genutzt werden.

= Können Mitglieder mehrere Jobs oder Expertenprofile anlegen? =
Ja. In den Job- und Experteneinstellungen legst Du die jeweilige Obergrenze pro Benutzer fest. Neue leere Formulare sind nur über die Erstellen-Schaltflächen erreichbar; vorhandene Einträge werden über die persönliche Übersicht bearbeitet.

= Können Einträge vor der Veröffentlichung geprüft werden? =
Ja. Neue Jobs und Expertenprofile können sofort veröffentlicht oder zunächst zur Prüfung eingereicht werden. Optional dürfen Mitglieder Entwürfe speichern.

= Unterstützt das Plugin Festanstellungen und Projektarbeit? =
Ja. Je Ausschreibung wählst Du zwischen Festanstellung und Freelance-/Projektarbeit. Die Vergütungs- und Terminfelder passen sich daran an.

= Ist das Plugin mehrsprachig? =
Ja. Sichtbare Plugin-Texte verwenden die Textdomain `psjb` und können mit den üblichen WordPress-Werkzeugen übersetzt werden.

= Kann ich damit Geld verdienen? =
Ja. Mit der optionalen MarketPress Integration kannst Du Guthabenpakete anbieten und Veröffentlichungsregeln definieren.

= Kann ich das Design anpassen? =
Ja. Die Ausgabe ist themefähig, besitzt eigene Styles und bietet Filter für Layouts und Navigation. Mit normalen Seiten kannst Du Jobboard-Shortcodes zudem in Deinem Seitenaufbau platzieren.

= Werden Kommentare auf Jobboard-Seiten angezeigt? =
Nein. Virtuelle Seiten, normale Seiten mit Jobboard-Shortcodes sowie Job- und Expertenausgaben unterdrücken Kommentarbereiche und Kommentarhinweise.

Weitere Dokumentation, Downloads und Support findest Du im [PS Jobboard Repository](https://github.com/cp-psource/ps-jobboard).

== ChangeLog ==

= 1.0.4 =
* Neu: Jobs können als Festanstellung oder Freelance-/Projektarbeit ausgeschrieben werden
* Neu: Beschäftigungsabhängige Vergütung mit Pflichtbudget für Projekte, optionalem Gehalt für Festanstellungen und Zeiträumen pro Jahr, Monat oder Stunde
* Neu: Flexible Terminangaben als konkretes Datum, "Ab sofort", "Nach Absprache" oder individuelle Angabe mit maximal 25 Zeichen
* Neu: Optionale Firmenwebseite oder externes Bewerbungsformular mit sicherer URL-Validierung und Ausgabe
* Verbesserung: Formularfelder, Validierung, Einzelansicht, Joblisten, Landingpage, "Meine Jobs" und Admin-Metabox an beide Beschäftigungsarten angepasst
* Verbesserung: Neutrale Bezeichnungen für Stellen und Projekte sowie verständlichere Veröffentlichungsdauer der Anzeige
* Verbesserung: Normale Jobarchive zeigen auch Festanstellungen ohne Gehaltsangabe; Vergütungsfilter greifen nur bei einer expliziten erweiterten Suche
* Verbesserung: Seitenmanager mit durchsuchbaren, per Mausrad bedienbaren Seitenauswahlen; Seitenerstellung für "Bearbeite Job" und "Jobkontakt" repariert
* Verbesserung: Experten- und Jobformulare laden vorhandene eigene Einträge; neue Einträge entstehen nur über die jeweiligen Erstellen-Schaltflächen und beachten die benutzerbezogene Obergrenze
* Verbesserung: WYSIWYG-Editor für Jobbeschreibungen ergänzt und das Jobformular um kurze, übersetzbare Hilfstexte in Du-Form erweitert
* Fix: Fehlerhafte Schriftzeichen in den Platzhaltern für Vor- und Nachname entfernt
* Fix: Kommentarbereiche und Kommentarhinweise wie "No comments" auf virtuellen sowie shortcodebasierten Jobboard-Seiten vollständig ausgeblendet
* Kompatibilität: Bestehende Jobs ohne Beschäftigungsart bleiben automatisch als Freelance-/Projektarbeit mit konkretem Termin erhalten

= 1.0.3 =
* Fix: open_basedir-Warnungen beim Laden aktivierter Addons behoben (nur lokale Addon-Dateien werden geprüft, keine veralteten absoluten Serverpfade)

= 1.0.2 =
* Fix: Profile-Panel AJAX stabilisiert (Assets/Styles/Skripte werden in Tabs konsistent geladen)
* Fix: Abstürze bei Job/Experten-Formularen im Profil-Panel behoben (null/array Guards)
* Fix: Kompatibilitätsprobleme bei der JS-Ausgabe im Profil-Panel behoben
* Verbesserte Robustheit bei Job-/Experten-Archivansichten (defensive Fallbacks)
* Fix: `open_basedir`-Warnung beim Laden aktivierter Addons nach Server-Migration behoben (Pfadauflösung/Validierung)

= 1.0.1 =
* Admin-Menü überarbeitet und Jobboard-Navigation vereinheitlicht
* Wallet-Einstellungen in die zentralen Plugin-Einstellungen integriert
* Datepicker auf Flatpickr umgestellt (jQuery UI entfernt)
* Wallet-Bereiche auf Deutsch lokalisiert und Textdomain vereinheitlicht
* Diverse Bugfixes (Wallet-Weiterleitungen, CORS bei Seitenerstellung, Tab-Layout)

= 1.0.0 =
* Offizielles Release von PS Job-Board

