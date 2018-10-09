<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<div class='ap-title'>There are 2 main settings tabs that will help you to setup the plugin to work properly.</div>
<dl>
  <dt><strong>Network Settings:</strong></dt>
  <dd>In this tab you can enable and disable the available social medias as per your need. Also you can order the apperance of the social medias simply by drag and drop.
  </dd>
   <p>For each social media you can</p>
   <ul class='how-list'>
   <li>Enable/Disable: You can enable and disable the social media.</li>
   <li>App ID: App id of the social media.</li>
   <li>App secret: App secret of the social media.</li>
   </ul>
   To get the App ID and App Secret please follow the instructions(notes).

  <dt><strong>Other settings:</strong></dt>
  <dd>
  <p>In this tab you can do various settings of a plugin.</p>
  <ul class="how-list">
    <li>Enable or disable the social login.</li>
    <li>Options to enable the social logins for login form, registration form and in comments.</li>
    <li>Set user role: Here you can assign the users role. These roles will be assigned only to those user who have login/register through accesspress social login plugin.</li>
    <li>Options to choose the pre available themes, You can choose any one theme from the pre available 10 themes.</li>
    <li>Text settings:
        <ul>
        <li>Login text: Here you can setup the login text as per your need.</li>
        <li>Login short text: Here you can setup the login short text. If you kept blank "Login" text will appear.</li>
        <li>login with long text: Here you can setup the login long text. If you kept blank "Login with" text will appear.</li>
        <li>Link title attribute: Here you can setup the link title attributes for social icons.</li>
        <li>Login error message: Here you can setup the login error message. If kept blank default error message will appear.</li>
        </ul>
    </li>
    <li>Logout redirect link: Here you can setup the redirect link for the user when user logout from a site using our plugin's logout button.</li>
    <li>Login redirect link: Here you can setup the redirect link for the user when user login/register from a site using our plugin's login buttons.</li>
    <li>User avatar: Here you can choose an options to display the user avatar from provided social medias or wordpress default avatar. </li>
    <li>Email notification settings: Here you can choose an option either notify user and admin about user registration.</li>
  </ul>
  </dd>

  <dt><strong>Shortcode:</strong></dt>
  <dd><p>You can use shortcode for the display of the social logins in the contents of a posts and pages.
    <ul class="how-list">
      <li>Example 1: [apsl-login theme='2' login_text='Social Connect' login_redirect_url='<?php echo site_url(); ?>']</li>
      <li>Shortcode attributes: <br />
      i. theme: you can set the theme attributes from 1 to 10 to select the desired theme.<br />
      ii. login_text: you can use the custom login text for the shortcodes using this attribute.<br />
      iii. login_redirect_url : You can set the login redirect url explicitly by defining here(if the login redirect can't detect the provided url it will be redirected back to home page).
      </li>

      <li>Example 2: [apsl-login-with-login-form template='3' theme='1' login_text='Please login to site' login_redirect_url='<?php echo site_url(); ?>']</li>
      <li>Shortcode attributes: <br />
      i. template: You can select the design of the social login with login form using this attributes. There are all together 4 templates available.
      ii. theme: You can set the theme attributes from 1 to 17 to select the desired theme.<br />
      iii. login_text: You can use the custom login text for the shortcodes using this attribute.<br />
      iv. login_redirect_url : You can set the login redirect url explicitly by defining here(if the login redirect can't detect the provided url it will be redirected back to home page).
      </li>

      
      </ul>
  </p></dd>

  <dt><strong>Widget:</strong></dt>
  <dd>
  <p>You can use widget for the display of the social logins in the widgets area. <br/>
  <ul class="how-list">
  <li>Widget attributes <br />
      i. Title: You can setup the widget title here.<br />
      ii. Login text: You can setup the login text here.<br />
      iii. Theme: You can choose any theme from the available dropdown options.<br />
  </li>
  </ul>
  </dd>

</dl>



