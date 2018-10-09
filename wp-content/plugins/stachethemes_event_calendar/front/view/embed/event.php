<?php

namespace Stachethemes\Stec;




if ( !isset($stec) || !$stec instanceof stachethemes_ec_main ) {
    return;
}

if ( !isset($event) || !$event instanceof Event_Post ) {
    return;
}
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='stylesheet' href='<?php echo plugins_url('fonts/font-awesome-4.5.0/css/font-awesome.min.css', STACHETHEMES_EC_FILE__) ?>' type='text/css' media='all' />
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
        
        <style>
            .stec-event-embed {
                margin:0;
                padding:15px;
                font-family: Roboto;
                width: 100%;
                background: #fff;
                box-sizing: border-box;
                overflow: hidden;
            }

            .stec-event-embed-icon {
                width: 50px;
                height: 50px;
                min-width: 50px;
                border-radius: 3px;
            }

            .stec-event-embed .stec-layout-single-month-full {
                display: none;
            }

            h1 {
                margin:0;
                padding:0;
                padding-left: 10px;
                font-size: 24px;
            }

            .stec-event-embed-icon i {
                width: 50px;
                line-height: 50px;
                color:#fff;
                text-align: center;
                font-size: 20px;
            }

            .stec-event-flex {
                display: flex;
                align-items: center;
                align-content: space-between;
                float: left;
                width: 100%;
            }

            .stec-event-embed-description {
                width: 100%;
                float: left;
                margin-top: 10px;
                font-size: 16px;
                line-height: 1.6;
                color:#999;
            }

            .stec-event-embed-list {
                border-top: 1px solid #ececec;
                border-bottom: 1px solid #ececec;
                float: left;
                width: 100%;
                margin-top: 10px;
                padding: 10px 0;
                list-style: none;
            }

            .stec-event-embed-list li {
                width: 100%;
                float: left;
                margin: 5px 0;
                color:#999;
                font-size: 16px;
            }

            .stec-event-embed-list li i {
                width: 35px;
                text-align: center;
                color:#999;
                font-size: 20px;
            }

            .stec-embed-permalink:hover {
                background-color:#f15e6e;
            }

            .stec-embed-permalink {
                background-color:#4d576c;
                color:#fff;
                padding: 15px 20px;
                border-radius: 3px;
                text-decoration: none;
                float: left;
            }

            @media screen and (max-width: 400px) {

                .stec-event-embed {

                }

                h1 {

                    font-size: 18px;
                }

                .stec-event-embed-icon {
                    width: 40px;
                    height: 40px;
                    min-width: 40px;

                }

                .stec-event-embed-icon i {
                    width: 40px;
                    line-height: 40px;
                    text-align: center;
                    font-size: 18px;
                }

                .stec-event-embed-description {
                    font-size: 14px;
                    line-height: 1.2;

                }
                .stec-embed-permalink {
                    width:100%;
                    padding: 10px;
                    text-align: center;
                    box-sizing: border-box;
                    font-size: 14px;

                }

                .stec-event-embed-list li {

                    font-size: 14px;
                }

                .stec-event-embed-list li i {
                    width: 30px;
                    font-size: 18px;
                }
            }
        </style>

    </head>
    <body class="stec-event-embed">

        <div class="stec-event-flex">
            <div class="stec-event-embed-icon" style="background-color: <?php echo $event->get_color(); ?>">
                <i class="<?php echo $event->get_icon(); ?>"></i>
            </div>

            <h1><?php echo $event->get_title(); ?></h1>
        </div>

        <div class="stec-event-embed-description"><?php echo $event->get_description_short(); ?></div>

        <ul class="stec-event-embed-list">
            <li><i class="fa fa-clock-o"></i><?php echo Admin_Helper::get_the_timespan($event, $repeat_offset); ?></li>
            <?php if ( $event->get_location() ) : ?>
                <li><i class="fa fa-map-marker"></i><?php echo $event->get_location(); ?></li>
            <?php endif; ?>
        </ul>

        <a target="_parent" class="stec-embed-permalink" href="<?php echo $event->get_permalink($repeat_offset); ?>"><?php _e('Learn more', 'stec'); ?></a>

    </body>
</html>