<?php


namespace App\Helpers;


class CommonHelper
{
    public function getCustomFieldValue($customFieldCode,$sub_domain_name) {
        

        if ($sub_domain_name == "o3group") {
              $customFieldArr = $this->getCustomFieldArrO3group();
        }else if($sub_domain_name == "mossrentals"){
            $customFieldArr = $this->getCustomFieldArrMoss();
        }else{
            $customFieldArr = $this->getCustomFieldArr();
        }

        if (isset($customFieldArr[$customFieldCode])) {
            return $customFieldArr[$customFieldCode];
        } else {
            return $customFieldCode;
        }
    }


    public function getCustomFieldArrMoss() {
        return $customFieldArr = [
            1000001 => 'CA',
            1000002 => 'CN',
            1000003 => 'US',
            1000004 => 'AU'];
      }
    
    public function getCustomFieldArrO3group() {
                  
            return $customFieldArr = [
            1000017 => "Black",
            1000021 => "Blue",
            1000126 => "Gold",
            1000115 => "Green",
            1000123 => "Grey",
            1000016 => "n/a",
            1000024 => "Orange",
            1000125 => "Pink",
            1000124 => "Purple",
            1000018 => "Red",
            1000019 => "Silver",
            1000020 => "White",
            1000022 => "Yellow",
            1000010 => "Magazijn",
            1000009 => "n/a",
            1000011 => "Shop BNP",
            1000005 => "AFT",
            1000004 => "Besix",
            1000026 => "BNP Paribas - Cash Management",
            1000002 => "BNP Paribas Fortis - BCQ",
            1000025 => "BNP Paribas Fortis - Foundation",
            1000028 => "BNP Paribas Fortis - RPB",
            1000027 => "BNP Paribas Fortis - WRT",
            1000007 => "FDT Group",
            1000003 => "Matexi",
            1000001 => "O3",
            1000008 => "Petercam",
            1000006 => "Straffe Streek",
            1000023 => "AFT / Functional",
            1000029 => "AFT / Goodies",
            1000030 => "AFT / Paddle Courts",
            1000031 => "AFT / Tennis Courts",
            1000032 => "Besix / Marketing Material",
            1000049 => "BNP Paribas / Audio-visual",
            1000051 => "BNP Paribas / Brand material",
            1000048 => "BNP Paribas / Catering",
            1000052 => "BNP Paribas / Decoration",
            1000033 => "BNP Paribas Fortis / Brand Material",
            1000040 => "BNP Paribas Fortis Foundation / Goodies",
            1000036 => "BNP Paribas Fortis / Functional",
            1000037 => "BNP Paribas Fortis / Goodies",
            1000034 => "BNP Paribas Fortis / Indoor Material",
            1000035 => "BNP Paribas Fortis / Outdoor Material",
            1000038 => "BNP Paribas Fortis WRT / Goodies",
            1000039 => "BNP Paribas Fortis WRT / Tennis Material",
            1000047 => "BNP Paribas / Furniture",
            1000050 => "BNP Paribas / Stand",
            1000091 => "Goodies / Bags",
            1000098 => "Goodies / Drinkware",
            1000097 => "Goodies / Giveaways",
            1000096 => "Goodies / Home &amp; Kitchen",
            1000093 => "Goodies / Office &amp; Stationery",
            1000095 => "Goodies / Outdoor &amp; Tools",
            1000094 => "Goodies / Sports &amp; Leisure",
            1000092 => "Goodies / Tech",
            1000099 => "Goodies / Wellness &amp; Beauty",
            1000044 => "Matexi / Fonctional",
            1000117 => "Matexi / Games &amp; Entertainment",
            1000041 => "Matexi / Goodies",
            1000042 => "Matexi / Indoor Material",
            1000043 => "Matexi / Outdoor Material",
            1000065 => "n/a",
            1000105 => "Paper Printing / Company Printing",
            1000108 => "Paper Printing / Horeca",
            1000107 => "Paper Printing / Packaging",
            1000106 => "Paper Printing / Promotional Printing",
            1000045 => "Petercam / Marketing Material",
            1000104 => "Promowear / Accessories",
            1000102 => "Promowear / Jackets",
            1000103 => "Promowear / Pants &amp; Shorts",
            1000101 => "Promowear / Pullovers",
            1000100 => "Promowear / T-shirts &amp; Polos",
            1000057 => "Rental / Audio-visual / Laptops &amp; tablets",
            1000058 => "Rental / Audio-visual / Podium &amp; Drape",
            1000059 => "Rental / Audio-visual / Screens &amp; Projectors",
            1000060 => "Rental / Audio-visual / Sound systems",
            1000061 => "Rental / Audio-visual / Spots &amp; lighting",
            1000079 => "Rental / Functional / Catering",
            1000078 => "Rental / Functional / Cloakroom",
            1000082 => "Rental / Functional / Heating",
            1000081 => "Rental / Functional / Separators",
            1000083 => "Rental / Functional / Signage",
            1000080 => "Rental / Functional / Workshops",
            1000072 => "Rental / Furniture &amp; Deco / Carpets",
            1000073 => "Rental / Furniture &amp; Deco / Desks &amp; Bars",
            1000074 => "Rental / Furniture &amp; Deco / Lamps",
            1000075 => "Rental / Furniture &amp; Deco / Plants &amp; Pots",
            1000076 => "Rental / Furniture &amp; Deco / Separators",
            1000077 => "Rental / Furniture &amp; Deco / Tables, Chairs, Lounge",
            1000064 => "Rental / Outdoor / Functional",
            1000062 => "Rental / Outdoor / Sport &amp; Entertainment",
            1000063 => "Rental / Outdoor / Tents, Parasols &amp; Heaters",
            1000084 => "Rental / Structures / Aluvision",
            1000085 => "Rental / Structures / Blacktruss",
            1000086 => "Rental / Structures / Meeting Rooms",
            1000116 => "Rental / Theme / Sint Nicolas",
            1000111 => "Roll &amp; Plate Printing / Banners",
            1000114 => "Roll &amp; Plate Printing / Decoration",
            1000109 => "Roll &amp; Plate Printing / Flags",
            1000112 => "Roll &amp; Plate Printing / Panels",
            1000110 => "Roll &amp; Plate Printing / Rollups &amp; Stands",
            1000113 => "Roll &amp; Plate Printing / Stickers",
            1000055 => "Services / Order Picking",
            1000056 => "Services / Shipments &amp; Deliveries",
            1000053 => "Services / Technical Interventions",
            1000054 => "Services / Warehousing",
            1000090 => "Stands / Meeting Rooms",
            1000088 => "Stands / Modular Stands",
            1000087 => "Stands / Pop-up Stands",
            1000089 => "Stands / Tailored Stands",
            1000046 => "Straffe Streek / Event Material",
            ];
    }

    public function getCustomFieldArr() {
        return $customFieldArr = [
            1000034 => 'Arri LED',
            1000035 => 'Litemat LED',
            1000036 => 'Kino LED',
            1000037 => 'Luniq LED',
            1000038 => 'Dedo LED',
            1000039 => 'Economy LED',
            1000040 => 'Arri M-Series HMI PAR',
            1000041 => 'Joker HMI',
            1000042 => 'Dedo HMI',
            1000043 => 'Economy HMI PAR',
            1000044 => 'Open Faced Tungsten Lamps',
            1000045 => 'Dedo Tungsten',
            1000046 => '2ft Kino Heads',
            1000047 => '4ft Kino Heads',
            1000048 => '2ft Kino Wands',
            1000049 => '4ft Kino Wands',
            1000050 => 'Kino Micro Flo',
            1000051 => '10AMP Cables',
            1000052 => '10AMP Distribution',
            1000053 => '32AMP Single Phase Cables',
            1000054 => '32AMP Single Phase Distribution and Adapters',
            1000055 => '63AMP Single Phase Cables',
            1000056 => '63AMP Single Phase Distribution and Adapters',
            1000057 => '32AMP Three Phase Ceeform',
            1000058 => '32AMP Three Phase Distribution',
            1000059 => '63AMP Three Phase Ceeform',
            1000060 => '63AMP Three Phase Distribution',
            1000061 => 'Dimmer & DMX',
            1000062 => 'Generators',
            1000063 => 'Batteries',
            1000064 => 'C-Stands',
            1000065 => 'Combo Stands',
            1000066 => 'Low Boys',
            1000067 => 'Assisted Rise Stands',
            1000068 => 'Hardware Essentials',
            1000069 => 'Clips',
            1000070 => 'Clamps',
            1000071 => 'Cardelleni Clamps',
            1000072 => 'Safety',
            1000073 => 'Polecats',
            1000074 => 'Boom Rigs',
            1000075 => 'Cutters',
            1000077 => 'Floppys',
            1000078 => '3x3 Diffusion Frames',
            1000079 => '4x4 Diffusion Frames',
            1000080 => 'Reflectors',
            1000081 => 'Cine Sticks',
            1000082 => '6x6 Frames',
            1000083 => '8x8 Frames',
            1000084 => '12x12 Frames',
            1000085 => '20x12 Frames',
            1000086 => '20x20 Frames',
            1000087 => 'Softboxes',
            1000088 => 'Eggcrates for Frames',
            1000089 => '4x4 Textiles',
            1000090 => '6x6 Textiles',
            1000091 => '8x8 Textiles',
            1000092 => '12x12 Textiles',
            1000093 => '20x12 Textiles',
            1000094 => '20x20 Textiles',
            1000095 => 'Solids',
            1000096 => 'Nets',
            1000097 => 'Green Screens',
            1000098 => 'Velvets and Shooting Blacks',
            1000099 => 'Basic',
            1000100 => 'Levelling',
            1000101 => 'Camera Rigging',
            1000102 => 'Car Rigging',
            1000103 => 'Dollies - Ride On',
            1000104 => 'Dolly Accessories',
            1000105 => 'Dolly Track',
            1000106 => 'Slider Kits',
            1000107 => 'Jib Kits',
            1000108 => 'Pipe Accessories',
            1000109 => 'Pipe Joiners',
            1000110 => 'Pipe Clamps',
            1000111 => 'Speedrail',
            1000112 => 'Pipe Lengths',
            1000113 => 'Smoke Machines',
            1000114 => 'Fog Liquid',
            1000115 => 'Vans',
            1000116 => 'Tubs',
            1000117 => 'Access',
            1000118 => 'Playback',
            1000119 => 'Weather Proofing',
            1000120 => 'Safety',
            1000121 => 'None',
            1000122 => 'LED',
            1000123 => 'Daylight',
            1000124 => 'Tungsten',
            1000125 => 'Grip',
            1000126 => 'Power',
            1000127 => 'C-Stand Accessories',
            1000128 => 'Hardware',
            1000129 => 'Rigging',
            1000130 => 'Fly Bags',
            1000131 => 'Fresnels',
            1000132 => 'Lanterns',
            1000133 => 'Eggcrates for Chimeras',
            1000134 => 'Eggcrates for Lamps',
            1000135 => 'Washing',
            1000136 => 'Grippy Basics',
            1000137 => 'Hazers',
            1000138 => 'Haze Liquid',
            1000139 => 'Fog Juice',
            1000140 => 'Ramps & Mats',
            1000141 => 'Popups',
            1000142 => 'Kino Tubes',
            1000143 => 'Kino Flo Fluorescent Wands',
            1000144 => 'Kino Flo Lighting Kits',
            1000145 => 'Kino Flo Fluorescent Tubes',
            1000146 => 'Octadomes',
            1000147 => 'Flag Bags',
            1000148 => 'Lightweight Stands (5/8th Spigot)',
            1000149 => 'Dollies - Lightweight',
            1000150 => 'Comfort',
            1000151 => 'Woodwork',
            1000152 => 'Cable Management',
            1000153 => 'Umbrellas',
            1000154 => 'Eggcrates for Softboxes',
            1000159 => 'Kino Packages',
            1000160 => 'LED Packages',
            1000161 => 'HMI Packages',
            1000162 => 'Tungsten Packages',
            1000163 => 'Mixed Lighting Packages',
            1000164 => 'Cable Packages',
            1000165 => 'Stand Packages',
            1000166 => '"T" Bar and Goalpost Kits',
            1000167 => 'Track Laying Accessories',
            1000168 => '6x6 Textile Packages',
            1000169 => '8x8 Textile Packages',
            1000170 => '12x12 Textile Packages',
            1000171 => 'Frame Packages',
            1000172 => 'Net Packages',
            1000173 => 'Cutter Packages',
            1000176 => 'Arri LED Softboxes',
            1000177 => 'Tungsten Softboxes and Lanterns',
            1000178 => 'Joker Softboxes and Lanterns',
            1000179 => 'Arri LED with Modifiers',
            1000180 => 'Arri LED Accessories',
            1000181 => 'Kino & Litemat LED',
            1000182 => 'LED - Economy',
            1000183 => 'LED Accessories',
            1000184 => 'Tungsten Lamps with Modifiers',
            1000185 => 'Eggcrates for Kinos',
            1000186 => '6x6 Frame Kits',
            1000187 => '8x8 Frame Kits',
            1000188 => '6x6 Outdoor Frame Kits',
            1000189 => '12x12 Frame Kits',
            1000190 => 'Textiles and Textile Support',
            1000191 => 'Weatherproofing',
            1000192 => 'Rigging Essentials',
            1000193 => 'DMX',
            1000194 => 'Dimmers',
            1000195 => 'Rigging Accessories',
            1000196 => 'Paddle Mount',
            1000197 => 'Essentials',
            1000198 => 'Smoke Machine Accessories',
            1000199 => 'Smoke / Haze Liquids',
            1000200 => 'Trolleys',
            1000201 => 'Aputure LED',            
            1000072 => 'Safety',            
            1000155 => 'Easy',
            1000156 => 'Intermediate',
            1000157 => 'Advanced',
            1000158 => 'Specialist Technician Required',
            1000023 => '2000K - 10,000K + Colour (RGB)',
            1000022 => '2000K - 8000K (Extended BiColour)',
            1000020 => '3200K, 3800K, 4400K, 5000K & 5600K',
            1000019 => '3200K, 4400K & 5600K',
            1000018 => '3200K & 5600K',
            1000021 => '3200K - 5600K (BiColour)',
            1000016 => '3200K Only',
            1000202 => '5500K',
            1000017 => '5600K Only',
            1000174 => 'None',
            1000025 => 'Battery Only',
            1000024 => 'Mains Power Only',
            1000026 => 'Mains Power or Battery',
            1000175 => 'None',
            1000051 => '10AMP Cables',
            1000052 => '10AMP Distribution',
            1000189 => '12x12 Frame Kits',
            1000084 => '12x12 Frames',
            1000170 => '12x12 Textile Packages',
            1000092 => '12x12 Textiles',
            1000085 => '20x12 Frames',
            1000093 => '20x12 Textiles',
            1000086 => '20x20 Frames',
            1000094 => '20x20 Textiles',
            1000046 => '2ft Kino Heads',
            1000048 => '2ft Kino Wands',
            1000053 => '32AMP Single Phase Cables',
            1000054 => '32AMP Single Phase Distribution and Adapters',
            1000057 => '32AMP Three Phase Ceeform',
            1000058 => '32AMP Three Phase Distribution',
            1000078 => '3x3 Diffusion Frames',
            1000047 => '4ft Kino Heads',
            1000049 => '4ft Kino Wands',
            1000079 => '4x4 Diffusion Frames',
            1000089 => '4x4 Textiles',
            1000055 => '63AMP Single Phase Cables',
            1000056 => '63AMP Single Phase Distribution and Adapters',
            1000059 => '63AMP Three Phase Ceeform',
            1000060 => '63AMP Three Phase Distribution',
            1000186 => '6x6 Frame Kits',
            1000082 => '6x6 Frames',
            1000188 => '6x6 Outdoor Frame Kits',
            1000168 => '6x6 Textile Packages',
            1000090 => '6x6 Textiles',
            1000187 => '8x8 Frame Kits',
            1000083 => '8x8 Frames',
            1000169 => '8x8 Textile Packages',
            1000091 => '8x8 Textiles',
            1000117 => 'Access',
            1000201 => 'Aputure LED',
            1000034 => 'Arri LED',
            1000180 => 'Arri LED Accessories',
            1000176 => 'Arri LED Softboxes',
            1000179 => 'Arri LED with Modifiers',
            1000040 => 'Arri M-Series HMI PAR',
            1000067 => 'Assisted Rise Stands',
            1000099 => 'Basic',
            1000063 => 'Batteries',
            1000074 => 'Boom Rigs',
            1000152 => 'Cable Management',
            1000164 => 'Cable Packages',
            1000101 => 'Camera Rigging',
            1000071 => 'Cardelleni Clamps',
            1000102 => 'Car Rigging',
            1000081 => 'Cine Sticks',
            1000070 => 'Clamps',
            1000069 => 'Clips',
            1000065 => 'Combo Stands',
            1000150 => 'Comfort',
            1000127 => 'C-Stand Accessories',
            1000064 => 'C-Stands',
            1000173 => 'Cutter Packages',
            1000075 => 'Cutters',
            1000123 => 'Daylight',
            1000042 => 'Dedo HMI',
            1000038 => 'Dedo LED',
            1000045 => 'Dedo Tungsten',
            1000061 => 'Dimmer &amp; DMX',
            1000194 => 'Dimmers',
            1000193 => 'DMX',
            1000149 => 'Dollies - Lightweight',
            1000103 => 'Dollies - Ride On',
            1000104 => 'Dolly Accessories',
            1000105 => 'Dolly Track',
            1000043 => 'Economy HMI PAR',
            1000039 => 'Economy LED',
            1000133 => 'Eggcrates for Chimeras',
            1000088 => 'Eggcrates for Frames',
            1000185 => 'Eggcrates for Kinos',
            1000134 => 'Eggcrates for Lamps',
            1000154 => 'Eggcrates for Softboxes',
            1000197 => 'Essentials',
            1000147 => 'Flag Bags',
            1000077 => 'Floppys',
            1000130 => 'Fly Bags',
            1000139 => 'Fog Juice',
            1000114 => 'Fog Liquid',
            1000171 => 'Frame Packages',
            1000131 => 'Fresnels',
            1000062 => 'Generators',
            1000097 => 'Green Screens',
            1000125 => 'Grip',
            1000136 => 'Grippy Basics',
            1000128 => 'Hardware',
            1000068 => 'Hardware Essentials',
            1000138 => 'Haze Liquid',
            1000137 => 'Hazers',
            1000161 => 'HMI Packages',
            1000107 => 'Jib Kits',
            1000041 => 'Joker HMI',
            1000178 => 'Joker Softboxes and Lanterns',
            1000145 => 'Kino Flo Fluorescent Tubes',
            1000143 => 'Kino Flo Fluorescent Wands',
            1000144 => 'Kino Flo Lighting Kits',
            1000036 => 'Kino LED',
            1000181 => 'Kino &amp; Litemat LED',
            1000050 => 'Kino Micro Flo',
            1000159 => 'Kino Packages',
            1000142 => 'Kino Tubes',
            1000132 => 'Lanterns',
            1000122 => 'LED',
            1000183 => 'LED Accessories',
            1000182 => 'LED - Economy',
            1000160 => 'LED Packages',
            1000100 => 'Levelling',
            1000148 => 'Lightweight Stands (5/8th Spigot)',
            1000035 => 'Litemat LED',
            1000066 => 'Low Boys',
            1000037 => 'Luniq LED',
            1000163 => 'Mixed Lighting Packages',
            1000172 => 'Net Packages',
            1000096 => 'Nets',
            1000121 => 'None',
            1000146 => 'Octadomes',
            1000044 => 'Open Faced Tungsten Lamps',
            1000196 => 'Paddle Mount',
            1000108 => 'Pipe Accessories',
            1000110 => 'Pipe Clamps',
            1000109 => 'Pipe Joiners',
            1000112 => 'Pipe Lengths',
            1000118 => 'Playback',
            1000073 => 'Polecats',
            1000141 => 'Popups',
            1000126 => 'Power',
            1000140 => 'Ramps &amp; Mats',
            1000080 => 'Reflectors',
            1000129 => 'Rigging',
            1000195 => 'Rigging Accessories',
            1000192 => 'Rigging Essentials',
            1000120 => 'Safety',
            1000072 => 'Safety',
            1000106 => 'Slider Kits',
            1000199 => 'Smoke / Haze Liquids',
            1000198 => 'Smoke Machine Accessories',
            1000113 => 'Smoke Machines',
            1000087 => 'Softboxes',
            1000095 => 'Solids',
            1000111 => 'Speedrail',
            1000165 => 'Stand Packages',
            1000166  => 'T Bar and Goalpost Kits',
            1000190 => 'Textiles and Textile Support',
            1000167 => 'Track Laying Accessories',
            1000200 => 'Trolleys',
            1000116 => 'Tubs',
            1000124 => 'Tungsten',
            1000184 => 'Tungsten Lamps with Modifiers',
            1000162 => 'Tungsten Packages',
            1000177 => 'Tungsten Softboxes and Lanterns',
            1000153 => 'Umbrellas',
            1000115 => 'Vans',
            1000098 => 'Velvets and Shooting Blacks',
            1000135 => 'Washing',
            1000191 => 'Weatherproofing',
            1000119 => 'Weather Proofing',
            1000151 => 'Woodwork'
            
        ];
    }
}