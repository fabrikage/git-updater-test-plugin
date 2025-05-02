<?php

namespace Fabrikage\GitHubUpdater\TestPlugin;

class Plugin
{
    public static function getPluginDir(): string
    {
        return GITHUB_UPDATER_TEST_PLUGIN_DIR;
    }

    public static function getPluginFile(): string
    {
        return GITHUB_UPDATER_TEST_PLUGIN_FILE;
    }

    public static function getPluginVersion(): string
    {
        return GITHUB_UPDATER_TEST_PLUGIN_VERSION;
    }

    public static function getPluginSlug(): string
    {
        return GITHUB_UPDATER_TEST_PLUGIN_SLUG;
    }

    public static function init(): static
    {
        return new static();
    }

    private function __construct()
    {
        register_activation_hook(GITHUB_UPDATER_TEST_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(GITHUB_UPDATER_TEST_PLUGIN_FILE, [$this, 'deactivate']);

        $updateChecker = new UpdateChecker();

        add_filter('pre_set_site_transient_update_plugins', [$updateChecker, 'checkForUpdates']);
        add_filter('http_request_args', [$updateChecker, 'addAuthorizationHeader'], 10, 2);
        add_filter('plugins_api', [$updateChecker, 'addPluginInfo'], 10, 3);

        add_action('admin_init', function () {
            if (isset($_GET['force-plugin-update'])) {
                delete_site_transient('update_plugins');
                wp_update_plugins();
                echo 'Plugin update check triggered.';
                exit;
            }
        });
    }

    public function activate(): void
    {
        // Activation logic here
    }

    public function deactivate(): void
    {
        // Deactivation logic here
    }
}
