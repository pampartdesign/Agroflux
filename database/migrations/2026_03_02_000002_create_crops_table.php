<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 120)->index();
            $table->string('scientific_name', 160)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')->on('crop_categories')
                  ->cascadeOnDelete();
        });

        // ── Seed crops ────────────────────────────────────────────────
        // We resolve category IDs by name so the order of inserts doesn't matter
        $cats = DB::table('crop_categories')->pluck('id', 'name');
        $now  = now();

        $crops = [
            // CEREALS
            ['name' => 'Wheat',      'scientific_name' => 'Triticum aestivum',                'category' => 'Cereal'],
            ['name' => 'Barley',     'scientific_name' => 'Hordeum vulgare',                  'category' => 'Cereal'],
            ['name' => 'Maize',      'scientific_name' => 'Zea mays',                         'category' => 'Cereal'],
            ['name' => 'Rye',        'scientific_name' => 'Secale cereale',                   'category' => 'Cereal'],
            ['name' => 'Oats',       'scientific_name' => 'Avena sativa',                     'category' => 'Cereal'],
            ['name' => 'Triticale',  'scientific_name' => 'Triticosecale',                    'category' => 'Cereal'],
            ['name' => 'Sorghum',    'scientific_name' => 'Sorghum bicolor',                  'category' => 'Cereal'],
            ['name' => 'Rice',       'scientific_name' => 'Oryza sativa',                     'category' => 'Cereal'],
            ['name' => 'Millet',     'scientific_name' => 'Panicum miliaceum',                'category' => 'Cereal'],
            // OILSEEDS
            ['name' => 'Sunflower',  'scientific_name' => 'Helianthus annuus',                'category' => 'Oilseed'],
            ['name' => 'Rapeseed',   'scientific_name' => 'Brassica napus',                   'category' => 'Oilseed'],
            ['name' => 'Soybean',    'scientific_name' => 'Glycine max',                      'category' => 'Oilseed'],
            ['name' => 'Linseed',    'scientific_name' => 'Linum usitatissimum',              'category' => 'Oilseed'],
            // INDUSTRIAL
            ['name' => 'Cotton',     'scientific_name' => 'Gossypium spp.',                   'category' => 'Industrial'],
            ['name' => 'Sugar Beet', 'scientific_name' => 'Beta vulgaris',                    'category' => 'Industrial'],
            ['name' => 'Tobacco',    'scientific_name' => 'Nicotiana tabacum',                'category' => 'Industrial'],
            ['name' => 'Hops',       'scientific_name' => 'Humulus lupulus',                  'category' => 'Industrial'],
            // PROTEIN
            ['name' => 'Field Pea',  'scientific_name' => 'Pisum sativum',                   'category' => 'Protein'],
            ['name' => 'Broad Bean', 'scientific_name' => 'Vicia faba',                      'category' => 'Protein'],
            ['name' => 'Lentil',     'scientific_name' => 'Lens culinaris',                  'category' => 'Protein'],
            ['name' => 'Chickpea',   'scientific_name' => 'Cicer arietinum',                 'category' => 'Protein'],
            ['name' => 'Lupin',      'scientific_name' => 'Lupinus albus',                   'category' => 'Protein'],
            // ROOT & TUBER
            ['name' => 'Potato',       'scientific_name' => 'Solanum tuberosum',             'category' => 'Root'],
            ['name' => 'Sweet Potato', 'scientific_name' => 'Ipomoea batatas',               'category' => 'Root'],
            ['name' => 'Sugar Turnip', 'scientific_name' => 'Brassica rapa',                 'category' => 'Root'],
            // VEGETABLES
            ['name' => 'Tomato',       'scientific_name' => 'Solanum lycopersicum',          'category' => 'Vegetable'],
            ['name' => 'Onion',        'scientific_name' => 'Allium cepa',                   'category' => 'Vegetable'],
            ['name' => 'Garlic',       'scientific_name' => 'Allium sativum',                'category' => 'Vegetable'],
            ['name' => 'Carrot',       'scientific_name' => 'Daucus carota',                 'category' => 'Vegetable'],
            ['name' => 'Cabbage',      'scientific_name' => 'Brassica oleracea',             'category' => 'Vegetable'],
            ['name' => 'Cauliflower',  'scientific_name' => 'Brassica oleracea var. botrytis', 'category' => 'Vegetable'],
            ['name' => 'Broccoli',     'scientific_name' => 'Brassica oleracea var. italica',  'category' => 'Vegetable'],
            ['name' => 'Pepper',       'scientific_name' => 'Capsicum annuum',               'category' => 'Vegetable'],
            ['name' => 'Eggplant',     'scientific_name' => 'Solanum melongena',             'category' => 'Vegetable'],
            ['name' => 'Zucchini',     'scientific_name' => 'Cucurbita pepo',               'category' => 'Vegetable'],
            ['name' => 'Pumpkin',      'scientific_name' => 'Cucurbita maxima',             'category' => 'Vegetable'],
            ['name' => 'Lettuce',      'scientific_name' => 'Lactuca sativa',               'category' => 'Vegetable'],
            ['name' => 'Spinach',      'scientific_name' => 'Spinacia oleracea',            'category' => 'Vegetable'],
            ['name' => 'Cucumber',     'scientific_name' => 'Cucumis sativus',             'category' => 'Vegetable'],
            ['name' => 'Melon',        'scientific_name' => 'Cucumis melo',                'category' => 'Vegetable'],
            ['name' => 'Watermelon',   'scientific_name' => 'Citrullus lanatus',           'category' => 'Vegetable'],
            // FRUIT & PERENNIAL
            ['name' => 'Apple',          'scientific_name' => 'Malus domestica',                      'category' => 'Fruit'],
            ['name' => 'Pear',           'scientific_name' => 'Pyrus communis',                       'category' => 'Fruit'],
            ['name' => 'Peach',          'scientific_name' => 'Prunus persica',                       'category' => 'Fruit'],
            ['name' => 'Nectarine',      'scientific_name' => 'Prunus persica var. nucipersica',      'category' => 'Fruit'],
            ['name' => 'Cherry',         'scientific_name' => 'Prunus avium',                         'category' => 'Fruit'],
            ['name' => 'Apricot',        'scientific_name' => 'Prunus armeniaca',                     'category' => 'Fruit'],
            ['name' => 'Plum',           'scientific_name' => 'Prunus domestica',                     'category' => 'Fruit'],
            ['name' => 'Olive',          'scientific_name' => 'Olea europaea',                        'category' => 'Fruit'],
            ['name' => 'Orange',         'scientific_name' => 'Citrus sinensis',                      'category' => 'Fruit'],
            ['name' => 'Mandarin',       'scientific_name' => 'Citrus reticulata',                    'category' => 'Fruit'],
            ['name' => 'Lemon',          'scientific_name' => 'Citrus limon',                         'category' => 'Fruit'],
            ['name' => 'Grape Vineyard', 'scientific_name' => 'Vitis vinifera',                       'category' => 'Fruit'],
            ['name' => 'Almond',         'scientific_name' => 'Prunus dulcis',                        'category' => 'Fruit'],
            ['name' => 'Walnut',         'scientific_name' => 'Juglans regia',                        'category' => 'Fruit'],
            ['name' => 'Hazelnut',       'scientific_name' => 'Corylus avellana',                     'category' => 'Fruit'],
            ['name' => 'Pistachio',      'scientific_name' => 'Pistacia vera',                        'category' => 'Fruit'],
            ['name' => 'Fig',            'scientific_name' => 'Ficus carica',                         'category' => 'Fruit'],
            ['name' => 'Pomegranate',    'scientific_name' => 'Punica granatum',                      'category' => 'Fruit'],
            ['name' => 'Avocado',        'scientific_name' => 'Persea americana',                     'category' => 'Fruit'],
            // FODDER
            ['name' => 'Alfalfa',      'scientific_name' => 'Medicago sativa',  'category' => 'Fodder'],
            ['name' => 'Clover',       'scientific_name' => 'Trifolium pratense','category' => 'Fodder'],
            ['name' => 'Silage Maize', 'scientific_name' => 'Zea mays',         'category' => 'Fodder'],
            ['name' => 'Grass Pasture','scientific_name' => null,                'category' => 'Fodder'],
            ['name' => 'Mixed Forage', 'scientific_name' => null,                'category' => 'Fodder'],
            // OTHER
            ['name' => 'Buckwheat', 'scientific_name' => 'Fagopyrum esculentum', 'category' => 'Other'],
            ['name' => 'Quinoa',    'scientific_name' => 'Chenopodium quinoa',   'category' => 'Other'],
        ];

        $rows = [];
        foreach ($crops as $c) {
            $rows[] = [
                'category_id'     => $cats[$c['category']],
                'name'            => $c['name'],
                'scientific_name' => $c['scientific_name'],
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('crops')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
