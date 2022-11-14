$(document).ready(function() {
  $(document).on('click', '.additional-product-gallery__header-title', function () {
    const target = $(this).data('additional-gallery');

    if (target) {
      const currentGallery = $("#additional-gallery-" + target);

      if (currentGallery.hasClass('additional-product-gallery--active')) {
        currentGallery.removeClass('additional-product-gallery--active');
        $(this).removeClass('additional-product-gallery__header-title--active');
      } else {
        $('.additional-product-galleries > .additional-product-gallery')
          .each(function () {
            $(this).removeClass('additional-product-gallery--active');
          });
        $('.additional-product-gallery__header-title')
          .each(function () {
            $(this).removeClass('additional-product-gallery__header-title--active');
          });
        currentGallery.addClass('additional-product-gallery--active');
        $(this).addClass('additional-product-gallery__header-title--active');
      }
    }
  });

  $(document).on('click', '.product-gallery__item', function () {
    const currentImage = $(this).children('div').children('img');
    const largeImageUrl = currentImage.attr('data-image-large-src');
    currentImage.addClass('selected');
    currentImage.addClass('js-thumb-selected');

    $('.product-gallery-modal').attr('src', largeImageUrl);
  });
});