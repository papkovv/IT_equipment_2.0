$(document).ready(function () {
   
	


    // var files_url = window.location.href;
    // $(document).on('click', '#popup .bx-disk-interface-filelist .main-grid .main-grid-row-body .main-grid-cell-checkbox', function(){
    //     if($(this).parents('tr').hasClass('main-grid-row-checked')){
    //         var fileId = $(this).find('input').val();
    //         var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
    //         var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
    //         var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
    //         var type_file = $(this).parents('.main-grid-row-body').data('is-file');
    //         console.log(fileId)
    //         console.log($('.file_pick[data-id='+fileId+']').text());
    //         $('.file_pick[data-id='+fileId+']').parents('.file_item_container').remove();
    
            
    //     }else{
    //         var fileId = $(this).find('input').val();
    //         var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
    //         var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
    //         var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
    //         var type_file = $(this).parents('.main-grid-row-body').data('is-file');
    //         var path = Array();
    //         $('.js-disk-breadcrumbs-folder').each(function(){
    //             path.push($(this).data('object-name'));
    //             // path = $(this).data('object-name');
    //         });
        
    //         var breadcrumbs = "";
    //         $.each(path,function(index,value){
    //             if(breadcrumbs == ''){
    //                 breadcrumbs = value;
    //             }else{
    //                 breadcrumbs = breadcrumbs+'/'+value;
    //             }
                
    //         });
    //         console.log(breadcrumbs)
    //         $('.picked_files').append("<div class='file_item_container'><div class='addclass'></div><div class='file_pick' data-is-folder="+type_folder+" data-is-file="+type_file+" data-id="+fileId+">"+fileName+"</div><div class='nav_file_item'>"+breadcrumbs+"</div><div class='file_del'>X</div></div>");
    //         $('.picked_files .addclass').addClass(icon);
    //         // if($('.file_item_container').length > 0){
    //         //     $('#popup .button_add_fele_on_page').css('display', 'block');
    //         // }else{
    //         //     $('#popup .button_add_fele_on_page').css('display', 'none');
    //         // }
    //     }
       
    // });
    // $(document).on('click', '#popup .file_del', function(){
    //     $(this).parent().remove();
    //     // if($('.file_item_container').length > 0){
    //     //     $('#popup .button_add_fele_on_page').css('display', 'block');
    //     // }else{
    //     //     $('#popup .button_add_fele_on_page').css('display', 'none');
    //     // }
    // });
    // $(document).on('click', '#popup #popupclose', function(){
    //     if(files_url != window.location.href){
    //         window.location.href = files_url;
    //     }
    // });
    // $(document).on('click', '#popup .button_add_fele_on_page', function(){
    //     var fileId = new Array();
    //     var filePath = new Array();
    //     var folderId = new Array();
    //     var isFile = new Array();
    //     var isFolder = new Array();
    //     var folderPath = new Array();
    //     var page = $('.file_add_popup').data('page');
    //     var iblock = $('.file_add_popup').data('iblock');
    //     console.log('111')
    //     $('.file_item_container').each(function(){
            
            
    //         isFile.push($(this).find('.file_pick').data('is-file'));
    //         isFolder.push($(this).find('.file_pick').data('is-folder'));
    //     //   $(this).find('.file_pick').data('id');
    //         if($(this).find('.file_pick').data('is-file') == true){
    //             fileId.push($(this).find('.file_pick').data('id'));
    //             filePath.push($(this).find('.nav_file_item').text());
    //         }
    
    //         if($(this).find('.file_pick').data('is-folder') == true){
    //             folderId.push($(this).find('.file_pick').data('id'));
    //             folderPath.push($(this).find('.nav_file_item').text());
    //         }
    //     console.log('data-is-file='+$(this).find('.file_pick').data('is-file'));
    //     });
    //     console.log(fileId);
    //     $.ajax({
    //         type: 'POST',
    //         url: '/ajax_page_file_add.php',
    //         data: {fileId: fileId, page: page, iblock: iblock, filePath: filePath, folderId: folderId, folderPath: folderPath},
    //         success: function(data) {
    //             $('.button_add_fele_on_page').remove();
    //           $('.picked_files').text('Файлы успешно добавлены!');
    //         //   $('.picked_files').text(data);
    //           setTimeout(window.location.href = files_url, 1000);
    
    //         },
    //         error:  function(xhr, str){
    //       alert('Возникла ошибка: ' + xhr.responseCode);
    //         }
    //       });
    // });
    // $('.two_tier__list>li').on('click', function(event) {
    //     let item = $(event.target);
    //     console.log(item)
    //     if(item.hasClass('active')){
    //         item.removeClass('active');
    //     }else{
    //         item.addClass('active');
    //     }
    // });
    // let datepicker = $('input[name="datepicker"]');
    // var search = document.location.search;
    // var searchParams = new URLSearchParams(search);
    // if(searchParams.has('DATESTART')){
    //     var dateStart = searchParams.get("DATESTART");
    // }
    // if(searchParams.has('DATESTART')){
    //     var dateEnd = searchParams.get("DATEEND");
    // }
    // datepicker.daterangepicker(
    //     {   
    //         startDate: dateStart,
    //         endDate: dateEnd,
    //         autoUpdateInput: false,
    //         showDropdowns: true,
    //         locale: {
    //             cancelClass: "cansel_custom_btn",
    //             cancelLabel: 'Очистить',
    //             applyLabel: 'Применить',
    //             separator : " -",
    //             daysOfWeek : [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    //             monthNames : ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь" ],
    //             firstDay : 1
    //         }
    //     }
    // );
    
    // datepicker.on('apply.daterangepicker', function(ev, picker) {
    //     const opt = {month: 'long'};
    //     let dateEnd = new Date(picker.endDate);
    //     $(this).val('C ' + picker.startDate.format('DD') + ' по ' + picker.endDate.format('DD') + ' ' + dateEnd.toLocaleDateString('ru-RU', opt));
    // });
    
    // datepicker.on('cancel.daterangepicker', function(ev, picker) {
    //     datepicker.val('');
    //     var protocol = document.location.protocol;
    //     var host = document.location.host;
    //     var pathname = document.location.pathname;
    //     var docurl = protocol+host+pathname;
    //     var url = document.location.href;
    //     var search = document.location.search;
    //     if(pathname == '/documents/'){
    //         if(searchParams.has("FILE_TYPE") || searchParams.has("FOLDER_ID")){
    //             window.location.href = '?FOLDER_ID='+ searchParams.get("FOLDER_ID")+'&FILE_TYPE='+searchParams.get("FILE_TYPE")+'&year=all';
    //         }else{
    //             window.location.href = '/documents/';
    //         }
    //     }else if (search != "") {
    //         var searchParams = new URLSearchParams(search);
    //         if(searchParams.has("ID")){
    //             window.location.href = '?ID='+ searchParams.get("ID")+'&year=all';
    //         }else{
    //             window.location.href = '/events/';
    //         }
    //     }else{
    //         window.location.href = '/events/';
    //     }
    
    // });
    // $('.applyBtn').on('click', function(){
    
    //     var url = document.location.href;
    //     var search = document.location.search;
    //     var input_val = $(this).parent().find('.drp-selected').text();
    //     var dateStart = (input_val.split('-')[0]).replace(" ",""); // 50ml
    //     var dateEnd = (input_val.split('-')[1]).replace(" ",""); // $100
    
    //     if (search != "") {
    //         var searchParams = new URLSearchParams(search);
    //         if(searchParams.has("ID")){
    //             window.location.href = '?ID='+ searchParams.get("ID")+'&year=all'+ '&DATESTART='+ dateStart + '&DATEEND='+dateEnd;
    //         }else{
    //             window.location.href = '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
    //         }
            
    //      }else{
            
    //         console.log(dateStart)
    //         console.log(dateEnd)
    //         window.location.href = document.location.href + '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
    //      }
       
    
    // });
    
        // let owl = $(".carousel__top.owl-carousel");
        // owl.owlCarousel({
        //     items: 1,
        //     loop: true,
        //     center: true,
        //     nav: true,
        //     dots: false,
        //     margin: 10,
        //     URLhashListener: true,
        //     autoplayHoverPause: true,
        //     startPosition: 'URLHash',
        //     autoHeight: false,
        //     autoHeightClass: 'owl-height',
        //     navText: ["<img src='/local/templates/avtodor/img/icon/ArrowPrev.svg' />", "<img src='/local/templates/avtodor/img/icon/ArrowNext.svg' />"]
        // });
    
        // owl.on('changed.owl.carousel', function (event) {
        //     let currentItem = event.item.index;
        //     let description = $(event.target).find(".owl-item").eq(currentItem).find("div").data("description")
        //     $('#carouselImgDescription').text(description);
        // });
    
        // let owlMob = $(".carousel__box");
        // owlMob.owlCarousel({
        //     items: 1.18,
        //     loop: true,
        //     center: false,
        //     nav: false,
        //     dots: false,
        //     margin: 20,
        // });
    
        // owlMob.on('changed.owl.carousel', function (event) {
        //     let currentItem = event.item.index;
        //     let description = $(event.target).find(".owl-item").eq(currentItem).find("div").data("description")
        //     $('#carouselImgDescriptionMob').text(description);
        // });
    
        $(".table__menu__fon:not('.info__btn__menu')").on('click', function(e) {
            $('.info__btn__menu, .table__info').removeClass('active');
            $('.table__menu__fon').css({height: '100%'});
            e.preventDefault();
        });
    
        $('.table__info__btn').on('click', function() {
            let menu = $(this).parent().children('.info__btn__menu');
            if(menu.hasClass('active')){
                menu.removeClass('active');
                $('.table__info').removeClass('active');
            }else{
                $('.info__btn__menu').removeClass('active');
                menu.addClass('active');
                $('.table__info').addClass('active');
                let heightBody = $('body').height();
                $('.table__menu__fon').css({height: heightBody});
            }
        });
    
        $('.choice__block .checkmark').on('click', function() {
            $(this).parent().children('input').prop('checked', true);
        });
    
        $('.show__more.bdotted').on('click', function () {
            let btn = $(this);
            if (btn.hasClass('active')) {
                btn.removeClass('active');
                $('.employee__card__item__option ul').css('max-height', '200px');
            } else {
                btn.addClass('active');
                $('.employee__card__item__option ul').css('max-height', '600px');
            }
        });
    
        $('.btn__panel.like, .cours__card__favorite__icon').on('click', function () {
            let item = $(this);
            if(item.hasClass('active')){
                item.removeClass('active');
            }else{
                item.addClass('active');
            }
        });
    
        $('.search__catalog').on('click', '.btn.search', function () {
            let item = $(this);
            let otherBtnNav = $('.filter, .tab.btn, .datepicker');
            if(item.parent().hasClass('active')){
                otherBtnNav.removeClass('hidden');
                item.parent().removeClass('active');
            }else{
                otherBtnNav.addClass('hidden');
                item.parent().addClass('active');
            }
        });
    
        $('.two_tier__list>li').on('click', function(event) {
            let item = $(event.target);
            console.log(item)
            if(item.hasClass('active')){
                item.removeClass('active');
            }else{
                item.addClass('active');
            }
        });
        // if($('.third').hasClass('active')){
            $('.third.active').parents('li.active').parents('li').addClass('active'); 
        // }
            $('li.second_lvl.active').parents('li').addClass('active');
        
        
        $('.employees__item__info__congratulate').on('click', function(){
    
            var date = $(this).parent().find('.employees__item__info__date').text();
            var name = $(this).parent().find('.employees__item__info__fio').text();
            var position = $(this).parent().find('.employees__item__info__job').text();
            var email = $(this).parent().find('.employees__item__info__email').text();
            var img = $(this).parents('.employees__item').find('.employees__item__info__img').attr('src');
    
            $('#popUp .modal-body .employees__item__img').attr('src', img);
            $('#popUp .modal-body .employees__item__info__date').text(date);
            $('#popUp .modal-body .employees__item__info__fio').text(name);
            $('#popUp .modal-body .employees__item__info__job').text(position);
            $('#popUp .modal-footer .comment__form__eamil').text(email);
            console.log(img)
    
    
        });
        $('#popUp .comment__form').on('click', '.btn.submit', function() {
            
            var email = $('#popUp .modal-footer .comment__form__eamil').text();
            var msg = $('#popUp .modal-footer .comment__form__textarea').val();
            $('#popUp .comment__form input[name="email"]').val(email);
            $('#popUp .comment__form textarea[name="msg"]').val(msg);
            // console.log($('#popUp .comment__form input[name="email"]').val());
            // console.log($('#popUp .comment__form textarea[name="msg"]').val());
            $.ajax({
                method: "POST",
                url: "ajax_birthdays.php",
                data: { email: email, msg: msg  }
            })
            .done(function( msg ) {
                console.log(msg)
                    $('#popUp .comment__form').addClass('disable__block');
                    $('#popUp .notification__form').removeClass('disable__block');
            });
        });
    
        $('#popUp .notification__form').on('click', '.btn.submit', function() {
    
    
    
            $(this).parent().addClass('disable__block');
            $('#popUp .comment__form').removeClass('disable__block');
            $('#popUp').modal('hide');
        });
    
        $('.searchbox__btn').on('click', function () {
            $('.searchbox__input input, .searchbox__btn').removeClass('active');
            let index = $(this).index();
            $('.searchbox__input input:eq(' + index + '), .searchbox__btn:eq(' + index + ')').addClass('active');
            if($('.searchbox__input input:eq(' + index + ')').hasClass('structure-search-input')){
                $('.structure-search-input').parent().removeClass('hidden');
            }else{
                $('.structure-search-input').parent().addClass('hidden');
            }
            // if($('.searchbox__btn:eq(' + index + ')').hasClass('active')){
            //     $('.searchbox__input input:eq(' + index + ')').parents('.header-search').css('display','none');
            //     console.log('123')
            // }else{
            //     console.log('456')
            //     $('.searchbox__input input:eq(' + index + ')').parents('.header-search').css('display','flex');
            // }
        });
        if($('.col-3 .favorites__item__block').length > 4){
            $('.favorites__more').css('display', 'flex');
        }else{
            $('.favorites__more').css('display', 'none');
        }
        $('.favorites__more').on('click', function () {
            let btn = $(this);
            if (btn.hasClass('active')) {
                btn.removeClass('active');
                $('.favorites__items').css('max-height', '135px');
            } else {
                btn.addClass('active');
                $('.favorites__items').css('max-height', '500px');
            }
        });
    
        $('.block__title .tab li').on('click', function () {
            $('.event__items, .block__title .tab li').removeClass('active');
            let index = $(this).index();
            $('.event__items:eq(' + index + '), .block__title .tab li:eq(' + index + ')').addClass('active');
        });
    
        $('.filter__block .tab li').on('click', function () {
            $('.surveys__block, .filter__block .tab li').removeClass('active');
            let index = $(this).index();
            $('.surveys__block:eq(' + index + '), .filter__block .tab li:eq(' + index + ')').addClass('active');
        });
    
        $('.widget__birthday__date__block .tab li').on('click', function () {
            $('.widget__birthday__date__items, .widget__birthday__date__block .tab li').removeClass('active');
            let index = $(this).index();
            $('.widget__birthday__date__items:eq(' + index + '), .widget__birthday__date__block .tab li:eq(' + index + ')').addClass('active');
        });
        $('.header__group_btn a').on('click', function () {
            let btn = $(this);
            if (btn.hasClass('active')) {
                btn.removeClass('active');
                $('.menu:eq(' + btn.index() + ')').removeClass('active');
                $('body').css({
                    height: 'auto',
                    overflow: 'unset'
                });
            } else {
                $('.menu, .header__group_btn a').removeClass('active');
                btn.addClass('active');
                $('.menu:eq(' + btn.index() + ')').addClass('active');
                $('body').css({
                    height: '100vh',
                    overflow: 'hidden'
                });
            }
        });
    
        $('.menu__group__btn a').on('click', function () {
            let btn = $(this);
            let index = btn.index();
            $('.menu__group__btn a, .search__box, .menu .searchbox__input input').removeClass('active');
            btn.addClass('active');
            $('.menu:eq(0) .searchbox__input input:eq(' + index + '), .search__box:eq(' + index + ')').addClass('active');
    
        });
    
        $('.accordion__item').on('click', function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                $(this).children('.accordion__item__description').css({ height : 0 })
            } else {
                $('.accordion__item').removeClass('active');
                $(this).addClass('active');
                let heightDesc = $(this).children('.accordion__item__description').children('.description__block').height();
                $(this).children('.accordion__item__description').css({ height : heightDesc + "px" });
            }
        });
    
        let datepicker = $('input[name="datepicker"]');
        var search = document.location.search;
        var searchParams = new URLSearchParams(search);
        if(searchParams.has('DATESTART')){
            var dateStart = searchParams.get("DATESTART");
        }
        if(searchParams.has('DATESTART')){
            var dateEnd = searchParams.get("DATEEND");
        }
        datepicker.daterangepicker(
            {   
                startDate: dateStart,
                endDate: dateEnd,
                autoUpdateInput: false,
                showDropdowns: true,
                locale: {
                    cancelClass: "cansel_custom_btn",
                    cancelLabel: 'Очистить',
                    applyLabel: 'Применить',
                    separator : " -",
                    daysOfWeek : [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                    monthNames : ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь" ],
                    firstDay : 1
                }
            }
        );
    
        datepicker.on('apply.daterangepicker', function(ev, picker) {
            const opt = {month: 'long'};
            let dateEnd = new Date(picker.endDate);
            $(this).val('C ' + picker.startDate.format('DD') + ' по ' + picker.endDate.format('DD') + ' ' + dateEnd.toLocaleDateString('ru-RU', opt));
        });
    
        datepicker.on('cancel.daterangepicker', function(ev, picker) {
            datepicker.val('');
            var protocol = document.location.protocol;
            var host = document.location.host;
            var pathname = document.location.pathname;
            var docurl = protocol+host+pathname;
            var url = document.location.href;
            var search = document.location.search;
            if(pathname == '/documents/'){
                if(searchParams.has("FILE_TYPE") || searchParams.has("FOLDER_ID")){
                    window.location.href = '?FOLDER_ID='+ searchParams.get("FOLDER_ID")+'&FILE_TYPE='+searchParams.get("FILE_TYPE")+'&year=all';
                }else{
                    window.location.href = '/documents/';
                }
            }else if (search != "") {
                var searchParams = new URLSearchParams(search);
                if(searchParams.has("ID")){
                    window.location.href = '?ID='+ searchParams.get("ID")+'&year=all';
                }else{
                    window.location.href = '/events/';
                }
            }else{
                window.location.href = '/events/';
            }
    
        });
        $('.applyBtn').on('click', function(){
    
            var url = document.location.href;
            var search = document.location.search;
            var input_val = $(this).parent().find('.drp-selected').text();
            var dateStart = (input_val.split('-')[0]).replace(" ",""); // 50ml
            var dateEnd = (input_val.split('-')[1]).replace(" ",""); // $100
    
            if (search != "") {
                var searchParams = new URLSearchParams(search);
                if(searchParams.has("ID")){
                    window.location.href = '?ID='+ searchParams.get("ID")+'&year=all'+ '&DATESTART='+ dateStart + '&DATEEND='+dateEnd;
                }else{
                    window.location.href = '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
                }
                
             }else{
                
                console.log(dateStart)
                console.log(dateEnd)
                window.location.href = document.location.href + '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
             }
           
    
        });
        $('.filter__block .filter select').on('change', function(){
    
            var val = $(this).val();
            var url = document.location.href;
            var search = document.location.search;
            if (search != "") {
                var searchParams = new URLSearchParams(search);
                if(searchParams.has("FOLDER_ID")){
                    window.location.href = '?FOLDER_ID='+ searchParams.get("FOLDER_ID")+'&FILE_TYPE='+val;
                }else  if(searchParams.has("DATESTART")){
                    window.location.href = '?DATESTART='+searchParams.get("DATESTART")+'&DATEEND='+searchParams.get("DATEEND")+'&FILE_TYPE='+val;
                }
                else{
                    window.location.href = '?FILE_TYPE='+ val;
                }
            }else{
                window.location.href = '?FILE_TYPE='+ val;
            }
            console.log(val)
    
        });
        // $('#popupcontent .bx-disk-interface-filelist .main-grid .main-grid-row-body .main-grid-cell-checkbox').on('click', function(){
        //     console.log($(this).val());
        // });
        $('.page__catalog__documents .search__catalog input, .header__bar #searchDocument').keydown(function(e) {
            if(e.keyCode === 13) {
                window.location.href = "/new-search/?q="+$(this).val()+"&where=disk";
              
            }
          });
          $('.widget__block__items .file_types').on('click', function(e){
            // e.preventDefault();
            var type = $(this).data('type');
            var url = document.location.href;
            var search = document.location.search;
    
            if (search != "") {
                var searchParams = new URLSearchParams(search);
                if(type == 'web'){
                    if(searchParams.has("file_type")){
                        window.location.href = '?q='+ searchParams.get("q")+'&where=iblock_content'+'&how='+ searchParams.get("how");
                    }else{
                        window.location.href = '?q='+ searchParams.get("q")+'&where=iblock_content' +'&how='+ searchParams.get("how");
                    }
                    
                }else if(type == 'all'){
                    console.log('123')
                        window.location.href = '?q='+ searchParams.get("q")+'&where=&how='+ searchParams.get("how");
                    
                    
                }
                // else if(searchParams.has("file_type")){
                //     window.location.href = '?q='+ searchParams.get("q")+'&where=disk&file_type='+type;
                // }else if(searchParams.has("how")){
                //     window.location.href = '?q='+ searchParams.get("q")+'&where=disk&file_type='+type+'&how='+ searchParams.get("how");
                // }
                // else{
                //     window.location.href = '?q='+ searchParams.get("q")+'&where=disk&file_type='+type;
                // }
            // }else{
            //     window.location.href = '?q='+ searchParams.get("q")+'&where='+searchParams.get("where")+"&file_type="+type;
            // }
          }
        });
    
    })
    if($('.two_tier__list__col li').hasClass('active')){
        $(this).parent().addClass('active');
    }
    var files_url = window.location.href;
    $(document).on('click', '#popup .bx-disk-interface-filelist .main-grid .main-grid-row-body .main-grid-cell-checkbox', function(){
        if($(this).parents('tr').hasClass('main-grid-row-checked')){
            var fileId = $(this).find('input').val();
            var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
            var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
            var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
            var type_file = $(this).parents('.main-grid-row-body').data('is-file');
            console.log(fileId)
            console.log($('.file_pick[data-id='+fileId+']').text());
            $('.file_pick[data-id='+fileId+']').parents('.file_item_container').remove();
    
            
        }else{
            var fileId = $(this).find('input').val();
            var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
            var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
            var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
            var type_file = $(this).parents('.main-grid-row-body').data('is-file');
            var path = Array();
            $('.js-disk-breadcrumbs-folder').each(function(){
                path.push($(this).data('object-name'));
                // path = $(this).data('object-name');
            });
        
            var breadcrumbs = "";
            $.each(path,function(index,value){
                if(breadcrumbs == ''){
                    breadcrumbs = value;
                }else{
                    breadcrumbs = breadcrumbs+'/'+value;
                }
                
            });
            console.log(breadcrumbs)
            $('.picked_files').append("<div class='file_item_container'><div class='addclass'></div><div class='file_pick' data-is-folder="+type_folder+" data-is-file="+type_file+" data-id="+fileId+">"+fileName+"</div><div class='nav_file_item'>"+breadcrumbs+"</div><div class='file_del'>X</div></div>");
            $('.picked_files .addclass').addClass(icon);
            // if($('.file_item_container').length > 0){
            //     $('#popup .button_add_fele_on_page').css('display', 'block');
            // }else{
            //     $('#popup .button_add_fele_on_page').css('display', 'none');
            // }
        }
       
    });
    $(document).on('click', '#popup .file_del', function(){
        $(this).parent().remove();
        // if($('.file_item_container').length > 0){
        //     $('#popup .button_add_fele_on_page').css('display', 'block');
        // }else{
        //     $('#popup .button_add_fele_on_page').css('display', 'none');
        // }
    });
    $(document).on('click', '#popup #popupclose', function(){
        if(files_url != window.location.href){
            window.location.href = files_url;
        }
    });
    $(document).on('click', '#popup .button_add_fele_on_page', function(){
        var fileId = new Array();
        var filePath = new Array();
        var folderId = new Array();
        var isFile = new Array();
        var isFolder = new Array();
        var folderPath = new Array();
        var page = $('.file_add_popup').data('page');
        var iblock = $('.file_add_popup').data('iblock');
        console.log('111')
        $('.file_item_container').each(function(){
            
            
            isFile.push($(this).find('.file_pick').data('is-file'));
            isFolder.push($(this).find('.file_pick').data('is-folder'));
        //   $(this).find('.file_pick').data('id');
            if($(this).find('.file_pick').data('is-file') == true){
                fileId.push($(this).find('.file_pick').data('id'));
                filePath.push($(this).find('.nav_file_item').text());
            }
    
            if($(this).find('.file_pick').data('is-folder') == true){
                folderId.push($(this).find('.file_pick').data('id'));
                folderPath.push($(this).find('.nav_file_item').text());
            }
        console.log('data-is-file='+$(this).find('.file_pick').data('is-file'));
        });
        console.log(fileId);
        $.ajax({
            type: 'POST',
            url: '/ajax_page_file_add.php',
            data: {fileId: fileId, page: page, iblock: iblock, filePath: filePath, folderId: folderId, folderPath: folderPath},
            success: function(data) {
                $('.button_add_fele_on_page').remove();
              $('.picked_files').text('Файлы успешно добавлены!');
            //   $('.picked_files').text(data);
              setTimeout(window.location.href = files_url, 1000);
    
            },
            error:  function(xhr, str){
          alert('Возникла ошибка: ' + xhr.responseCode);
            }
          });
    });
    $(document).ready(function(){
        $('.depart__card .plus-open, .dep_card .plus-open').on('click', function(){
            var conteainer = $(this).parents('.card_container').find('.card_more');
            conteainer.slideToggle();
            $(this).toggleClass('active');
        });
        $('.lk_edit_btn').on('click', function(){
            
            var form = $(this).parents('.employee__card__item').find('.lk_edit_form');
            var string = $(this).parents('.employee__card__item').find('.edit_group');
            form.toggleClass('active');
            string.toggleClass('hidden');
     
         });
         $('.lk_close_btn').on('click', function(){
             
             var form = $(this).parents('.employee__card__item').find('.lk_edit_form');
             var string = $(this).parents('.employee__card__item').find('.edit_group');
             form.toggleClass('active');
             string.toggleClass('hidden');
      
          });
          
          $('.lk_apply_btn.phone').on('click', function(){
             
             var form = $(this).parents('.employee__card__item').find('.lk_edit_form');
             var string = $(this).parents('.employee__card__item').find('.edit_group');
             var edit = $(this).parents('.employee__card__item').find('.lk_edit_string');
             var val = $(this).parent().find('input').val();
             
             $.ajax({
                 type: 'POST',
                 url: 'lk-update.php',
                 data: {PHONE: val},
                 success: function(data) {
                 edit.text(val);
                 form.hide();
                 },
                 error:  function(xhr, str){
             //   alert('Возникла ошибка: ' + xhr.responseCode);
                 }
                 
               });
               form.toggleClass('active');
             string.toggleClass('hidden');
          });
          $('.lk_apply_btn.telegram').on('click', function(){
             
             var form = $(this).parents('.employee__card__item').find('.lk_edit_form');
             var string = $(this).parents('.employee__card__item').find('.edit_group');
             var edit = $(this).parents('.employee__card__item').find('.lk_edit_string');
             var val = $(this).parent().find('input').val();
             $.ajax({
                 type: 'POST',
                 url: 'lk-update.php',
                 data: {TELEGRAM: val},
                 success: function(data) {
                 edit.text(val);
                 },
                 error:  function(xhr, str){
             //   alert('Возникла ошибка: ' + xhr.responseCode);
                 }
                 
               });
               form.toggleClass('active');
             string.toggleClass('hidden');
          });
          $(document).on('click', '.add_skills', function(){
             $(this).parents('.skills_list').css({"maxHeight":"100%"});
             // $(this).parents('.skills_list').css('display', 'none');
             $('<form class="skills_form"><div class="skills_group"><input name="SKILL" type="text" placeholder=""></div><button  class="btn add_skills_list">Сохранить</button><button type="button" class="btn close_skills_add" >Отмена</button></form>').prependTo('.skills_list');
             $(this).removeClass('add_skills');
             $(this).addClass('add_another_skills');
             $(this).text('Добавить ещё навык');
             
          });
          $(document).on('click', '.close_skills_add', function(){
                 
                 
                     $(this).parents('.skills_list').find('div').removeClass('add_another_skills');
                     $(this).parents('.skills_list').find('div').addClass('add_skills');
                     $(this).parents('.skills_list').find('div').text('Добавить навыки');
                 
                 $('.skills_form').remove();
          });
          var q = 0;
          $(document).on('click', '.add_another_skills', function(){
             q++;
             $('<input name="SKILL'+q+'" type="text" placeholder="">').appendTo('.skills_group');
     
          });
          $(document).on('click', '.skills_form .add_skills_list', function(e){
             e.preventDefault();
             var data = Array();
             var edit = $('.skills_list');
             $('.skills_form input').each(function(){
                 data.push($(this).val());
             });
             console.log(data);
             $.ajax({
                 type: 'POST',
                 url: 'lk-update.php',
                 data: {SKILLS: data},
                 success: function(data) {
                 // edit.text(val);
                 location.reload();
                 // edit.text(data);
     
                 },
                 error:  function(xhr, str){
             //   alert('Возникла ошибка: ' + xhr.responseCode);
                 }
                 
               });
     
     
          });
          $('.lk_edit_btn_skills').on('click',function(){
             $('.not-empty form').css('display', 'block');
             $('.not-empty .add_another_skills').css('display', 'block');
             $('.not-empty li').css('display', 'none');
             $('.not-empty').css({"maxHeight":"100%"});
          });
          $(document).on('click', '.lk_del_skill_btn', function(){
     
             $(this).prev().remove();
             $(this).remove();
     
          });
          $('.lk_apply_btn.skills').on('click', function(){
             
             var form = $(this).parents('.employee__card__item').find('.lk_edit_form');
             var string = $(this).parents('.employee__card__item').find('.edit_group');
             var edit = $(this).parents('.employee__card__item').find('.lk_edit_string');
             var val = $(this).parent().find('textarea').val();
             $.ajax({
                 type: 'POST',
                 url: 'lk-update.php',
                 data: {SKILLS: val},
                 success: function(data) {
                 edit.text(val);
                 },
                 error:  function(xhr, str){
             //   alert('Возникла ошибка: ' + xhr.responseCode);
                 }
                 
               });
               form.toggleClass('active');
             string.toggleClass('hidden');
          });
          $(document).on('click','.popup_create_links_close, .popup_update_links_close, .popup_creat_links .close, .popup_update_links .close', function(){
        //   $('.popup_update_links_close, .popup_create_links_close').on('.click', function(){
            console.log('dsa')
             $('.popup_layout_creat').removeClass('active');
             $('.popup_layout_update').removeClass('active');
             $('.popup_update_links').removeClass('active');
             $('.popup_creat_links').removeClass('active');
          });
          $('.widget__lk__favorites .add__link').on('click', function(){
             if($('.popup_create_links').hasClass('active')){
                 $('.popup_layout_creat').removeClass('active');
                 $('.popup_creat_links').removeClass('active');
              }else{
                  $('.popup_layout_creat').addClass('active');
                  $('.popup_creat_links').addClass('active');
              }
                 // console.log('dsadas')
                 // $('<form class="link-create-form"><input name="NAME" type="text" placeholder="Имя ссылки"><input type="text" name="LINK" placeholder="Ссылка"><input type="text" name="SORT" placeholder="Сортировка"><input type="button" class="btn create" value="Сохранить"><input type="button" class="btn close" value="Отмена"></form>').prependTo('.favorites__items');
             
             });
             $(document).on('click','.widget__lk__favorites .close', function(){
                 $(this).parents('.favorites__item__block').removeClass('active_edit');
                 $(this).parent().remove();
                 
     
             });
             $(document).on('click','.link-create-form .create', function(e){
                 e.preventDefault();
                 var errors = 0;
    
                 if($(this).parent().find('input[name="NAME"]').val() == ''){
                    console.log('NAME----'+$(this).parent().find('input[name="NAME"]'));
                    $(this).parent().find('input[name="NAME"]').css('border', '2px solid red');
                    if($('.error_name_link').length > 0){
                    
                    }else{
                        $('.input_name').before('<label for="NAME" class="error_name_link" style="color:red;">Заполните название ссылки</label>');
                    }
                    errors++;
                 }else{
                    $(this).parent().find('input[name="NAME"]').css('border', '2px solid #E0E0E0');
                    $('.error_name_link').remove();
                 }
    
                 if($(this).parent().find('input[name="LINK"]').val() == ''){
                    console.log('LINK----'+$(this).parent().find('input[name="LINK"]'));
                    $(this).parent().find('input[name="LINK"]').css('border', '2px solid red');
                    if($('.error_link').length > 0){
    
                    }else{
                        $('.input_link').before('<label for="LINK" class="error_link" style="color:red;">Добавьте ссылку</label>');
                    }
                    
                    
                    errors++;
                 }else{
                    $(this).parent().find('input[name="LINK"]').css('border', '2px solid #E0E0E0');
                    $('.error_link').remove();
                 }
                 console.log(errors)
                 if(errors == 0){
                    // console.log('3222')
                    var block = $(this).parent();
                   var data = $(this).parent().serialize();
                    $.ajax({
                        type: 'POST',
                        url: '/create_link.php',
                        data: data,
                        success: function(data) {
                        //     var obj = jQuery.parseJSON( data );
                        // $('<div class="favorites__item__block"><a class="favorites__item" href="'+obj.link+'">'+obj.name+'</a><span data-id="'+obj.id+'"></span></div>').prependTo('.favorites__items');
                            // $('.link-create-form').remove();
                            // block.toggleClass('active_edit');
                            $('.popup_creat_links').html('<div class="link_create_success">Ссылка успешно добавлена</div>');
                            // $('.popup_creat_links').html('<div class="link_create_success">'+data+'</div>');
                            location.reload();
        
                        },
                        error:  function(xhr, str){
                        }
                        
                      });
                errors = 0;
                 }else{
                    // console.log('error')
                 }
                
     
             });
             $('.favorites__item__block span').on('click', function(){
                 var id = $(this).data('id');
                 var block = $(this).parent();
                 $.ajax({
                     type: 'POST',
                     url: '/del_user_link.php',
                     data: {ID: id},
                     success: function(data) {
                         block.remove();
                     },
                     error:  function(xhr, str){
                     }
                     
                   });
             });
             $(document).on('click', '.link_edit_btn', function(){
                 if($('.popup_update_links').hasClass('active')){
                    $('.popup_layout_update').removeClass('active');
                    $('.popup_update_links').removeClass('active');
                 }else{
                     var name = $(this).prev().text();
                     var link = $(this).prev().attr('href');
                     var sort = $(this).prev().data('sort');
                     var item_id = $(this).next().data('id');
                     $('.popup_layout_update').addClass('active');
                     $('.popup_update_links').addClass('active');
                     $('.popup_update_links input[name="NAME"]').val(name);
                     $('.popup_update_links input[name="LINK"]').val(link);
                     $('.popup_update_links input[name="SORT"]').val(sort);
                     $('.popup_update_links input[name="ID"]').val(item_id);
     
     
                 }
                 // $('.popup_layout').css('display', 'block');
                 // var block = $(this).parent();
                 // var link = block.find('.favorites__item').attr('href');
                 // var name_link = block.find('.favorites__item').text();
                 // var sort = block.find('.favorites__item').data('sort');
                 // block.toggleClass('active_edit');
                 // $('<form class="link-create-form"><input name="NAME" type="text" value="'+name_link+'" placeholder="Имя ссылки"><input value="'+link+'" type="text" name="LINK" placeholder="Ссылка"><input type="text" name="SORT" value="'+sort+'" placeholder="Сортировка"><input type="button" class="btn update" value="Сохранить"><input type="button" class="btn close" value="Отмена"></form>').appendTo(block);
             });
             $(document).on('click', '.popup_update_links', function(){
                 if($('.popup_creat_links').hasClass('active')){
                    $('.popup_layout').removeClass('active');
                    $('.popup_creat_links').removeClass('active');
                 }else{
                     $('.popup_layout').addClass('active');
                     $('.popup_creat_links').addClass('active');
                 }
             });
             $(document).on('click', '.link-create-form .update', function(){
                var errors = 0;
    
                if($(this).parent().find('input[name="NAME"]').val() == ''){
                   console.log('NAME----'+$(this).parent().find('input[name="NAME"]'));
                   $(this).parent().find('input[name="NAME"]').css('border', '2px solid red');
                   if($('.error_name_link').length > 0){
                   
                   }else{
                       $('.input_name').before('<label for="NAME" class="error_name_link" style="color:red;">Заполните название ссылки</label>');
                   }
                   errors++;
                }else{
                   $(this).parent().find('input[name="NAME"]').css('border', '2px solid #E0E0E0');
                   $('.error_name_link').remove();
                }
    
                if($(this).parent().find('input[name="LINK"]').val() == ''){
                   console.log('LINK----'+$(this).parent().find('input[name="LINK"]'));
                   $(this).parent().find('input[name="LINK"]').css('border', '2px solid red');
                   if($('.error_link').length > 0){
    
                   }else{
                       $('.input_link').before('<label for="LINK" class="error_link" style="color:red;">Добавьте ссылку</label>');
                   }
                   
                   
                   errors++;
                }else{
                   $(this).parent().find('input[name="LINK"]').css('border', '2px solid #E0E0E0');
                   $('.error_link').remove();
                }
                console.log(errors)
                if(errors == 0){
                 var name_link = $('.popup_update_links input[name="NAME"]').val();
                     var link = $('.popup_update_links input[name="LINK"]').val();
                     var sort = $('.popup_update_links input[name="SORT"]').val();
                     var id = $('.popup_update_links input[name="ID"]').val();
    
                 $.ajax({
                     type: 'POST',
                     url: '/update_user_link.php',
                     data: {ID:id, LINK:link, NAME:name_link, SORT: sort },
                     success: function(data) {
                         // block.find('.favorites__item').text(name_link);
                         // block.find('.favorites__item').attr('href', link);
                         // block.find('.favorites__item').data('sort', sort);
                         // $('.link-create-form').remove();
                         // block.removeClass('active_edit');
                         // $('.popup_update_links').html('<div class="link_update_success">'+data+'</div>');
                         $('.popup_update_links').html('<div class="link_create_success">Ссылка успешно обновленна</div>');
                         location.reload();
                     },
                     error:  function(xhr, str){
                     }
                     
                   });
                }else{
                    // console.log('error')
                }
             });
            //  $('.employee__card__title').on('.click', function(){
            //     console.log('qqq')
            //  });
            //  $('.add_phone').on('.click', function(){
            //     console.log('qqq')
            //     $(this).hide();
            //     $(this).next().show();
        
            //   });
              $(document).on('click', '.add_phone', function(){
                $(this).hide();
                $(this).next().show();
              });
              $('.page__block__text table').wrapAll('<div style="width:100%; overflow:auto;">');
            });
            $(document).ready(function(){
    
                $(document).on('click', '.load-more-items', function(){
            
                    var targetContainer = $('.press__row'),
                        url =  $('.load-more-items').attr('data-url');
            
                    if (url !== undefined) {
                        $.ajax({
                            type: 'GET',
                            url: url,
                            dataType: 'html',
                            success: function(data){
            
                                $('.load-more-items').remove();
            
                                var elements = $(data).find('.press__news'),
                                    pagination = $(data).find('.load-more-items');
            
                                targetContainer.append(elements);
                               $('#pag').append(pagination);
            
                            }
                        });
                    }
            
                });
    
                var structureMore = {
                    init: function() {
                      $('.structure__subjects').each(function() {
                        if($(this).find('> .structure__subject').length == 3) {
                          $(this).addClass('structure__subjects--3');
                        }
                        if($(this).find('> .structure__subject').length == 2) {
                          $(this).addClass('structure__subjects--2');
                        }
                        if($(this).find('> .structure__subject').length == 1) {
                          $(this).addClass('structure__subjects--1');
                        }
                      });
                      $('.structure__sub-subjects').each(function() {
                        if($(this).find('> .structure__subject').length == 3) {
                          $(this).addClass('structure__sub-subjects--3');
                        }
                        if($(this).find('> .structure__subject').length == 2) {
                          $(this).addClass('structure__sub-subjects--2');
                        }
                        if($(this).find('> .structure__subject').length == 1) {
                          $(this).addClass('structure__sub-subjects--1');
                        }
                      });
                      function setCardLeft() {
                        $('.structure__subject.active').each(function() {
                          var $this = $(this);
                          var nLeft = $this.closest('.structure__subjects').offset().left - $this.offset().left - 14;
                          $this.find('> .structure__sub-subjects-wrap').css({'left': nLeft});
                          $this.find('> .structure__sub-subjects-wrap > .structure__sub-subjects').css({'min-width': $this.closest('.structure__subjects').outerWidth()});
                  
                        });
                      }
                      function setCardHeight() {
                  
                          $('.structure__subject-data').css('min-height', '0');
                          $('.structure__subject').each(function() {
                              var $first = $(this);
                              $first.siblings().each(function() {
                                  var $second = $(this);
                                  if($first.offset().top == $second.offset().top) {
                  
                                      if($first.find('.structure__subject-data').height() < $second.find('.structure__subject-data').height()) {
                                          $first.find('.structure__subject-data').css('min-height', $second.find('.structure__subject-data').height());
                                      }
                                  }
                              });
                          });
                      }
                      function structureOpen(targ) {
                          var $this = targ,
                              $parent = $this.closest('.structure__subject');
                  
                          $this.addClass('active');
                  
                          $parent.addClass('active').siblings().removeClass('active');
                          //
                          setCardLeft();
                  
                          $parent.find('> .js-structure-sub-subjects').animate({height: $parent.find('.js-structure-sub').outerHeight() + 30}, function() {
                              $parent.find('.structure__sub-subjects').addClass('active');
                              $(this).css('height', 'auto');
                              setCardHeight();
                              $('html, body').stop().animate({
                                scrollTop: $this.offset().top - 60
                              });
                          });
                  
                          $parent.addClass('active');
                  
                  
                      }
                      function structureClose(targ) {
                          var $this = targ,
                              $parent = $this.closest('.structure__subject');
                  
                          $this.removeClass('active');
                          $parent.find('> .js-structure-sub-subjects').animate({height: 0}, 100, function() {
                              $parent.find('.structure__sub-subjects').removeClass('active');
                              $parent.removeClass('active');
                          });
                      }
                  
                      setTimeout(setCardHeight, 300);
                  
                  
                        $('.js-structure-btn-more').click(function(e) {
                          e.preventDefault();
                  
                          //
                          var $this = $(this);
                          var $otherBtn = $this.closest('.structure__subject').siblings().find('> .js-structure-btn-more');
                          //
                          if(!$this.hasClass('active')) {
                  
                            if($otherBtn.filter('.active').length != 0) {
                              structureClose($otherBtn.filter('.active'));
                            }
                            structureOpen($this);
                          } else {
                              structureClose($this);
                          }
                  
                          $otherBtn.removeClass('active');
                  
                        });
                  
                        $(window).resize(function() {
                          setCardLeft();
                          setCardHeight();
                        });
                        $('.js-switcher').each(function() {
                            var $index = $(this).find('.js-switcher-link-structure.active').index();
                            $(this).find('.js-switcher-in-s').eq($index).show();
                        });
                        $('.js-switcher-link-structure').on('click', function(e) {
                            e.preventDefault();
                            var $this = $(this);
                  
                            if(!$this.hasClass('active')) {
                                var $index = $(this).index();
                                $this.siblings().removeClass('active');
                                $this.addClass('active');
                                $this.parents('.js-switcher').find('.js-switcher-in-s').hide().eq($index).fadeIn();
                  
                                $('.js-structure-sub-subjects').css('height', 0);
                                $('.js-structure-btn-more').removeClass('active');
                                $('.structure__subject').removeClass('active');
                                setCardHeight();
                            }
                        });
                  
                    }
                  }
                  structureMore.init();
                  
    
                  $(document).on("click",".equip_btn",function() {
                    console.log('5555')
                    var $arr = [];
                    var url = '?mode=edit&list_id=87&section_id=0&element_id=0&list_section_id='; 
                    // $(document).on("each",".main-grid-row-body",function() {
                    $('.main-grid-row-body').each(function(){
                        // console.log(url)
                        
            
                        // url = '?mode=edit&list_id=87&section_id=0&element_id=0&list_section_id=';
                        if($(this).hasClass('main-grid-row-checked')){
                            var id = '&item-id='+ $(this).data('id');
                            url = url+id;
                            var name = '&item-name='+$(this).data('name');
                            url = url+name;
                            var mol_id = '&mol='+$(this).data('mol');
                            url = url+mol_id;
                            var fakt_id = '&fakt='+$(this).data('fakt');
                            url = url+fakt_id;
                            var mol_name = '&molname='+$(this).data('molname');
                            url = url+mol_name;
                            var fakt_name = '&faktname='+$(this).data('faktname');
                            url = url+fakt_name;
            
                            // $arr.push($(this).data('id'));
                            
                           
                        
                        }
                        
                    });
                    console.log(url);
                    console.log($(location).attr("origin"));
                    var newurl = $(location).attr("origin")+'/equipment/'+ url;
                    $(location).attr('href',newurl);
                });
            
                $('.hidden_values').each(function(){
                    console.log('qqes');
                    var name = $(this).data('name');
                    var id = $(this).val();
                    $('.no-js').append('<a href="/equipment/?mode=edit&list_id=85&;section_id=0&element_id='+id+'" target="_blank">['+id+']'+name+'</a>')
                }); 
                $('#lists-list-row-count-wrapper>a').trigger('click');
            
                var sPageURL = new URLSearchParams(window.location.search.substring(1));
                    var mol = sPageURL.get("mol");
                        if(sPageURL.get("fakt") != "null"){
                            fakt = sPageURL.get("fakt");
                        }
                        mol_name = sPageURL.get("molname");
                        if(sPageURL.get("faktname") != "null"){
                            fakt_name = sPageURL.get("faktname");
                        }
                    
                        fakt_name = sPageURL.get("faktname");
                        element_id = sPageURL.get("element_id");
                        if(fakt_name != ''  &&  fakt != '' && element_id == '0'){
                            console.log(fakt_name)
                            console.log(fakt)
                            $("input[name='PROPERTY_600[]']").parent().next().append('['+fakt+'] '+fakt_name+'');
                            $("input[name='PROPERTY_600[]']").before('<input type="hidden" name="PROPERTY_600[]"  value="'+fakt+'">');
                        }
                        if(mol_name != '' &&  mol != '' && element_id == '0'){
                            $("input[name='PROPERTY_599[]']").parent().next().append('['+mol+'] '+mol_name+'');
                            $("input[name='PROPERTY_599[]']").before('<input type="hidden" name="PROPERTY_599[]"  value="'+mol+'">');
                        }
                        
                        
                
                
                
                        function myFunction() {
                            var popup = document.getElementById("myPopup");
                            popup.classList.toggle("show");
                        }


                // $('.decommission').on('click', function(){
                //     var id = $(this).data('id');
                //     // console.log(id);
                //     $.ajax({
                //         type: 'POST',
                //         url: '/decommission.php',
                //         data: {ID: id},
                //         success: function(data) {
                //
                //             // console.log(data);
                //             window.location.href = '/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=';
                //             // location.reload();
                //             // location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
                //         },
                //         error:  function(xhr, str){
                //             alert('Возникла ошибка: ' + xhr.responseCode);
                //         }
                //
                //     });
                // });
            
                $('.recover').on('click', function(){
                    var id = $(this).data('id');
                    $.ajax({
                        type: 'POST',
                        url: '/recover_archive.php',
                        data: {ID: id},
                        success: function(data) {
                            console.log(data);
                            location.href = '/equipment/?mode=view&list_id=89&section_id=0&list_section_id=';
                        },
                        error:  function(xhr, str){
                    //   alert('Возникла ошибка: ' + xhr.responseCode);
                        }
                        
                      });
                });
                $('.save_file').on('click', function(){
                    var data = new FormData();
                    // var data = $('.update_file').serialize();
                    //Form data
                        var form_data = $('.update_file').serializeArray();
                        $.each(form_data, function (key, input) {
                            data.append(input.name, input.value);
                        });
            
                    var file_data = $('input[name="FILE"]')[0].files;
                        for (var i = 0; i < file_data.length; i++) {
                            data.append("FILE[]", file_data[i]);
                        }
            
                        //Custom data
            // data.append('key', 'value');
                    $.ajax({
                        type: 'POST',
                        url: '/update_file.php',
                        processData: false,
                        contentType: false,
                        data: data,
                        success: function(data) {
                            console.log(data);
                        // location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
                        location.reload();
                        },
                        error:  function(xhr, str){
                    //   alert('Возникла ошибка: ' + xhr.responseCode);
                        }
                        
                      });
                });
    
                $('.file_add_popup_page').on('click',function(){
    
                    $('.input_file').trigger('click');
                    $('.update_file_page').css('display', 'block');
                    $(this).css('display', 'none');
    
                });
                $('.save_file_page').on('click', function(){
                    var data = new FormData();
                    // var data = $('.update_file').serialize();
                    //Form data
                        var form_data = $('.update_file_page').serializeArray();
                        $.each(form_data, function (key, input) {
                            data.append(input.name, input.value);
                        });
            
                    var file_data = $('input[name="FILE"]')[0].files;
                        for (var i = 0; i < file_data.length; i++) {
                            data.append("FILE[]", file_data[i]);
                        }
            
                        //Custom data
            // data.append('key', 'value');
                    $.ajax({
                        type: 'POST',
                        url: '/update_file_page.php',
                        processData: false,
                        contentType: false,
                        data: data,
                        success: function(data) {
                            console.log(data);
                        // location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
                        location.reload();
                        },
                        error:  function(xhr, str){
                    //   alert('Возникла ошибка: ' + xhr.responseCode);
                        }
                        
                      });
                });
    
                var val_poverk = $('#tab_el_edit_table select[name="PROPERTY_563"] option:selected').val();
                var val_atestats = $('#tab_el_edit_table select[name="PROPERTY_567"] option:selected').val();
                var val_kalibrovka = $('#tab_el_edit_table select[name="PROPERTY_571"] option:selected').val();
                if(val_poverk == 273){
                   var first_tr = $('#tab_el_edit_table select[name="PROPERTY_563"] option:selected').parents('tr');
                   for (let i = 0; i < 4; i++) {
                    first_tr.next().css('display','none');
                    first_tr = first_tr.next();
                    
                   }
                }
    
                if(val_atestats == 275){
                    var first_tr = $('#tab_el_edit_table select[name="PROPERTY_567"] option:selected').parents('tr');
                    for (let i = 0; i < 4; i++) {
                     first_tr.next().css('display','none');
                     first_tr = first_tr.next();
                     
                    }
                 }
    
                 if(val_kalibrovka == 277){
                    var first_tr = $('#tab_el_edit_table select[name="PROPERTY_571"] option:selected').parents('tr');
                    for (let i = 0; i < 4; i++) {
                     first_tr.next().css('display','none');
                     first_tr = first_tr.next();
                     
                    }
                 }
                 let poverk = $('#tab_el_edit_table select[name="PROPERTY_563"]');
                 poverk.on('change', function(){
                    
                    let val = $(this).find('option:selected').val();
                    console.log(val);
                    if(val == 272){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_563"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','table-row');
                            first_tr = first_tr.next();
                            
                        }
                    }
                    if(val == 273){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_563"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','none');
                            first_tr = first_tr.next();
                            
                        }
                    }
                 });
    
                 let atestats = $('#tab_el_edit_table select[name="PROPERTY_567"]');
                 atestats.on('change', function(){
                    
                    let val = $(this).find('option:selected').val();
                    console.log(val);
                    if(val == 274){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_567"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','table-row');
                            first_tr = first_tr.next();
                            
                        }
                    }
                    if(val == 275){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_567"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','none');
                            first_tr = first_tr.next();
                            
                        }
                    }
                 });
    
                 let kalibrovka = $('#tab_el_edit_table select[name="PROPERTY_571"]');
                 kalibrovka.on('change', function(){
                    
                    let val = $(this).find('option:selected').val();
                    console.log(val);
                    if(val == 276){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_571"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','table-row');
                            first_tr = first_tr.next();
                            
                        }
                    }
                    if(val == 277){
                        var first_tr = $('#tab_el_edit_table select[name="PROPERTY_571"] option:selected').parents('tr');
                        for (let i = 0; i < 4; i++) {
                            first_tr.next().css('display','none');
                            first_tr = first_tr.next();
                            
                        }
                    }
                 });
    
                //  $(document).on("change keyup input click", "input[name='PROPERTY_559[n0][VALUE]']", function() {
                //     if(this.value.match(/[^0-9.]/g)){
                //         this.value = this.value.replace(/[^0-9.]/g, "");
                //     };
                // });
                
                $("input[name='PROPERTY_559[n0][VALUE]'], input[name='PROPERTY_565[n0][VALUE]'],input[name='PROPERTY_569[n0][VALUE]'],input[name='PROPERTY_566[n0][VALUE]'],input[name='PROPERTY_570[n0][VALUE]'],input[name='PROPERTY_573[n0][VALUE]'],input[name='PROPERTY_574[n0][VALUE]'],input[name='PROPERTY_553[n0][VALUE]'").mask("99.99.9999");
                
                $(document).on('blur, change', 'input[name="PROPERTY_566[n0][VALUE]"], input[name="PROPERTY_570[n0][VALUE]"], input[name="PROPERTY_574[n0][VALUE]"]', function(){
                    let end_date = $(this).val();
                    var end_days = end_date.substring(0, 2);
                    var end_month = end_date.substring(3, 5);
                    var end_years = end_date.substring(7, 11);
                    var test_date_end = end_month+'.'+end_days+'.'+end_years;

                    let start_date = $(this).parents('tr').prev().find('input').val();
                    var start_days = start_date.substring(0, 2);
                    var start_month = start_date.substring(3, 5);
                    var start_years = start_date.substring(7, 11);
                    var test_date_start = start_month+'.'+start_days+'.'+start_years; 
                    
                    var days = (new Date(test_date_end).getTime() - new Date(test_date_start).getTime())/1000/60/60/24
                    var mounth = (new Date(test_date_end).getTime() - new Date(test_date_start).getTime())/1000/60/60/24/30.41
                    countMounth = Math.floor(mounth).toFixed(0);
                    console.log(countMounth)
                    console.log(start_date)
                    if(days < 365){
                        $(this).css('border-color', 'red');
                        if($(this).parent().find('.error_date_form').length < 1){
                            $(this).next().after('<div class="error_date_form">Следующая поверка не может быть раньше чем через 365 дней</div>');
                        }
                        
                    }else{
                        $(this).css('border-color', 'green');
                        $(this).parent().find('.error_date_form').remove();

                        $(this).parents('tr').next().find('input').val(countMounth+' мес.');
                    }
                    // console.log(days);
                });

                // function search_popup(){
                    
                // }

                // $('.accardion-container').accordion({
                //     heightStyle: 'content',
                //     header: '.accardion-item'
                // });
                $(document).on('click', '.accardion-item-title', function(){
                    $(this).toggleClass('active');
                    $(this).next().toggleClass('active');
                    // $('.accardion-item').removeClass('active');
                    // $(this).addClass('active');
                });

                // $(".accardion-item").prev().click(function() {
                //     $(this).parents(".accardion-container").find(".accardion-item").not(this).slideUp().prev().removeClass("active");
                //     $(this).next().not(":visible").slideDown().prev().addClass("active");
                // });
                deleted = new Array();
                $(document).on('click', '.del_selected_equip', function(){
                    
                    deleted.push($(this).parent().find('a').data('id'));
                    $(this).parent().remove();
                });
                deleted_sost = new Array();
                $(document).on('click', '.del_selected_equip_revers', function(){
                    
                    deleted_sost.push($(this).parent().find('a').data('id'));
                    $(this).parent().remove();
                });
                
                $(document).on('click', '.btn_add_composite', function(){
                    
                    let link = $(this).parent().find('a').clone();
                    let name = $(this).parent().find('a').text();
                    let href = $(this).parent().find('a').attr('href');
                    let id = $(this).parent().find('a').data('id');
                    // console.log(deleted);
                    // $('.eqp_selected').after('<div class="eqp_selected_item"><span class="del_selected_equip">'+link+'</span></div>');
                    // $('.eqp_selected').after(link);
                    // $('.eqp_selected').append("<div class='eqp_selected_item'><span class='del_selected_equip'>"+link+"</span></div>");
                    $('.eqp_selected').append("<div class='eqp_selected_item sostoit_iz'><span class='del_selected_equip'>X</span><a data-id="+id+" href="+href+">"+name+"</a></div>");
                    $(this).text('Добавлено');
                // $('.btn_add_composite').on('click', function(e){
                //     e.preventDefault();
                //     console.log('qq');
                //     // let link = $(this).parent.find('a').clone();
                    
                //     // $('.eqp_selected').appendTo(link);
                });

                $(document).on('click', '.btn_add_composite_revers', function(){
                    
                    let link = $(this).parent().find('a').clone();
                    let name = $(this).parent().find('a').text();
                    let href = $(this).parent().find('a').attr('href');
                    let id = $(this).parent().find('a').data('id');
                    // console.log(deleted);
                    // $('.eqp_selected').after('<div class="eqp_selected_item"><span class="del_selected_equip">'+link+'</span></div>');
                    // $('.eqp_selected').after(link);
                    // $('.eqp_selected').append("<div class='eqp_selected_item'><span class='del_selected_equip'>"+link+"</span></div>");
                    $('.eqp_selected_revers').append("<div class='eqp_selected_item vhodit_v_sostav'><span class='del_selected_equip'>X</span><a data-id="+id+" href="+href+">"+name+"</a></div>");
                    $(this).text('Добавлено');
                // $('.btn_add_composite').on('click', function(e){
                //     e.preventDefault();
                //     console.log('qq');
                //     // let link = $(this).parent.find('a').clone();
                    
                //     // $('.eqp_selected').appendTo(link);
                });
                    
                $(document).on('click', '.save_selected_eqp', function(){
                    console.log(deleted_sost);
                    var cur_element_id = $(this).data('element-id');
                    
                    var data = new Array();
                    var vhodit_v_sostav = new Array();
                    $('.eqp_selected_item').each(function(){
                        if($(this).hasClass('sostoit_iz')){
                            data.push($(this).find('a').data('id'));
                        }else if($(this).hasClass('vhodit_v_sostav')){
                            vhodit_v_sostav.push($(this).find('a').data('id'));
                        }
                        

                    })

                    console.log(vhodit_v_sostav);
                    $.ajax({
                        type: 'POST',
                        url: '/update_equip_composite.php',
                        // processData: false,
                        // contentType: false,
                        data: {data: data, element_id: cur_element_id, deleted: deleted, vhodit_v_sostav:vhodit_v_sostav, deleted_sost:deleted_sost},
                        success: function(data) {
                            console.log(data);
                        // location.href('/equipment/?mode=view&list_id=85&section_id=0&list_section_id=');
                        location.reload();
                        },
                        error:  function(xhr, str){
                    //   alert('Возникла ошибка: ' + xhr.responseCode);
                        }
                        
                      });
                });
                
            
            // $('.equip_btn').on('click', function(e){
            //     console.log('321')
            //     $('.main-grid-row-body').each(function(){
            //         console.log($(this))
            //         if($(this).hasClass('.main-grid-row-checked')){
                        
                    
            //         }
            //     });
            // });
            
                // Initialize Variables
                var closePopup = document.getElementById("popupclose");
                var overlay = document.getElementById("overlay");
                var popup = document.getElementById("popup");
                var button = document.getElementById("button");
                // Close Popup Event
                closePopup.onclick = function() {
                  overlay.style.display = 'none';
                  popup.style.display = 'none';
                };
                // Show Overlay and Popup
                button.onclick = function() {
                  overlay.style.display = 'block';
                  popup.style.display = 'block';
                }
    
                // Initialize Variables
                var closePopup2 = document.getElementById("popupclose2");
                // var overlay2 = document.getElementById("overlay2");
                var popup2 = document.getElementById("popup2");
                var button2 = document.getElementById("button2");
                // Close Popup Event
                closePopup2.onclick = function() {
                    overlay.style.display = 'none';
                    popup2.style.display = 'none';
                };
                // Show Overlay and Popup
                button2.onclick = function() {
                    overlay.style.display = 'block';
                    popup2.style.display = 'block';
                }
    
                $('#popup2 table.bx-edit-tab table.bx-edit-table tbody>tr').css('display', 'none');
                $('#popup2 table.bx-edit-tab table.bx-edit-table tbody>tr table#tblPROPERTY_577').parents("tr").css('display', 'block');
                $('#popup2 table.bx-edit-tab table.bx-edit-table tbody>tr table#tblPROPERTY_577 tr').css('display', 'block');
            });
    