<?php

return [

    // --- Dashboard ---
    'management_title'          => 'Διαχείριση Νερού',
    'dashboard_title'           => 'Έξυπνη Διαχείριση Νερού',
    'dashboard_subtitle'        => 'Παρακολουθήστε υγρασία εδάφους και ελέγξτε την άρδευση. Δεδομένα από αισθητήρες IoT ή χειροκίνητη καταχώρηση.',

    // KPI labels
    'kpi_avg_moisture'          => 'Μέση Υγρασία',
    'kpi_avg_moisture_sub'      => 'σε αισθητήρες υγρασίας',
    'kpi_active_sensors'        => 'Ενεργοί Αισθητήρες',
    'kpi_active_sensors_sub'    => 'συνδεδεμένοι τώρα',
    'kpi_controllers'           => 'Ελεγκτές',
    'kpi_controllers_sub'       => 'μονάδες άρδευσης',
    'kpi_water_used'            => 'Νερό που Χρησιμοποιήθηκε',
    'kpi_water_used_sub'        => 'μέσω αισθητήρων επιπέδου',

    // Panels
    'panel_water_resources'     => 'Υδατικοί Πόροι',
    'panel_water_resources_desc'=> 'Πηγάδια, συστήματα άρδευσης, δεξαμενές και παρακολούθηση χρήσης.',
    'btn_manage_resources'      => 'Διαχείριση Πόρων',
    'panel_weather_report'      => 'Αναφορά Καιρού',
    'panel_weather_report_desc' => 'Ζωντανά καιρικά δεδομένα, πρόγνωση 7 ημερών και ειδοποιήσεις ξηρασίας.',
    'btn_view_weather'          => 'Προβολή Καιρού',

    // Moisture Map
    'moisture_map_title'        => 'Χάρτης Υγρασίας',
    'moisture_map_subtitle'     => 'Ωριαίοι μέσοι όροι — τελευταίες 24 ώρες',
    'legend_dry'                => 'Ξηρό <30%',
    'legend_low'                => 'Χαμηλό 30–50%',
    'legend_good'               => 'Καλό 50–70%',
    'legend_wet'                => 'Υγρό >70%',
    'legend_no_data'            => 'Χωρίς δεδομένα',
    'no_moisture_sensors'       => 'Δεν έχουν ρυθμιστεί αισθητήρες υγρασίας.',

    // Moisture Trend
    'moisture_trends_title'     => 'Τάσεις Υγρασίας',
    'moisture_trends_subtitle'  => 'Τελευταίες 24 ώρες ανά αισθητήρα',
    'no_readings'               => 'Δεν υπάρχουν διαθέσιμες μετρήσεις.',

    // Sensor tables
    'moisture_sensors_title'    => 'Αισθητήρες Υγρασίας',
    'moisture_sensors_sub'      => 'υγρασία & στάθμη νερού',
    'irrigation_ctrl_title'     => 'Ελεγκτές Άρδευσης',
    'irrigation_ctrl_sub'       => 'αισθητήρες ομάδας άρδευσης',
    'no_reading'                => 'Χωρίς μέτρηση',
    'no_moisture_sensors_yet'   => 'Δεν υπάρχουν ακόμα αισθητήρες υγρασίας.',
    'no_irrigation_ctrl_yet'    => 'Δεν υπάρχουν ακόμα ελεγκτές άρδευσης.',
    'add_link'                  => '+ Προσθήκη',
    'chart_x_label'             => 'Ώρα (τελευταίες 24ω)',
    'chart_y_label'             => 'Υγρασία %',

    // --- Weather Report ---
    'breadcrumb_water'          => 'Διαχείριση Νερού',
    'breadcrumb_weather'        => 'Αναφορά Καιρού',
    'weather_title'             => 'Αναφορά Καιρού',
    'set_farm_location'         => 'Ορίστε τοποθεσία αγροκτήματος στον Διαχειριστή → Χρήστες',
    'powered_by'                => 'Από',
    'updated_every'             => 'Ενημέρωση κάθε 30 λεπτά',
    'weather_error_title'       => 'Αδυναμία φόρτωσης καιρικών δεδομένων',

    // Current conditions
    'feels_like'                => 'Αίσθηση',
    'label_humidity'            => 'Υγρασία',
    'label_wind'                => 'Άνεμος',
    'label_gusts'               => 'Ριπές',
    'label_cloud_cover'         => 'Νέφωση',
    'label_visibility'          => 'Ορατότητα',
    'label_pressure'            => 'Πίεση',
    'label_dew_point'           => 'Σημείο Δρόσου',
    'label_rain_chance'         => 'Πιθανότητα Βροχής',
    'label_uv_index'            => 'Δείκτης UV',

    // KPI strip
    'kpi_rain_7days'            => 'Βροχή (7 ημέρες)',
    'kpi_uv_index'              => 'Δείκτης UV',
    'kpi_soil_moisture'         => 'Υγρασία Εδάφους',
    'kpi_soil_depth'            => 'Βάθος 0–10 εκ.',
    'kpi_evapotranspiration'    => 'Εξατμισοδιαπνοή',

    // 7-day forecast
    'forecast_7day'             => 'Πρόγνωση 7 Ημερών',
    'today'                     => 'Σήμερα',

    // Hourly forecast
    'hourly_forecast'           => 'Ωριαία Πρόγνωση — Επόμενες 24 Ώρες',
    'col_time'                  => 'Ώρα',
    'col_condition'             => 'Κατάσταση',
    'col_temp'                  => 'Θερμ.',
    'col_feels'                 => 'Αίσθηση',
    'col_humidity'              => 'Υγρασία',
    'col_rain_pct'              => 'Βροχή %',
    'col_rain_mm'               => 'Βροχή mm',
    'col_wind'                  => 'Άνεμος m/s',
    'col_uv'                    => 'UV',
    'col_clouds'                => 'Νέφωση',

    // Agricultural data
    'agri_data_title'           => 'Αγρονομικά Δεδομένα — Έδαφος & Άρδευση 7 Ημερών',
    'col_date'                  => 'Ημερομηνία',
    'col_soil_moisture'         => 'Υγρασία Εδάφους (0–10εκ.)',
    'col_soil_temp'             => 'Θερμ. Εδάφους (0–10εκ.)',
    'col_evapotranspiration'    => 'Εξατμισοδιαπνοή',
    'col_rain_sum'              => 'Σύνολο Βροχής',

    // Sunrise/sunset
    'sunrise_sunset'            => 'Ανατολή & Δύση',

    // --- Water Resources ---
    'breadcrumb_resources'      => 'Υδατικοί Πόροι',
    'resources_title'           => 'Υδατικοί Πόροι',
    'resources_subtitle'        => 'Καταχωρίστε πηγάδια, δεξαμενές και συστήματα άρδευσης με παρακολούθηση χωρητικότητας.',
    'btn_add_resource'          => '+ Προσθήκη Πόρου',
    'btn_add_first_source'      => '+ Προσθήκη Πρώτης Πηγής',

    // KPI
    'kpi_total_sources'         => 'Σύνολο Πηγών',
    'kpi_wells'                 => 'Πηγάδια',
    'kpi_reservoirs'            => 'Δεξαμενές',
    'kpi_irrigation_systems'    => 'Συστήματα Άρδευσης',

    // Table
    'registered_sources'        => 'Καταχωρημένες Υδατικές Πηγές',
    'col_name'                  => 'Όνομα',
    'col_type'                  => 'Τύπος',
    'col_capacity'              => 'Χωρητικότητα (m³)',
    'col_level'                 => 'Επίπεδο',

    // Empty state
    'no_sources_title'          => 'Δεν υπάρχουν καταχωρημένες υδατικές πηγές',
    'no_sources_desc'           => 'Προσθέστε πηγάδια, δεξαμενές ή συστήματα άρδευσης για να ξεκινήσετε την παρακολούθηση χρήσης νερού.',

    // Modals
    'modal_add_source'          => 'Προσθήκη Υδατικής Πηγής',
    'modal_edit_source'         => 'Επεξεργασία Υδατικής Πηγής',
    'label_name'                => 'Όνομα',
    'label_type'                => 'Τύπος',
    'label_capacity_m3'         => 'Χωρητικότητα (m³)',
    'label_level_pct'           => 'Τρέχον Επίπεδο (%)',
    'label_notes'               => 'Σημειώσεις',
    'placeholder_name'          => 'π.χ. Κεντρικό Πηγάδι',
    'placeholder_capacity'      => 'π.χ. 500',
    'placeholder_level'         => 'π.χ. 75',
    'placeholder_notes'         => 'Τοποθεσία, συνδεδεμένα χωράφια, ID αισθητήρα...',

    // Type options
    'type_well'                 => 'Πηγάδι',
    'type_reservoir'            => 'Δεξαμενή',
    'type_irrigation_system'    => 'Σύστημα Άρδευσης',
    'type_borehole'             => 'Γεώτρηση',
    'type_stream'               => 'Ρεύμα',

    // Actions
    'btn_edit'                  => 'Επεξεργασία',
    'btn_save_source'           => 'Αποθήκευση Πηγής',
    'btn_save_changes'          => 'Αποθήκευση Αλλαγών',
    'btn_cancel'                => 'Ακύρωση',
    'confirm_remove_source'     => 'Αφαίρεση αυτής της υδατικής πηγής;',

    // Moisture badge labels (short form for sensor table)
    'moisture_dry'              => 'Ξηρό',
    'moisture_low'              => 'Χαμηλό',
    'moisture_good'             => 'Καλό',
    'moisture_wet'              => 'Υγρό',

    // Sensor status
    'status_online'             => 'Σε σύνδεση',
    'status_offline'            => 'Εκτός σύνδεσης',

    // UV risk labels
    'uv_low'                    => 'Χαμηλός',
    'uv_moderate'               => 'Μέτριος',
    'uv_high'                   => 'Υψηλός',
    'uv_very_high'              => 'Πολύ Υψηλός',
    'uv_extreme'                => 'Ακραίος',

    // Dashboard hints
    'sensor_hint_add_pre'       => 'Προσθέστε αισθητήρες με κλειδί ομάδας',
    'sensor_hint_or'            => 'ή',
    'sensor_hint_via'           => 'μέσω του',
    'sensor_hint_manager'       => 'διαχειριστή αισθητήρων IoT',
    'use_the'                   => 'Χρησιμοποιήστε',
    'manual_entry'              => 'χειροκίνητη καταχώρηση',
    'simulator'                 => 'προσομοιωτή',
    'to_add_readings'           => 'για να προσθέσετε μετρήσεις αισθητήρων.',

];
