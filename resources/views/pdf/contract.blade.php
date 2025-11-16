<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat {{ $contract->contract_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header .contract-number {
            font-size: 14px;
            color: #666;
            font-weight: bold;
        }
        .parties {
            margin-bottom: 30px;
        }
        .party {
            margin-bottom: 20px;
            padding: 15px;
            background: #F3F4F6;
            border-left: 4px solid #4F46E5;
        }
        .party h3 {
            color: #4F46E5;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #4F46E5;
            font-size: 14px;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #E5E7EB;
        }
        .section-content {
            padding-left: 15px;
        }
        .box-details {
            background: #FEF3C7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #FDE047;
        }
        .box-details h4 {
            color: #F59E0B;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .details-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .details-row {
            display: table-row;
        }
        .details-cell {
            display: table-cell;
            padding: 5px 10px;
            width: 50%;
        }
        .details-cell strong {
            color: #4F46E5;
        }
        .terms {
            font-size: 10px;
            line-height: 1.4;
            margin-top: 20px;
        }
        .terms ol {
            margin-left: 20px;
        }
        .terms li {
            margin-bottom: 8px;
        }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-boxes {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            padding: 20px;
            border: 1px solid #E5E7EB;
        }
        .signature-box h4 {
            margin-bottom: 60px;
            font-size: 12px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .highlight {
            background: #E0E7FF;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRAT DE LOCATION DE BOX</h1>
        <div class="contract-number">N° {{ $contract->contract_number }}</div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Le Bailleur</h3>
            <strong>{{ $contract->box->site->name ?? 'BoxManager' }}</strong><br>
            {{ $contract->box->site->address ?? '' }}<br>
            {{ $contract->box->site->postal_code ?? '' }} {{ $contract->box->site->city ?? '' }}<br>
            @if($contract->box->site->email)
                Email: {{ $contract->box->site->email }}<br>
            @endif
            @if($contract->box->site->phone)
                Tél: {{ $contract->box->site->phone }}
            @endif
        </div>

        <div class="party">
            <h3>Le Locataire</h3>
            <strong>{{ $contract->customer->display_name }}</strong><br>
            @if($contract->customer->company_name)
                {{ $contract->customer->company_name }}<br>
                @if($contract->customer->siret)
                    SIRET: {{ $contract->customer->siret }}<br>
                @endif
            @endif
            {{ $contract->customer->address }}<br>
            {{ $contract->customer->postal_code }} {{ $contract->customer->city }}<br>
            {{ $contract->customer->country }}<br>
            Email: {{ $contract->customer->email }}<br>
            Tél: {{ $contract->customer->phone_mobile ?? $contract->customer->phone_landline ?? 'N/A' }}
        </div>
    </div>

    <div class="section">
        <h2>1. OBJET DU CONTRAT</h2>
        <div class="section-content">
            <p>Le présent contrat a pour objet la location d'un espace de stockage (ci-après "le Box") dans les conditions définies ci-après.</p>

            <div class="box-details">
                <h4>Détails du Box</h4>
                <div class="details-grid">
                    <div class="details-row">
                        <div class="details-cell"><strong>Numéro:</strong> {{ $contract->box->box_number }}</div>
                        <div class="details-cell"><strong>Site:</strong> {{ $contract->box->site->name }}</div>
                    </div>
                    <div class="details-row">
                        <div class="details-cell"><strong>Bâtiment:</strong> {{ $contract->box->building ?? 'N/A' }}</div>
                        <div class="details-cell"><strong>Étage:</strong> {{ $contract->box->floor ?? 'N/A' }}</div>
                    </div>
                    <div class="details-row">
                        <div class="details-cell"><strong>Dimensions:</strong> {{ $contract->box->length }}m x {{ $contract->box->width }}m x {{ $contract->box->height }}m</div>
                        <div class="details-cell"><strong>Volume:</strong> {{ $contract->box->volume }} m³</div>
                    </div>
                    <div class="details-row">
                        <div class="details-cell"><strong>Code d'accès:</strong> <span class="highlight">{{ $contract->access_code }}</span></div>
                        <div class="details-cell"><strong>Équipements:</strong>
                            @if($contract->box->has_electricity) Électricité @endif
                            @if($contract->box->is_climate_controlled) Climatisé @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>2. DURÉE DU CONTRAT</h2>
        <div class="section-content">
            <div class="details-grid">
                <div class="details-row">
                    <div class="details-cell"><strong>Date de début:</strong> {{ $contract->start_date->format('d/m/Y') }}</div>
                    <div class="details-cell"><strong>Date de fin:</strong> {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indéterminée' }}</div>
                </div>
            </div>
            <p style="margin-top: 10px;">
                Le contrat prend effet le {{ $contract->start_date->format('d/m/Y') }} et est conclu pour une durée
                @if($contract->end_date)
                    déterminée jusqu'au {{ $contract->end_date->format('d/m/Y') }}.
                @else
                    indéterminée.
                @endif
            </p>
        </div>
    </div>

    <div class="section">
        <h2>3. CONDITIONS FINANCIÈRES</h2>
        <div class="section-content">
            <div class="details-grid">
                <div class="details-row">
                    <div class="details-cell">
                        <strong>Loyer mensuel:</strong>
                        <span class="highlight">{{ number_format($contract->monthly_amount, 2, ',', ' ') }} {{ $contract->currency->symbol ?? '€' }}</span>
                    </div>
                    <div class="details-cell">
                        <strong>Fréquence de facturation:</strong>
                        @switch($contract->billing_frequency)
                            @case('monthly') Mensuelle @break
                            @case('quarterly') Trimestrielle @break
                            @case('yearly') Annuelle @break
                            @default {{ $contract->billing_frequency }} @break
                        @endswitch
                    </div>
                </div>
                <div class="details-row">
                    <div class="details-cell">
                        <strong>Caution:</strong> {{ number_format($contract->deposit_amount, 2, ',', ' ') }} {{ $contract->currency->symbol ?? '€' }}
                    </div>
                    <div class="details-cell">
                        <strong>Statut:</strong>
                        @switch($contract->status)
                            @case('active') <span style="color: #10B981;">Actif</span> @break
                            @case('pending') <span style="color: #F59E0B;">En attente</span> @break
                            @case('expired') <span style="color: #EF4444;">Expiré</span> @break
                            @default {{ $contract->status }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($contract->stored_items)
    <div class="section">
        <h2>4. OBJETS STOCKÉS</h2>
        <div class="section-content">
            <p>{{ $contract->stored_items }}</p>
        </div>
    </div>
    @endif

    <div class="section">
        <h2>{{ $contract->stored_items ? '5' : '4' }}. CONDITIONS GÉNÉRALES</h2>
        <div class="section-content">
            <div class="terms">
                <ol>
                    <li><strong>Utilisation du box:</strong> Le locataire s'engage à utiliser le box uniquement pour le stockage d'objets conformes à la législation en vigueur. Sont notamment interdits: produits inflammables, toxiques, périssables, illégaux ou dangereux.</li>
                    <li><strong>Accès:</strong> Le locataire dispose d'un code d'accès personnel et confidentiel. Il s'engage à ne pas le communiquer à des tiers.</li>
                    <li><strong>Paiement:</strong> Le loyer est payable d'avance selon la fréquence convenue. Tout retard de paiement entraînera l'application de pénalités.</li>
                    <li><strong>Assurance:</strong> Le locataire est responsable de l'assurance de ses biens stockés. Le bailleur décline toute responsabilité en cas de vol, dégradation ou destruction.</li>
                    <li><strong>Résiliation:</strong> Le contrat peut être résilié par l'une ou l'autre des parties moyennant un préavis de 30 jours par lettre recommandée avec accusé de réception.</li>
                    <li><strong>Caution:</strong> La caution sera restituée dans un délai de 30 jours après la fin du contrat, déduction faite des sommes éventuellement dues.</li>
                    <li><strong>Entretien:</strong> Le locataire s'engage à maintenir le box dans un état de propreté normal. Le bailleur se réserve le droit de visiter le box en présence du locataire.</li>
                    <li><strong>Modification:</strong> Toute modification des présentes conditions doit faire l'objet d'un avenant signé par les deux parties.</li>
                </ol>
            </div>
        </div>
    </div>

    @if($contract->notes)
    <div class="section">
        <h2>NOTES PARTICULIÈRES</h2>
        <div class="section-content">
            <p style="font-style: italic; color: #666;">{{ $contract->notes }}</p>
        </div>
    </div>
    @endif

    <div class="signature-section">
        <p style="text-align: center; margin-bottom: 20px;">
            <strong>Fait en deux exemplaires, le {{ $contract->signed_at ? $contract->signed_at->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
        </p>

        <div class="signature-boxes">
            <div class="signature-box" style="margin-right: 5%;">
                <h4>Le Bailleur</h4>
                <p>{{ $contract->box->site->name ?? 'BoxManager' }}</p>
                <div class="signature-line">
                    Signature et cachet
                </div>
            </div>
            <div class="signature-box">
                <h4>Le Locataire</h4>
                <p>{{ $contract->customer->display_name }}</p>
                <div class="signature-line">
                    Signature précédée de la mention "Lu et approuvé"
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Document généré le {{ now()->format('d/m/Y à H:i') }}</strong></p>
        <p>Ce contrat est soumis au droit français. En cas de litige, seuls les tribunaux français seront compétents.</p>
    </div>
</body>
</html>
