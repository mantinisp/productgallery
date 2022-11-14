{if $gallery.images|@count}
    <div class="modal fade js-product-images-modal" id="gallery-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {assign var=imagesCount value=$currentGallery.images|count}
                    <figure>
                        {if $currentGallery}
                            <img
                                class="js-modal-product-cover product-gallery-modal"
                                width="{$currentGallery.images[0].bySize.large_default.width}"
                                src="{$currentGallery.images[0].medium.url}"
                                {if !empty($currentGallery.images[0].legend)}
                                    alt="{$currentGallery.images[0].legend}"
                                    title="{$currentGallery.images[0].legend}"
                                {else}
                                    alt="{$product.name}"
                                {/if}
                                height="{$currentGallery.images[0].bySize.large_default.height}"
                            >
                        {/if}
                    </figure>
                </div>
            </div>
        </div>
    </div>
{/if}