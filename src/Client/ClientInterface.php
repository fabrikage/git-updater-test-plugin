<?php

namespace Fabrikage\GitHubUpdater\TestPlugin\Client;

use Fabrikage\GitHubUpdater\TestPlugin\Data\PluginInfo;
use Fabrikage\GitHubUpdater\TestPlugin\Data\PluginUpdate;
use Fabrikage\GitHubUpdater\TestPlugin\Data\ReleaseInfo;

interface ClientInterface
{
    public function getLatestRelease(): ?ReleaseInfo;

    public function getPluginInfo(): ?PluginInfo;

    public function getPluginUpdate(): ?PluginUpdate;

    public function addAuthorizationHeader(array $args, string $url): array;
}
