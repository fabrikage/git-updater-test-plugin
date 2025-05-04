<?php

namespace Fabrikage\GitUpdater\TestPlugin\Client;

use Fabrikage\GitUpdater\TestPlugin\Data\PluginInfo;
use Fabrikage\GitUpdater\TestPlugin\Data\PluginUpdate;
use Fabrikage\GitUpdater\TestPlugin\Data\ReleaseInfo;

interface ClientInterface
{
    public function getLatestRelease(): ?ReleaseInfo;

    public function getPluginInfo(): ?PluginInfo;

    public function getPluginUpdate(): ?PluginUpdate;

    public function addAuthorizationHeader(array $args, string $url): array;
}
