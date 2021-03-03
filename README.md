# PagedIndex #

### A cosa serve questa repo? ###

Questa repo serve per creare velocemente un metodo di index che ritorni i dati per essere utilizzati in una server-sided
loading table.

### Come si usa? ###

Le richieste al metodo di index devono avere una struttura predefinita per funzionare:

```
filter?: string,
page_index?: int,
page_size?: int,
sort_column?: int,
sort_direction?: "asc"|"desc"
```

La richiesta viene passata direttamente all'oggetto di classe _**PagedIndex**_, assieme alla collezione base di dati da
eventualmente ordinare, filtrare e paginare.

Come primo passo bisogna istanziare un oggetto di classe _**PagedIndex**_, passando come argomenti la richiesta e la
collezione.

```injectablephp

use M3Team\PagedIndex\PagedIndex;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

function(Request $request, Collection $collection){
    $p = new PagedIndex($request, $collection);
}
```

#### Paginazione #####

La paginazione è già attiva, basta passare i campi *page_index* e *page_size* nella richiesta, se non passati o lasciati
a 0 la paginazione sarà disattivata.

#### Filtro ####

Per attivare il filtro bisogna passare al _**PagedIndex**_ una funzione di filtro oltre al campo *filter*
nella richiesta. La funzione di filtro può avere due parametri, il primo è il modello da filtrare e il secondo è il
campo filtro, deve restituire un valore booleano, __true__ nel caso in cui il modello rispetti i requisiti del filtro
e __false__ in caso contrario.

Nel seguente esempio viene usato come modello di esempio **User**, che ha al suo interno i campi _email_ e _name_.

```injectablephp
use M3Team\PagedIndex\PagedIndex;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

function(Request $request, Collection $collection){
    $p = new PagedIndex($request, $collection);
    $p->setFilterFn(function(User $user, $filter){
        return $user->email == 0;
    });
}
```

#### Ordinamento ####

Per attivare l'ordinamento bisogna passare al _**PagedIndex**_ una funzione di ordinamento oltre ai campi *sort_column*
e *sort_direction* nella richiesta. La funzione di ordinamento può avere due parametri, il primo è il modello, il
secondo è il campo **sort_column**. La funzione si comporta esattamente come la funzione di callback passata al filter
di
[/**
Illuminate/Database/Eloquent/Collection**](https://laravel.com/api/8.x/Illuminate/Database/Eloquent/Collection.html#method_filter)
.

Nel seguente esempio viene usato come modello di esempio **User**, che ha al suo interno i campi _email_ e _name_.

```injectablephp
use M3Team\PagedIndex\PagedIndex;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

function(Request $request, Collection $collection){
    $p = new PagedIndex($request, $collection);
    $p->setSortFn(function (User $model, $column){
        return $column == 0 ? $user->email : $user->name;
    });
}
```

#### Risultato ####

Per richiedere il risultato delle operazioni bisogna usare il metodo *getObjects()*

```injectablephp
use M3Team\PagedIndex\PagedIndex;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

function(Request $request, Collection $collection){
    $p = new PagedIndex($request, $collection);
    $p->setFilterFn(function(User $user, $filter){
        return $user->email == 0;
    });
    $p->setSortFn(function (User $model, $column){
        return $column == 0 ? $user->email : $user->name;
    });
    return $p->getObjects();
}
```

#### Requisiti ####

Questo pacchetto come requisiti ha:

```json
{
  "require": {
    "laravel/framework": "^v8.0.0"
  }
}
```

