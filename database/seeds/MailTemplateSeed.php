<?php

use Illuminate\Database\Seeder;

class MailTemplateSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default = [
            [
                'name' => 'billet.emited',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => " Bonjour %SURNAME% %NAME%,
 
 Nous vous confirmons l'obtention d'un billet pour la soirée :
*  %BILLET-NAME% pour %NAME% %SURNAME%

%BILLET-QRCODE%

Pensez à vous munir d'une pièce d'identité pour la soirée.

Nous vous remercions et attendons avec impatience de vous voir.

Cordialement,

L'équipe organisatrice"
            ],
            [
                'name' => 'guichet.created',
                'title' => "Création du guichet %GUICHET-NAME%",
                'content' => "Bonjour,

Un guichet %GUICHET-NAME% vient d'être crée sur la billeterie. Ce dernier sera fonctionnel du %GUICHET-START-AT% au %GUICHET-END-AT%.

Lien: [%GUICHET-LINK%](%GUICHET-LINK%)

En cas de soucis, l'équipe est disponible par mail [%CONTACT%](mailto://%CONTACT%).

Cordialement,
L'équipe organisatrice"
            ],
            [
                'name' => 'billet.updated',
                'title' => "Commande mis à jour",
                'content' => " Bonjour %SURNAME% %NAME%,
 
 Votre dossier ayant été mis à jour, ce billet annule et remplace le précédent :
*  %BILLET-NAME% pour %NAME% %SURNAME%

%BILLET-QRCODE%

Pensez à vous munir d'une pièce d'identité pour la soirée.

Nous vous remercions et attendons avec impatience de vous voir.

Cordialement,

L'équipe organisatrice"
            ],
            [
                'name' => 'order.refused',
                'title' => "Annulation de commande",
                'content' => " Bonjour %ORDER-SURNAME% %ORDER-NAME%,
 
 Votre commande n°%ORDER-ID% vient d'être annulé suite au refus de votre moyen de paiment.
 
 Cordialement,
 L'équipe organisatrice"
            ],
            [
                'name' => 'order.validated',
                'title' => "Confirmation de commande",
                'content' => "Bonjour %ORDER-SURNAME% %ORDER-NAME%,

Nous vous confirmons l'obtention de billet(s) pour l'évenement:

%ORDER-SUMMARY%

Nous vous remercions et attendons avec impatience de vous voir lors de l'événement.

Cordialement,
L'équipe organisatrice."
            ],

        ];
        foreach ($default as $line)
        {
            $i = \App\Models\MailTemplate::firstOrNew(['name'=> $line['name']]);
            $i->title = $line['title'];
            $i->content = $line['content'];
            $i->save();
        }
    }
}
