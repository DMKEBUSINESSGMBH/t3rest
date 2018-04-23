# T3REST - REST for TYPO3

Mit dieser Extension kann man ein
REST-Interface für TYPO3 bereitstellen.
Die Extension sorgt dabei lediglich für die Infrastruktur,
die Breitstellung und Verarbeitung der Anfragen
wird von externen Datenprovidern durchgeführt.

Als Controller wird aktuell [Respect\Rest](http://respect.github.io/Rest/) mitgeliefert.
T3REST kann Problemlos auch andere Frameworks wie Slim ansprechen.

## Konfiguration

**restEnableHook**

Aktiviert die neue REST-API. Andernfalls werden die URL's vom TYPO3 verarbeitet.

**restApiUriPath**

Der Pfad, anhand die Extension erkennt, ob es sich um eine REST-Anfrage handelt.
T3REST prüft beim Aufruf die URLs. Beginnt die REQUEST_URI mit diesem Pfadsegment,
so wird T3REST aktiv und verarbeitet die Routen.
Ist beispielsweise rest-api konfiguriert, so werden alle Aufrufe
von domain.net/rest-api/* an den Router weiter gegeben.

**restAuthUserStoragePid**

Um die Authentifizierungs Routinen für den FE-Nutzer nutzen zu können,
muss hier die PID des Storages angegeben werden, wo sich die fe_users Datensätze befinden.

**restApiController**

Der zu verwendende Controller. Dieser wird angesteuert,
wenn der restApiUriPath übereinstimmt. Der Controller kümmert sich
um das Registrieren der Routen über die Provider.
Aktuell ist ein JSON Controller implementiert,
der die Rückgabewerte der Provider als JSON ausliefert.

**restApiRouter**

Der zu verwendende Router. Dieser wird vom Controller angesteuert
und verarbeitet die aktuelle REQUEST_URI mit
den von den Providern konfigurierten Routen.
Aktuell wird nur eine Route für
das [Respect\Rest](http://respect.github.io/Rest/) Framework mitgeliefert.


## Provider

Ein Provider ist dafür zuständig eine oder mehrere Routen zu konfigurieren.
Er kümmert sich weiter um die Verarbeitung und Auslieferung der Daten.

Einem Provider-Datensatz kann eine Nutzergruppe zugewiesen werden.
Damit lässt sich der Zugriff auf den Provider schützen.
Mehr dazu unter [Authentifizierung](#authentifizierung)


### Datensatz konfiguration

Für einen neuen Provider muss im TYPO3 zunächst
ein Datensatz vom Typ *T3rest provider* angelegt werden.
Das Feld *Name* ist lediglich zur Wiedererkennung in der Listenansicht.
Das Feld *REST key* wird für die obsolete API verwendet und spiegelt die Action wieder.
Für die neue API ist dieses Feld nicht erforderlich.
Im Feld *Configuration* kann über die TypoScript Notation
eine Konfiguration abgelegt werden,
welche dem Provider zur Verfügung gestellt wird.
In das Feld *Classname* ist der Klassenname des Providers einzutragen.


### Providerklasse

Die Providerklasse muss das Interface Tx_T3rest_Provider_InterfaceProvider implementieren.
Der Einfachheit halber sollte von dem mitgeliefertem abstrakten Provider Tx_T3rest_Provider_AbstractProvider geerbt werden.

Die wichtigste Methode des Providers ist *prepareRouter*.
Diese Registriert in dem Router die Routen.

Im Folgendem Beispiel wird die Route /news/search registriert.
Wird dann die URI /api/news/search/dmk aufgerufen,
so wird der Router dies erkennen und den Aufruf an die getSearch Methode weiter geben.
Als Parameter wird in diesem Falle *dmk* mitgeliefert.

```php
    /**
     * initializes the router.
     *
     * @return void
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        $router->addRoute(
            $router::METHOD_GET,
            '/news/search/*',
            array($this, 'getSearch')
        );
    }
```

Ein Beispiel der getSearch Methode,
welche sich in unserem Fall in der Providerklasse befindet,
die Suche durchführt, die Daten aufbereitet und zurück gibt:


```php
    /**
     * search for news.
     *
     * @param string $query
     * @return Tx_T3rest_Model_Supplier
     */
    public function getSearch($query)
    {
        $transformer = $this->getTransformer();
        $news = array();
        foreach ($service->search($query) as $item) {
            $news[] = $transformer->transform(
                $item,
                'search.news.'
            );
        }

        $return = Tx_T3rest_Utility_Factory::getSupplier();
        return $return->add('items', $news);
    }
```

Je nachdem, was der Service für Daten für die News liefert,
könnte das Ergebnis dann so aussehen:

```javascript
    {
        "items": {
            "0": {
                "uid": 5,
                "title": "MKMAILER: komfortable Mailing-Features"
            },
            "1": {
                "uid": 7,
                "title": "Offenheit leben, Innovation stärken! - Social Collaboration mit HumHub"
            }
        }
    }
```

## Transformer

Ein Transformer ist dafür zuständig die Rohdaten der Provider
für den Rückgabewert zu verarbeiten.
Dies kann zum einen das Sammeln weiterer Informationen,
wie Kategorien zu einer Angefragten News oder
das erzeugen von Links auf Detailseiten etc., sein.

Ein Transformer muss das Interface
*Tx_T3rest_Transformer_InterfaceTransformer* implementieren oder
erbt idealerweise vom Bereitgestellten *Tx_T3rest_Transformer_Simple*.

Der Transformer kann zum einen im Providerdatensatz
über TypoScript oder über die Providerklasse angegeben werden.

```
    transformer.class = Tx_T3rest_Transformer_Simple
```
```php
    /**
     * returns the transformer class for this provider.
     *
     * @return Tx_T3rest_Transformer_InterfaceTransformer
     */
    protected function getTransformerClass()
    {
        return 'Tx_T3rest_Transformer_Simple';
    }
```


<a id="authentifizierung" />

## Authentifizierung

Die Authentifizierung ist aktuell über FE-user-Datensätze gelöst.
Im Providerdatensatz kann eine Gruppe hinterlegt werden,
welche zugriff auf die API erhalten soll.

```php
    /**
     * initializes the router.
     *
     * @return void
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        $this->getAuthFeUserRoutine()->prepareRoute(
            $router->addRoute(
                $router::METHOD_GET,
                '/news/edit/*',
                array($this, 'getEdit')
            )
        );
    }
```

Die Providerklasse muss jede zu schützende Route
an die AuthFeUser Routine koppeln.
Diese Routine kümmert sich um den Login des Nutzers,
prüft anschließend die Zugriffsberechtigung über die Nutzergruppen
und liefert ggf. einen 401 zurück.

Ein FE-Nutzer kann entweder über den fe_typo_user Cookie
oder über den Authorization Header mit Nutzer-Passwort-Daten angemeldet werden.
Die Daten müssen dann bei jedem Request erneut mitgesendet werden.
