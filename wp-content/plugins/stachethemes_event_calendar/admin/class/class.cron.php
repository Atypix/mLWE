<?php

namespace Stachethemes\Stec;




class Cron {



    public static function delete_calendar_jobs($calendar_id) {

        // Delete all import cronjobs for this calendar
        $jobs = Cron::get_jobs(array(
                        'key'     => 'import',
                        'compare' => 'EXISTS',
        ));

        foreach ( $jobs as $job ) {

            if ( !$job instanceof Cron_Post ) {
                continue;
            }

            $importer = $job->get_custom_meta('import');

            if ( !$importer instanceof Import ) {
                continue;
            }

            if ( $calendar_id == $importer->get_calendar_id() ) {
                $job->delete_post();
            }
        }

        return true;
    }



    public static function delete_job($id) {

        $cron_post = new Cron_Post($id);

        if ( $cron_post->get_id() ) {
            return $cron_post->delete_post();
        }

        return true;
    }



    public static function create_import_job($freq, Import $importer) {

        $cron_post = new Cron_Post();
        $cron_post->set_title($importer->get_ics_url());
        $cron_post->set_custom_meta(array(
                'import' => $importer,
                'freq'   => $freq
        ));

        return $cron_post->insert_post();
    }



    public static function get_jobs($custom_meta_query = false) {

        $cron_jobs = array();

        $meta_query = array();

        if ( $custom_meta_query ) {
            $meta_query[] = $custom_meta_query;
        }

        $cron_job_ids = get_posts(array(
                'posts_per_page' => -1,
                'post_type'      => 'stec_cron',
                'fields'         => 'ids',
                'meta_query'     => $meta_query
        ));

        if ( !$cron_job_ids ) {
            return array();
        }

        foreach ( $cron_job_ids as $id ) {

            $cron_jobs[] = new Cron_Post($id);
        }

        return apply_filters('stec_admin_get_cron_jobs', $cron_jobs);
    }



    public static function bulk_delete($jobs = array()) {
        $filter_job_ids = filter_var_array($jobs, FILTER_VALIDATE_INT);
        $unique_job_ids = array_filter($filter_job_ids);

        foreach ( $unique_job_ids as $job_id ) {
            $job = new Cron_Post($job_id);
            if ( !$job->get_id() ) {
                continue;
            }

            $job->delete_post();
        }
        unset($job_id);

        return true;
    }



    public static function do_import_job($freq) {

        Admin_Helper::debug_log('Started import job');

        $jobs = self::get_jobs(array(
                        'key' => 'freq',
                        'value' => $freq
        ));

        foreach ( $jobs as $job ) {

            $importer = $job->get_custom_meta('import');

            if ( !$importer instanceof Import ) {
                continue;
            }

            $importer->import_ics();
        }

        Admin_Helper::debug_log('Completed import job');
    }



    public static function do_remind_job() {

        Admin_Helper::debug_log('Started remind job');

        $jobs = get_option('stec_reminder', array());

        foreach ( $jobs as $k => $job ) {
            if ( !$job instanceof Remind_Object ) {
                continue;
            }

            if ( true === Admin_Helper::send_mail_remind($job) ) {
                unset($jobs[$k]);
            }
        }

        update_option('stec_reminder', $jobs);

        Admin_Helper::debug_log('Completed remind job');
    }

}
