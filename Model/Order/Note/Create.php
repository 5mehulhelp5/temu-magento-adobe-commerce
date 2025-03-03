<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Note;

class Create
{
    use MagentoOrderUpdateTrait;

    private \M2E\Temu\Model\Order\Note\Repository $repository;
    private \M2E\Temu\Model\Order\NoteFactory $noteFactory;

    public function __construct(
        \M2E\Temu\Model\Order\Note\Repository $repository,
        \M2E\Temu\Model\Order\NoteFactory $noteFactory,
        \M2E\Temu\Model\Magento\Order\Updater $magentoOrderUpdater
    ) {
        $this->repository = $repository;
        $this->noteFactory = $noteFactory;
        $this->magentoOrderUpdater = $magentoOrderUpdater;
    }

    public function process(\M2E\Temu\Model\Order $order, string $note): \M2E\Temu\Model\Order\Note
    {
        $obj = $this->noteFactory->create((int)$order->getId(), $note);

        $this->repository->create($obj);

        $comment = (string)__(
            'Custom Note was added to the corresponding %channel_title order: %note.',
            [
                'note' => $obj->getNote(),
                'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
            ],
        );
        $this->updateMagentoOrderComment($order, $comment);

        return $obj;
    }
}
