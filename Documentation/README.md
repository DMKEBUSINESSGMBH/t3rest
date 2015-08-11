#T3rest - REST for TYPO3
Mit dieser Extension kann man ein einfaches REST-Interface für TYPO3 bereitstellen. Die Extension sorgt dabei lediglich für die Infrastruktur, die Breitstellung und Verarbeitung der Anfragen wird von externen Datenprovidern durchgeführt.

##Datenprovider

Zur Nutzung der Extension muss man einen eigenen Datenprovider registrieren. Dafür sind zwei Schritte notwendig:
 # Bereitstellung einer Klasse die das Interface *tx_t3rest_provider_IProvider* implementiert
 # Registrierung diese Klasse in TYPO3 über einen Datensatz vom Typ *T3rest provider*

 