<?php

return [

    // --- Dashboard ---
    'management_title'        => 'Gestion des drones',
    'management_subtitle'     => 'Vue d’ensemble de votre flotte de drones, des limites des parcelles et des missions.',
    'btn_map_field'           => 'Cartographier la parcelle',
    'btn_plan_mission'        => '+ Planifier une mission',

    // KPI labels
    'kpi_total_drones'        => 'Total des drones',
    'kpi_active_sub'          => ':count actifs',
    'kpi_field_boundaries'    => 'Limites des parcelles',
    'kpi_mapped_areas'        => 'zones cartographiées',
    'kpi_total_area'          => 'Superficie totale',
    'kpi_hectares_mapped'     => 'hectares cartographiés',
    'kpi_missions'            => 'Missions',
    'kpi_completed_sub'       => ':count terminées',
    'kpi_in_progress'         => 'En cours',
    'kpi_planned_sub'         => ':count planifiées',

    // Recent missions panel
    'recent_missions'         => 'Missions récentes',
    'view_all'                => 'Voir tout →',
    'no_missions_yet'         => 'Aucune mission pour le moment.',
    'edit_action'             => 'Modifier',

    // Quick actions sidebar
    'quick_actions'           => 'Actions rapides',
    'draw_field_boundary'     => 'Tracer la limite de la parcelle',
    'plan_new_mission'        => 'Planifier une nouvelle mission',
    'manage_drones'           => 'Gérer les drones',
    'view_field_maps'         => 'Voir les cartes des parcelles',

    // Mission status sidebar
    'mission_status'          => 'Statut de la mission',
    'status_draft'            => 'Brouillon',
    'status_planned'          => 'Planifiée',
    'status_in_progress'      => 'En cours',
    'status_completed'        => 'Terminée',
    'status_aborted'          => 'Interrompue',

    // --- Drone Configuration (index) ---
    'config_title'            => 'Configuration des drones',
    'config_subtitle'         => 'Enregistrez et gérez votre flotte de drones avec des paramètres de vol par défaut.',
    'btn_register_drone'      => '+ Enregistrer un drone',

    // Empty state
    'no_drones_title'         => 'Aucun drone enregistré',
    'no_drones_desc'          => 'Enregistrez votre premier drone pour commencer à planifier des missions.',

    // Table columns
    'col_drone'               => 'Drone',
    'col_model_serial'        => 'Modèle / Série',
    'col_status'              => 'Statut',
    'col_default_alt'         => 'Alt. par défaut',
    'col_speed'               => 'Vitesse',
    'col_overlap'             => 'Recouvrement',
    'col_actions'             => 'Actions',

    // Confirm delete drone
    'confirm_delete_drone'    => 'Supprimer ce drone ?',

    // Add / Edit Drone Modals
    'modal_register_drone'    => 'Enregistrer un nouveau drone',
    'modal_edit_drone'        => 'Modifier le drone',
    'btn_register_submit'     => 'Enregistrer le drone',
    'btn_save_changes'        => 'Enregistrer les modifications',

    // Drone status options
    'drone_status_active'     => 'Actif',
    'drone_status_maintenance'=> 'Maintenance',
    'drone_status_retired'    => 'Retiré',

    // --- Drone _form partial ---
    'label_drone_name'        => 'Nom du drone',
    'label_model'             => 'Modèle',
    'label_serial_number'     => 'Numéro de série',
    'label_drone_status'      => 'Statut',
    'label_default_params'    => 'Paramètres de vol par défaut',
    'label_altitude_m'        => 'Altitude (m)',
    'label_speed_ms'          => 'Vitesse (m/s)',
    'label_overlap_pct'       => 'Recouvrement (%)',
    'label_spacing_m'         => 'Espacement des bandes (m)',
    'label_buffer_m'          => 'Zone tampon des limites (m)',
    'label_notes'             => 'Notes',
    'placeholder_drone_name'  => 'ex. DJI Matrice 300 RTK',
    'placeholder_model'       => 'ex. Matrice 300 RTK',
    'placeholder_serial'      => 'ex. SN-123456',
    'placeholder_notes'       => 'Notes optionnelles concernant ce drone…',

    // --- Field Maps (index) ---
    'field_maps_title'        => 'Cartes des parcelles',
    'field_maps_subtitle'     => 'Tracer et gérer les limites des parcelles pour la planification des missions',
    'btn_draw_new_field'      => 'Tracer une nouvelle parcelle',
    'no_fields_title'         => 'Aucune limite de parcelle',
    'no_fields_desc'          => 'Tracez la première limite de parcelle sur la carte pour commencer.',
    'btn_draw_first_field'    => 'Tracer la première parcelle',
    'perimeter_label'         => 'm périmètre',
    'btn_edit_on_map'         => 'Modifier sur la carte',
    'btn_plan_mission_field'  => 'Planifier la mission',
    'confirm_delete_field'    => 'Supprimer cette limite de parcelle ? Cette action est irréversible.',

    // --- Field Map editor (map.blade.php) ---
    'draw_title_new'          => 'Tracer une nouvelle limite de parcelle',
    'draw_title_edit'         => 'Modifier : :name',
    'draw_subtitle'           => 'Tracez un polygone sur la carte satellite pour définir la limite de la parcelle',
    'back_to_field_maps'      => 'Retour aux cartes des parcelles',
    'label_load_existing'     => 'Charger une parcelle existante',
    'select_to_load'          => '— Sélectionner pour charger —',
    'label_field_name'        => 'Nom de la parcelle',
    'placeholder_field_name'  => 'ex. Bloc blé nord',
    'label_notes_field'       => 'Notes',
    'placeholder_notes_field' => 'Notes optionnelles…',
    'stat_hectares'           => 'hectares',
    'stat_perimeter'          => 'm périm.',
    'stat_vertices'           => 'sommets',
    'centroid_label'          => 'Centroïde :',
    'draw_polygon_first'      => 'Tracez d’abord un polygone sur la carte, puis enregistrez.',
    'btn_clear'               => 'Effacer',
    'btn_save_field'          => '💾 Enregistrer la parcelle',
    'btn_update_field'        => '💾 Mettre à jour la parcelle',
    'how_to_draw'             => 'Comment tracer',

    // --- Mission Planning (missions/index) ---
    'missions_title'          => 'Planification des missions',
    'missions_subtitle'       => 'Planifier, prévisualiser et exporter les missions de vol des drones',
    'btn_new_mission'         => 'Nouvelle mission',
    'no_missions_title'       => 'Aucune mission',
    'no_missions_desc'        => 'Créez votre première mission pour planifier un trajet de vol de drone.',
    'btn_create_first_mission'=> 'Créer la première mission',

    // Mission table columns
    'col_mission'             => 'Mission',
    'col_field'               => 'Parcelle',
    'col_drone'               => 'Drone',
    'col_type'                => 'Type',
    'col_mission_status'      => 'Statut',
    'col_planned'             => 'Planifiée',

    // Mission action buttons
    'btn_plan'                => 'Planifier',
    'btn_delete'              => 'Supprimer',
    'confirm_delete_mission'  => 'Supprimer cette mission ?',

    // Status change options in dropdown
    'mark_planned'            => 'Marquer comme planifiée',
    'mark_in_progress'        => 'Marquer comme en cours',
    'mark_completed'          => 'Marquer comme terminée',
    'mark_aborted'            => 'Marquer comme interrompue',

    // --- Mission Plan editor (missions/plan.blade.php) ---
    'plan_title_new'          => 'Nouvelle mission',
    'plan_title_edit'         => 'Mission : :name',
    'plan_subtitle'           => 'Configurer les paramètres et générer le schéma de vol en bandes parallèles',
    'btn_all_missions'        => 'Toutes les missions',
    'section_mission_details' => 'Détails de la mission',
    'label_mission_name'      => 'Nom de la mission',
    'placeholder_mission_name'=> 'ex. Relevé de printemps n°1',
    'label_field_boundary'    => 'Limite de la parcelle',
    'select_field'            => '— Sélectionner une parcelle —',
    'label_drone_select'      => 'Drone',
    'select_drone_optional'   => '— Optionnel —',
    'label_mission_type'      => 'Type de mission',
    'type_survey'             => 'Relevé / Cartographie',
    'type_spray'              => 'Pulvérisation',
    'type_imaging'            => 'Imagerie / Inspection',
    'label_planned_datetime'  => 'Date / Heure planifiée',
    'label_notes_mission'     => 'Notes',
    'placeholder_notes_mission'=> 'Notes optionnelles…',
    'section_flight_params'   => 'Paramètres de vol',
    'label_altitude'          => 'Altitude (m)',
    'label_speed'             => 'Vitesse (m/s)',
    'label_overlap'           => 'Recouvrement (%)',
    'label_strip_spacing'     => 'Espacement des bandes (m)',
    'label_boundary_buffer'   => 'Zone tampon des limites (m)',
    'label_sweep_angle'       => 'Angle de balayage (°)',
    'sweep_angle_hint'        => '0° = bandes Nord-Sud',
    'btn_generate'            => '⟳ Générer le schéma de vol',
    'btn_save_mission'        => 'Enregistrer la mission',
    'btn_update_mission'      => 'Mettre à jour la mission',
    'section_send_to_drone'   => 'Envoyer au drone',
    'send_to_drone_desc'      => 'Exportez le plan de vol et chargez-le dans votre application de station sol (DJI GS Pro, Litchi, Mission Planner, etc.)',
    'stat_wps'                => 'Points',
    'stat_km'                 => 'km',
    'stat_min'                => 'min',
    'stat_strips'             => 'bandes',

];