# Holidays WordPress Plugin

## Description

The Holidays plugin provides a way for WordPress site owners to list, organize, and showcase holiday destinations. It features an intuitive, custom post type `Destination` where the site owner can add details about the holiday destinations.

The plugin also exposes RESTful APIs to retrieve deals and request brochures for holiday destinations.

The plugin uses shortcodes to help you easily add the destinations to any post or page on your site.

## Installation

1. Upload the Holidays plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Creating a Destination

1. Navigate to the `Destinations` menu option in your WordPress dashboard.
2. Click on `Add New` to create a new destination.
3. Add a title, content, and a featured image for your destination.
4. Click `Publish`.

### Displaying Destinations on a Page

Add the `[holiday-destinations]` shortcode to any page or post where you want the holiday destinations to be displayed.

### REST APIs

The plugin offers the following APIs:

- `GET /v1/holidays/deals`: Returns a list of deals.
- `POST /v1/holidays/request_brochure`: Allows users to request a brochure. The request body should contain 'name', 'email', and 'address'.

### Styles

You can customize the look of the destinations list by overriding the styles from `app.css` file in your theme's CSS.

## Developer Notes

- Error handling: Any errors caught in the code are sent to a log function, which should be replaced with actual logging logic.
- API Key: The API key in the `sendBrochureApiRequest` function is a placeholder and should be replaced with an actual API key.

## Changelog

### 1.0.0
- Initial version

## Support

For support, feature requests, or bug reporting, please send an email to haxhiuargjend@gmail.com
