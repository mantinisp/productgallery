$(document).ready(function() {
  const liTabs6 = document.getElementById('tab_step6');
  const newTabs7 = document.createElement('li');
  const newHref = document.createElement('a');
  newTabs7.className = 'nav-item';
  newTabs7.id = 'tab_step7';
  newHref.setAttribute('role', 'tab');
  newHref.setAttribute('data-toogle', 'tab');
  newHref.className = 'nav-link';
  newHref.href = '#step7';
  newTabs7.appendChild(newHref);
  newHref.innerHTML = 'Gallery';
  newHref.id = '_step7';
  liTabs6.parentNode.insertBefore(newTabs7, liTabs6.nextSibling);

  newTabs7.onclick = function () {
    removeActiveTab();
  };

  const content6 = document.getElementById('step6');
  const contentNewTab7 = document.createElement('div');
  contentNewTab7.className = 'form-contenttab tab-pane';
  contentNewTab7.setAttribute('role', 'tabpanel');
  contentNewTab7.setAttribute('wfd-invisible', 'true');
  contentNewTab7.id = 'step7';
  content6.parentNode.insertBefore(contentNewTab7, content6.nextSibling);

  readAjaxContent();

  $(document).on('click', '#product-gallery-create-new-gallery', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const createButton = $('#product-gallery-create-new-gallery');
    const createGalleryField = $('.product-gallery__field');
    const createGalleryInput = $('#product-gallery-create-input');

    if (createGalleryField.hasClass('product-gallery__field--active')) {
      const createButton = $('#product-gallery-create-new-gallery');
      const createGalleryField = $('.product-gallery__field');
      const url = createButton.attr('url-create-gallery');

      if (createGalleryInput.val().length < 4) {
        createButton.addClass('btn-warning');
        createButton.html('Please provide gallery name!');
      } else {
        createProductGalleryAjax(url, createGalleryInput.val());
        createGalleryField.removeClass('product-gallery__field--active');
        createButton.html('Create new gallery');
      }
    } else {
      createGalleryInput.val('');
      createGalleryField.addClass('product-gallery__field--active');
      createButton.html('Save!');
    }
  });

  $(document).on('click', '.delete-product-gallery', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const deleteButton = $(this);
    const url = deleteButton.attr('url-delete-gallery');

    if (deleteButton && url) {
      deleteProductGalleryAjax(url);
    }
  });

  $(document).on('click', '.product-gallery__name', function () {
    const currentGallery = $(this).attr('data-gallery-id');
    const currentGalleryCounter = $(this).attr('data-gallery-counter');
    const currentToggle = $('#toggle-gallery-' + currentGallery);

    if (currentGalleryCounter > 0) {
      currentToggle.slideToggle();
    }
  })

  $(document).on('change', '#form_image_id_image_gallery', function () {
    const gallery = $('#form_image_id_image_gallery').val();
    const imageId = $('#product-gallery-select').data('image-id');
    const url = $('#product-gallery-select').attr('update-image-gallery-url');

    if (imageId && gallery) {
      updateImageGallery(url, gallery);
    }
  })

  $(document).on('click', '.product-gallery-image', function () {
    const currentGallery = $(this);
    const currentGalleryId = $(this).attr('data-gallery-id');
    const currentGalleryImageId = $(this).attr('data-id');

    if (currentGallery.hasClass('active'))
    {
      currentGallery.removeClass('active');
      handleImageGalleryEditor(currentGalleryId, currentGalleryImageId, false);
    } else {
      $('.product-gallery-image').each(function () {
          $('.product-gallery-image').removeClass('active');
      });
      currentGallery.addClass('active');
      handleImageGalleryEditor(currentGalleryId, currentGalleryImageId, true);
    }
  });

  $(document).on('click', '.product-image-update-gallery', function () {
    const currentGalleryId = $(this).attr('data-gallery-id');
    const idField = $('#hidden-gallery-id-' + currentGalleryId);
    const selectField = $('#field-gallery-id-' + currentGalleryId);

    if (idField.val() && selectField.val()) {
      const currentUpdateUrl = $('#product-image-updater-id-' + idField.val()).attr('update-image-gallery-url');
      updateImageGalleryFromGalleryTab(currentUpdateUrl, selectField.val());
    }
  });
});

function handleImageGalleryEditor(currentGalleryId, currentGalleryImageId, show = false) {
  const editor = $('.product-images-gallery__edit');
  const selectField = $('#field-gallery-id-' + currentGalleryId);
  const idField = $('#hidden-gallery-id-' + currentGalleryId);

  if (show) {
    editor.addClass('product-images-gallery__edit--active');
    idField.val(currentGalleryImageId);

    if (selectField.find('option[value="' + currentGalleryId + '"]').length) {
      selectField.val(currentGalleryId);
    }
  } else {
    editor.removeClass('product-images-gallery__edit--active');
  }
}

function removeActiveTab() {
  const ul = document.getElementById('form-nav');
  const listItems = ul.getElementsByTagName('a');

  for (let i = 0; i <= listItems.length - 1; i++) {
    listItems[i].classList.remove('active');
  }

  document.getElementById('_step7').classList.add('active');

  removeActiveTabContent();
}

function removeActiveTabContent() {
  const div = document.getElementById('form_content');
  const listItems = div.getElementsByTagName('div');

  for (let i = 0; i <= listItems.length - 1; i++) {
    listItems[i].classList.remove('active');
  }

  document.getElementById('step7').classList.add('active');

  readAjaxContent();
}

function readAjaxContent() {
  $.ajax({
    type: "GET",
    url: product_gallery_content_ajax,
    data: 'idProduct='+document.getElementById('form_id_product').value,
    dataType: "html",
    crossDomain: true,
    async: true,
    success: function(response){
      $('#step7').html(response);
    },
  });
}

function createProductGalleryAjax(url, inputValue) {
  $('#step7').html('');
  $.ajax({
    type: "POST",
    url: url,
    data: {
      idProduct: document.getElementById('form_id_product').value,
      inputValue,
    },
    dataType: "html",
    crossDomain: true,
    async: true,
    success: function(response) {
      $('#step7').html(response);
    },
  });
}

function deleteProductGalleryAjax(url) {
  $('#step7').html('');
  $.ajax({
    type: "DELETE",
    url: url,
    dataType: "html",
    crossDomain: true,
    async: true,
    success: function(response) {
      $('#step7').html(response);
    },
  });
}

function updateImageGalleryFromGalleryTab(url, galleryId) {
  $.ajax({
    type: "POST",
    url: url,
    data: {
      id_gallery: galleryId,
      tab: true,
    },
    dataType: "html",
    crossDomain: true,
    async: true,
    success: function(response) {
      $('#step7').html(response);
    },
  });
}

function updateImageGallery(url, galleryId) {
  $.ajax({
    type: "POST",
    url: url,
    data: {
      id_gallery: galleryId,
    },
    dataType: "html",
    crossDomain: true,
    async: true,
    success: function(response) {},
  });
}