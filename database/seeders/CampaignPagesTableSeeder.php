<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CampaignPagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('campaign_pages')->delete();

        \DB::table('campaign_pages')->insert(array (
            0 =>
            array (
                'id' => 1,
                'campaign_id' => 1,
                'organization_id' => 1,
                'locale' => 'de',
                'slug' => 'kein-beton-am-silbersee',
                'content' => '{"time": 1776121430552, "blocks": [{"id": "m99ti8styD", "data": {"text": "Der Silbersee ist das letzte intakte Naherholungsgebiet in unserer Region. Er ist die Heimat seltener Vogelarten, darunter der streng geschützte Eisvogel, und ein Rückzugsort für Tausende von Menschen. Ein Mega-Einkaufszentrum würde nicht nur die Tierwelt vertreiben, sondern auch ein massives Verkehrsaufkommen durch unser beschauliches Tal leiten."}, "type": "paragraph"}, {"id": "QPp2QpBOL_", "data": {"text": "Unsere Forderungen an den Gemeinderat:", "level": 2}, "type": "header"}, {"id": "nhbL5ihTnR", "data": {"items": ["Keine Umzonung:&nbsp;Das Seeufer muss als Naturschutzzone erhalten bleiben.", "Transparenz:&nbsp;Sofortige Offenlegung aller geheimen Verkehrsgutachten.", "Bürgerbeteiligung:&nbsp;Eine verbindliche Volksabstimmung über die Zukunft des Areals."], "style": "unordered"}, "type": "list"}, {"id": "ZbsMcXeE4l", "data": {"text": "Unterstütze uns jetzt. "}, "type": "paragraph"}, {"id": "xZsd7bRHMl", "data": {"text": "<b>Jede Stimme zählt, um den See für kommende Generationen zu bewahren!</b>"}, "type": "paragraph"}], "version": "2.30.6"}',
                'theme' => 'minimal',
                'is_published' => 1,
                'created_at' => '2026-04-13 22:48:48',
                'updated_at' => '2026-04-14 01:03:50',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'campaign_id' => 1,
                'organization_id' => 1,
                'locale' => 'en',
                'slug' => 'no-concrete-at-silbersee',
                'content' => '{"time": 1776121430552, "blocks": [{"id": "m99ti8styD", "data": {"text": "Lake Silbersee is the last intact local recreation area in our region. It is home to rare bird species, including the strictly protected kingfisher, and a retreat for thousands of people. A mega-mall would not only drive away the wildlife but also route massive traffic through our tranquil valley."}, "type": "paragraph"}, {"id": "QPp2QpBOL_", "data": {"text": "Our demands to the local council:", "level": 2}, "type": "header"}, {"id": "nhbL5ihTnR", "data": {"items": ["No rezoning:&nbsp;The lake shore must be preserved as a nature conservation area.", "Transparency:&nbsp;Immediate disclosure of all secret traffic reports.", "Citizen participation:&nbsp;A binding referendum on the future of the area."], "style": "unordered"}, "type": "list"}, {"id": "ZbsMcXeE4l", "data": {"text": "Support us now. "}, "type": "paragraph"}, {"id": "xZsd7bRHMl", "data": {"text": "<b>Every vote counts to preserve the lake for future generations!</b>"}, "type": "paragraph"}], "version": "2.30.6"}',
                'theme' => 'minimal',
                'is_published' => 1,
                'created_at' => '2026-04-13 22:48:48',
                'updated_at' => '2026-04-14 01:03:50',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'campaign_id' => 1,
                'organization_id' => 1,
                'locale' => 'fr',
                'slug' => 'pas-de-beton-au-lac-silbersee',
                'content' => '{"time": 1776121430552, "blocks": [{"id": "m99ti8styD", "data": {"text": "Le lac de Silbersee est la dernière zone de loisirs intacte de notre région. Il abrite des espèces d\'oiseaux rares, dont le martin-pêcheur strictement protégé, et constitue un refuge pour des milliers de personnes. Un méga-centre commercial ne chasserait pas seulement la faune, mais dirigerait également un trafic massif à travers notre vallée tranquille."}, "type": "paragraph"}, {"id": "QPp2QpBOL_", "data": {"text": "Nos exigences envers le conseil communal :", "level": 2}, "type": "header"}, {"id": "nhbL5ihTnR", "data": {"items": ["Pas de déclassement :&nbsp;Les rives du lac doivent être préservées en tant que zone de protection de la nature.", "Transparence :&nbsp;Divulgation immédiate de toutes les expertises secrètes sur la circulation.", "Participation citoyenne :&nbsp;Un référendum contraignant sur l\'avenir du site."], "style": "unordered"}, "type": "list"}, {"id": "ZbsMcXeE4l", "data": {"text": "Soutenez-nous maintenant. "}, "type": "paragraph"}, {"id": "xZsd7bRHMl", "data": {"text": "<b>Chaque voix compte pour préserver le lac pour les générations futures !</b>"}, "type": "paragraph"}], "version": "2.30.6"}',
                'theme' => 'minimal',
                'is_published' => 1,
                'created_at' => '2026-04-13 22:48:48',
                'updated_at' => '2026-04-14 01:03:50',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'campaign_id' => 1,
                'organization_id' => 1,
                'locale' => 'it',
                'slug' => 'niente-cemento-al-lago-silbersee',
                'content' => '{"time": 1776121430552, "blocks": [{"id": "m99ti8styD", "data": {"text": "Il lago Silbersee è l\'ultima area ricreativa intatta della nostra regione. Ospita rare specie di uccelli, tra cui il martin pescatore rigorosamente protetto, ed è un rifugio per migliaia di persone. Un mega-centro commerciale non solo allontanerebbe la fauna selvatica, ma convoglierebbe anche un traffico massiccio attraverso la nostra tranquilla valle."}, "type": "paragraph"}, {"id": "QPp2QpBOL_", "data": {"text": "Le nostre richieste al consiglio comunale:", "level": 2}, "type": "header"}, {"id": "nhbL5ihTnR", "data": {"items": ["Nessun cambio di destinazione d\'uso:&nbsp;La riva del lago deve essere preservata come area di conservazione della natura.", "Trasparenza:&nbsp;Divulgazione immediata di tutte le perizie segrete sul traffico.", "Partecipazione dei cittadini:&nbsp;Un referendum vincolante sul futuro dell\'area."], "style": "unordered"}, "type": "list"}, {"id": "ZbsMcXeE4l", "data": {"text": "Sostienici ora. "}, "type": "paragraph"}, {"id": "xZsd7bRHMl", "data": {"text": "<b>Ogni voce conta per preservare il lago per le generazioni future!</b>"}, "type": "paragraph"}], "version": "2.30.6"}',
                'theme' => 'minimal',
                'is_published' => 1,
                'created_at' => '2026-04-13 22:48:48',
                'updated_at' => '2026-04-14 01:03:50',
                'deleted_at' => NULL,
            ),
        ));
    }
}
