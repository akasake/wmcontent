<?php

namespace Drupal\wmcontent;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\wmcontent\Entity\EntityTypeBundleInfo;

/**
 * TODO: Simplify this & maybe move to controller?
 */
class WmContentDescriptiveTitles
{
    use StringTranslationTrait;

    /** @var CurrentRouteMatch */
    protected $currentRouteMatch;
    /** @var EntityTypeBundleInfo */
    protected $entityTypeBundleInfo;

    public function __construct(
        CurrentRouteMatch $currentRouteMatch,
        EntityTypeBundleInfo $entityTypeBundleInfo
    ) {
        $this->currentRouteMatch = $currentRouteMatch;
        $this->entityTypeBundleInfo = $entityTypeBundleInfo;
    }

    /** @return TranslatableMarkup */
    public function getPageTitle()
    {
        $bundleInfo = $this->entityTypeBundleInfo->getAllBundleInfo();
        $container = $this->getContainerType();
        $hostType = $this->currentRouteMatch->getParameter('host_type_id');
        $host = $this->currentRouteMatch->getParameter($hostType);

        if ($child = $this->currentRouteMatch->getParameter('child')) {
            $bundle = $child->bundle();
        } else {
            // Get bundle from its parameter
            $bundle = $this->currentRouteMatch->getParameter('bundle');
        }

        // Build title
        $host = $host->label() ?: $bundleInfo[$hostType][$host->bundle()]['label'];
        $type = $bundleInfo[$container][$bundle]['label'];

        $routeName = $this->currentRouteMatch->getRouteName();
        switch (true) {
            case strpos($routeName, 'wmcontent_add') !== false:
                return $this->t(
                    'Add new %type to %host',
                    [
                        '%type' => $type,
                        '%host' => $host,
                    ]
                );
            case strpos($routeName, 'wmcontent_edit') !== false:
                return $this->t(
                    'Edit %type from %host',
                    [
                        '%type' => $type,
                        '%host' => $host,
                    ]
                );
            default:
                return $this->t('');
        }
    }

    private function getContainer()
    {
        return $this->currentRouteMatch->getParameter('container');
    }

    private function getContainerType()
    {
        return $this->getContainer()->getChildEntityType();
    }
}
