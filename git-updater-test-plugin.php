<?php

/**
 * Plugin Name: Git Updater Test Plugin
 * Plugin URI:  https://fabrikage.nl
 * Description: A test plugin for Git Updater.
 * Version:     {version}
 * Author:      Fabrikage
 * Author URI:  https://fabrikage.nl
 * Text Domain: git-updater-test-plugin
 *
 */

namespace Fabrikage\GitUpdater\TestPlugin;

use Fabrikage\GitUpdater\TestPlugin\Bootstrap;

const GIT_UPDATER_TEST_PLUGIN_FILE = __FILE__;
const GIT_UPDATER_TEST_PLUGIN_DIR = __DIR__;
const GIT_UPDATER_TEST_PLUGIN_VERSION = '{version}';
const GIT_UPDATER_TEST_PLUGIN_SLUG = 'git-updater-test-plugin';

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize the plugin
Bootstrap::init();
