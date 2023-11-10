$(document).ready(function () {
    $('.two_tier__structure li').on('click', function() {
        let list = $(this).children('ul');
        if(list.hasClass('active')){
            list.removeClass('active');
            list.css({height : 0});
        }else{
            $('.two_tier__structure__col').removeClass('active');
            list.addClass('active');
            let link = $('.two_tier__structure__col.active li');
            let countLink = link.length;
            let height = countLink * link.height();
            $('.two_tier__structure__col.active').css({height : height});
        }
    });

    let owl = $(".carousel__top.owl-carousel");
    owl.owlCarousel({
        items: 1,
        loop: true,
        center: true,
        nav: true,
        dots: false,
        margin: 10,
        URLhashListener: true,
        autoplayHoverPause: true,
        startPosition: 'URLHash',
        autoHeight: false,
        autoHeightClass: 'owl-height',
        navText: ["<img src='./img/icon/ArrowPrev.svg' />", "<img src='./img/icon/ArrowNext.svg' />"]
    });

    owl.on('changed.owl.carousel', function (event) {
        let currentItem = event.item.index;
        let description = $(event.target).find(".owl-item").eq(currentItem).find("div").data("description")
        $('#carouselImgDescription').text(description);
    });

    let owlMob = $(".carousel__box");
    owlMob.owlCarousel({
        items: 1.18,
        loop: true,
        center: false,
        nav: false,
        dots: false,
        margin: 20,
    });

    owlMob.on('changed.owl.carousel', function (event) {
        let currentItem = event.item.index;
        let description = $(event.target).find(".owl-item").eq(currentItem).find("div").data("description")
        $('#carouselImgDescriptionMob').text(description);
    });
});