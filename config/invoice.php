<?php
 
return [
    // Seuils de régime fiscal
    'tps_threshold' => 50000000, // 50M FCFA
    
    // TVA
    'tva_rate' => 18, // 18%
    'tva_enabled_for_regimes' => ['normal'], // TVA pour régime normal uniquement
    
    // Remise
    'remise_type' => 'fixed', // 'fixed' = montant FCFA, pas de %
    
    // Calcul
    'calculation_order' => [
        'ht_before_remise',
        'apply_remise',
        'ht_after_remise',
        'apply_tva',
        'ttc_final'
    ],
];