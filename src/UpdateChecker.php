<?php

namespace Fabrikage\GitHubUpdater\TestPlugin;

class UpdateChecker
{
    private string $githubUsername = 'fabrikage';
    private string $githubRepository = 'github-updater-test-plugin';

    private function githubSlug(): string
    {
        return sprintf(
            '%s/%s',
            $this->githubUsername,
            $this->githubRepository
        );
    }

    public function checkForUpdates($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $currentVersion = Plugin::getPluginVersion();
        $apiUrl = sprintf('https://api.github.com/repos/%s/releases/latest', $this->githubSlug());

        $args = [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            ],
        ];

        $response = wp_remote_get($apiUrl, $args);

        if (is_wp_error($response)) {
            return $transient;
        }

        $release = json_decode(wp_remote_retrieve_body($response));
        if (!$release || empty($release->tag_name) || empty($release->assets)) {
            return $transient;
        }

        // Search for your desired asset (e.g. named plugin zip)
        $assetUrl = null;
        foreach ($release->assets as $asset) {
            if (str_contains($asset->name, Plugin::getPluginSlug())) {
                $assetUrl = $asset->browser_download_url;
                break;
            }
        }

        if (!$assetUrl) {
            return $transient; // No matching asset found
        }

        if (version_compare($currentVersion, ltrim($release->tag_name, 'v'), '<')) {
            $plugin = new \stdClass();
            $plugin->slug = Plugin::getPluginSlug();
            $plugin->plugin = Plugin::getPluginSlug();
            $plugin->new_version = ltrim($release->tag_name, 'v');
            $plugin->package = $assetUrl; // Auth is needed here too
            $plugin->url = $release->html_url;
            $transient->response[basename(Plugin::getPluginDir()) . '/' . basename(Plugin::getPluginFile())] = $plugin;
        }

        return $transient;
    }

    public function addAuthorizationHeader($args, $url)
    {
        if (strpos($url, $this->githubSlug()) !== false) {
            $github_token = defined('GITHUB_TOKEN') ? GITHUB_TOKEN : null;

            if ($github_token) {
                $args['headers']['Authorization'] = 'Bearer ' . $github_token;
                $args['headers']['User-Agent'] = 'WordPress/' . get_bloginfo('version') . '; ' . home_url();
            }
        }

        return $args;
    }

    public function addPluginInfo($result, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if ($args->slug !== Plugin::getPluginSlug()) {
            return $result;
        }

        // Fetch the plugin info from GitHub
        $apiUrl = sprintf('https://api.github.com/repos/%s/releases/latest', $this->githubSlug());

        $response = wp_remote_get($apiUrl, [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            ],
        ]);

        if (is_wp_error($response)) {
            return $result;
        }

        $repo = json_decode(wp_remote_retrieve_body($response));

        // Convert markdown $repo->body to HTML
        if (isset($repo->body)) {
            $markdown = new \Parsedown();
            $repo->body = $markdown->text($repo->body);
        }

        // Build the plugin info response
        $plugin_info = new \stdClass();
        $plugin_info->name = Plugin::getPluginSlug();
        $plugin_info->slug = Plugin::getPluginSlug();
        $plugin_info->homepage = $repo->html_url;
        $plugin_info->version = ltrim($repo->tag_name, 'v');
        $plugin_info->sections = [
            'changelog' => $repo->body,
        ];

        return $plugin_info;
    }
}
