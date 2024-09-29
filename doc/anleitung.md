# contao-mpdf-template-bundle

**Mit der contao-mpdf-template-bundle Erweiterung können Sie die PDF-Ausgabe im Artikel (Syndikation) mit einer PDF-Vorlagedatei gestalten. Verwendet wird in dieser Erweiterung die Bibliothek mPDF**


Mit der Erweiterung contao-mpdf-template-bundle kann eine gespeicherte PDF-Datei als Layoutvorlage für die PDF-Ausgabe dienen. Die Vorlage muss im Format **PDF Specification 1.4 (Acrobat 5)** vorliegen. Die Vorlage kann eine oder mehrere Seiten enthalten, die der Reihenfolge nach in die Ausgabe übernommen werden. Werden mehr Seiten ausgegeben, als in der Vorlage vorhanden sind, wiederholt sich die letzte Seite bis zum Ende der Ausgabe.

Der **Dateiname des Downloads** kann als GET-Parameter `&t=foo` in der URL mit angegeben werden. Dazu kann beispielsweise das Template `mod_article.html5` angepasst werden.


### Installation
Installieren Sie die Erweiterung einfach mit dem **Contao Manager** oder auf der Kommandozeile mit dem **Composer**:

`composer require do-while/contao-mpdf-template-bundle`



### Einstellungen
Die Vorgaben werden in dem "Startpunkt einer Webseite" gemacht und gelten auf allen Seiten unterhalb dieses Startpunkts. So ist es möglich sprachen- oder domainabhängig verschiedene PDF-Layouts zu verwenden. Abhängig vom Layout können die Ränder der Textausgabe eingestellt werden, damit nicht Kopf- oder Fußbereich der Vorlage von dem Artikelinhalt überschrieben werden.

Über das Template `mpdf_default` oder eigene Templates, die mit `mpdf_...` beginnen können dem mpdf-Modul weitere Befehle oder Einstellungen, wie Header/Footer, o.ä. übermittelt werden. Das Template wird 2x verarbeitet, einmal in der Initialisierung des PDF und einmal bei der Ausgabe der Inhalte. Hier lassen sich spezifische Anpassungen machen, das Template ist mit Kommentaren versehen, so dass Sie schnell die richtige Position für Ihre Anpassung finden.

Direkt in dem Artikel, der für die PDF-Ausgabe durch das Anhaken der Checkbox freigeschaltet wird, können Daten, wie die verwendete Vorlagedatei, die Ränder und das verwendete Template **überschrieben** werden.


### HTML/CSS-Unterstützung
Die HTML- und CSS-Unterstüzung von mPDF ist in manchen Punkten eingeschränkt. Genauere Informationen finden Sie in der [mPDF-Dokumentation](https://mpdf.github.io/css-stylesheets/introduction.html)


Für die Ausgabe können **eigene CSS-Dateien** zur Formtierung der Inhalte angegeben werden. Andere CSS-Dateien aus der Website werden nicht verarbeitet.

Mit den CSS-Anweisungen muss man ggf. probieren, da nicht alle Feinheiten von mPDF unterstützt werden.


___
Softleister - 2024-09-29

Die Erweiterung basiert auf dem Modul mPDF, siehe (https://github.com/mpdf/mpdf)
