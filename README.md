#### This module is deprecated. Use [DdpPickup](https://github.com/thelia-modules/DpdPickup) now.


Ici Relais module v1.0
author: Thelia <info@thelia.net>

=== SUMMARY ===

fr_FR:
I)   Installation
II)  Utilisation
III) Intégration

en_US:
I)   Install notes
II)  How to use
III) Integration


=== fr_FR ===

I) Installation
---------------
L'installation du module IciRelais se fait de la même manière que les autres, vous pouvez soit importer directement le zip dans le back office,
soit le décompresser dans <dossier de Thélia2>/local/modules.
Un exemple d'intégration dans le thème par défault de Thelia est fourni dans le dossier templates, il vous suffit de copier les fichiers:
	- <dossier du module>/templates/frontOffice/default/ajax/order-delivery-module-list.html
	- <dossier du module>/templates/frontOffice/default/order-delivery.html
	- <dossier du module>/templates/frontOffice/default/order-invoice.html
dans le dossier du template en suivant la même arborescence.

Il nous vous reste plus qu'à activer le module et à associer vos zones de livraison.

II) Utilisation
---------------
Une page de configuration est mise à votre disposition pour vous permettre d'effectuer deux tâches:
	- exporter un fichier EXAPRINT (export.dat) contenant les informations sur les livraisons effectuées via IciRelais
	- configurer les tranches de prix des livraisons par IciRelais

Pour vous y rendre, il vous suffit d'aller dans le back Office, onglet "Modules" et de cliquer sur "Configurer" sur la ligne du module IciRelais.
Pour exporter un fichier EXAPRINT, il faut renseigner tous les champs présents dans le formulaire.

III) Intégration
----------------
Pour l'exemple d'intégration, j'ai utilisé une google map, ceci n'est pas nécessaire mais préférable.
En effet, le module n'interagit pas avec pendant la commande.
Une fois le module activé, il devient néanmoins indispensable de transmettre une variable $_POST['pr_code'] dans le formulaire "thelia.order.delivery",
sinon, vous ne pourrez plus passer à l'étape 3 ( order-invoice ).
De plus, une boucle "delivery.ici" est disponible et doit remplacer la boucle "delivery" dans order-delivery-module-list.html,
les deux sont semblable, mais delivery.ici possède une variable en plus, qui permet de savoir si le module est ou non IciRelais ( ce qui permet une intégration spécifique
de la ligne IciRelais).
La variable "pr_code" doit contenir l'identifiant du point relais choisi par l'utilisateur.
Une boucle vous est fournie pour obtenir les 10 points relais les plus proches de l'adresse par défault de l'utilisateur: icirelais.relais.around
Sinon, une route est disponible pour obtenir 10 points relais dans une ville: /module/icirelais/{ville}/{code postal}
Cette route pointe vers le controlleur "SearchCityController" qui génère un fichier json, que vous pouvez utiliser, par exemple, avec jquery/ajax.

Pour afficher l'adresse du point relais en adresse de livraison sur la page order-invoice.html, 
il vous suffit de replacer le type de la boucle nommée "delivery-address" en address.ici, à la place de "delivery"

Pour rajouter l'adresse de suivi du colis dans le mail de confirmation de la commande, une boucle est mise à votre disposition: "icirelais.urltracking"
elle prend un argument ref, qui est la référence de la commande, et une sortie $URL.
Si l'url ne peut être générée, elle ne renvoie rien.
On peut donc l'intégrer de la manière suivante:
{loop name="tracking" type="icirelais.urltracking" ref=$REF}
Vous pouvez suivre votre colis <a href="{$URL}">ici</a>
{/loop}
=== en_US ===

I) Install notes
---------------
The install process of IciRelais module is the same than the other modules, you can import it directly from the back office,
or unzip it in <path to thelia2>/local/modules.
An integration example in Thelia's default theme is provided in templates directory, you only have to copy those files:
	- <module directory>/templates/frontOffice/default/ajax/order-delivery-module-list.html
	- <module directory>/templates/frontOffice/default/order-delivery.html
	- <module directory>/templates/frontOffice/default/order-invoice.html
respectively in the directory of the template.

Then you can activate IciRelais module and configure you shipping zones.

II)  How to use
---------------
A configuration page is provided with the module, so you can:
	- export an EXAPRINT file (export.dat), with informations on all deliveries done with IciRelais
	- configure price slices for shipping zones.

You can use it in the back office by going to "Modules" tab, then "configure" button on IciRelais' line.
For exporting an EXAPRINT file, you must complete the entire form.

III) Integration
----------------
For the integration example, I used a google map, but it's not necessary.
In fact, the module doesn't interact with the map during the order.
Once the module is active, you must create an input named "pr_code" in your form "thelia.order.delivery",
whereas you won't be able to go to step 3 ( order-invoice ).
Moreover, the loop "delivery.ici" is available and must replace "delivery" in order-delivery-module-list.html,
they do the same thing, but delivery.ici has a new variable that allows you to know if the delivery module that's being looped is IciRelais.
The input "pr_code" must contain the ID of the pick-up & go store choosed by the user.
A loop is provided to get the 10 nearest pick-up & go stores of user's default address: icirelais.relais.around
There's also a route to get 10 pick-p & go stores in another city: /module/icirelais/{city}/{zipcode}
This route uses "SearchCityController" controller. It generate a json output, which you can use with, for example, jquery/ajax.

If you want to show the store's address as delivery address, you just have to replace the "delivery-address" loop type by address.ici

If you want to add the package tracking link to the order email, you can use the loop: "icirelais.urltracking"
It take only one argument ref, that is the order's reference, and it has one output $URL.
If the link can't be generated, there's no output.
You can, for exemple, integrate the link like that in the email:
{loop name="tracking" type="icirelais.urltracking" ref=$REF}
You can track your package <a href="{$URL}">here</a>
{/loop}