<td style="font-family:sans-serif;font-size:14px;vertical-align:top;">
    <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">Hey,</p>
    <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:30px;">New reviews have been posted on your site <?php echo get_bloginfo()?>, <a href="<?php echo get_bloginfo('url')?>" style="color:#3498db;"><?php echo get_bloginfo('url')?></a>. You can manage them in the User Reviews page of Reviewer Plugin.</p>
    
    <?php  foreach( $reviews as $review ): ?>
    <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:20px; border-bottom: 1px dotted #cccccc; padding-bottom: 10px";>
        <span style="display:block;margin:0;Margin-bottom:5px;"><a href="<?php echo $review->post_permalink; ?>" style="color:#3498db;"><?php echo $review->post_title; ?></a></span>
        <span style="display:block;margin:0;Margin-bottom:5px; font-weight:bold;"><?php echo $review->author; ?>, <a href="mailto:<?php echo $review->author_email; ?>" style="color:#3498db;"><?php echo $review->author_email; ?></a></span>
        <span style="display:block;margin:0;Margin-bottom:5px;font-weight:bold;">Score: <?php echo $review->score; ?></span>
        <span style="display:block;margin:0;Margin-bottom:5px;"><?php echo $review->title; ?></span>
        <span style="display:block;margin:0;Margin-bottom:5px;"><?php echo $review->comment; ?></span>
    </p>
    <?php endforeach; ?>
    
    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%;">
        <tbody>
            <tr>
                <td align="center" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto;">
                        <tbody>
                            <tr>
                            <?php if( !function_exists('menu_page_url') ) { require_once(ABSPATH . 'wp-admin/includes/plugin.php'); } ?>
                                <td style="font-family:sans-serif;font-size:14px;vertical-align:top;background-color:#ffffff;border-radius:5px;text-align:center;background-color:#3498db;"> 
                                    <a href="<?php menu_page_url('reviewer-users-ratings-page') ?>" target="_blank" style="text-decoration:underline;background-color:#ffffff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize;background-color:#3498db;border-color:#3498db;color:#ffffff;">View Reviews</a> 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</td>