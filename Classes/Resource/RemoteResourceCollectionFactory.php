<?php
namespace IchHabRecht\Filefill\Resource;

use IchHabRecht\Filefill\Resource\Domain\DomainResource;
use IchHabRecht\Filefill\Resource\Domain\DomainResourceRepository;
use IchHabRecht\Filefill\Resource\Placeholder\PlaceholderResource;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RemoteResourceCollectionFactory
{
    /**
     * @param string $configuration
     * @return RemoteResourceCollection
     */
    public static function createRemoteResourceCollectionFromConfiguration($configuration)
    {
        $remoteResources = [];

        $resourcesConfiguration = GeneralUtility::xml2array($configuration);

        foreach ((array)$resourcesConfiguration['data']['sDEF']['lDEF']['resources']['el'] as $resource) {
            if (empty($resource)) {
                continue;
            }

            $key = key($resource);

            switch ($key) {
                case 'domain':
                    $remoteResources[] = GeneralUtility::makeInstance(DomainResource::class, $resource['domain']['el']['domain']['vDEF']);
                    break;
                case 'sys_domain':
                    $domainResourceRepository = GeneralUtility::makeInstance(DomainResourceRepository::class);
                    $remoteResources = array_merge($remoteResources, $domainResourceRepository->findAll());
                    break;
                case 'placeholder':
                    $remoteResources[] = GeneralUtility::makeInstance(PlaceholderResource::class);
                    break;
                default:
                    throw new \RuntimeException('Unexpected File Fill Resource configuration "' . $key . '"', 1519788775);
            }
        }

        return GeneralUtility::makeInstance(RemoteResourceCollection::class, $remoteResources);
    }
}
