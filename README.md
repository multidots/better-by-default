# Better By Default Plugin
![better-by-default-banner](https://github.com/user-attachments/assets/133b8d8b-adea-483e-bf2b-60ee0d3116d0)

WordPress Plugin for [Multidots](https://www.multidots.com/)

Better By Default is a WordPress plugin designed to simplify and improve the WordPress admin experience. It enhances security, performance, and personalization options by removing unnecessary features, improving existing ones, and making WordPress easier to use for both developers and administrators.

## Features

- Enhanced WordPress admin interface for a clean, clutter-free experience.
- Security settings to enhance default WordPress installations.
- Performance improvements by disabling non-essential features.
- Customization options for personalizing the WordPress admin area.

## Requirements

`Better By Default` requires the following dependencies:

- [Node.js](https://nodejs.org/) - JavaScript runtime for building and compiling the assets.
- [NVM](https://wptraining.md10x.com/lessons/install-nvm/) - Node Version Manager for managing different versions of Node.js.

## Installation

1. Download and upload the `better-by-default` plugin to your `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Better By Default** to configure the plugin settings.

## Build Process

To work on the plugin locally, follow these steps:

### Install Dependencies

1. Ensure you have the correct Node.js version:

   ```bash
   cd assets
   nvm use
   ```
2. Install all necessary Node.js dependencies:

    ```bash
    npm install
    ```

### During development
To build the development version of the plugin assets:

```bash
npm start
```

### Production
To build the production version of the plugin assets, run:

```bash
npm run build
```

## Usage
After installation, the plugin can be configured from the Settings > Better By Default page. The plugin will automatically improve security, enhance performance, and simplify the admin area, with additional settings to adjust based on user needs.

## Contributing
We welcome contributions from the community! If you would like to contribute:

1. Fork the repository.
2. Make your changes.
3. Create a pull request.
4. Make sure to run npm run build before submitting changes to ensure all assets are properly built.

## License
Better By Default is licensed under the GNU General Public License v3. See the license file for more details.

## Credits
Better By Default is developed by Multidots. We appreciate the contributions from the open-source community.

## Support
For support, please visit our website or submit an issue on the GitHub repository.

## Changelog
1.0.0

Initial release with admin enhancements, security settings, and performance improvements.


### Key Sections Explained
1. **Header**: A brief description of the plugin, including who developed it and what it is used for.
2. **Features**: A list highlighting the main capabilities of the plugin.
3. **Requirements**: Dependencies for the development environment.
4. **Installation**: Step-by-step instructions on installing and activating the plugin.
5. **Development Setup**: Information on setting up the development environment, installing dependencies, and building the plugin.
6. **Usage**: Where to find the plugin settings and how to start using it.
7. **Contributing**: How to contribute to the project.
8. **License**: Licensing information.
9. **Credits**: Who made the plugin.
10. **Support**: Where to get help or raise issues.
11. **Changelog**: Version history, which is important for users to track updates.

This format should make your `readme.md` clear, informative, and helpful for users and developers alike.

## See potential here?
<a href="https://www.multidots.com/contact-us/" rel="nofollow"><img width="1692" height="296" alt="01-GitHub Footer" src="https://github.com/user-attachments/assets/6b9d63e7-3990-472d-acb9-5e4e51b446fc" /></a>
