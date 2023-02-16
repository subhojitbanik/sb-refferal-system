<?php

function sb_show_referral_count_map(){
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . "refferal_table";
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE affiliate_id = '$user_id' ");
    $referal_count = count($results);
        echo'<pre>'; print_r($results); echo'</pre>';
    ob_start();?>
        <h2>Tensorflow Visor</h2>
        <div id="sb_graph"></div>
            <script>
                const surface = document.getElementById('sb_graph');
                let values = [
                {x: 0, y: 20},
                {x: 1, y: 30},
                {x: 2, y: 15},
                {x: 3, y: 12},  
                {x: 4, y: 60},
                {x: 5, y: 10},

                ];

                //tfvis.render.linechart({name: 'my Lines'}, {values});
                tfvis.render.linechart(surface, {values});

            </script>
        
        <?php
    return ob_get_clean();
}
add_shortcode( 'sb_show_referral_count_map', 'sb_show_referral_count_map' );