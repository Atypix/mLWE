=== Extended Location for WP Job Manager ===
Contributors: Astoundify
Requires at least: 4.7.0
Tested up to: 4.7.5
Stable tag: 3.4.0
License: GNU General Public License v3.0

Allow your users to manage their map presence by explicitly choosing exactly where their business is actually located on a map. Not only does this ensure their listings are found on your website, but also by Google.

= Documentation =

Usage instructions for this plugin can be found on our documentation: [http://docs.astoundify.com/](http://docs.astoundify.com/).

= Support Policy =

Please contact https://astoundify.com/account/new-ticket/ for technical support regarding the plugin. We are partnered with and highly recommend WP Curve (https://astoundify.com/go/wpcurve) Envato Studio (https://astoundify.com/go/envato-studio/) or Codeable (https://astoundify.com/go/codeable/) if you need help customizing your website.

== Installation ==

To install this plugin, please refer to the guide here: [http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Changelog ==

= 3.4.0: June 8, 2017 =

* New: Use WP Job Manager's Google Maps API key. Add your key in "Listings > Settings".
* New: Use the site's current language to return IP-powered location suggestion.

= 3.3.0: April 12, 2017 =

* New: Update README

= 3.2.0: March 24, 2017 = 

* Fix: Avoid PHP warning on $lock_status
* Fix: String translation fixes.

= 3.1.0: March 2, 2017 =

* New: Pass the site's current language and locale to Google Maps API for localized maps.
* New: Pull translations from https://astoundify.com/glotpress/projects/extended-location/
* New: Allow found IP location to be filtered.
* New: Allow coordinates to be used on the location field.
* New: Admin notification when Google Maps API key is missing.
* Fix: Avoid PHP warning on $lock_status

= 3.0.0: September 29, 2016 =

* New: Separate "Location" metabox when editing a listing in the WordPress dashboard.
* New: Hide map if no API key is added.
* Fix: Various improvements and stability fixes.

= 2.6.1: August 3, 2016 =

* Fix: Simplify logic on submission form map.

= 2.6.0: August 2, 2016 =

* New: Use all available IP location data to prefill search field.
* Fix: Ensure pin location remains the same after editing a listing.

= 2.5.2: June 22, 2016 =

* Fix: Avoid PHP error on empty() call.

= 2.5.1: June 21, 2016 =

* Fix: Avoid PHP error.

= 2.5.0: June 21, 2016 =

* New: Fallback to Default Location when no IP location data is found.
* Fix: Pin location when previewing or editing a listing.

= 2.4.0: May 6, 2016 =

* Fix: Update IP address lookup service to http://ip-api.com/docs/api:json
* Fix: Don't set a null value if location cannot be found or set.
* Fix: Prevent form from submitting listing when enter is used to select a location.
* Fix: Don't geocode when a coordinate is entered.
* Fix: Update compatibility with the Client Side Geocoder plugin.
* Fix: Remove `sensor` parameter from the Google Maps API library asset.

= 2.3.0: June 30, 2015

* New: "Lock" the pin in place while the address remains updatable.
* Fix: Backend view in Firefox.
* Fix: Pin moving when other fields are edited.
* Fix: Use country if only available data for auto suggest.

= 2.2.3: April 20, 2015  =

* Fix: Remove debug code.

= 2.2.2: April 17, 2015 =

* Fix: Use proper IP address.

= 2.2.1: April 16, 2015 =

* Fix: Use wp_remote_get() to try many options of retrieving IP data.

= 2.2.0: March 11, 2015 =

* New: City Suggest - automatically recommend the users current city.
* Fix: Avoid error if no default is set in admin.
* Tweak: String changes.

= 2.1.0: February 17, 2015 =

* New: Add autolocation to all location inputs.
* Fix: Avoid breaking other plugins using tabs on their settings.
* Fix: Pin's geolocation lat/long will be used instead of geocoded address location.
* Tweak: Improve javascript mapping.
* Tweak: Move license input field to the correct tab.

= 2.0.0: February 1, 2015 =

* Note: This is a major update. Please disable the old "WP Job Manager - Google Places Suggest" plugin if it remains active after update.

* New: Rename plugin for future expansion
* New: Add a map and draggable pin to the submission form (frontend and backend). The default point can be set in "Listing &gt; Settings &gt; Extended Locations"

= 1.0.0: January 17, 2015 =

New: Initial Release!
