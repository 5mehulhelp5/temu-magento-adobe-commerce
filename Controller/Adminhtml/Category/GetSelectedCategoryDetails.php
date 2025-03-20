<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Category;

class GetSelectedCategoryDetails extends \M2E\Temu\Controller\Adminhtml\AbstractCategory
{
    private \M2E\Temu\Model\Category\Tree\Repository $treeRepository;
    private \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder;

    public function __construct(
        \M2E\Temu\Model\Category\Tree\Repository $treeRepository,
        \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder
    ) {
        parent::__construct();

        $this->treeRepository = $treeRepository;
        $this->pathBuilder = $pathBuilder;
    }

    public function execute()
    {
        $region = $this->getRequest()->getParam('region');
        $categoryId = $this->getRequest()->getParam('value');

        if (
            empty($region)
            || empty($categoryId)
        ) {
            throw new \M2E\Temu\Model\Exception\Logic('Invalid input');
        }

        $category = $this->treeRepository->getCategoryByRegionAndCategoryId($region, (int)$categoryId);
        if ($category === null) {
            throw new \M2E\Temu\Model\Exception\Logic('Category invalid');
        }

        $path = $this->pathBuilder->getPath($category);
        $details = [
            'path' => $path,
            'interface_path' => sprintf('%s (%s)', $path, $categoryId),
            'template_id' => null,
            'is_custom_template' => null,
        ];

        $this->setJsonContent($details);

        return $this->getResult();
    }
}
