<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Politique de Confidentialité - GloShop</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">GloShop</h1>
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Politique de Confidentialité</h1>
                <p class="text-gray-600 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                <div class="prose prose-lg max-w-none">
                    <!-- Introduction -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introduction</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Bienvenue sur GloShop. Nous nous engageons à protéger votre vie privée et à garantir la sécurité de vos informations personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons, partageons et protégeons vos données lorsque vous utilisez notre application mobile et nos services.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            En utilisant GloShop, vous acceptez les pratiques décrites dans cette politique. Si vous n'acceptez pas cette politique, veuillez ne pas utiliser nos services.
                        </p>
                    </section>

                    <!-- Données collectées -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Données que nous collectons</h2>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.1. Informations personnelles</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous collectons les informations suivantes lorsque vous créez un compte ou utilisez nos services :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Nom et prénom</li>
                            <li>Adresse e-mail</li>
                            <li>Numéro de téléphone</li>
                            <li>Adresses de livraison</li>
                            <li>Informations de paiement (cryptées)</li>
                            <li>Historique des commandes</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3 mt-6">2.2. Données d'utilisation</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous collectons automatiquement certaines informations lorsque vous utilisez notre application :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Données de localisation (si vous autorisez l'accès)</li>
                            <li>Informations sur votre appareil (modèle, système d'exploitation)</li>
                            <li>Données de navigation et d'interaction</li>
                            <li>Logs d'utilisation et erreurs techniques</li>
                        </ul>
                    </section>

                    <!-- Utilisation des données -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Comment nous utilisons vos données</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous utilisons vos données personnelles pour :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li><strong>Fournir nos services</strong> : Traiter vos commandes, gérer votre compte et vous fournir un support client</li>
                            <li><strong>Améliorer l'expérience utilisateur</strong> : Personnaliser votre expérience et améliorer nos services</li>
                            <li><strong>Communication</strong> : Vous envoyer des notifications importantes concernant vos commandes et nos services</li>
                            <li><strong>Sécurité</strong> : Détecter et prévenir la fraude, les abus et autres activités illégales</li>
                            <li><strong>Conformité légale</strong> : Respecter nos obligations légales et réglementaires</li>
                        </ul>
                    </section>

                    <!-- Partage des données -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Partage de vos données</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous ne vendons jamais vos données personnelles. Nous pouvons partager vos informations uniquement dans les cas suivants :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li><strong>Prestataires de services</strong> : Avec des tiers qui nous aident à exploiter notre service (livraison, paiement, hébergement)</li>
                            <li><strong>Obligations légales</strong> : Lorsque la loi l'exige ou pour répondre à une procédure judiciaire</li>
                            <li><strong>Protection de nos droits</strong> : Pour protéger nos droits, notre propriété ou notre sécurité</li>
                            <li><strong>Avec votre consentement</strong> : Dans tout autre cas avec votre autorisation explicite</li>
                        </ul>
                    </section>

                    <!-- Sécurité -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Sécurité de vos données</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données personnelles contre l'accès non autorisé, la perte, la destruction ou l'altération :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Chiffrement des données sensibles (SSL/TLS)</li>
                            <li>Stockage sécurisé des informations de paiement</li>
                            <li>Contrôles d'accès stricts</li>
                            <li>Surveillance régulière de nos systèmes</li>
                            <li>Formation de notre personnel à la protection des données</li>
                        </ul>
                    </section>

                    <!-- Vos droits -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Vos droits</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Conformément au Règlement Général sur la Protection des Données (RGPD) et aux lois applicables, vous disposez des droits suivants :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li><strong>Droit d'accès</strong> : Vous pouvez demander une copie de vos données personnelles</li>
                            <li><strong>Droit de rectification</strong> : Vous pouvez corriger vos données inexactes ou incomplètes</li>
                            <li><strong>Droit à l'effacement</strong> : Vous pouvez demander la suppression de vos données</li>
                            <li><strong>Droit à la portabilité</strong> : Vous pouvez recevoir vos données dans un format structuré</li>
                            <li><strong>Droit d'opposition</strong> : Vous pouvez vous opposer au traitement de vos données</li>
                            <li><strong>Droit de retirer votre consentement</strong> : À tout moment, vous pouvez retirer votre consentement</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed">
                            Pour exercer ces droits, contactez-nous à l'adresse indiquée dans la section "Contact" ci-dessous.
                        </p>
                    </section>

                    <!-- Cookies et technologies similaires -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Cookies et technologies similaires</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Notre application peut utiliser des cookies et des technologies similaires pour améliorer votre expérience. Ces technologies nous aident à :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Mémoriser vos préférences</li>
                            <li>Analyser l'utilisation de l'application</li>
                            <li>Améliorer nos services</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed">
                            Vous pouvez gérer vos préférences de cookies dans les paramètres de votre appareil.
                        </p>
                    </section>

                    <!-- Conservation des données -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Conservation des données</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous conservons vos données personnelles aussi longtemps que nécessaire pour :
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Fournir nos services</li>
                            <li>Respecter nos obligations légales</li>
                            <li>Résoudre les litiges</li>
                            <li>Faire respecter nos accords</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed">
                            Lorsque vos données ne sont plus nécessaires, nous les supprimons de manière sécurisée.
                        </p>
                    </section>

                    <!-- Modifications -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Modifications de cette politique</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Nous pouvons mettre à jour cette politique de confidentialité de temps à autre. Nous vous informerons de tout changement significatif en publiant la nouvelle politique sur cette page et en mettant à jour la date de "Dernière mise à jour".
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Nous vous encourageons à consulter régulièrement cette page pour rester informé de la façon dont nous protégeons vos informations.
                        </p>
                    </section>

                    <!-- Contact -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Contact</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Si vous avez des questions concernant cette politique de confidentialité ou si vous souhaitez exercer vos droits, veuillez nous contacter :
                        </p>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <p class="text-gray-700 mb-2"><strong>GloShop</strong></p>
                            <p class="text-gray-700 mb-2">Email : <a href="mailto:privacy@gloshop.com" class="text-blue-600 hover:underline">privacy@gloshop.com</a></p>
                            <p class="text-gray-700">Support : <a href="mailto:support@gloshop.com" class="text-blue-600 hover:underline">support@gloshop.com</a></p>
                        </div>
                    </section>

                    <!-- Juridiction -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Juridiction applicable</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Cette politique de confidentialité est régie par les lois françaises et européennes en matière de protection des données. Tout litige relatif à cette politique sera soumis à la juridiction des tribunaux compétents.
                        </p>
                    </section>
                </div>

                <!-- Footer -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <p class="text-sm text-gray-500 text-center">
                        © {{ date('Y') }} GloShop. Tous droits réservés.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>


