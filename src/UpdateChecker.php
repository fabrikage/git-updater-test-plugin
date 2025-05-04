<?php

namespace Fabrikage\GitHubUpdater\TestPlugin;

use Fabrikage\GitHubUpdater\TestPlugin\Client\ClientInterface;

class UpdateChecker
{
    public function __construct(private ClientInterface $client)
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'checkForUpdates']);
        add_filter('plugins_api', [$this, 'addPluginInfo'], 10, 3);
        add_filter('http_request_args', [$this, 'addAuthorizationHeader'], 10, 2);
        add_action('admin_init', [$this, 'forceUpdateCheck'], 10);
    }

    public function forceUpdateCheck(): void
    {
        if (isset($_GET['force-plugin-update'])) {
            delete_site_transient('update_plugins');
            wp_update_plugins();
            wp_redirect(admin_url('plugins.php'));
            exit;
        }
    }

    public static function init(ClientInterface $client): static
    {
        return new static($client);
    }

    public function checkForUpdates($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $update = $this->client->getPluginUpdate();

        if (!$update) {
            return $transient;
        }

        $transient->response[$update->pluginFile] = (object) $update->toArray();

        return $transient;
    }

    public function addAuthorizationHeader($args, $url)
    {
        return $this->client->addAuthorizationHeader($args, $url);
    }

    public function addPluginInfo($result, $action, $args)
    {
        // Check if the action is 'plugin_information'
        if ($action !== 'plugin_information') {
            return $result;
        }

        // Check if the slug matches the plugin slug
        if (isset($args->slug) && $args->slug !== Bootstrap::getPluginSlug()) {
            return $result;
        }

        // Fetch the plugin info from the API client
        $pluginInfo = $this->client->getPluginInfo();

        // If the plugin info is not found, return the original result
        if (!$pluginInfo) {
            return $result;
        }

        // Convert the plugin info to an array and add the download URL
        return (object) $pluginInfo->toArray(['download_url' => $this->client->getPluginUpdate()->packageUrl]);
    }
}
