<?php

/**
 * Plugin Name: GitHub Updater Test Plugin
 * Plugin URI:  https://fabrikage.nl
 * Description: A test plugin for GitHub Updater.
 * Version:     0.0.1
 * Author:      Fabrikage
 * Author URI:  https://fabrikage.nl
 * Text Domain: github-updater-test-plugin
 *
 */

namespace Fabrikage\GitHubUpdater\TestPlugin;

use Fabrikage\GitHubUpdater\TestPlugin\Plugin;

const GITHUB_UPDATER_TEST_PLUGIN_FILE = __FILE__;
const GITHUB_UPDATER_TEST_PLUGIN_DIR = __DIR__;
const GITHUB_UPDATER_TEST_PLUGIN_VERSION = '{version}';
const GITHUB_UPDATER_TEST_PLUGIN_SLUG = 'github-updater-test-plugin';

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
// Initialize the plugin
Plugin::init();
