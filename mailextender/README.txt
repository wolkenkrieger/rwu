Modul um zus�tzliche Dateien (zB. AGB, Widerrufsrecht, etc) in der Bestellbest�tigung einzupflegen
==================================================================================================

Den gesamten Ordner in den Modules Ordner kopieren und im Admin aktivieren.

In den Moduleinstellungen von 'Attachement to customer order mail' den Reiter Grundeinstellungen �ffnen
und dort die notwendigen Daten hinterlegen.

Pfadangabe: zB /download/attachment/      =>  Der Pfad Ihrer Dateien, definiert ab root ( / )

S�mtliche Dateien in dem Pfad m�ssen in einem zus�tzlichen Sprachordner hinterlegt sein.

F�r eine Deutsche Bestellbest�tigung lautet der Pfad in der Moduleinstellung '/download/attachment/',
in dem Verzeichnis Ihrer Servers jedoch '/download/attachment/de/' => hier kopieren Sie bitte alle Dateien hinein, die Sie im Modul deklarieren.

Sollte eine datei beim versenden nicht gefunden werden,
wird ein entsprechender Hinweis in der /log/EXCEPTION_LOG.txt hinterlegt.

F�r Fehler, Tips und Anregungen bin ich gerne dankbar.

Hardy Thiergart
