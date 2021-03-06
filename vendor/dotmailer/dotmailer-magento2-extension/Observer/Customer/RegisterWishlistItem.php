<?php

namespace Dotdigitalgroup\Email\Observer\Customer;

/**
 * Register new wishlist items automation.
 */
class RegisterWishlistItem implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Dotdigitalgroup\Email\Model\ResourceModel\Wishlist
     */
    private $emailWishlistResource;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist
     */
    private $wishlistResource;

    /**
     * @var \Dotdigitalgroup\Email\Helper\Data
     */
    private $helper;

    /**
     * @var \Dotdigitalgroup\Email\Model\WishlistFactory
     */
    private $wishlistFactory;
    
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    private $wishlist;

    /**
     * RegisterWishlistItem constructor.
     *
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist $wishlistResource
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlist
     * @param \Dotdigitalgroup\Email\Model\WishlistFactory $wishlistFactory
     * @param \Dotdigitalgroup\Email\Model\ResourceModel\Wishlist $emailWishlistResource
     * @param \Dotdigitalgroup\Email\Helper\Data $data
     */
    public function __construct(
        \Magento\Wishlist\Model\ResourceModel\Wishlist $wishlistResource,
        \Magento\Wishlist\Model\WishlistFactory $wishlist,
        \Dotdigitalgroup\Email\Model\WishlistFactory $wishlistFactory,
        \Dotdigitalgroup\Email\Model\ResourceModel\Wishlist $emailWishlistResource,
        \Dotdigitalgroup\Email\Helper\Data $data
    ) {
        $this->wishlist        = $wishlist;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistResource = $wishlistResource;
        $this->helper          = $data;
        $this->emailWishlistResource = $emailWishlistResource;
    }

    /**
     * If it's configured to capture on shipment - do this.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $wishlistItem = $observer->getEvent()->getItem();
        $wishlist = $this->wishlist->create();
        $this->wishlistResource->load($wishlist, $wishlistItem->getWishlistId());
        $emailWishlist = $this->wishlistFactory->create();
        try {
            if ($wishlistItem->getWishlistId()) {
                $itemCount = count($wishlist->getItemCollection());
                $item = $emailWishlist->getWishlist($wishlistItem->getWishlistId());

                if ($item && $item->getId()) {
                    $preSaveItemCount = $item->getItemCount();

                    if ($itemCount != $item->getItemCount()) {
                        $item->setItemCount($itemCount);
                    }

                    if ($itemCount == 1 && $preSaveItemCount == 0) {
                        $item->setWishlistImported(null);
                    } elseif ($item->getWishlistImported()) {
                        $item->setWishlistModified(1);
                    }

                    $this->emailWishlistResource->save($item);
                }
            }
        } catch (\Exception $e) {
            $this->helper->debug((string)$e, []);
        }

        return $this;
    }
}
