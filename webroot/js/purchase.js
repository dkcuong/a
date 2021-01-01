var PURCHASE = {
    item_gallery : '',
    init_page: function() {
        COMMON.item_gallery = new Swiper('.div-item-slide-gallery', {
            slidesPerView: 1,
            spaceBetween: 30,
            slidesPerGroup: 1,
            centeredSlides: true,
            loop: false,
            autoplay: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                type: 'bullets'
            },
            navigation: {
                nextEl: '.btn-next',
                prevEl: '.btn-prev',
            },
        });
    },
}