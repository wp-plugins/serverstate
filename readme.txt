=== Serverstate ===
Contributors: sergej.mueller
Tags: stats, server, monitoring, response, uptime
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5RDDW9FEHGLG6
Requires at least: 3.3
Tested up to: 3.4
Stable tag: trunk



Dashboard-Widget für Serverstate, das zuverlässige Server Monitoring Tool. Blogausfälle protokollieren, Performance messen.



== Description ==

= Online-Status & Performance =
[Serverstate](http://serverstate.de/?referrer=245049071 "Server Monitoring") *(Partnerlink)* ist ein Monitoring Service, welches die Erreichbarkeit von Webseiten überwacht und deren Antwortzeiten misst. Im Fall einer Nichterreichbarkeit der Zielseite verschickt der Dienst eine E-Mail, Tweet oder SMS als Benachrichtigung.

Das Serverstate Plugin legt im WordPress-Administrationsbereich ein Dashboard-Widget an, welches Antwortzeiten und Erreichbarkeitswerte als Statistik abbildet. Wie oft war der Blog offline? Hat sich nach einem Update die Performance verschlechtert? Das Serverstate Widget liefert Antworten und schafft einen Überblick über die Erreichbarkeit und Geschwindigkeit des Blogs in den letzten 30 Tagen.

Direkt im Serverstate Widget über den Link *Konfigurieren* werden die Zugangsdaten des Serverstate Accounts und die zuständige Sensor-ID (ID des Überwachungsauftrags) hinterlegt. Bedauerlicherweise stellt Serverstate keine API-Kommunikation mithilfe eines API-Schlüssels zur Verfügung. Somit werden Zugangsdaten des Accounts in WordPress verschlüsselt gespeichert und ausschließlich zum Datenabgleich mit der Serverstate Schnittstelle verwendet.

Die auf dem WordPress-Dashboard abgebildete Statistik ist interaktiv, d.h. bei Mausberührungen erscheint die jeweilige Kennzahl zum gewählten Tag: Wahlweise die *Antwortzeit in Millisekunden* oder die *Erreichbarkeit in Prozent*.

= Hinweise =
1. Bei neu angelegten Serverstate Überwachungsaufträgen kann es mehrere Stunden dauern, bis Serverstate hierzu Daten zum Abruf bereit stellt.
1. Das Serverstate Plugin verfügt über einen internen Cache, wo die Statistik für eine Stunde aufbewahrt wird. Nach Ablauf des Zeitraumes wird eine Synchronisation durchgeführt.
1. Die Sensor-ID ist eine Zahl, die von Serverstate einem Überwachungsauftrag zugewiesen und beim Anzeigen bzw. Bearbeiten in der URL sichtbar ist. Der Muster: *?sensor_id=912164573*
1. Serverstate ist ein kostenpflichtiger Dienst. Der Kostenfaktor richtet sich ja nach Verbrauch und Häufigkeit der Prüfungen aus. [Kurze Einführung](https://plus.google.com/110569673423509816572/posts/hWdRrhWyots).


= Systemanforderungen =
* PHP ab 5.3
* WordPress ab 3.3
* Serverstate Account

= Autor =
* [Google+](https://plus.google.com/110569673423509816572 "Google+")
* [Plugins](http://wpcoder.de "Plugins")
* [Portfolio](http://ebiene.de "Portfolio")



== Changelog ==

= 0.1 =
* Serverstate geht online



== Screenshots ==

1. Serverstate Dashboard Widget mit Verlauf



== Installation ==

1. Den Plugin-Ordner ins WordPress-Verzeichnis `/wp-content/plugins/` übertragen.
1. Das Plugin unter *Plugins* aktivieren.
1. In der gleichen Ansicht auf *Einstellungen* klicken.
1. Im Formular die Serverstate Zugangsdaten und die Sensor-ID eingeben.
1. Die Eingabe speichern.