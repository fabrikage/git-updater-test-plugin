<?php

namespace Fabrikage\GitHubUpdater\TestPlugin;

use Fabrikage\GitHubUpdater\TestPlugin\Client\GitHubClient;

class Bootstrap
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

        if (!defined('GITHUB_UPDATER_GITHUB_TOKEN')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>GitHub token is not defined. Please set the GITHUB_UPDATER_GITHUB_TOKEN (with access to the repository) in your wp-config.php file.</p></div>';
            });

            return;
        }

        // Set-up the GitHub client
        $client = new GitHubClient(
            username: 'fabrikage',
            repository: 'github-updater-test-plugin',
            token: GITHUB_UPDATER_GITHUB_TOKEN
        );

        // Initialize the update checker
        UpdateChecker::init($client);
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
