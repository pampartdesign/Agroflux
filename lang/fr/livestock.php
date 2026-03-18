<?php

return [

    // --- Dashboard ---
    'management_title'        => 'Gestion de l’élevage',
    'management_subtitle'     => 'Vue d’ensemble de votre cheptel, de la production et des contrôles quotidiens.',
    'btn_add_animal'          => '+ Ajouter un animal',

    // KPI labels (dashboard)
    'kpi_total_animals'       => 'Nombre total d’animaux',
    'kpi_registered_stock'    => 'Cheptel enregistré',
    'kpi_pregnant'            => 'Gestantes',
    'kpi_sick'                => ':count malades',
    'kpi_no_sick'             => 'Aucun animal malade',
    'kpi_checks_today'        => 'Contrôles aujourd’hui',
    'kpi_alerts_month'        => ':count alertes ce mois-ci',
    'kpi_no_alerts'           => 'Aucune alerte ce mois-ci',
    'kpi_produce_today'       => 'Production du jour',
    'kpi_entries_logged'      => 'Entrées enregistrées',

    // Recent checks panel
    'recent_checks'           => 'Contrôles récents',
    'view_all'                => 'Voir tout →',
    'no_checks_yet'           => 'Aucun contrôle enregistré pour le moment.',
    'btn_log_first_check'     => '+ Enregistrer le premier contrôle',

    // Species sidebar
    'species_title'           => 'Espèces',
    'no_animals_yet'          => 'Aucun animal pour le moment.',

    // Quick access sidebar
    'quick_access'            => 'Accès rapide',
    'stock_management_title'  => 'Gestion du cheptel',
    'animals_registered'      => 'animal enregistré',
    'animals_registered_pl'   => 'animaux enregistrés',
    'produce_management_title'=> 'Gestion de la production',
    'produce_management_desc' => 'Enregistrer lait, œufs, laine et autres',
    'routine_monitor_title'   => 'Suivi des routines',
    'checks_today_singular'   => 'contrôle aujourd’hui',
    'checks_today_plural'     => 'contrôles aujourd’hui',

    // --- Stock Management ---
    'stock_title'             => 'Gestion du cheptel',
    'stock_subtitle'          => 'Enregistrez les animaux et suivez vaccinations, gestations et événements sanitaires.',
    'breadcrumb_stock'        => 'Gestion du cheptel',

    // KPI (stock)
    'kpi_vacs_due'            => 'Vaccinations à effectuer',
    'kpi_new_this_month'      => 'Nouveaux ce mois-ci',

    // Animal register table
    'animal_register'         => 'Registre des animaux',
    'no_animals_title'        => 'Aucun animal enregistré',
    'no_animals_desc'         => 'Ajoutez votre premier animal pour commencer à suivre votre élevage.',
    'btn_add_first_animal'    => '+ Ajouter le premier animal',

    // Table columns
    'col_tag'                 => 'Boucle / ID',
    'col_species'             => 'Espèce',
    'col_breed'               => 'Race',
    'col_gender'              => 'Sexe',
    'col_dob'                 => 'Date de naissance',
    'col_status'              => 'Statut',
    'col_actions'             => 'Actions',

    // Confirm
    'confirm_remove_animal'   => 'Supprimer cet animal ?',

    // Add / Edit Animal Modal
    'modal_add_animal'        => 'Ajouter un animal',
    'modal_edit_animal'       => 'Modifier l’animal',
    'label_tag'               => 'Boucle / ID',
    'label_species'           => 'Espèce',
    'label_breed'             => 'Race',
    'label_gender'            => 'Sexe',
    'label_dob'               => 'Date de naissance',
    'label_status'            => 'Statut',
    'label_notes'             => 'Notes',
    'placeholder_tag'         => 'ex. EAR-001',
    'placeholder_breed'       => 'ex. Holstein',
    'placeholder_notes'       => 'Historique sanitaire, origine, notes particulières...',
    'select_placeholder'      => '— Sélectionner —',
    'btn_save_animal'         => 'Enregistrer l’animal',
    'btn_update_animal'       => 'Mettre à jour l’animal',

    // Species options
    'species_cattle'          => 'Bovins',
    'species_sheep'           => 'Ovins',
    'species_goats'           => 'Caprins',
    'species_pigs'            => 'Porcins',
    'species_poultry'         => 'Volailles',
    'species_other'           => 'Autre',

    // Gender options
    'gender_female'           => 'Femelle',
    'gender_male'             => 'Mâle',

    // Status options
    'status_active'           => 'Actif',
    'status_pregnant'         => 'Gestante',
    'status_sick'             => 'Malade',
    'status_sold'             => 'Vendu',

    // --- Produce Management ---
    'produce_title'           => 'Gestion de la production',
    'produce_subtitle'        => 'Enregistrez la production quotidienne : lait, viande, œufs, laine et autres produits animaux.',
    'breadcrumb_produce'      => 'Gestion de la production',
    'btn_log_produce'         => '+ Enregistrer la production',

    // KPI (produce)
    'kpi_milk_today'          => 'Aujourd’hui — Lait',
    'kpi_eggs_today'          => 'Aujourd’hui — Œufs',
    'kpi_this_week'           => 'Cette semaine',
    'kpi_avg_daily'           => 'Rendement moyen journalier',
    'week_entry'              => 'entrée',
    'week_entries'            => 'entrées',

    // Produce log table
    'produce_log'             => 'Journal de production',
    'no_produce_title'        => 'Aucune production enregistrée',
    'no_produce_desc'         => 'Commencez à enregistrer la production quotidienne pour suivre les tendances de rendement.',
    'btn_log_first_entry'     => '+ Enregistrer la première entrée',

    // Produce table columns
    'col_date'                => 'Date',
    'col_type'                => 'Type',
    'col_quantity'            => 'Quantité',
    'col_animal'              => 'Animal',
    'col_notes'               => 'Notes',

    // Confirm (produce)
    'confirm_remove_entry'    => 'Supprimer cette entrée ?',

    // Log Produce Modal
    'modal_log_produce'       => 'Enregistrer une entrée de production',
    'label_produce_type'      => 'Type de production',
    'label_date'              => 'Date',
    'label_quantity'          => 'Quantité',
    'label_unit'              => 'Unité',
    'label_animal'            => 'Animal',
    'placeholder_quantity'    => 'ex. 12.5',
    'placeholder_notes_produce' => 'Qualité, observations...',
    'select_all_herd'         => '— Tous / Troupeau —',
    'btn_save_entry'          => 'Enregistrer l’entrée',

    // Produce type options
    'type_milk'               => 'Lait',
    'type_eggs'               => 'Œufs',
    'type_meat'               => 'Viande',
    'type_wool'               => 'Laine',
    'type_honey'              => 'Miel',
    'type_other'              => 'Autre',

    // Unit options
    'unit_litres'             => 'Litres',
    'unit_kg'                 => 'kg',
    'unit_units'              => 'Unités',
    'unit_dozen'              => 'Douzaine',

    // --- Routine Monitor ---
    'routine_title'           => 'Suivi des routines d’élevage',
    'routine_subtitle'        => 'Enregistrez les contrôles quotidiens, l’alimentation, les observations sanitaires et les visites vétérinaires.',
    'breadcrumb_routine'      => 'Suivi des routines',
    'btn_log_check'           => '+ Enregistrer un contrôle',

    // KPI (routine)
    'kpi_todays_checks'       => "Contrôles d’aujourd’hui",
    'kpi_feeding_logs'        => 'Enregistrements d’alimentation',
    'kpi_health_alerts'       => 'Alertes sanitaires',
    'kpi_vet_visits_month'    => 'Visites vétérinaires (mois)',

    // Daily check log table
    'daily_check_log'         => 'Journal des contrôles quotidiens',
    'no_checks_title'         => 'Aucun contrôle enregistré',
    'no_checks_desc'          => 'Enregistrez votre premier contrôle quotidien du cheptel.',

    // Routine table columns
    'col_check_date'          => 'Date',
    'col_check_type'          => 'Type',
    'col_check_animal'        => 'Animal',
    'col_check_status'        => 'Statut',
    'col_check_notes'         => 'Notes',
    'col_check_actions'       => 'Actions',

    // Confirm (routine)
    'confirm_remove_check'    => 'Supprimer ce contrôle ?',

    // Log Check Modal
    'modal_log_check'         => 'Enregistrer un contrôle / une observation',
    'label_check_type'        => 'Type de contrôle',
    'label_check_date'        => 'Date',
    'label_check_animal'      => 'Animal',
    'label_check_status'      => 'Statut',
    'label_observations'      => 'Observations',
    'placeholder_observations'=> 'Quantité d’alimentation, comportement, symptômes, traitement...',
    'btn_save_check'          => 'Enregistrer le contrôle',

    // Check type options
    'check_morning_feeding'   => 'Alimentation du matin',
    'check_evening_feeding'   => 'Alimentation du soir',
    'check_health_obs'        => 'Observation sanitaire',
    'check_vaccination'       => 'Vaccination',
    'check_vet_visit'         => 'Visite vétérinaire',
    'check_weight_check'      => 'Contrôle du poids',
    'check_other'             => 'Autre',

    // Status radio labels
    'status_normal'           => 'Normal',
    'status_alert'            => 'Alerte',
    'status_critical'         => 'Critique',

];