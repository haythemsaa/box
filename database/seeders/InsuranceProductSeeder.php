<?php

namespace Database\Seeders;

use App\Models\InsuranceProduct;
use Illuminate\Database\Seeder;

class InsuranceProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $insuranceProducts = [
            [
                'tenant_id' => null, // Global products
                'name' => 'Assurance Essentielle',
                'description' => 'Protection de base pour vos biens stockés contre le vol, l\'incendie et les dégâts des eaux.',
                'max_coverage_amount' => 3000.00,
                'coverage_details' => 'Vol avec effraction, Incendie, Dégâts des eaux, Catastrophes naturelles',
                'exclusions' => 'Objets de valeur > 500€, Bijoux, Espèces, Documents confidentiels',
                'monthly_price' => 9.90,
                'yearly_price' => 99.00, // 2 mois offerts
                'commission_rate' => 25.00,
                'is_active' => true,
                'is_mandatory' => false,
            ],
            [
                'tenant_id' => null,
                'name' => 'Assurance Confort',
                'description' => 'Protection renforcée incluant la couverture des objets de valeur et une assistance 24/7.',
                'max_coverage_amount' => 7500.00,
                'coverage_details' => 'Toutes garanties Essentielle + Objets de valeur, Bris de glace, Vol sans effraction, Assistance rapatriement, Remplacement à neuf',
                'exclusions' => 'Bijoux > 2000€, Espèces > 500€, Oeuvres d\'art',
                'monthly_price' => 19.90,
                'yearly_price' => 199.00,
                'commission_rate' => 30.00,
                'is_active' => true,
                'is_mandatory' => false,
            ],
            [
                'tenant_id' => null,
                'name' => 'Assurance Premium',
                'description' => 'Protection maximale tous risques pour professionnels et particuliers exigeants.',
                'max_coverage_amount' => 15000.00,
                'coverage_details' => 'Toutes garanties Confort + Tous risques, Bijoux et oeuvres d\'art, Matériel professionnel, Marchandises, Responsabilité civile professionnelle, Perte d\'exploitation',
                'exclusions' => 'Produits périssables, Animaux vivants, Matières dangereuses',
                'monthly_price' => 34.90,
                'yearly_price' => 349.00,
                'commission_rate' => 35.00,
                'is_active' => true,
                'is_mandatory' => false,
            ],
            [
                'tenant_id' => null,
                'name' => 'Assurance Professionnelle',
                'description' => 'Solution dédiée aux professionnels avec couverture adaptée aux stocks et marchandises.',
                'max_coverage_amount' => 30000.00,
                'coverage_details' => 'Stocks et marchandises, Matériel professionnel, Archives et documents, Perte d\'exploitation, RC professionnelle, Protection juridique, Cyber-risques',
                'exclusions' => 'Produits périssables, Produits illégaux, Déchets dangereux',
                'monthly_price' => 59.90,
                'yearly_price' => 599.00,
                'commission_rate' => 40.00,
                'is_active' => true,
                'is_mandatory' => false,
            ],
            [
                'tenant_id' => null,
                'name' => 'Assurance Minimale (Obligatoire)',
                'description' => 'Assurance de base obligatoire pour tous les locataires.',
                'max_coverage_amount' => 1500.00,
                'coverage_details' => 'Responsabilité civile, Incendie, Explosion',
                'exclusions' => 'Vol, Dégâts des eaux (sauf incendie), Objets de valeur',
                'monthly_price' => 4.90,
                'yearly_price' => 49.00,
                'commission_rate' => 20.00,
                'is_active' => true,
                'is_mandatory' => true,
            ],
        ];

        foreach ($insuranceProducts as $product) {
            InsuranceProduct::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }

        $this->command->info('✓ Insurance products seeded! (5 products)');
    }
}
