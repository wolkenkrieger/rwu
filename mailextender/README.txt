Modul um zusätzliche Dateien (zB. AGB, Widerrufsrecht, etc) in der Bestellbestätigung einzupflegen
==================================================================================================

Den gesamten Ordner in den Modules Ordner kopieren und im Admin aktivieren.

In den Moduleinstellungen von 'Attachement to customer order mail' den Reiter Grundeinstellungen öffnen
und dort die notwendigen Daten hinterlegen.

Pfadangabe: zB /download/attachment/      =>  Der Pfad Ihrer Dateien, definiert ab root ( / )

Sämtliche Dateien in dem Pfad müssen in einem zusätzlichen Sprachordner hinterlegt sein.

Für eine Deutsche Bestellbestätigung lautet der Pfad in der Moduleinstellung '/download/attachment/',
in dem Verzeichnis Ihrer Servers jedoch '/download/attachment/de/' => hier kopieren Sie bitte alle Dateien hinein, die Sie im Modul deklarieren.

Sollte eine datei beim versenden nicht gefunden werden,
wird ein entsprechender Hinweis in der /log/EXCEPTION_LOG.txt hinterlegt.

Für Fehler, Tips und Anregungen bin ich gerne dankbar.

Hardy Thiergart
