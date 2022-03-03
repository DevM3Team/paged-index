# PagedIndex #

## EN ##

### What is this package for? ###
This package offers a class that creates a quick implementation of a server-sided loading table. The class deals with
sorting, filtering and paginating functions.

### How to use it? ###

The keys of the request are 5:
- `filter`: the string value passed to the filtering function.
- `page_index`: the index of the page, it's an integer.
- `page_size`: the size of the pages, it's an integer.
- `sort_column`: the integer that represents the column passed to the sorting function.
- `sort_direction`: the direction of the sorting it can be "asc" or "desc".

The `PagedIndex` abstract class has got 2 abstract methods, `sort` and `filter`.

There is an Artisan Command that creates a model referred PagedIndex. 

### Examples ###
```shell
    php artisan make:paged_index ModelPagedIndex
```

It creates an extension of the `PagedIndex` abstract class, the class will be saved inside `app/Http/PagedIndexes`.


This is the way to use a simple model related PagedIndex:
```injectablephp
use Illuminate\Database\Eloquent\Collection;use Illuminate\Http\Request;

//CONTROLLER CLASS

public function index(Collection $collection){
    $p = new ModelPagedIndex($collection);
    return new Response($p->getObjects());
}
```



## IT ##

### A cosa serve questa repo? ###

Questo package offre una classe che permette di creare una veloce implementazione di tabelle caricate nel lato server.
Di default permette di implementare funzioni di ordinamento, filtro e paginazione.

### Come si usa? ###

Le key delle richieste sono 5:
- `filter`: la stringa passata alla funzione di filtro.
- `page_index`: l'indice della pagina, è un integer.
- `page_size`: il numero di oggetti per pagina, è un integer.
- `sort_column`: l'intero che rappresenta la colonna passata alla funzione di ordinamento.
- `sort_direction`: il valore della direzione dell'ordinamento, può essere "asc" per l'ordine crescente e "desc" per decresente.

La classe astratta `PagedIndex` ha 2 metodi da implementare, `sort` per l'ordinamento e `filter` per il filtro.

C'è un comando Artisan che crea un PagedIndex.

### Esempi ###
```shell
    php artisan make:paged_index ModelPagedIndex
```
Crea un estensione della classe astratta `PagedIndex`, la classe sarà salvata all'interno di `app/Http/PagedIndexes`.

Questo è un esempio di come si usa un semplice PagedIndex:
```injectablephp
use Illuminate\Database\Eloquent\Collection;use Illuminate\Http\Request;

//CONTROLLER CLASS

public function index(Collection $collection){
    $p = new ModelPagedIndex($collection);
    return new Response($p->getObjects());
}
```
