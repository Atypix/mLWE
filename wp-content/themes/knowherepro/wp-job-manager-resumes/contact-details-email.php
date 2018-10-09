
<?php global $knowhere_settings; ?>

<?php if ( !$knowhere_settings['job-resume-social-links'] ) return; ?>

<ul class="kw-social-links">
	<li><a title="Gmail" href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo $email; ?>&su=<?php echo urlencode( $subject ); ?>" target="_blank" class="job_application_email"><i class="fa fa-google-plus"></i></a></li>
	<li><a title="AOL" href="http://webmail.aol.com/Mail/ComposeMessage.aspx?to=<?php echo $email; ?>&subject=<?php echo urlencode( $subject ); ?>" target="_blank" class="job_application_email"><i class="fa fa-mail-reply-all"></i></a></li>
	<li><a title="Yahoo" href="http://compose.mail.yahoo.com/?to=<?php echo $email; ?>&subject=<?php echo urlencode( $subject ); ?>" target="_blank" class="job_application_email"><i class="fa fa-yahoo"></i></a></li>
	<li><a title="Outlook" href="http://mail.live.com/mail/EditMessageLight.aspx?n=&to=<?php echo $email; ?>&subject=<?php echo urlencode( $subject ); ?>" target="_blank" class="job_application_email"><i class="fa fa-mail-reply"></i></a></li>
</ul>