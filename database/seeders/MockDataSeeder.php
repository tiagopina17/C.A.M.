<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use Illuminate\Database\Seeder;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds with temporary mock data.
     * All items include "[MOCK]" in their titles for easy cleanup.
     */
    public function run(): void
    {
        // 1. Categories
        $catMercearia = Category::firstOrCreate(
            ['slug' => 'mock-mercearias'],
            [
                'name' => '[MOCK] Mercearias',
                'description' => 'Mercearias de bairro com produtos secos, conservas e azeite.',
                'icon' => 'fa-basket-shopping',
            ]
        );

        $catPadaria = Category::firstOrCreate(
            ['slug' => 'mock-padarias'],
            [
                'name' => '[MOCK] Padarias & Pastelarias',
                'description' => 'Pão cozido em forno de lenha, broas e doces tradicionais.',
                'icon' => 'fa-bread-slice',
            ]
        );

        $catFrutaria = Category::firstOrCreate(
            ['slug' => 'mock-frutarias'],
            [
                'name' => '[MOCK] Frutarias & Hortícolas',
                'description' => 'Fruta fresca nacional, legumes da horta e produtos da época.',
                'icon' => 'fa-apple-whole',
            ]
        );

        $catTalho = Category::firstOrCreate(
            ['slug' => 'mock-talhos'],
            [
                'name' => '[MOCK] Talhos & Peixarias',
                'description' => 'Carnes selecionadas e peixe fresco da costa portuguesa.',
                'icon' => 'fa-drumstick-bite',
            ]
        );

        $catDrogaria = Category::firstOrCreate(
            ['slug' => 'mock-drogarias'],
            [
                'name' => '[MOCK] Drogarias & Tradicionais',
                'description' => 'Drogarias com produtos para o lar, retrosarias e utilidades.',
                'icon' => 'fa-spray-can',
            ]
        );

        // 2. Services (products/types offered)
        $srvMercearia = Service::firstOrCreate(
            ['slug' => 'mock-produtos-mercearia'],
            ['category_id' => $catMercearia->id, 'name' => '[MOCK] Mercearia Tradicional']
        );

        $srvPadaria = Service::firstOrCreate(
            ['slug' => 'mock-pao-artesanal'],
            ['category_id' => $catPadaria->id, 'name' => '[MOCK] Pão Artesanal e Broa']
        );

        $srvFruta = Service::firstOrCreate(
            ['slug' => 'mock-fruta-fresca'],
            ['category_id' => $catFrutaria->id, 'name' => '[MOCK] Fruta e Legumes da Terra']
        );

        $srvTalho = Service::firstOrCreate(
            ['slug' => 'mock-carnes-nacionais'],
            ['category_id' => $catTalho->id, 'name' => '[MOCK] Carnes e Peixe Fresco']
        );

        $srvDrogaria = Service::firstOrCreate(
            ['slug' => 'mock-artigos-casa'],
            ['category_id' => $catDrogaria->id, 'name' => '[MOCK] Utilidades do Lar']
        );

        // 3. Stores (all with "[MOCK]" in title)
        $storesData = [
            [
                'name' => '[MOCK] Mercearia do Sr. José',
                'description' => 'Fundada em 1968. Queijo da serra, azeite virgem transmontano, conservas artesanais e aquele atendimento de vizinho que já não se encontra nos hipermercados.',
                'place_id' => 'mock_place_jose',
                'lat' => 38.7118,
                'lon' => -9.1322,
                'services' => [$srvMercearia->id],
            ],
            [
                'name' => '[MOCK] Padaria do Bairro Velho',
                'description' => 'Fornadas quentes às 07h00 e às 17h00. Pão de centeio, broa de milho amarela e pastéis de nata estaladiços saídos do forno.',
                'place_id' => 'mock_place_padaria',
                'lat' => 38.7142,
                'lon' => -9.1350,
                'services' => [$srvPadaria->id],
            ],
            [
                'name' => '[MOCK] Frutaria Rosa & Filhos',
                'description' => 'Fruta escolhida diariamente na praça. Laranjas do Algarve, maçãs de Alcobaça e legumes frescos entregues por produtores locais da região.',
                'place_id' => 'mock_place_frutaria',
                'lat' => 38.7160,
                'lon' => -9.1390,
                'services' => [$srvFruta->id],
            ],
            [
                'name' => '[MOCK] Talho São Pedro',
                'description' => 'Carnes de pasto com corte à antiga. Chouriços caseiros, alheiras de Mirandela e conselhos culinários do mestre cortador.',
                'place_id' => 'mock_place_talho',
                'lat' => 38.7130,
                'lon' => -9.1410,
                'services' => [$srvTalho->id],
            ],
            [
                'name' => '[MOCK] Drogaria da Esquina',
                'description' => 'Tudo o que precisa para a casa e pequenas reparações. Desde sabão azul e branco a pincéis, utilidades e parafusos avulso.',
                'place_id' => 'mock_place_drogaria',
                'lat' => 38.7105,
                'lon' => -9.1365,
                'services' => [$srvDrogaria->id],
            ],
            [
                'name' => '[MOCK] Pastelaria e Café Central',
                'description' => 'Ponto de encontro dos vizinhos. Bicas tiradas com mestria, queijadas da vila e torradas em pão caseiro amanteigado.',
                'place_id' => 'mock_place_pastelaria',
                'lat' => 38.7122,
                'lon' => -9.1380,
                'services' => [$srvPadaria->id, $srvMercearia->id],
            ],
            [
                'name' => '[MOCK] Peixaria Mar Fresco',
                'description' => 'Robalos, douradas, carapauzinhos e sardinhas frescas acabadas de chegar da lota de Peniche. Peixe amanhado e escalado a gosto.',
                'place_id' => 'mock_place_peixaria',
                'lat' => 38.7155,
                'lon' => -9.1335,
                'services' => [$srvTalho->id],
            ],
            [
                'name' => '[MOCK] Mercearia e Vinhos da Praça',
                'description' => 'Garrafeira de pequenos produtores portugueses, frutos secos a granel, mel de rosmaninho e enchidos fumados no pote.',
                'place_id' => 'mock_place_vinhos',
                'lat' => 38.7170,
                'lon' => -9.1340,
                'services' => [$srvMercearia->id],
            ],
        ];

        foreach ($storesData as $data) {
            $services = $data['services'];
            unset($data['services']);

            $store = Store::firstOrCreate(
                ['name' => $data['name']],
                $data
            );

            $store->services()->syncWithoutDetaching($services);
        }
    }
}
