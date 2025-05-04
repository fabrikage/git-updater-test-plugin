# Git Updater Test Plugin

A WordPress plugin that demonstrates remote plugin updates using repository releases API.

## Disclaimer

This README file is incomplete and might not provide all the necessary information for using the plugin. Make sure to read through the source code for a better understanding of the plugin's functionality and configuration options.

## Overview

This plugin serves as a demonstration of how to implement automatic updates for WordPress plugins using external repository hosting services. It sets up a system that connects to a repository's API, checks for new releases, and provides users with update notifications within the WordPress admin panel.

This repository serves as a proof-of-concept for the plugin's functionality. You can use it as a reference for implementing similar functionality in your own WordPress plugins.

## Features

- Connects to external repository hosting service API (only GitHub for now)
- Authenticates with repository services using personal access tokens
- Fetches release information including version numbers, changelog, and download URLs
- Provides update notifications in WordPress admin when a new version is available
- Handles the update process through WordPress's built-in updater system

## Configuration

The plugin can be configured to check for updates from any compatible repository. The access token must have appropriate permissions to access the repository.

## How It Works

The plugin uses the following components:

- `ClientInterface` class: Handles communication with the repository API
- `UpdateChecker` class: Integrates with WordPress update system
- `Bootstrap` class: Initializes the plugin and sets up the update system

The update process follows these steps:

1. The plugin checks for new releases on the remote with the `pre_set_site_transient_update_plugins` hook
2. It compares the current version with the latest release version
3. If a newer version is available, it displays an update notification
4. When the user initiates the update, it downloads the new version from the repository

## Requirements

- WordPress 5.0 or higher
- PHP 8.0 or higher
- Repository access token with appropriate permissions

## Development

For developers, the plugin includes a CI/CD workflow that automatically creates releases when tags are pushed. The workflow:

- Generates a changelog from commits
- Prepares the plugin files for distribution
- Creates a ZIP file with the appropriate version number
- Uploads the ZIP file as a release asset
