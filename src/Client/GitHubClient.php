<?php

namespace Fabrikage\GitHubUpdater\TestPlugin\Client;

use Fabrikage\GitHubUpdater\TestPlugin\Data\PluginInfo;
use Fabrikage\GitHubUpdater\TestPlugin\Data\PluginUpdate;
use Fabrikage\GitHubUpdater\TestPlugin\Data\ReleaseInfo;
use Fabrikage\GitHubUpdater\TestPlugin\Bootstrap;
use Parsedown;

class GitHubClient implements ClientInterface
{
    public function __construct(private string $username, private string $repository, private ?string $token = null) {}

    /**
     * Returns the slug of the GitHub repository in the format "username/repository".
     *
     * @return string The slug of the GitHub repository.
     */
    private function slug(): string
    {
        return "{$this->username}/{$this->repository}";
    }

    /**
     * Adds the authorization header to the request if the URL matches the GitHub repository.
     *
     * @param array $args The request arguments.
     * @param string $url The request URL.
     * @return array The modified request arguments.
     */
    public function addAuthorizationHeader(array $args, string $url): array
    {
        if (strpos($url, $this->slug()) === false) {
            return $args;
        }

        if (!empty($this->token)) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->token;
        }

        if (strpos($url, 'releases/latest') !== false) {
            $args['headers']['Accept'] = 'application/vnd.github+json';
        } elseif (strpos($url, 'releases/assets/') !== false) {
            $args['headers']['Accept'] = 'application/octet-stream';
        }

        $args['headers']['User-Agent'] = 'WordPress/' . get_bloginfo('version') . '; ' . home_url();

        return $args;
    }

    /**
     * Fetches the latest release information from the GitHub repository.
     *
     * @return ReleaseInfo|null The latest release information or null if not found.
     */
    public function getLatestRelease(): ?ReleaseInfo
    {
        $url = "https://api.github.com/repos/{$this->slug()}/releases/latest";

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return null;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        if (!isset($release->tag_name)) {
            return null;
        }

        $assets = array_map(function ($asset) {
            return [
                'name' => $asset->name,
                'id' => $asset->id,
                'url' => $asset->browser_download_url,
            ];
        }, $release->assets ?? []);

        return new ReleaseInfo(
            version: ltrim($release->tag_name, 'v'),
            htmlUrl: $release->html_url,
            changelog: (new Parsedown())->text($release->body ?? ''),
            assets: $assets
        );
    }

    /**
     * Fetches the plugin information from the GitHub repository.
     *
     * @return PluginInfo|null The plugin information or null if not found.
     */
    public function getPluginInfo(): ?PluginInfo
    {
        $release = $this->getLatestRelease();

        if (!$release) {
            return null;
        }

        $htmlBody = $release->changelog ?? __('No changelog available.', 'github-updater-test-plugin');

        return new PluginInfo(
            slug: Bootstrap::getPluginSlug(),
            version: $release->version,
            homepage: $release->htmlUrl,
            sections: ['changelog' => $htmlBody]
        );
    }

    /**
     * Fetches the plugin update information from the GitHub repository.
     *
     * @return PluginUpdate|null The plugin update information or null if not found.
     */
    public function getPluginUpdate(): ?PluginUpdate
    {
        $release = $this->getLatestRelease();

        if (!$release) {
            return null;
        }

        $currentVersion = ltrim(Bootstrap::getPluginVersion(), 'v');
        if (version_compare($currentVersion, $release->version, '>=')) {
            return null;
        }

        foreach ($release->assets as $asset) {
            if (strpos($asset['name'], Bootstrap::getPluginSlug()) !== false) {
                $assetUrl = sprintf(
                    'https://api.github.com/repos/%s/releases/assets/%d',
                    $this->slug(),
                    $asset['id']
                );

                return new PluginUpdate(
                    slug: Bootstrap::getPluginSlug(),
                    pluginFile: basename(Bootstrap::getPluginDir()) . '/' . basename(Bootstrap::getPluginFile()),
                    newVersion: $release->version,
                    packageUrl: $assetUrl,
                    infoUrl: $release->htmlUrl
                );
            }
        }

        return null;
    }
}
