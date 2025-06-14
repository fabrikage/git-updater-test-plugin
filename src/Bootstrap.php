<?php

namespace Fabrikage\GitUpdater\TestPlugin;

use Fabrikage\GitUpdater\TestPlugin\Client\GitHubClient;

class Bootstrap
{
    public static function getPluginDir(): string
    {
        return GIT_UPDATER_TEST_PLUGIN_DIR;
    }

    public static function getPluginFile(): string
    {
        return GIT_UPDATER_TEST_PLUGIN_FILE;
    }

    public static function getPluginVersion(): string
    {
        return GIT_UPDATER_TEST_PLUGIN_VERSION;
    }

    public static function getPluginSlug(): string
    {
        return GIT_UPDATER_TEST_PLUGIN_SLUG;
    }

    public static function init(): static
    {
        return new static();
    }

    private function __construct()
    {
        register_activation_hook(GIT_UPDATER_TEST_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(GIT_UPDATER_TEST_PLUGIN_FILE, [$this, 'deactivate']);

        if (!defined('GIT_UPDATER_GITHUB_TOKEN')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>GitHub token is not defined. Please set the GIT_UPDATER_GITHUB_TOKEN (with access to the repository) in your wp-config.php file.</p></div>';
            });

            return;
        }

        // Set-up the GitHub client
        $client = new GitHubClient(
            username: 'fabrikage',
            repository: 'git-updater-test-plugin',
            token: GIT_UPDATER_GITHUB_TOKEN
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
