# progetto_week_10


Inserire i parametri per la connessione al DB nel file config.php.

Si possono personalizzare i parametri di mailtrap nel file mail.php.

L'appllicazione permette di registrarsi e fare il login. Una volta registrati, 
manda un'email di benvenuto al nuovo utente. Il database viene popolato al primo avvio 
con 4 utenti fake e qualche libro. Ogni utente, dalla sezione profilo, ha la possibilità 
di modificare o eliminare dal database solamente i libri da lui inseriti. Nella sezione allBooks 
sono visibili tutti i libri caricati dagli utenti. Cliccando sull'avatar nel libro si accede ad 
una semplice pagina di dettaglio dell'utente che ha aggiunto quel libro. I libri nella sezione 
principale possono essere filtrati, indifferentemente per autore o titolo. 
Cliccando sull'icona del cuore in copertina, si può aggiungere (o rimuovere) un libro alla lista
dei preferiti. Tutti i dati inseriti dall'utente sono controllati lato server, e vengono visualizzati 
messaggi di errore o di successo a seconda dell'esito delle operazioni effettuate.

In questo esercizio ho sperimentato divesi modi per interagire con il database. 
Sempre son mysqli, qualche richiesta è stata fatta usando anche i prepared statements.
Con le tabelle 'genres' e 'favourites' ho usato le relazioni uno a molti e molti a molti.