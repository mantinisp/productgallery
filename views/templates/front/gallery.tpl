{if $galleries|@count}
    <div class="additional-product-galleries__wrapper">
        <div class="additional-product-galleries__header">
            <div class="additional-product-galleries__header-title">
                {l s='Additional product galleries' d='Modules.Productgallery.Shop'} ({$total})
            </div>
        </div>
        <div class="additional-product-galleries">
            <div class="additional-product-gallery__header">
                {foreach from=$galleries item="gallery"}
                    {if $gallery.images|@count}
                        <a href="#{$gallery.id_image_gallery}" class="additional-product-gallery__header-title" data-additional-gallery="{$gallery.id_image_gallery}">
                            {$gallery.gallery_name}
                        </a>
                    {/if}
                {/foreach}
            </div>
            {foreach from=$galleries item="gallery"}
                <div
                    id="additional-gallery-{$gallery.id_image_gallery}"
                    class="additional-product-gallery"
                >
                    {if $gallery.images|@count}
                        <ul class="product-images js-modal-product-images product-gallery-images">
                            {foreach from=$gallery.images item="image"}
                                <li class="product-gallery__item">
                                    <div class="product-cover">
                                        <img
                                            data-image-large-src="{$image.large.url}"
                                            class="thumb js-thumb product-gallery-image-popup"
                                            src="{$image.medium.url}"{if !empty($image.legend)}
                                            alt="{$image.legend}"
                                            title="{$image.legend}"{else}
                                            alt="{$product.name}"{/if}
                                            width="{$image.medium.width}"
                                            loading="lazy"
                                        >
                                        <div class="layer product-gallery-layer hidden-sm-down" data-toggle="modal" data-target="#gallery-modal">
                                            <i class="material-icons zoom-in">search</i>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </div>
                {include file='module:productgallery/views/templates/front/gallery-images-modal.tpl'
                    currentGallery=$gallery
                }
            {/foreach}
        </div>
    </div>
{/if}