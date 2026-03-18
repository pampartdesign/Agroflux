<?php

return [

    // --- Dashboard ---
    'management_title'          => 'Gestion de l’eau',
    'dashboard_title'           => 'Gestion intelligente de l’eau',
    'dashboard_subtitle'        => 'Surveillez l’humidité du sol et contrôlez l’irrigation. Données issues des capteurs IoT ou saisies manuellement.',

    // KPI labels
    'kpi_avg_moisture'          => 'Humidité moyenne',
    'kpi_avg_moisture_sub'      => 'sur l’ensemble des capteurs d’humidité',
    'kpi_active_sensors'        => 'Capteurs actifs',
    'kpi_active_sensors_sub'    => 'en ligne actuellement',
    'kpi_controllers'           => 'Contrôleurs',
    'kpi_controllers_sub'       => 'unités d’irrigation',
    'kpi_water_used'            => 'Eau utilisée',
    'kpi_water_used_sub'        => 'capteurs de niveau des abreuvoirs',

    // Panels
    'panel_water_resources'     => 'Ressources en eau',
    'panel_water_resources_desc'=> 'Puits, systèmes d’irrigation, réservoirs et suivi de l’utilisation.',
    'btn_manage_resources'      => 'Gérer les ressources',
    'panel_weather_report'      => 'Bulletin météorologique',
    'panel_weather_report_desc' => 'Données météo en temps réel, prévisions sur 7 jours et alertes de sécheresse.',
    'btn_view_weather'          => 'Voir la météo',

    // Moisture Map
    'moisture_map_title'        => 'Carte d’humidité',
    'moisture_map_subtitle'     => 'Moyennes horaires — dernières 24 heures',
    'legend_dry'                => 'Sec <30%',
    'legend_low'                => 'Faible 30–50%',
    'legend_good'               => 'Bon 50–70%',
    'legend_wet'                => 'Humide >70%',
    'legend_no_data'            => 'Aucune donnée',
    'no_moisture_sensors'       => 'Aucun capteur d’humidité configuré pour le moment.',

    // Moisture Trend
    'moisture_trends_title'     => 'Tendances d’humidité',
    'moisture_trends_subtitle'  => 'Dernières 24 heures par capteur',
    'no_readings'               => 'Aucune mesure disponible.',

    // Sensor tables
    'moisture_sensors_title'    => 'Capteurs d’humidité',
    'moisture_sensors_sub'      => 'humidité & niveau d’eau',
    'irrigation_ctrl_title'     => 'Contrôleurs d’irrigation',
    'irrigation_ctrl_sub'       => 'capteurs de groupes d’irrigation',
    'no_reading'                => 'Aucune mesure',
    'no_moisture_sensors_yet'   => 'Aucun capteur d’humidité pour le moment.',
    'no_irrigation_ctrl_yet'    => 'Aucun contrôleur d’irrigation pour le moment.',
    'add_link'                  => '+ Ajouter',
    'chart_x_label'             => 'Temps (dernières 24 h)',
    'chart_y_label'             => 'Humidité %',

    // --- Weather Report ---
    'breadcrumb_water'          => 'Gestion de l’eau',
    'breadcrumb_weather'        => 'Bulletin météorologique',
    'weather_title'             => 'Bulletin météorologique',
    'set_farm_location'         => 'Définissez la localisation de votre exploitation dans Admin → Utilisateurs',
    'powered_by'                => 'Fourni par',
    'updated_every'             => 'Mis à jour toutes les 30 min',
    'weather_error_title'       => 'Impossible de charger les données météo',

    // Current conditions
    'feels_like'                => 'Température ressentie',
    'label_humidity'            => 'Humidité',
    'label_wind'                => 'Vent',
    'label_gusts'               => 'Rafales',
    'label_cloud_cover'         => 'Couverture nuageuse',
    'label_visibility'          => 'Visibilité',
    'label_pressure'            => 'Pression',
    'label_dew_point'           => 'Point de rosée',
    'label_rain_chance'         => 'Probabilité de pluie',
    'label_uv_index'            => 'Indice UV',

    // KPI strip
    'kpi_rain_7days'            => 'Pluie (7 jours)',
    'kpi_uv_index'              => 'Indice UV',
    'kpi_soil_moisture'         => 'Humidité du sol',
    'kpi_soil_depth'            => 'profondeur 0–10 cm',
    'kpi_evapotranspiration'    => 'Évapotranspiration',

    // 7-day forecast
    'forecast_7day'             => 'Prévisions sur 7 jours',
    'today'                     => 'Aujourd’hui',

    // Hourly forecast
    'hourly_forecast'           => 'Prévisions horaires — prochaines 24 heures',
    'col_time'                  => 'Heure',
    'col_condition'             => 'Conditions',
    'col_temp'                  => 'Temp.',
    'col_feels'                 => 'Ressentie',
    'col_humidity'              => 'Humidité',
    'col_rain_pct'              => 'Pluie %',
    'col_rain_mm'               => 'Pluie mm',
    'col_wind'                  => 'Vent m/s',
    'col_uv'                    => 'UV',
    'col_clouds'                => 'Nuages',

    // Agricultural data
    'agri_data_title'           => 'Données agricoles — sol & irrigation sur 7 jours',
    'col_date'                  => 'Date',
    'col_soil_moisture'         => 'Humidité du sol (0–10 cm)',
    'col_soil_temp'             => 'Temp. du sol (0–10 cm)',
    'col_evapotranspiration'    => 'Évapotranspiration',
    'col_rain_sum'              => 'Cumul des pluies',

    // Sunrise/sunset
    'sunrise_sunset'            => 'Lever & coucher du soleil',

    // --- Water Resources ---
    'breadcrumb_resources'      => 'Ressources en eau',
    'resources_title'           => 'Ressources en eau',
    'resources_subtitle'        => 'Enregistrez les puits, réservoirs et systèmes d’irrigation avec suivi de capacité.',
    'btn_add_resource'          => '+ Ajouter une ressource',
    'btn_add_first_source'      => '+ Ajouter la première source',

    // KPI
    'kpi_total_sources'         => 'Total des sources',
    'kpi_wells'                 => 'Puits',
    'kpi_reservoirs'            => 'Réservoirs',
    'kpi_irrigation_systems'    => 'Systèmes d’irrigation',

    // Table
    'registered_sources'        => 'Sources d’eau enregistrées',
    'col_name'                  => 'Nom',
    'col_type'                  => 'Type',
    'col_capacity'              => 'Capacité (m³)',
    'col_level'                 => 'Niveau',

    // Empty state
    'no_sources_title'          => 'Aucune source d’eau enregistrée',
    'no_sources_desc'           => 'Ajoutez des puits, réservoirs ou systèmes d’irrigation pour commencer à surveiller l’utilisation de l’eau.',

    // Modals
    'modal_add_source'          => 'Ajouter une source d’eau',
    'modal_edit_source'         => 'Modifier la source d’eau',
    'label_name'                => 'Nom',
    'label_type'                => 'Type',
    'label_capacity_m3'         => 'Capacité (m³)',
    'label_level_pct'           => 'Niveau actuel (%)',
    'label_notes'               => 'Notes',
    'placeholder_name'          => 'ex. Puits principal',
    'placeholder_capacity'      => 'ex. 500',
    'placeholder_level'         => 'ex. 75',
    'placeholder_notes'         => 'Localisation, parcelles connectées, ID capteur...',

    // Type options
    'type_well'                 => 'Puits',
    'type_reservoir'            => 'Réservoir',
    'type_irrigation_system'    => 'Système d’irrigation',
    'type_borehole'             => 'Forage',
    'type_stream'               => 'Ruisseau',

    // Actions
    'btn_edit'                  => 'Modifier',
    'btn_save_source'           => 'Enregistrer la source',
    'btn_save_changes'          => 'Enregistrer les modifications',
    'btn_cancel'                => 'Annuler',
    'confirm_remove_source'     => 'Supprimer cette source d’eau ?',

    // Moisture badge labels (short form for sensor table)
    'moisture_dry'              => 'Sec',
    'moisture_low'              => 'Faible',
    'moisture_good'             => 'Bon',
    'moisture_wet'              => 'Humide',

    // Sensor status
    'status_online'             => 'En ligne',
    'status_offline'            => 'Hors ligne',

    // UV risk labels
    'uv_low'                    => 'Faible',
    'uv_moderate'               => 'Modéré',
    'uv_high'                   => 'Élevé',
    'uv_very_high'              => 'Très élevé',
    'uv_extreme'                => 'Extrême',

    // Dashboard hints
    'sensor_hint_add_pre'       => 'Ajoutez des capteurs avec une clé de groupe',
    'sensor_hint_or'            => 'ou',
    'sensor_hint_via'           => 'via le',
    'sensor_hint_manager'       => 'gestionnaire de capteurs IoT',
    'use_the'                   => 'Utilisez le',
    'manual_entry'              => 'mode de saisie manuelle',
    'simulator'                 => 'simulateur',
    'to_add_readings'           => 'pour ajouter des relevés de capteurs.',

];